<?php

namespace App\Enums;

enum SlotStatus: string
{
    case Available = 'available';
    case Booked = 'booked';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'متاح',
            self::Booked => 'محجوز',
            self::Blocked => 'محظور',
        };
    }
}
