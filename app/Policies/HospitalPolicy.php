<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Hospital;
use App\Models\HospitalUser;

class HospitalPolicy
{
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasPermission('hospitals') || $admin->hasPermission('hospitals.view');
    }

    public function view(Admin $admin, Hospital $hospital): bool
    {
        return $this->viewAny($admin);
    }

    public function create(Admin $admin): bool
    {
        return $admin->hasPermission('hospitals');
    }

    public function update(Admin $admin, Hospital $hospital): bool
    {
        return $admin->hasPermission('hospitals');
    }

    public function delete(Admin $admin, Hospital $hospital): bool
    {
        return $admin->hasPermission('hospitals');
    }

    public function viewStats(Admin $admin, Hospital $hospital): bool
    {
        return $admin->hasPermission('hospitals.stats');
    }

    public function manageWallet(Admin $admin, Hospital $hospital): bool
    {
        return $admin->hasPermission('wallet');
    }

    public function manageSubscription(Admin $admin, Hospital $hospital): bool
    {
        return $admin->hasPermission('subscriptions');
    }

    public function updateProfile(HospitalUser $user, Hospital $hospital): bool
    {
        return $user->hospital_id === $hospital->id;
    }
}
