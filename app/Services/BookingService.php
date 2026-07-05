<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\SlotStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionType;
use App\Models\Booking;
use App\Models\DoctorSlot;
use App\Models\Hospital;
use App\Models\Patient;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\BookingRescheduleLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly BookingRescheduleLogRepositoryInterface $rescheduleLogRepository,
        private readonly HospitalWalletService $walletService,
    ) {}

    public function listForHospital(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->bookingRepository->paginateForHospital($hospitalId, $filters, $perPage);
    }

    public function listRescheduleLogs(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->rescheduleLogRepository->paginateForHospital($hospitalId, $filters, $perPage);
    }

    public function getAvailableSlotsForDoctor(int $hospitalId, int $doctorId): Collection
    {
        return DoctorSlot::query()
            ->where('doctor_id', $doctorId)
            ->whereHas('doctor', fn ($q) => $q->where('hospital_id', $hospitalId)->where('is_active', true))
            ->where('status', SlotStatus::Available)
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    public function createForHospital(Hospital $hospital, array $data): Booking
    {
        $this->assertHospitalCanBook($hospital);

        return DB::transaction(function () use ($hospital, $data) {
            $slot = $this->lockAvailableSlot((int) $data['doctor_slot_id'], $hospital->id);
            $doctor = $slot->doctor;

            $patientId = Patient::query()
                ->where('phone', $data['patient_phone'])
                ->value('id');

            $booking = $this->bookingRepository->create([
                'hospital_id' => $hospital->id,
                'doctor_id' => $doctor->id,
                'specialty_id' => $doctor->specialty_id,
                'doctor_slot_id' => $slot->id,
                'patient_id' => $patientId,
                'patient_name' => $data['patient_name'],
                'patient_phone' => $data['patient_phone'],
                'booking_date' => $slot->date,
                'booking_time' => $slot->start_time,
                'status' => BookingStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
                'consultation_price' => $doctor->consultation_price,
            ]);

            $slot->update(['status' => SlotStatus::Booked]);

            return $booking;
        });
    }

    public function confirm(Booking $booking): Booking
    {
        $this->assertStatusTransition($booking, [BookingStatus::Pending], BookingStatus::Confirmed);

        return DB::transaction(function () use ($booking) {
            $booking->load('hospital');
            $this->chargeUsageFeeIfNeeded($booking->hospital, $booking);
            $booking->update(['status' => BookingStatus::Confirmed]);

            return $booking->fresh();
        });
    }

    public function cancel(Booking $booking): Booking
    {
        $this->assertStatusTransition(
            $booking,
            [BookingStatus::Pending, BookingStatus::Confirmed],
            BookingStatus::Cancelled,
        );

        return DB::transaction(function () use ($booking) {
            $booking->update(['status' => BookingStatus::Cancelled]);
            $this->releaseSlot($booking);

            return $booking->fresh();
        });
    }

    public function complete(Booking $booking): Booking
    {
        $this->assertStatusTransition($booking, [BookingStatus::Confirmed], BookingStatus::Completed);

        $booking->update(['status' => BookingStatus::Completed]);

        return $booking->fresh();
    }

    public function markNoShow(Booking $booking): Booking
    {
        $this->assertStatusTransition(
            $booking,
            [BookingStatus::Pending, BookingStatus::Confirmed],
            BookingStatus::NoShow,
        );

        $booking->update(['status' => BookingStatus::NoShow]);

        return $booking->fresh();
    }

    public function markPaid(Booking $booking): Booking
    {
        if ($booking->payment_status === PaymentStatus::Paid) {
            return $booking;
        }

        $booking->update(['payment_status' => PaymentStatus::Paid]);

        return $booking->fresh();
    }

    private function assertHospitalCanBook(Hospital $hospital): void
    {
        if (! $hospital->is_active) {
            throw ValidationException::withMessages([
                'hospital' => 'حساب المستشفى غير مفعّل.',
            ]);
        }

        if (in_array($hospital->subscription_status, [SubscriptionStatus::Suspended, SubscriptionStatus::Expired], true)) {
            throw ValidationException::withMessages([
                'subscription' => 'الاشتراك غير نشط. لا يمكن إنشاء حجوزات جديدة.',
            ]);
        }
    }

    private function lockAvailableSlot(int $slotId, int $hospitalId): DoctorSlot
    {
        $slot = DoctorSlot::query()
            ->where('id', $slotId)
            ->where('status', SlotStatus::Available)
            ->whereHas('doctor', fn ($q) => $q->where('hospital_id', $hospitalId)->where('is_active', true))
            ->lockForUpdate()
            ->first();

        if (! $slot) {
            throw ValidationException::withMessages([
                'doctor_slot_id' => 'الموعد المحدد غير متاح.',
            ]);
        }

        return $slot->load('doctor');
    }

    private function chargeUsageFeeIfNeeded(Hospital $hospital, Booking $booking): void
    {
        if ($hospital->subscription_type !== SubscriptionType::UsageBased) {
            return;
        }

        $fee = (float) $hospital->usage_fee_per_booking;
        if ($fee <= 0) {
            return;
        }

        $transaction = $this->walletService->deductBookingFee($hospital, $fee, $booking->id);
        if (! $transaction) {
            throw ValidationException::withMessages([
                'wallet' => 'رصيد المحفظة غير كافٍ لتأكيد الحجز. المطلوب: '.$fee.' د.ل',
            ]);
        }
    }

    private function releaseSlot(Booking $booking): void
    {
        if (! $booking->doctor_slot_id) {
            return;
        }

        DoctorSlot::query()
            ->where('id', $booking->doctor_slot_id)
            ->where('status', SlotStatus::Booked)
            ->update(['status' => SlotStatus::Available]);
    }

    /**
     * @param  BookingStatus[]  $allowedFrom
     */
    private function assertStatusTransition(Booking $booking, array $allowedFrom, BookingStatus $to): void
    {
        if (! in_array($booking->status, $allowedFrom, true)) {
            throw ValidationException::withMessages([
                'status' => 'لا يمكن تغيير حالة الحجز من «'.$booking->status->label().'» إلى «'.$to->label().'».',
            ]);
        }
    }
}
