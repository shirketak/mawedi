<?php

namespace App\Services;

use App\Models\Hospital;
use App\Models\HospitalUser;
use App\Repositories\Contracts\HospitalRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HospitalService
{
    public function __construct(
        private readonly HospitalRepositoryInterface $hospitalRepository,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->hospitalRepository->paginateWithFilters($filters, $perPage);
    }

    public function findByUuid(string $uuid): ?Hospital
    {
        return $this->hospitalRepository->findByUuid($uuid);
    }

    public function create(array $data, array $userData): Hospital
    {
        return DB::transaction(function () use ($data, $userData) {
            $hospital = $this->hospitalRepository->create($data);

            HospitalUser::create([
                'hospital_id' => $hospital->id,
                'name' => $userData['name'] ?? $hospital->name,
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'is_active' => true,
            ]);

            return $hospital->load('primaryUser');
        });
    }

    public function update(Hospital $hospital, array $data): Hospital
    {
        return $this->hospitalRepository->update($hospital, $data);
    }

    public function delete(Hospital $hospital): bool
    {
        return DB::transaction(fn () => $this->hospitalRepository->delete($hospital));
    }

    public function toggleStatus(Hospital $hospital): Hospital
    {
        return $this->hospitalRepository->toggleStatus($hospital);
    }
}
