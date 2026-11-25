<?php

namespace App\Http\Controllers\Member;

use App\Contracts\BookingServiceInterface;
use App\Enums\BookingStatus;
use App\Exceptions\BookingRuleException;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GymClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller {
    public function __construct(
        private readonly BookingServiceInterface $bookingService
    ) {}

    public function index(): View {
        /** @var \App\Models\User $member */
        $member = Auth::user();

        $bookings = $member->bookings()->with(['gymClass.classType', 'gymClass.trainer'])->latest()->get();

        return view('member.bookings', ['bookings' => $bookings]);
    }

    public function store(GymClass $gymClass): RedirectResponse {
        /** @var \App\Models\User $member */
        $member = Auth::user();

        try {
            $booking = $this->bookingService->book($member, $gymClass);

            $message = $booking->status === BookingStatus::Confirmed
                ? 'Your spot in the class has been confirmed!'
                : 'Class is full. You have been added to the waitlist.';

            return back()->with('success', $message);
        } catch (BookingRuleException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Booking $booking): RedirectResponse {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->bookingService->cancel($booking);

            return back()->with('success', 'Booking cancelled successfully.');
        } catch (BookingRuleException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}