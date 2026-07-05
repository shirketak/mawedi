<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\DeactivationReason;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionType;
use App\Models\Admin;
use App\Models\Hospital;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HospitalSubscriptionService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly SystemSettingService $settings,
        private readonly HospitalWalletService $walletService,
    ) {}

    public function initializeForNewHospital(Hospital $hospital, array $overrides = []): Hospital
    {
        $trialDays = (int) $this->settings->get('default_free_trial_days', 14);
        $type = isset($overrides['subscription_type'])
            ? ($overrides['subscription_type'] instanceof SubscriptionType
                ? $overrides['subscription_type']
                : SubscriptionType::from($overrides['subscription_type']))
            : SubscriptionType::Monthly;
        $monthlyPrice = (float) ($overrides['monthly_price'] ?? $this->settings->get('default_monthly_price', 100));
        $usageFee = (float) ($overrides['usage_fee_per_booking'] ?? $this->settings->get('default_usage_fee_per_booking', 5));

        $hospital->update([
            'subscription_type' => $type,
            'subscription_status' => $type === SubscriptionType::UsageBased
                ? SubscriptionStatus::Active
                : SubscriptionStatus::Trial,
            'monthly_price' => $monthlyPrice,
            'usage_fee_per_booking' => $usageFee,
            'free_trial_days' => $trialDays,
            'trial_ends_at' => now()->addDays($trialDays)->toDateString(),
            'subscription_starts_at' => now()->toDateString(),
        ]);

        $this->walletService->ensureWallet($hospital);

        return $hospital->fresh();
    }

    public function updateSubscription(Hospital $hospital, array $data, ?Admin $admin = null): Hospital
    {
        return DB::transaction(function () use ($hospital, $data, $admin) {
            $old = $hospital->only([
                'subscription_type', 'subscription_status', 'monthly_price',
                'usage_fee_per_booking', 'subscription_starts_at', 'subscription_ends_at',
            ]);

            $updates = [];

            if (isset($data['subscription_type'])) {
                $updates['subscription_type'] = $data['subscription_type'];
            }

            if (isset($data['monthly_price'])) {
                $updates['monthly_price'] = $data['monthly_price'];
            }

            if (isset($data['usage_fee_per_booking'])) {
                $updates['usage_fee_per_booking'] = $data['usage_fee_per_booking'];
            }

            if (isset($data['subscription_starts_at'])) {
                $updates['subscription_starts_at'] = $data['subscription_starts_at'];
            }

            if (isset($data['subscription_duration_months']) && isset($data['subscription_starts_at'])) {
                $startsAt = Carbon::parse($data['subscription_starts_at']);
                $updates['subscription_ends_at'] = $startsAt
                    ->copy()
                    ->addMonths((int) $data['subscription_duration_months'])
                    ->toDateString();
                $updates['subscription_status'] = SubscriptionStatus::Active;
            } elseif (isset($data['subscription_ends_at'])) {
                $updates['subscription_ends_at'] = $data['subscription_ends_at'];
            }

            if (($updates['subscription_type'] ?? $hospital->subscription_type) === SubscriptionType::UsageBased) {
                $updates['subscription_status'] = SubscriptionStatus::Active;
            }

            $hospital->update($updates);

            $this->auditLogService->log(
                AuditAction::SubscriptionChanged,
                $hospital,
                $old,
                $hospital->fresh()->only(array_keys($old)),
                $admin,
            );

            return $hospital->fresh();
        });
    }

    public function grantFreeTrial(Hospital $hospital, int $days, ?Admin $admin = null): Hospital
    {
        return DB::transaction(function () use ($hospital, $days, $admin) {
            $old = [
                'free_trial_days' => $hospital->free_trial_days,
                'trial_ends_at' => $hospital->trial_ends_at?->toDateString(),
                'subscription_status' => $hospital->subscription_status?->value,
            ];

            $trialEndsAt = now()->addDays($days)->toDateString();

            $hospital->update([
                'free_trial_days' => $days,
                'trial_ends_at' => $trialEndsAt,
                'subscription_status' => SubscriptionStatus::Trial,
                'is_active' => true,
                'deactivation_reason' => null,
                'deactivated_at' => null,
            ]);

            $this->auditLogService->log(
                AuditAction::FreeTrialGranted,
                $hospital,
                $old,
                ['free_trial_days' => $days, 'trial_ends_at' => $trialEndsAt],
                $admin,
            );

            return $hospital->fresh();
        });
    }

    public function deactivate(Hospital $hospital, DeactivationReason $reason): Hospital
    {
        $hospital->update([
            'is_active' => false,
            'deactivation_reason' => $reason,
            'deactivated_at' => now(),
            'subscription_status' => $reason === DeactivationReason::SubscriptionExpired
                || $reason === DeactivationReason::TrialExpired
                ? SubscriptionStatus::Expired
                : $hospital->subscription_status,
        ]);

        $this->auditLogService->log(
            AuditAction::Deactivated,
            $hospital,
            ['is_active' => true],
            ['is_active' => false, 'reason' => $reason->value],
        );

        return $hospital->fresh();
    }

    public function activate(Hospital $hospital, ?Admin $admin = null): Hospital
    {
        $hospital->update([
            'is_active' => true,
            'deactivation_reason' => null,
            'deactivated_at' => null,
        ]);

        $this->auditLogService->log(
            AuditAction::Activated,
            $hospital,
            ['is_active' => false],
            ['is_active' => true],
            $admin,
        );

        return $hospital->fresh();
    }
}
