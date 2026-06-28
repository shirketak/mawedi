<?php

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Models\Doctor;
use App\Models\DoctorWorkingDay;
use App\Models\DoctorWorkingPeriod;
use Illuminate\Support\Facades\DB;

class DoctorScheduleService
{
    public function __construct(
        private readonly DoctorSlotService $slotService,
        private readonly BookingRescheduleService $rescheduleService,
    ) {}

    public function getSchedule(Doctor $doctor): array
    {
        $days = $doctor->workingDays()
            ->with('periods')
            ->get()
            ->sortBy(fn ($d) => $d->day_of_week->value);

        return $days->map(fn ($day) => [
            'day' => $day->day_of_week,
            'periods' => $day->periods->map(fn ($p) => [
                'id' => $p->id,
                'start_time' => substr($p->start_time, 0, 5),
                'end_time' => substr($p->end_time, 0, 5),
            ]),
        ])->values()->all();
    }

    public function syncSchedule(Doctor $doctor, array $schedule, bool $rescheduleBookings = false, ?string $rescheduleReason = null, ?object $rescheduledBy = null): void
    {
        DB::transaction(function () use ($doctor, $schedule) {
            $existingDayIds = $doctor->workingDays()->pluck('id');

            DoctorWorkingPeriod::query()
                ->whereIn('doctor_working_day_id', $existingDayIds)
                ->delete();

            $doctor->workingDays()->delete();

            foreach ($schedule as $dayData) {
                if (empty($dayData['periods'])) {
                    continue;
                }

                $workingDay = DoctorWorkingDay::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => DayOfWeek::from((int) $dayData['day_of_week']),
                ]);

                foreach ($dayData['periods'] as $period) {
                    if (empty($period['start_time']) || empty($period['end_time'])) {
                        continue;
                    }

                    DoctorWorkingPeriod::create([
                        'doctor_working_day_id' => $workingDay->id,
                        'start_time' => $period['start_time'],
                        'end_time' => $period['end_time'],
                    ]);
                }
            }

            $this->slotService->regenerateSlotsForDoctor($doctor);
        });
    }

    public function postponeDay(
        Doctor $doctor,
        string $date,
        string $reason,
        object $rescheduledBy,
    ) {
        return $this->rescheduleService->postponeDoctorDay($doctor, $date, $reason, $rescheduledBy);
    }
}
