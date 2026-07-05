<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\HospitalUser;
use App\Models\Specialty;

class SpecialtyPolicy
{
    public function viewAny(Admin|HospitalUser $user): bool
    {
        return true;
    }

    public function create(Admin $admin): bool
    {
        return $admin->hasPermission('specialties');
    }

    public function update(Admin $admin, Specialty $specialty): bool
    {
        return $admin->hasPermission('specialties');
    }

    public function delete(Admin $admin, Specialty $specialty): bool
    {
        return $admin->hasPermission('specialties');
    }
}
