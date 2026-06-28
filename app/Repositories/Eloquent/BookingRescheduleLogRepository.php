<?php

namespace App\Repositories\Eloquent;

use App\Models\BookingRescheduleLog;
use App\Repositories\Contracts\BookingRescheduleLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookingRescheduleLogRepository extends BaseRepository implements BookingRescheduleLogRepositoryInterface
{
    public function __construct(BookingRescheduleLog $model)
    {
        parent::__construct($model);
    }

    public function paginateForHospital(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['doctor', 'hospital'])
            ->where('hospital_id', $hospitalId);

        if (! empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('original_date', '>=', $filters['date_from']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }
}
