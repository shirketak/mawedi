<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Trial = 'trial';
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Trial => 'فترة مجانية',
            self::Active => 'نشط',
            self::Expired => 'منتهي',
            self::Suspended => 'موقوف',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Trial => 'bg-info text-dark',
            self::Active => 'bg-success',
            self::Expired => 'bg-danger',
            self::Suspended => 'bg-warning text-dark',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $status) => [$status->value => $status->label()]
        )->all();
    }
}
