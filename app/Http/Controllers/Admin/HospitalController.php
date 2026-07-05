<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LibyaGovernorate;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionType;
use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHospitalRequest;
use App\Http\Requests\Admin\UpdateHospitalRequest;
use App\Models\Hospital;
use App\Services\HospitalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HospitalController extends Controller
{
    public function __construct(
        private readonly HospitalService $hospitalService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Hospital::class);

        $hospitals = $this->hospitalService->list($request->only([
            'search', 'governorate', 'is_active',
            'subscription_type', 'subscription_status', 'sort', 'direction',
        ]));
        $governorates = LibyaGovernorate::options();
        $subscriptionTypes = SubscriptionType::options();
        $subscriptionStatuses = SubscriptionStatus::options();

        return view('admin.hospitals.index', compact(
            'hospitals',
            'governorates',
            'subscriptionTypes',
            'subscriptionStatuses',
        ));
    }

    public function create(): View
    {
        $this->authorize('create', Hospital::class);
        $governorates = LibyaGovernorate::options();

        return view('admin.hospitals.create', compact('governorates'));
    }

    public function store(StoreHospitalRequest $request): RedirectResponse
    {
        $this->authorize('create', Hospital::class);

        $data = $request->safe()->except(['user_email', 'user_password', 'user_password_confirmation', 'logo']);
        $data['is_active'] = true;

        if ($request->hasFile('logo')) {
            $data['logo'] = FileUploader::upload($request->file('logo'), 'hospitals/logos');
        }

        $this->hospitalService->create($data, [
            'email' => $request->user_email,
            'password' => $request->user_password,
        ]);

        return redirect()->route('admin.hospitals.index')
            ->with('success', 'تم إضافة المستشفى بنجاح.');
    }

    public function edit(Hospital $hospital): View
    {
        $this->authorize('update', $hospital);
        $governorates = LibyaGovernorate::options();

        return view('admin.hospitals.edit', compact('hospital', 'governorates'));
    }

    public function update(UpdateHospitalRequest $request, Hospital $hospital): RedirectResponse
    {
        $this->authorize('update', $hospital);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            FileUploader::delete($hospital->logo);
            $data['logo'] = FileUploader::upload($request->file('logo'), 'hospitals/logos');
        }

        $this->hospitalService->update($hospital, $data);

        return redirect()->route('admin.hospitals.index')
            ->with('success', 'تم تحديث بيانات المستشفى بنجاح.');
    }

    public function destroy(Hospital $hospital): RedirectResponse
    {
        $this->authorize('delete', $hospital);

        FileUploader::delete($hospital->logo);
        $this->hospitalService->delete($hospital);

        return redirect()->route('admin.hospitals.index')
            ->with('success', 'تم حذف المستشفى بنجاح.');
    }

    public function toggleStatus(Hospital $hospital): RedirectResponse
    {
        $this->authorize('update', $hospital);

        $this->hospitalService->toggleStatus($hospital);
        $status = $hospital->fresh()->is_active ? 'تفعيل' : 'إيقاف';

        return back()->with('success', "تم {$status} المستشفى بنجاح.");
    }
}
