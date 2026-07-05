<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        Patient::query()->firstOrCreate(
            ['phone' => '0911111111'],
            [
                'name' => 'محمد علي',
                'email' => 'patient@mawedi.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        Patient::query()->firstOrCreate(
            ['phone' => '0922222222'],
            [
                'name' => 'فاطمة أحمد',
                'email' => 'fatima@mawedi.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
