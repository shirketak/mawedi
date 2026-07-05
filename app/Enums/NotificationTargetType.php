<?php

namespace App\Enums;

enum NotificationTargetType: string
{
    case AllHospitals = 'all_hospitals';
    case Hospital = 'hospital';
    case AllPatients = 'all_patients';
    case Patient = 'patient';

    public function label(): string
    {
        return match ($this) {
            self::AllHospitals => 'جميع المستشفيات',
            self::Hospital => 'مستشفى محدد',
            self::AllPatients => 'جميع المستخدمين',
            self::Patient => 'مستخدم محدد',
        };
    }
}
