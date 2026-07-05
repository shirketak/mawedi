<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function __construct(
        private readonly PatientService $patientService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Patient::class);

        $patients = $this->patientService->list($request->only(['search', 'is_active', 'sort', 'direction']));

        return view('admin.patients.index', compact('patients'));
    }

    public function show(Patient $patient): View
    {
        $this->authorize('view', $patient);

        $bookingGroups = $this->patientService->bookingsGrouped($patient);

        return view('admin.patients.show', compact('patient', 'bookingGroups'));
    }

    public function edit(Patient $patient): View
    {
        $this->authorize('update', $patient);

        return view('admin.patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $this->patientService->update($patient, $request->validated());

        return redirect()->route('admin.patients.show', $patient)
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    public function toggleStatus(Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $this->patientService->toggleStatus($patient);
        $status = $patient->fresh()->is_active ? 'تفعيل' : 'إيقاف';

        return back()->with('success', "تم {$status} المستخدم بنجاح.");
    }
}
