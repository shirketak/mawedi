<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\SlotStatus;
use App\Models\BookingRescheduleLog;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingRescheduleService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly DoctorSlotService $slotService,
    ) {}

    /**
     * Reschedule all bookings on a specific date to the next available slots.
     */
    public function postponeDoctorDay(
        Doctor $doctor,
        string $originalDate,
        string $reason,
        object $rescheduledBy,
    ): BookingRescheduleLog {
        return DB::transaction(function () use ($doctor, $originalDate, $reason, $rescheduledBy) {
            $bookings = $this->bookingRepository->getBookingsForDoctorOnDate($doctor->id, $originalDate);

            if ($bookings->isEmpty()) {
                throw new \RuntimeException('لا توجد حجوزات في هذا اليوم.');
            }

            $fromDate = Carbon::parse($originalDate)->addDay();
            $availableSlots = $this->slotService->getAvailableSlotsFromDate(
                $doctor,
                $fromDate,
                $bookings->count() + 50
            );

            if ($availableSlots->count() < $bookings->count()) {
                throw new \RuntimeException('لا توجد مواعيد كافية لنقل جميع الحجوزات.');
            }

            $moves = [];

            foreach ($bookings as $index => $booking) {
                $newSlot = $availableSlots[$index];

                $oldDate = $booking->booking_date->toDateString();
                $oldTime = $booking->booking_time;

                if ($booking->doctor_slot_id) {
                    DoctorSlot::query()
                        ->where('id', $booking->doctor_slot_id)
                        ->update(['status' => SlotStatus::Available]);
                }

                DoctorSlot::query()
                    ->where('id', $newSlot->id)
                    ->update(['status' => SlotStatus::Booked]);

                $booking->update([
                    'doctor_slot_id' => $newSlot->id,
                    'booking_date' => $newSlot->date,
                    'booking_time' => $newSlot->start_time,
                    'status' => BookingStatus::Rescheduled,
                ]);

                $moves[] = [
                    'booking_uuid' => $booking->uuid,
                    'patient_name' => $booking->patient_name,
                    'from' => ['date' => $oldDate, 'time' => $oldTime],
                    'to' => ['date' => $newSlot->date->toDateString(), 'time' => $newSlot->start_time],
                ];
            }

            return BookingRescheduleLog::create([
                'hospital_id' => $doctor->hospital_id,
                'doctor_id' => $doctor->id,
                'original_date' => $originalDate,
                'reason' => $reason,
                'rescheduled_by_type' => get_class($rescheduledBy),
                'rescheduled_by_id' => $rescheduledBy->id,
                'details' => [
                    'total_moved' => count($moves),
                    'moves' => $moves,
                ],
            ]);
        });
    }
}
