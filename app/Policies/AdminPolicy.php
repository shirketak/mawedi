<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasPermission('admin_users');
    }

    public function view(Admin $admin, Admin $model): bool
    {
        return $admin->hasPermission('admin_users');
    }

    public function create(Admin $admin): bool
    {
        return $admin->hasPermission('admin_users');
    }

    public function update(Admin $admin, Admin $model): bool
    {
        return $admin->hasPermission('admin_users');
    }

    public function delete(Admin $admin, Admin $model): bool
    {
        return $admin->hasPermission('admin_users') && $admin->id !== $model->id;
    }

    public function toggleStatus(Admin $admin, Admin $model): bool
    {
        return $admin->hasPermission('admin_users') && $admin->id !== $model->id;
    }
}
