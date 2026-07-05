<?php

namespace App\Enums;

enum SubscriptionType: string
{
    case Monthly = 'monthly';
    case UsageBased = 'usage_based';

    public function label(): string
    {
        return match ($this) {
            self::Monthly => 'اشتراك شهري',
            self::UsageBased => 'حسب الاستخدام',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $type) => [$type->value => $type->label()]
        )->all();
    }
}
