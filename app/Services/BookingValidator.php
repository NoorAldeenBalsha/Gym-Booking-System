<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\BookingRuleException;
use App\Models\Booking;
use App\Models\GymClass;
use App\Models\User;

class BookingValidator
{
    // Validate all core eligibility rules for a member to hold a confirmed spot in a class.
    public function validateEligibility(User $member, GymClass $class): void {
        if (! $member->isMember()) {
            throw new BookingRuleException('Only gym members can book classes.');
        }

        if ($member->isSuspended()) {
            $until = $member->suspended_until->format('Y-m-d H:i');
            throw new BookingRuleException("Your account is suspended from booking until {$until}.");
        }

        if (! $member->activeSubscription()) {
            throw new BookingRuleException('You do not have an active subscription. Please contact the gym front desk.');
        }

        if ($class->hasStarted()) {
            throw new BookingRuleException('Cannot book a class that has already started or ended.');
        }

        if ($member->remainingWeeklyBookings($class->starts_at) <= 0) {
            throw new BookingRuleException('You have reached your weekly booking limit for this week.');
        }

        if ($this->hasTimeConflict($member, $class)) {
            throw new BookingRuleException('You already have another confirmed class scheduled during this time.');
        }
    }

    // Ensure the member does not already hold an active (confirmed or waitlisted) booking for this class.
    public function ensureNotAlreadyBooked(User $member, GymClass $class): void {
        $exists = Booking::where('user_id', $member->id)
            ->where('gym_class_id', $class->id)
            ->active()
            ->exists();

        if ($exists) {throw new BookingRuleException('You are already booked or on the waitlist for this class.');}
    }

    // Check if the member has another confirmed class whose time overlaps with the target class.
    public function hasTimeConflict(User $member, GymClass $targetClass): bool {
        return Booking::where('user_id', $member->id)
            ->where('gym_class_id', '!=', $targetClass->id)
            ->where('status', BookingStatus::Confirmed)
            ->whereHas('gymClass', function ($query) use ($targetClass) {
                $query->where('starts_at', '<', $targetClass->ends_at)
                    ->where('ends_at', '>', $targetClass->starts_at);
            })
            ->exists();
    }
}