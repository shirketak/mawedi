<?php

namespace App\Repositories\Contracts;

use App\Models\Hospital;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface HospitalRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function toggleStatus(Hospital $hospital): Hospital;
}
