<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\HospitalUser;

class BookingPolicy
{
    public function viewAny(Admin|HospitalUser $user): bool
    {
        return $user instanceof HospitalUser || $user->hasPermission('bookings');
    }

    public function view(Admin|HospitalUser $user, Booking $booking): bool
    {
        if ($user instanceof HospitalUser) {
            return $booking->hospital_id === $user->hospital_id;
        }

        return $user->hasPermission('bookings');
    }

    public function create(Admin|HospitalUser $user): bool
    {
        return $user instanceof HospitalUser;
    }

    public function manage(Admin|HospitalUser $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }
}
