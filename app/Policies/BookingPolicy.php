<?php

namespace App\Policies;

use App\Models\HospitalUser;

class BookingPolicy
{
    public function viewAny(HospitalUser $user): bool
    {
        return true;
    }
}
