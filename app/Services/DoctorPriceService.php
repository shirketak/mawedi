<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorPriceLog;
use App\Models\HospitalUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DoctorPriceService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {}

    public function updatePrice(Doctor $doctor, float $newPrice, ?Model $changedBy = null): Doctor
    {
        return DB::transaction(function () use ($doctor, $newPrice, $changedBy) {
            $oldPrice = (float) $doctor->consultation_price;

            if ($oldPrice === $newPrice) {
                return $doctor;
            }

            $doctor->update(['consultation_price' => $newPrice]);

            DoctorPriceLog::create([
                'doctor_id' => $doctor->id,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'changed_by_type' => $changedBy?->getMorphClass(),
                'changed_by_id' => $changedBy?->getKey(),
            ]);

            if ($changedBy instanceof Admin || $changedBy instanceof HospitalUser) {
                $this->auditLogService->log(
                    AuditAction::PriceChanged,
                    $doctor,
                    ['consultation_price' => $oldPrice],
                    ['consultation_price' => $newPrice],
                    $changedBy instanceof Admin ? $changedBy : null,
                );
            }

            return $doctor->fresh();
        });
    }
}
