<?php

namespace App\Enums;

enum AuditAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case Activated = 'activated';
    case Deactivated = 'deactivated';
    case SubscriptionChanged = 'subscription_changed';
    case WalletDeposit = 'wallet_deposit';
    case WalletDeduction = 'wallet_deduction';
    case WalletAdjustment = 'wallet_adjustment';
    case PriceChanged = 'price_changed';
    case FreeTrialGranted = 'free_trial_granted';
    case SettingsUpdated = 'settings_updated';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'إنشاء',
            self::Updated => 'تعديل',
            self::Deleted => 'حذف',
            self::Restored => 'استعادة',
            self::Activated => 'تفعيل',
            self::Deactivated => 'إيقاف',
            self::SubscriptionChanged => 'تغيير اشتراك',
            self::WalletDeposit => 'إيداع محفظة',
            self::WalletDeduction => 'خصم محفظة',
            self::WalletAdjustment => 'تعديل محفظة',
            self::PriceChanged => 'تغيير سعر',
            self::FreeTrialGranted => 'منح فترة مجانية',
            self::SettingsUpdated => 'تحديث إعدادات',
        };
    }
}
