<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GrantFreeTrialRequest;
use App\Http\Requests\Admin\UpdateHospitalSubscriptionRequest;
use App\Models\Hospital;
use App\Services\HospitalSubscriptionService;
use Illuminate\Http\RedirectResponse;

class HospitalSubscriptionController extends Controller
{
    public function __construct(
        private readonly HospitalSubscriptionService $subscriptionService,
    ) {}

    public function update(UpdateHospitalSubscriptionRequest $request, Hospital $hospital): RedirectResponse
    {
        $this->authorize('manageSubscription', $hospital);

        $this->subscriptionService->updateSubscription(
            $hospital,
            $request->validated(),
            auth('admin')->user(),
        );

        return back()->with('success', 'تم تحديث الاشتراك بنجاح.');
    }

    public function grantTrial(GrantFreeTrialRequest $request, Hospital $hospital): RedirectResponse
    {
        $this->authorize('manageSubscription', $hospital);

        $this->subscriptionService->grantFreeTrial(
            $hospital,
            (int) $request->days,
            auth('admin')->user(),
        );

        return back()->with('success', 'تم منح الفترة المجانية بنجاح.');
    }

    public function activate(Hospital $hospital): RedirectResponse
    {
        $this->authorize('update', $hospital);

        $this->subscriptionService->activate($hospital, auth('admin')->user());

        return back()->with('success', 'تم تفعيل المستشفى بنجاح.');
    }
}
