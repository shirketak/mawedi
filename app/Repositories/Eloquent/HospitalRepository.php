<?php

namespace App\Repositories\Eloquent;

use App\Models\Hospital;
use App\Repositories\Contracts\HospitalRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HospitalRepository extends BaseRepository implements HospitalRepositoryInterface
{
    public function __construct(Hospital $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('primaryUser');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['governorate']) && $filters['governorate'] !== '') {
            $query->where('governorate', $filters['governorate']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function toggleStatus(Hospital $hospital): Hospital
    {
        $hospital->update(['is_active' => ! $hospital->is_active]);

        return $hospital->fresh();
    }
}
