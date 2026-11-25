<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View {
        /** @var \App\Models\User $member */
        $member = Auth::user();

        // Eager load relationships and confirmed count to prevent N+1 queries
        $classes = GymClass::with(['classType', 'trainer'])
            ->withCount('confirmedBookings')
            ->upcoming()
            ->get();

        // Fetch member's active bookings in a single query indexed by gym_class_id
        $userBookings = $member->bookings()
            ->active()
            ->get()
            ->keyBy('gym_class_id');

        return view('dashboard', [
            'member' => $member,
            'subscription' => $member->activeSubscription(),
            'remainingQuota' => $member->remainingWeeklyBookings(),
            'classes' => $classes,
            'userBookings' => $userBookings,
        ]);
    }
}