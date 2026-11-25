<?php

namespace App\Enums;

enum BookingStatus: string {
    case Confirmed = 'confirmed';
    case Waitlisted = 'waitlisted';
    case Cancelled = 'cancelled';
    case Attended = 'attended';
    case NoShow = 'no_show';

    public function label(): string {
        return match ($this) {
            self::Confirmed => 'Confirmed',
            self::Waitlisted => 'Waitlisted',
            self::Cancelled => 'Cancelled',
            self::Attended => 'Attended',
            self::NoShow => 'No Show',
        };
    }

    public function color(): string {
        return match ($this) {
            self::Confirmed => 'success',
            self::Waitlisted => 'warning',
            self::Cancelled => 'gray',
            self::Attended => 'info',
            self::NoShow => 'danger',
        };
    }
}