<?php

namespace App\Services;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\BookingRescheduleLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookingService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly BookingRescheduleLogRepositoryInterface $rescheduleLogRepository,
    ) {}

    public function listForHospital(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->bookingRepository->paginateForHospital($hospitalId, $filters, $perPage);
    }

    public function listRescheduleLogs(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->rescheduleLogRepository->paginateForHospital($hospitalId, $filters, $perPage);
    }
}
