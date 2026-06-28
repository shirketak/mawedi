<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\UpdateHospitalProfileRequest;
use App\Services\HospitalProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly HospitalProfileService $profileService,
    ) {}

    public function edit(): View
    {
        $hospital = auth('hospital')->user()->hospital;
        $this->authorize('updateProfile', $hospital);

        return view('hospital.profile.edit', compact('hospital'));
    }

    public function update(UpdateHospitalProfileRequest $request): RedirectResponse
    {
        $hospital = auth('hospital')->user()->hospital;
        $this->authorize('updateProfile', $hospital);

        $this->profileService->update(
            $hospital,
            $request->safe()->except('logo'),
            $request->file('logo')
        );

        return back()->with('success', 'تم تحديث بيانات المستشفى بنجاح.');
    }
}
