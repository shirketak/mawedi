<?php

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Enums\SlotStatus;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use App\Models\DoctorVacation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DoctorSlotService
{
    public const GENERATION_WEEKS = 8;

    public function regenerateSlotsForDoctor(Doctor $doctor, ?Carbon $fromDate = null): void
    {
        $fromDate = ($fromDate ?? now())->startOfDay();
        $toDate = $fromDate->copy()->addWeeks(self::GENERATION_WEEKS);

        $workingDays = $doctor->workingDays()
            ->with('periods')
            ->get()
            ->keyBy(fn ($day) => $day->day_of_week->value);

        $vacationDates = $doctor->vacations()
            ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->flip();

        $duration = $doctor->consultation_duration_minutes;

        DB::transaction(function () use ($doctor, $fromDate, $toDate, $workingDays, $vacationDates, $duration) {
            $period = CarbonPeriod::create($fromDate, $toDate);

            foreach ($period as $date) {
                $dateString = $date->toDateString();
                $dayOfWeek = DayOfWeek::from($date->dayOfWeek);

                if ($vacationDates->has($dateString)) {
                    $this->blockDateSlots($doctor, $dateString);

                    continue;
                }

                if (! $workingDays->has($dayOfWeek->value)) {
                    $this->removeUnbookedSlotsForDate($doctor, $dateString);

                    continue;
                }

                $this->syncSlotsForDate(
                    $doctor,
                    $dateString,
                    $workingDays->get($dayOfWeek->value)->periods,
                    $duration
                );
            }
        });
    }

    private function syncSlotsForDate(Doctor $doctor, string $date, Collection $periods, int $duration): void
    {
        $expectedSlots = collect();

        foreach ($periods as $period) {
            $start = Carbon::parse($date.' '.$period->start_time);
            $end = Carbon::parse($date.' '.$period->end_time);

            while ($start->copy()->addMinutes($duration)->lte($end)) {
                $slotEnd = $start->copy()->addMinutes($duration);
                $expectedSlots->push([
                    'start_time' => $start->format('H:i:s'),
                    'end_time' => $slotEnd->format('H:i:s'),
                ]);
                $start->addMinutes($duration);
            }
        }

        $existingSlots = DoctorSlot::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('start_time');

        $expectedStartTimes = $expectedSlots->pluck('start_time')->all();

        foreach ($existingSlots as $startTime => $slot) {
            if (! in_array($startTime, $expectedStartTimes, true) && $slot->status !== SlotStatus::Booked) {
                $slot->delete();
            }
        }

        foreach ($expectedSlots as $slotData) {
            $existing = $existingSlots->get($slotData['start_time']);

            if ($existing) {
                if ($existing->status === SlotStatus::Blocked) {
                    $existing->update(['status' => SlotStatus::Available]);
                }

                continue;
            }

            DoctorSlot::create([
                'doctor_id' => $doctor->id,
                'date' => $date,
                'start_time' => $slotData['start_time'],
                'end_time' => $slotData['end_time'],
                'status' => SlotStatus::Available,
            ]);
        }
    }

    private function blockDateSlots(Doctor $doctor, string $date): void
    {
        DoctorSlot::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', $date)
            ->where('status', SlotStatus::Available)
            ->update(['status' => SlotStatus::Blocked]);

        DoctorSlot::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', $date)
            ->where('status', '!=', SlotStatus::Booked)
            ->where('status', '!=', SlotStatus::Blocked)
            ->delete();
    }

    private function removeUnbookedSlotsForDate(Doctor $doctor, string $date): void
    {
        DoctorSlot::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', $date)
            ->where('status', '!=', SlotStatus::Booked)
            ->delete();
    }

    public function getAvailableSlotsFromDate(Doctor $doctor, Carbon $fromDate, int $limit = 100): Collection
    {
        return DoctorSlot::query()
            ->where('doctor_id', $doctor->id)
            ->where('status', SlotStatus::Available)
            ->where(function ($q) use ($fromDate) {
                $q->whereDate('date', '>', $fromDate->toDateString())
                    ->orWhere(function ($q2) use ($fromDate) {
                        $q2->whereDate('date', $fromDate->toDateString())
                            ->whereTime('start_time', '>=', $fromDate->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
    }
}
