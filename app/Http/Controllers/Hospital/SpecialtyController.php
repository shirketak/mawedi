<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\AttachHospitalSpecialtyRequest;
use App\Models\Specialty;
use App\Services\HospitalSpecialtyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SpecialtyController extends Controller
{
    public function __construct(
        private readonly HospitalSpecialtyService $specialtyService,
    ) {}

    public function index(): View
    {
        $hospital = auth('hospital')->user()->hospital;
        $specialties = $this->specialtyService->getHospitalSpecialties($hospital);
        $availableSpecialties = $this->specialtyService->getAvailableSpecialties($hospital);

        return view('hospital.specialties.index', compact('specialties', 'availableSpecialties'));
    }

    public function store(AttachHospitalSpecialtyRequest $request): RedirectResponse
    {
        $hospital = auth('hospital')->user()->hospital;

        try {
            $this->specialtyService->attach($hospital, (int) $request->specialty_id);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم إضافة التخصص للمستشفى بنجاح.');
    }

    public function destroy(Specialty $specialty): RedirectResponse
    {
        $hospital = auth('hospital')->user()->hospital;

        try {
            $this->specialtyService->detach($hospital, $specialty);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم حذف التخصص من المستشفى بنجاح.');
    }
}
