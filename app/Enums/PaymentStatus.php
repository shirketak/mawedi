<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'غير مدفوع',
            self::Paid => 'مدفوع',
            self::Refunded => 'مسترد',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-warning text-dark',
            self::Paid => 'bg-success',
            self::Refunded => 'bg-secondary',
        };
    }
}
