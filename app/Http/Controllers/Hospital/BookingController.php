<?php

namespace App\Http\Controllers\Hospital;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Doctor;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $hospital = auth('hospital')->user()->hospital;
        $filters = $request->only(['search', 'doctor_id', 'status', 'payment_status', 'date_from', 'date_to']);
        $bookings = $this->bookingService->listForHospital($hospital->id, $filters);
        $doctors = Doctor::where('hospital_id', $hospital->id)->orderBy('name')->get();
        $statuses = BookingStatus::cases();
        $paymentStatuses = PaymentStatus::cases();

        return view('hospital.bookings.index', compact('bookings', 'doctors', 'statuses', 'paymentStatuses', 'filters'));
    }
}
