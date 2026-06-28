<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()->firstOrCreate(
            ['email' => 'admin@mawedi.ly'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
            ]
        );
    }
}
