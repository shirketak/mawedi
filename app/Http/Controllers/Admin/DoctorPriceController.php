<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDoctorPriceRequest;
use App\Models\Doctor;
use App\Services\DoctorPriceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DoctorPriceController extends Controller
{
    public function __construct(
        private readonly DoctorPriceService $priceService,
    ) {}

    public function edit(Doctor $doctor): View
    {
        $this->authorize('updatePrice', $doctor);

        $doctor->load(['hospital', 'specialty', 'priceLogs.changedBy']);

        return view('admin.doctors.price', compact('doctor'));
    }

    public function update(UpdateDoctorPriceRequest $request, Doctor $doctor): RedirectResponse
    {
        $this->authorize('updatePrice', $doctor);

        $this->priceService->updatePrice(
            $doctor,
            (float) $request->consultation_price,
            auth('admin')->user(),
        );

        return back()->with('success', 'تم تحديث سعر الكشف بنجاح.');
    }
}
