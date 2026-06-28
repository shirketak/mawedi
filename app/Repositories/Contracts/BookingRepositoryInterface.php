<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForHospital(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getBookingsForDoctorOnDate(int $doctorId, string $date): \Illuminate\Database\Eloquent\Collection;
}
