<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\HospitalUser;

class DoctorPolicy
{
    public function viewAny(HospitalUser $user): bool
    {
        return true;
    }

    public function view(HospitalUser $user, Doctor $doctor): bool
    {
        return $user->hospital_id === $doctor->hospital_id;
    }

    public function create(HospitalUser $user): bool
    {
        return true;
    }

    public function update(HospitalUser $user, Doctor $doctor): bool
    {
        return $user->hospital_id === $doctor->hospital_id;
    }

    public function delete(HospitalUser $user, Doctor $doctor): bool
    {
        return $user->hospital_id === $doctor->hospital_id;
    }

    public function manageSchedule(HospitalUser $user, Doctor $doctor): bool
    {
        return $user->hospital_id === $doctor->hospital_id;
    }
}
