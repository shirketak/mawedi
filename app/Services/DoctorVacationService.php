<?php

namespace App\Services;

use App\Enums\VacationType;
use App\Models\Doctor;
use App\Models\DoctorVacation;
use Illuminate\Support\Facades\DB;

class DoctorVacationService
{
    public function __construct(
        private readonly DoctorSlotService $slotService,
        private readonly BookingRescheduleService $rescheduleService,
    ) {}

    public function listForDoctor(Doctor $doctor)
    {
        return $doctor->vacations()->orderByDesc('date')->get();
    }

    public function add(Doctor $doctor, array $data, bool $rescheduleBookings = false, ?string $rescheduleReason = null, ?object $rescheduledBy = null): DoctorVacation
    {
        return DB::transaction(function () use ($doctor, $data, $rescheduleBookings, $rescheduleReason, $rescheduledBy) {
            $vacation = DoctorVacation::create([
                'doctor_id' => $doctor->id,
                'date' => $data['date'],
                'type' => VacationType::from($data['type']),
                'reason' => $data['reason'] ?? null,
            ]);

            $this->slotService->regenerateSlotsForDoctor($doctor, \Carbon\Carbon::parse($data['date']));

            if ($rescheduleBookings && $rescheduledBy) {
                $this->rescheduleService->postponeDoctorDay(
                    $doctor,
                    $data['date'],
                    $rescheduleReason ?? 'إجازة الطبيب',
                    $rescheduledBy
                );
            }

            return $vacation;
        });
    }

    public function delete(DoctorVacation $vacation): void
    {
        DB::transaction(function () use ($vacation) {
            $doctor = $vacation->doctor;
            $date = $vacation->date->toDateString();
            $vacation->delete();
            $this->slotService->regenerateSlotsForDoctor($doctor, \Carbon\Carbon::parse($date));
        });
    }
}
