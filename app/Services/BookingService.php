<?php

namespace App\Services;

use App\Contracts\AttendanceServiceInterface;
use App\Contracts\BookingServiceInterface;
use App\Enums\BookingStatus;
use App\Exceptions\BookingRuleException;
use App\Models\Booking;
use App\Models\GymClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingService implements BookingServiceInterface, AttendanceServiceInterface
{
    public function __construct(
        private readonly BookingValidator $validator,
        private readonly WaitlistService $waitlistService,
        private readonly SuspensionService $suspensionService
    ) {}

    // Create a confirmed or waitlisted booking atomically.
    public function book(User $member, GymClass $class): Booking {
        return DB::transaction(function () use ($member, $class) {
            // Lock class row to prevent concurrent overbooking (race conditions).
            $lockedClass = GymClass::whereKey($class->id)->lockForUpdate()->firstOrFail();

            $this->validator->validateEligibility($member, $lockedClass);
            $this->validator->ensureNotAlreadyBooked($member, $lockedClass);

            $confirmedCount = $lockedClass->confirmedBookings()->count();
            $status = $confirmedCount < $lockedClass->capacity
                ? BookingStatus::Confirmed
                : BookingStatus::Waitlisted;

            return Booking::create([
                'user_id' => $member->id,
                'gym_class_id' => $lockedClass->id,
                'status' => $status,
            ]);
        });
    }

    // Cancel a booking (enforcing the 3-hour rule) and trigger waitlist promotion if applicable.
    public function cancel(Booking $booking): Booking {
        return DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::with('gymClass')
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedClass = GymClass::whereKey($lockedBooking->gym_class_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lockedBooking->status, [BookingStatus::Confirmed, BookingStatus::Waitlisted], true)) {
                throw new BookingRuleException('This booking cannot be cancelled in its current state.');
            }

            if (! $lockedBooking->canBeCancelled()) {
                throw new BookingRuleException('Cancellations are only allowed at least 3 hours before the class starts.');
            }

            $wasConfirmed = $lockedBooking->status === BookingStatus::Confirmed;

            $lockedBooking->update(['status' => BookingStatus::Cancelled,'cancelled_at' => now(),]);

            if ($wasConfirmed) {$this->waitlistService->promoteNextEligible($lockedClass);}

            return $lockedBooking->fresh();
        });
    }

    // Record attendance after a class ends and evaluate suspension rules on no-show.
    public function markAttendance(Booking $booking, BookingStatus $status): Booking {
        if (! in_array($status, [BookingStatus::Attended, BookingStatus::NoShow], true)) {
            throw new BookingRuleException('Invalid attendance status provided.');
        }

        return DB::transaction(function () use ($booking, $status) {
            $lockedBooking = Booking::with(['gymClass', 'user'])
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedBooking->gymClass->hasEnded()) {
                throw new BookingRuleException('Attendance can only be recorded after the class has finished.');
            }

            if (! in_array($lockedBooking->status, [BookingStatus::Confirmed, BookingStatus::Attended, BookingStatus::NoShow], true)) {
                throw new BookingRuleException('Attendance can only be recorded for confirmed bookings.');
            }

            $lockedBooking->update([
                'status' => $status,
            ]);

            if ($status === BookingStatus::NoShow) {
                $this->suspensionService->evaluateAndApplyBan($lockedBooking->user);
            }

            return $lockedBooking->fresh();
        });
    }
}