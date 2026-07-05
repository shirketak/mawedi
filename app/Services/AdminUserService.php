<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Admin::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['role']) && $filters['role'] !== '') {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Admin
    {
        return DB::transaction(function () use ($data) {
            $admin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->auditLogService->log(
                AuditAction::Created,
                $admin,
                null,
                $admin->only(['name', 'email', 'role', 'is_active']),
            );

            return $admin;
        });
    }

    public function update(Admin $admin, array $data): Admin
    {
        return DB::transaction(function () use ($admin, $data) {
            $old = $admin->only(['name', 'email', 'role', 'is_active']);

            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'is_active' => $data['is_active'] ?? $admin->is_active,
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $admin->update($payload);

            $this->auditLogService->log(
                AuditAction::Updated,
                $admin,
                $old,
                $admin->fresh()->only(['name', 'email', 'role', 'is_active']),
            );

            return $admin->fresh();
        });
    }

    public function delete(Admin $admin, Admin $actor): void
    {
        if ($actor->id === $admin->id) {
            throw new RuntimeException('لا يمكنك حذف حسابك الحالي.');
        }

        DB::transaction(function () use ($admin) {
            $this->auditLogService->log(
                AuditAction::Deleted,
                $admin,
                $admin->only(['name', 'email', 'role']),
            );

            $admin->delete();
        });
    }

    public function toggleStatus(Admin $admin, Admin $actor): Admin
    {
        if ($actor->id === $admin->id) {
            throw new RuntimeException('لا يمكنك إيقاف حسابك الحالي.');
        }

        return DB::transaction(function () use ($admin) {
            $wasActive = $admin->is_active;
            $admin->update(['is_active' => ! $admin->is_active]);

            $this->auditLogService->log(
                $admin->is_active ? AuditAction::Activated : AuditAction::Deactivated,
                $admin,
                ['is_active' => $wasActive],
                ['is_active' => $admin->is_active],
            );

            return $admin->fresh();
        });
    }
}
