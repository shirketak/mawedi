<?php

namespace App\Enums;

enum LibyaGovernorate: string
{
    case Tripoli = 'tripoli';
    case Benghazi = 'benghazi';
    case Misrata = 'misrata';
    case Bayda = 'bayda';
    case Zawiya = 'zawiya';
    case Sabha = 'sabha';
    case Sirte = 'sirte';
    case Tobruk = 'tobruk';
    case Ajdabiya = 'ajdabiya';
    case Khoms = 'khoms';
    case Derna = 'derna';
    case Zliten = 'zliten';
    case Gharyan = 'gharyan';
    case Nalut = 'nalut';
    case Murzuq = 'murzuq';
    case Ghat = 'ghat';
    case Kufra = 'kufra';
    case Jufra = 'jufra';
    case WadiAlShatii = 'wadi_al_shatii';
    case WadiAlHayaa = 'wadi_al_hayaa';
    case Marj = 'marj';
    case JabalAlGharbi = 'jabal_al_gharbi';
    case NuwqatAlKhams = 'nuwqat_al_khams';
    case Murqub = 'murqub';

    public function label(): string
    {
        return match ($this) {
            self::Tripoli => 'طرابلس',
            self::Benghazi => 'بنغازي',
            self::Misrata => 'مصراتة',
            self::Bayda => 'البيضاء',
            self::Zawiya => 'الزاوية',
            self::Sabha => 'سبها',
            self::Sirte => 'سرت',
            self::Tobruk => 'طبرق',
            self::Ajdabiya => 'أجدابيا',
            self::Khoms => 'الخمس',
            self::Derna => 'درنة',
            self::Zliten => 'زليتن',
            self::Gharyan => 'غريان',
            self::Nalut => 'نالوت',
            self::Murzuq => 'مرزق',
            self::Ghat => 'غات',
            self::Kufra => 'الكفرة',
            self::Jufra => 'الجفرة',
            self::WadiAlShatii => 'وادي الشاطئ',
            self::WadiAlHayaa => 'وادي الحياة',
            self::Marj => 'المرج',
            self::JabalAlGharbi => 'الجبل الغربي',
            self::NuwqatAlKhams => 'نقاط الخمس',
            self::Murqub => 'المرقب',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
