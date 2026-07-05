<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case Deposit = 'deposit';
    case Deduction = 'deduction';
    case Adjustment = 'adjustment';
    case BookingFee = 'booking_fee';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => 'إيداع',
            self::Deduction => 'خصم',
            self::Adjustment => 'تعديل رصيد',
            self::BookingFee => 'رسوم حجز',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Deposit => 'bg-success',
            self::Deduction, self::BookingFee => 'bg-danger',
            self::Adjustment => 'bg-warning text-dark',
        };
    }
}
