<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Hospital;
use App\Models\HospitalUser;

class HospitalPolicy
{
    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    public function view(Admin $admin, Hospital $hospital): bool
    {
        return true;
    }

    public function create(Admin $admin): bool
    {
        return true;
    }

    public function update(Admin $admin, Hospital $hospital): bool
    {
        return true;
    }

    public function delete(Admin $admin, Hospital $hospital): bool
    {
        return true;
    }

    public function updateProfile(HospitalUser $user, Hospital $hospital): bool
    {
        return $user->hospital_id === $hospital->id;
    }
}
