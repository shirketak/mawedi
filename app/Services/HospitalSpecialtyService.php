<?php

namespace App\Services;

use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\Support\Facades\DB;

class HospitalSpecialtyService
{
    public function getHospitalSpecialties(Hospital $hospital)
    {
        return $hospital->specialties()->orderBy('name')->get();
    }

    public function getAvailableSpecialties(Hospital $hospital)
    {
        $attachedIds = $hospital->specialties()->pluck('specialties.id');

        return Specialty::query()
            ->where('is_active', true)
            ->whereNotIn('id', $attachedIds)
            ->orderBy('name')
            ->get();
    }

    public function attach(Hospital $hospital, int $specialtyId): void
    {
        DB::transaction(function () use ($hospital, $specialtyId) {
            $specialty = Specialty::query()
                ->where('is_active', true)
                ->findOrFail($specialtyId);

            $hospital->specialties()->syncWithoutDetaching([$specialty->id]);
        });
    }

    public function detach(Hospital $hospital, Specialty $specialty): void
    {
        DB::transaction(function () use ($hospital, $specialty) {
            if ($hospital->doctors()->where('specialty_id', $specialty->id)->exists()) {
                throw new \RuntimeException('لا يمكن حذف التخصص لوجود أطباء مرتبطين به.');
            }

            $hospital->specialties()->detach($specialty->id);
        });
    }
}
