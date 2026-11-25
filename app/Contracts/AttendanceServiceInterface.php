<?php

namespace App\Contracts;

use App\Enums\BookingStatus;
use App\Models\Booking;

interface AttendanceServiceInterface {
    public function markAttendance(Booking $booking, BookingStatus $status): Booking;
}