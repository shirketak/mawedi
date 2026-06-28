<?php

namespace App\Services;

use App\Models\Specialty;
use App\Repositories\Contracts\SpecialtyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SpecialtyService
{
    public function __construct(
        private readonly SpecialtyRepositoryInterface $specialtyRepository,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->specialtyRepository->paginateWithFilters($filters, $perPage);
    }

    public function getActiveList(): Collection
    {
        return $this->specialtyRepository->getActiveList();
    }

    public function findByUuid(string $uuid): ?Specialty
    {
        return $this->specialtyRepository->findByUuid($uuid);
    }

    public function create(array $data): Specialty
    {
        return $this->specialtyRepository->create($data);
    }

    public function update(Specialty $specialty, array $data): Specialty
    {
        return $this->specialtyRepository->update($specialty, $data);
    }

    public function delete(Specialty $specialty): bool
    {
        return DB::transaction(fn () => $this->specialtyRepository->delete($specialty));
    }

    public function toggleStatus(Specialty $specialty): Specialty
    {
        return $this->specialtyRepository->update($specialty, [
            'is_active' => ! $specialty->is_active,
        ]);
    }
}
