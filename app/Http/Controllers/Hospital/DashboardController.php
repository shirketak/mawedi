<?php

namespace App\Http\Controllers\Hospital;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Doctor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hospital = auth('hospital')->user()->hospital;

        $stats = [
            'doctors' => $hospital->doctors()->count(),
            'active_doctors' => $hospital->doctors()->where('is_active', true)->count(),
            'specialties' => $hospital->specialties()->count(),
            'bookings_today' => Booking::where('hospital_id', $hospital->id)
                ->whereDate('booking_date', today())->count(),
            'pending_bookings' => Booking::where('hospital_id', $hospital->id)
                ->where('status', BookingStatus::Pending)->count(),
            'unpaid_bookings' => Booking::where('hospital_id', $hospital->id)
                ->where('payment_status', PaymentStatus::Unpaid)->count(),
        ];

        $upcomingBookings = Booking::with(['doctor', 'specialty'])
            ->where('hospital_id', $hospital->id)
            ->whereDate('booking_date', '>=', today())
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->take(8)
            ->get();

        $recentDoctors = Doctor::with('specialty')
            ->where('hospital_id', $hospital->id)
            ->latest()
            ->take(5)
            ->get();

        return view('hospital.dashboard.index', compact('stats', 'upcomingBookings', 'recentDoctors', 'hospital'));
    }
}
