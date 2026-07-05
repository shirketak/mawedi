<?php

namespace App\Enums;

enum AdminRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Finance = 'finance';
    case Support = 'support';
    case Reports = 'reports';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'مدير النظام',
            self::Admin => 'مشرف',
            self::Finance => 'مالية',
            self::Support => 'دعم فني',
            self::Reports => 'تقارير',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::SuperAdmin => ['*'],
            self::Admin => [
                'dashboard', 'reports', 'hospitals', 'hospitals.stats', 'specialties', 'doctors.price',
                'patients', 'bookings', 'subscriptions', 'settings', 'audit_logs', 'admin_users',
            ],
            self::Finance => [
                'dashboard', 'hospitals.view', 'hospitals.stats', 'wallet', 'subscriptions', 'reports',
            ],
            self::Support => [
                'dashboard', 'hospitals.view', 'hospitals.stats', 'patients', 'bookings', 'audit_logs',
            ],
            self::Reports => [
                'dashboard', 'reports', 'hospitals.view', 'hospitals.stats', 'bookings',
            ],
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $role) => [$role->value => $role->label()]
        )->all();
    }
}
