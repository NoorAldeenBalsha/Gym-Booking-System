<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class SuspensionService
{
    // Suspend the member for 7 days if they accumulated 3+ no-shows within the last 30 days.
    public function evaluateAndApplyBan(User $member): void {
        if ($member->isSuspended()) { return; }

        $windowStart = now()->subDays(30);

        // Count only no-shows recorded after any previous suspension ended within the 30-day window.
        if ($member->suspended_until !== null && $member->suspended_until->gt($windowStart)) {
            $windowStart = $member->suspended_until;
        }

        $noShowCount = Booking::where('user_id', $member->id)
            ->where('status', BookingStatus::NoShow)
            ->where('updated_at', '>=', $windowStart)
            ->count();

        if ($noShowCount >= 3) {
            $member->update([
                'suspended_until' => now()->addDays(7),
            ]);
        }
    }

    // Lift an active suspension early (Admin action).
    public function liftBan(User $member): void {
        $member->update([
            'suspended_until' => null,
        ]);
    }
}