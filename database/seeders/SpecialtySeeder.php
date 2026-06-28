<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            'طب عام',
            'طب أطفال',
            'طب باطني',
            'جراحة عامة',
            'أمراض قلب',
            'جلدية',
            'عظام',
            'نساء وولادة',
            'أنف وأذن وحنجرة',
            'عيون',
            'أسنان',
            'طب نفسي',
        ];

        foreach ($specialties as $name) {
            Specialty::query()->firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
