<?php

namespace App\Contracts;

use App\Models\Booking;
use App\Models\GymClass;
use App\Models\User;

interface BookingServiceInterface {

    public function book(User $member, GymClass $class): Booking;
    public function cancel(Booking $booking): Booking;
}