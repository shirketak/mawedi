<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Hospital;
use Illuminate\Support\Facades\DB;

class HospitalStatsService
{
    public function statsForHospital(Hospital $hospital): array
    {
        $today = now()->toDateString();

        $bookingStats = $hospital->bookings()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status IN (?, ?) AND booking_date >= ? THEN 1 ELSE 0 END) as upcoming
            ', [
                BookingStatus::Completed->value,
                BookingStatus::Cancelled->value,
                BookingStatus::Pending->value,
                BookingStatus::Confirmed->value,
                $today,
            ])
            ->first();

        $revenue = $hospital->bookings()
            ->where('payment_status', PaymentStatus::Paid)
            ->sum(DB::raw('COALESCE(consultation_price, 0)'));

        return [
            'total_bookings' => (int) ($bookingStats->total ?? 0),
            'completed_bookings' => (int) ($bookingStats->completed ?? 0),
            'cancelled_bookings' => (int) ($bookingStats->cancelled ?? 0),
            'upcoming_bookings' => (int) ($bookingStats->upcoming ?? 0),
            'doctors_count' => $hospital->doctors()->count(),
            'specialties_count' => $hospital->specialties()->count(),
            'total_revenue' => (float) $revenue,
            'wallet_balance' => $hospital->walletBalance(),
            'subscription_status' => $hospital->subscription_status,
            'subscription_type' => $hospital->subscription_type,
        ];
    }
}
