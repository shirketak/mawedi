<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\BookingStatus;
use App\Models\Patient;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PatientService
{
    public function __construct(
        private readonly PatientRepositoryInterface $patientRepository,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->patientRepository->paginateWithFilters($filters, $perPage);
    }

    public function findByUuid(string $uuid): ?Patient
    {
        return $this->patientRepository->findByUuid($uuid);
    }

    public function update(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            $old = $patient->only(['name', 'phone', 'email', 'is_active']);
            $patient->update($data);

            $this->auditLogService->log(
                AuditAction::Updated,
                $patient,
                $old,
                $patient->fresh()->only(array_keys($old)),
            );

            return $patient->fresh();
        });
    }

    public function toggleStatus(Patient $patient): Patient
    {
        return DB::transaction(function () use ($patient) {
            $patient->update(['is_active' => ! $patient->is_active]);

            $this->auditLogService->log(
                $patient->is_active ? AuditAction::Activated : AuditAction::Deactivated,
                $patient,
                ['is_active' => ! $patient->is_active],
                ['is_active' => $patient->is_active],
            );

            return $patient->fresh();
        });
    }

    public function bookingsGrouped(Patient $patient): array
    {
        $today = now()->toDateString();
        $bookings = $patient->bookings()
            ->with(['hospital', 'doctor', 'specialty'])
            ->latest('booking_date')
            ->get();

        return [
            'completed' => $bookings->where('status', BookingStatus::Completed),
            'cancelled' => $bookings->where('status', BookingStatus::Cancelled),
            'upcoming' => $bookings->filter(
                fn ($b) => in_array($b->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)
                    && $b->booking_date->toDateString() >= $today
            ),
            'future' => $bookings->filter(
                fn ($b) => in_array($b->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)
                    && $b->booking_date->toDateString() > $today
            ),
            'no_show' => $bookings->where('status', BookingStatus::NoShow),
            'missed' => $bookings->filter(
                fn ($b) => in_array($b->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)
                    && $b->booking_date->toDateString() < $today
            ),
            'all' => $bookings,
        ];
    }
}
