<?php

namespace App\Http\Controllers\Hospital;

use App\Enums\DayOfWeek;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\PostponeDoctorDayRequest;
use App\Http\Requests\Hospital\StoreDoctorRequest;
use App\Http\Requests\Hospital\StoreDoctorVacationRequest;
use App\Http\Requests\Hospital\SyncDoctorScheduleRequest;
use App\Http\Requests\Hospital\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\DoctorVacation;
use App\Services\DoctorScheduleService;
use App\Services\DoctorService;
use App\Services\DoctorVacationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function __construct(
        private readonly DoctorService $doctorService,
        private readonly DoctorScheduleService $scheduleService,
        private readonly DoctorVacationService $vacationService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Doctor::class);

        $hospital = auth('hospital')->user()->hospital;
        $doctors = $this->doctorService->listForHospital($hospital, $request->only(['search', 'specialty_id', 'is_active']));
        $specialties = $hospital->specialties()->orderBy('name')->get();

        return view('hospital.doctors.index', compact('doctors', 'specialties'));
    }

    public function create(): View
    {
        $this->authorize('create', Doctor::class);

        $hospital = auth('hospital')->user()->hospital;
        $specialties = $hospital->specialties()->orderBy('name')->get();

        return view('hospital.doctors.create', compact('specialties'));
    }

    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $this->authorize('create', Doctor::class);

        $hospital = auth('hospital')->user()->hospital;

        try {
            $doctor = $this->doctorService->create(
                $hospital,
                $request->validated(),
                $request->file('photo')
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('hospital.doctors.schedule', $doctor)
            ->with('success', 'تم إضافة الطبيب بنجاح. يمكنك الآن إعداد جدول العمل.');
    }

    public function edit(Doctor $doctor): View
    {
        $this->authorize('update', $doctor);

        $hospital = auth('hospital')->user()->hospital;
        $specialties = $hospital->specialties()->orderBy('name')->get();

        return view('hospital.doctors.edit', compact('doctor', 'specialties'));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->authorize('update', $doctor);

        try {
            $this->doctorService->update($doctor, $request->validated(), $request->file('photo'));
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('hospital.doctors.index')
            ->with('success', 'تم تحديث بيانات الطبيب بنجاح.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $this->authorize('delete', $doctor);

        $this->doctorService->delete($doctor);

        return redirect()->route('hospital.doctors.index')
            ->with('success', 'تم حذف الطبيب بنجاح.');
    }

    public function toggleStatus(Doctor $doctor): RedirectResponse
    {
        $this->authorize('update', $doctor);

        $this->doctorService->toggleStatus($doctor);
        $status = $doctor->fresh()->is_active ? 'تفعيل' : 'إيقاف';

        return back()->with('success', "تم {$status} الطبيب بنجاح.");
    }

    public function schedule(Doctor $doctor): View
    {
        $this->authorize('manageSchedule', $doctor);

        $schedule = $this->scheduleService->getSchedule($doctor);
        $days = DayOfWeek::options();

        return view('hospital.doctors.schedule', compact('doctor', 'schedule', 'days'));
    }

    public function updateSchedule(SyncDoctorScheduleRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->authorize('manageSchedule', $doctor);

        $this->scheduleService->syncSchedule($doctor, $request->schedule);

        return back()->with('success', 'تم تحديث جدول العمل وإنشاء المواعيد تلقائياً.');
    }

    public function vacations(Doctor $doctor): View
    {
        $this->authorize('manageSchedule', $doctor);

        $vacations = $this->vacationService->listForDoctor($doctor);

        return view('hospital.doctors.vacations', compact('doctor', 'vacations'));
    }

    public function storeVacation(StoreDoctorVacationRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->authorize('manageSchedule', $doctor);

        try {
            $this->vacationService->add(
                $doctor,
                $request->validated(),
                $request->boolean('reschedule_bookings'),
                $request->reschedule_reason,
                auth('hospital')->user()
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم إضافة الإجازة بنجاح.');
    }

    public function destroyVacation(Doctor $doctor, DoctorVacation $vacation): RedirectResponse
    {
        $this->authorize('manageSchedule', $doctor);

        if ($vacation->doctor_id !== $doctor->id) {
            abort(404);
        }

        $this->vacationService->delete($vacation);

        return back()->with('success', 'تم حذف الإجازة بنجاح.');
    }

    public function postpone(PostponeDoctorDayRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->authorize('manageSchedule', $doctor);

        try {
            $this->scheduleService->postponeDay(
                $doctor,
                $request->date,
                $request->reason,
                auth('hospital')->user()
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم تأجيل جميع الحجوزات بنجاح.');
    }
}
