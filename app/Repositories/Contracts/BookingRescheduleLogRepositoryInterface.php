<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookingRescheduleLogRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForHospital(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator;
}
