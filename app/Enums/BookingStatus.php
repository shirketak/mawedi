<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rescheduled = 'rescheduled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Confirmed => 'مؤكد',
            self::Completed => 'مكتمل',
            self::Cancelled => 'ملغي',
            self::Rescheduled => 'تم إعادة الجدولة',
            self::NoShow => 'لم يحضر',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-warning text-dark',
            self::Confirmed => 'bg-primary',
            self::Completed => 'bg-success',
            self::Cancelled => 'bg-danger',
            self::Rescheduled => 'bg-info text-dark',
            self::NoShow => 'bg-secondary',
        };
    }
}
