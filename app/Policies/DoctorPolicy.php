<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\HospitalUser;

class DoctorPolicy
{
    public function viewAny(Admin|HospitalUser $user): bool
    {
        return $user instanceof HospitalUser || $user->hasPermission('doctors.price');
    }

    public function create(Admin|HospitalUser $user): bool
    {
        return $user instanceof HospitalUser;
    }

    public function update(Admin|HospitalUser $user, Doctor $doctor): bool
    {
        return $user instanceof HospitalUser
            ? $user->hospital_id === $doctor->hospital_id
            : $user->hasPermission('doctors.price');
    }

    public function delete(Admin|HospitalUser $user, Doctor $doctor): bool
    {
        return $user instanceof HospitalUser && $user->hospital_id === $doctor->hospital_id;
    }

    public function updatePrice(Admin|HospitalUser $user, Doctor $doctor): bool
    {
        return $user instanceof HospitalUser
            ? $user->hospital_id === $doctor->hospital_id
            : $user->hasPermission('doctors.price');
    }
}
