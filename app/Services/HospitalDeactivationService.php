<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\DeactivationReason;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionType;
use App\Models\Hospital;
use Carbon\Carbon;

class HospitalDeactivationService
{
    public function __construct(
        private readonly HospitalSubscriptionService $subscriptionService,
    ) {}

    public function processAll(): int
    {
        $count = 0;
        $today = now()->toDateString();

        Hospital::query()
            ->where('is_active', true)
            ->chunkById(50, function ($hospitals) use (&$count, $today) {
                foreach ($hospitals as $hospital) {
                    if ($this->shouldDeactivate($hospital, $today)) {
                        $this->subscriptionService->deactivate(
                            $hospital,
                            $this->resolveReason($hospital, $today),
                        );
                        $count++;
                    }
                }
            });

        return $count;
    }

    public function shouldDeactivate(Hospital $hospital, ?string $today = null): bool
    {
        $today = $today ?? now()->toDateString();

        if ($hospital->subscription_status === SubscriptionStatus::Trial
            && $hospital->trial_ends_at
            && $hospital->trial_ends_at->toDateString() < $today) {
            return true;
        }

        if ($hospital->subscription_type === SubscriptionType::Monthly
            && $hospital->subscription_ends_at
            && $hospital->subscription_ends_at->toDateString() < $today
            && ! $hospital->isOnTrial()) {
            return true;
        }

        if ($hospital->subscription_type === SubscriptionType::UsageBased
            && ! $hospital->isOnTrial()
            && $hospital->walletBalance() <= 0) {
            return true;
        }

        return false;
    }

    private function resolveReason(Hospital $hospital, string $today): DeactivationReason
    {
        if ($hospital->subscription_status === SubscriptionStatus::Trial
            && $hospital->trial_ends_at?->toDateString() < $today) {
            return DeactivationReason::TrialExpired;
        }

        if ($hospital->subscription_type === SubscriptionType::UsageBased
            && $hospital->walletBalance() <= 0) {
            return DeactivationReason::WalletEmpty;
        }

        return DeactivationReason::SubscriptionExpired;
    }
}
