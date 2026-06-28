<?php

namespace App\Repositories\Contracts;

use App\Models\Doctor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DoctorRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForHospital(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findForHospital(int $hospitalId, string $uuid): ?Doctor;
}
