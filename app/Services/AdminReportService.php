<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\SubscriptionType;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\HospitalWallet;
use App\Models\Patient;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportService
{
    public function dashboardStats(): array
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        return [
            'hospitals' => Hospital::count(),
            'active_hospitals' => Hospital::where('is_active', true)->count(),
            'inactive_hospitals' => Hospital::where('is_active', false)->count(),
            'patients' => Patient::count(),
            'doctors' => Doctor::count(),
            'specialties' => Specialty::count(),
            'bookings_today' => Booking::whereDate('booking_date', $today)->count(),
            'bookings_month' => Booking::where('booking_date', '>=', $monthStart)->count(),
            'completed_bookings' => Booking::where('status', BookingStatus::Completed)->count(),
            'cancelled_bookings' => Booking::where('status', BookingStatus::Cancelled)->count(),
            'monthly_subscriptions' => Hospital::where('subscription_type', SubscriptionType::Monthly)->count(),
            'usage_subscriptions' => Hospital::where('subscription_type', SubscriptionType::UsageBased)->count(),
            'total_wallet_balance' => (float) HospitalWallet::sum('balance'),
        ];
    }

    public function dailyBookingsChart(int $days = 30): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $dateExpr = $this->dateExpression('created_at');

        $data = Booking::query()
            ->where('created_at', '>=', $start)
            ->selectRaw("{$dateExpr} as date, COUNT(*) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $values = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $labels[] = Carbon::parse($date)->format('m/d');
            $values[] = (int) ($data[$date] ?? 0);
        }

        return compact('labels', 'values');
    }

    public function monthlyBookingsChart(int $months = 12): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();
        $monthExpr = $this->monthExpression('created_at');

        $data = Booking::query()
            ->where('created_at', '>=', $start)
            ->selectRaw("{$monthExpr} as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $values = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonths($i)->format('Y-m');
            $labels[] = $start->copy()->addMonths($i)->format('Y/m');
            $values[] = (int) ($data[$month] ?? 0);
        }

        return compact('labels', 'values');
    }

    public function userGrowthChart(int $months = 12): array
    {
        return $this->growthChart(Patient::query(), $months);
    }

    public function hospitalGrowthChart(int $months = 12): array
    {
        return $this->growthChart(Hospital::query(), $months);
    }

    public function topHospitalsByBookings(int $limit = 10): array
    {
        return Hospital::query()
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->limit($limit)
            ->get()
            ->map(fn ($h) => ['name' => $h->name, 'total' => $h->bookings_count])
            ->all();
    }

    public function topSpecialtiesByBookings(int $limit = 10): array
    {
        return Specialty::query()
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->limit($limit)
            ->get()
            ->map(fn ($s) => ['name' => $s->name, 'total' => $s->bookings_count])
            ->all();
    }

    public function hospitalStats(): array
    {
        $stats = $this->dashboardStats();

        return [
            'hospitals' => $stats['hospitals'],
            'active_hospitals' => $stats['active_hospitals'],
            'inactive_hospitals' => $stats['inactive_hospitals'],
            'doctors' => $stats['doctors'],
            'specialties' => $stats['specialties'],
        ];
    }

    public function bookingStats(): array
    {
        $stats = $this->dashboardStats();

        return [
            'bookings_today' => $stats['bookings_today'],
            'bookings_month' => $stats['bookings_month'],
            'completed_bookings' => $stats['completed_bookings'],
            'cancelled_bookings' => $stats['cancelled_bookings'],
        ];
    }

    public function financialStats(): array
    {
        $stats = $this->dashboardStats();

        return [
            'monthly_subscriptions' => $stats['monthly_subscriptions'],
            'usage_subscriptions' => $stats['usage_subscriptions'],
            'total_wallet_balance' => $stats['total_wallet_balance'],
        ];
    }

    private function growthChart($query, int $months): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();
        $monthExpr = $this->monthExpression('created_at');

        $data = (clone $query)
            ->where('created_at', '>=', $start)
            ->selectRaw("{$monthExpr} as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $values = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonths($i)->format('Y-m');
            $labels[] = $start->copy()->addMonths($i)->format('Y/m');
            $values[] = (int) ($data[$month] ?? 0);
        }

        return compact('labels', 'values');
    }

    private function monthExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    private function dateExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "date({$column})",
            default => "DATE({$column})",
        };
    }
}
