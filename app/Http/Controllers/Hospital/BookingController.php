<?php

namespace App\Http\Controllers\Hospital;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Doctor;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
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

    public function create(Request $request): View
    {
        $this->authorize('create', Booking::class);

        $hospital = auth('hospital')->user()->hospital;
        $doctors = Doctor::where('hospital_id', $hospital->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedDoctorId = (int) $request->query('doctor_id', $doctors->first()?->id ?? 0);
        $slots = $selectedDoctorId
            ? $this->bookingService->getAvailableSlotsForDoctor($hospital->id, $selectedDoctorId)
            : collect();

        return view('hospital.bookings.create', compact('doctors', 'selectedDoctorId', 'slots'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $this->authorize('create', Booking::class);

        $hospital = auth('hospital')->user()->hospital;

        $this->bookingService->createForHospital($hospital, $request->validated());

        return redirect()
            ->route('hospital.bookings.index')
            ->with('success', 'تم إنشاء الحجز بنجاح.');
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        $this->authorize('manage', $booking);

        try {
            $this->bookingService->confirm($booking);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'تم تأكيد الحجز.');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorize('manage', $booking);

        try {
            $this->bookingService->cancel($booking);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'تم إلغاء الحجز.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        $this->authorize('manage', $booking);

        try {
            $this->bookingService->complete($booking);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'تم إكمال الحجز.');
    }

    public function noShow(Booking $booking): RedirectResponse
    {
        $this->authorize('manage', $booking);

        try {
            $this->bookingService->markNoShow($booking);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'تم تسجيل عدم حضور المريض.');
    }

    public function markPaid(Booking $booking): RedirectResponse
    {
        $this->authorize('manage', $booking);

        $this->bookingService->markPaid($booking);

        return back()->with('success', 'تم تسجيل الدفع.');
    }
}
