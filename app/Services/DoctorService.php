<?php

namespace App\Services;

use App\Helpers\FileUploader;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DoctorService
{
    public function __construct(
        private readonly DoctorRepositoryInterface $doctorRepository,
        private readonly DoctorSlotService $slotService,
        private readonly DoctorPriceService $priceService,
    ) {}

    public function listForHospital(Hospital $hospital, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->doctorRepository->paginateForHospital($hospital->id, $filters, $perPage);
    }

    public function findForHospital(Hospital $hospital, string $uuid): ?Doctor
    {
        return $this->doctorRepository->findForHospital($hospital->id, $uuid);
    }

    public function create(Hospital $hospital, array $data, ?\Illuminate\Http\UploadedFile $photo = null): Doctor
    {
        return DB::transaction(function () use ($hospital, $data, $photo) {
            $this->ensureSpecialtyBelongsToHospital($hospital, (int) $data['specialty_id']);

            if ($photo) {
                $data['photo'] = FileUploader::upload($photo, 'doctors/photos');
            }

            $price = $data['consultation_price'] ?? 0;
            unset($data['consultation_price']);

            $data['hospital_id'] = $hospital->id;

            $doctor = $this->doctorRepository->create($data);

            if ($price > 0) {
                $this->priceService->updatePrice($doctor, (float) $price, auth('hospital')->user());
            }

            return $doctor->fresh();
        });
    }

    public function update(Doctor $doctor, array $data, ?\Illuminate\Http\UploadedFile $photo = null): Doctor
    {
        return DB::transaction(function () use ($doctor, $data, $photo) {
            if (isset($data['specialty_id'])) {
                $this->ensureSpecialtyBelongsToHospital($doctor->hospital, (int) $data['specialty_id']);
            }

            if ($photo) {
                FileUploader::delete($doctor->photo);
                $data['photo'] = FileUploader::upload($photo, 'doctors/photos');
            }

            $durationChanged = isset($data['consultation_duration_minutes'])
                && (int) $data['consultation_duration_minutes'] !== $doctor->consultation_duration_minutes;

            $price = $data['consultation_price'] ?? null;
            unset($data['consultation_price']);

            $doctor = $this->doctorRepository->update($doctor, $data);

            if ($price !== null) {
                $this->priceService->updatePrice($doctor, (float) $price, auth('hospital')->user());
                $doctor = $doctor->fresh();
            }

            if ($durationChanged) {
                $this->slotService->regenerateSlotsForDoctor($doctor);
            }

            return $doctor;
        });
    }

    public function delete(Doctor $doctor): bool
    {
        return DB::transaction(function () use ($doctor) {
            FileUploader::delete($doctor->photo);

            return $this->doctorRepository->delete($doctor);
        });
    }

    public function toggleStatus(Doctor $doctor): Doctor
    {
        return $this->doctorRepository->update($doctor, [
            'is_active' => ! $doctor->is_active,
        ]);
    }

    private function ensureSpecialtyBelongsToHospital(Hospital $hospital, int $specialtyId): void
    {
        if (! $hospital->specialties()->where('specialties.id', $specialtyId)->exists()) {
            throw new \RuntimeException('التخصص غير متاح في هذا المستشفى.');
        }
    }
}
