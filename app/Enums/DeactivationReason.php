<?php

namespace App\Enums;

enum DeactivationReason: string
{
    case Manual = 'manual';
    case SubscriptionExpired = 'subscription_expired';
    case TrialExpired = 'trial_expired';
    case WalletEmpty = 'wallet_empty';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'إيقاف يدوي',
            self::SubscriptionExpired => 'انتهاء الاشتراك',
            self::TrialExpired => 'انتهاء الفترة المجانية',
            self::WalletEmpty => 'رصيد المحفظة صفر',
        };
    }
}
