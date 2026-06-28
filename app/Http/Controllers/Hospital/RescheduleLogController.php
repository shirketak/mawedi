<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RescheduleLogController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    public function index(Request $request): View
    {
        $hospital = auth('hospital')->user()->hospital;
        $filters = $request->only(['doctor_id', 'date_from']);
        $logs = $this->bookingService->listRescheduleLogs($hospital->id, $filters);
        $doctors = Doctor::where('hospital_id', $hospital->id)->orderBy('name')->get();

        return view('hospital.reschedule-logs.index', compact('logs', 'doctors', 'filters'));
    }
}
