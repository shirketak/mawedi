<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()
            ->whereIn('email', ['admin@mawedi.ly', 'finance@mawedi.ly'])
            ->delete();

        Admin::query()->updateOrCreate(
            ['email' => 'admin@mawedi.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'role' => AdminRole::SuperAdmin,
                'is_active' => true,
            ]
        );

        Admin::query()->updateOrCreate(
            ['email' => 'finance@mawedi.com'],
            [
                'name' => 'مسؤول المالية',
                'password' => Hash::make('password'),
                'role' => AdminRole::Finance,
                'is_active' => true,
            ]
        );
    }
}
