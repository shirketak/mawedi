<?php

namespace App\Enums;

enum VacationType: string
{
    case FullDay = 'full_day';
    case Exception = 'exception';

    public function label(): string
    {
        return match ($this) {
            self::FullDay => 'يوم كامل',
            self::Exception => 'استثنائية',
        };
    }
}
