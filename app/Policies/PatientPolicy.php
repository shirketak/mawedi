<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Patient;

class PatientPolicy
{
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasPermission('patients');
    }

    public function view(Admin $admin, Patient $patient): bool
    {
        return $admin->hasPermission('patients');
    }

    public function update(Admin $admin, Patient $patient): bool
    {
        return $admin->hasPermission('patients');
    }
}
