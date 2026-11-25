<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\BookingRuleException;
use App\Models\GymClass;

class WaitlistService
{
    public function __construct(
        private readonly BookingValidator $validator
    ) {}

    // Promote the earliest eligible waitlisted member to Confirmed when a spot opens up.
    public function promoteNextEligible(GymClass $class): void {
        if ($class->hasStarted()) { return;}

        $waitlist = $class->waitlistedBookings()->with('user')->get();

        foreach ($waitlist as $candidateBooking) {
            try {
                $this->validator->validateEligibility($candidateBooking->user, $class);

                $candidateBooking->update([
                    'status' => BookingStatus::Confirmed,
                    'promoted_at' => now(),
                ]);

                break;
            } catch (BookingRuleException) {
                continue;
            }
        }
    }
}