<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionType;
use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Services\HospitalStatsService;
use Illuminate\View\View;

class HospitalStatsController extends Controller
{
    public function __construct(
        private readonly HospitalStatsService $statsService,
    ) {}

    public function show(Hospital $hospital): View
    {
        $this->authorize('viewStats', $hospital);

        $hospital->load(['wallet', 'primaryUser']);
        $stats = $this->statsService->statsForHospital($hospital);
        $subscriptionTypes = SubscriptionType::options();
        $subscriptionStatuses = SubscriptionStatus::options();

        return view('admin.hospitals.show', compact(
            'hospital',
            'stats',
            'subscriptionTypes',
            'subscriptionStatuses',
        ));
    }
}
