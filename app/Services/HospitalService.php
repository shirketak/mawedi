<?php

namespace App\Services;

use App\Enums\AuditAction;
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
        private readonly HospitalSubscriptionService $subscriptionService,
        private readonly AuditLogService $auditLogService,
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
            $data['is_active'] = $data['is_active'] ?? true;
            $hospital = $this->hospitalRepository->create($data);

            HospitalUser::create([
                'hospital_id' => $hospital->id,
                'name' => $userData['name'] ?? $hospital->name,
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'is_active' => true,
            ]);

            $this->subscriptionService->initializeForNewHospital($hospital);

            $this->auditLogService->log(AuditAction::Created, $hospital, null, $hospital->toArray());

            return $hospital->load(['primaryUser', 'wallet']);
        });
    }

    public function update(Hospital $hospital, array $data): Hospital
    {
        return DB::transaction(function () use ($hospital, $data) {
            $old = $hospital->toArray();
            $hospital = $this->hospitalRepository->update($hospital, $data);

            $this->auditLogService->log(AuditAction::Updated, $hospital, $old, $hospital->fresh()->toArray());

            return $hospital;
        });
    }

    public function delete(Hospital $hospital): bool
    {
        return DB::transaction(function () use ($hospital) {
            $this->auditLogService->log(AuditAction::Deleted, $hospital, $hospital->toArray());

            return $this->hospitalRepository->delete($hospital);
        });
    }

    public function toggleStatus(Hospital $hospital): Hospital
    {
        return DB::transaction(function () use ($hospital) {
            $wasActive = $hospital->is_active;
            $hospital = $this->hospitalRepository->toggleStatus($hospital);

            $this->auditLogService->log(
                $hospital->is_active ? AuditAction::Activated : AuditAction::Deactivated,
                $hospital,
                ['is_active' => $wasActive],
                ['is_active' => $hospital->is_active],
            );

            return $hospital;
        });
    }
}
