<?php

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    public function paginateForHospital(int $hospitalId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['doctor', 'specialty'])
            ->where('hospital_id', $hospitalId);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('patient_phone', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('booking_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('booking_date', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('booking_date')->orderByDesc('booking_time')
            ->paginate($perPage)->withQueryString();
    }

    public function getBookingsForDoctorOnDate(int $doctorId, string $date): Collection
    {
        return $this->model->newQuery()
            ->where('doctor_id', $doctorId)
            ->whereDate('booking_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('booking_time')
            ->get();
    }
}
