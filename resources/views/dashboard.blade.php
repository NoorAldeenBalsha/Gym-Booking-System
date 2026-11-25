<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-[#2e3192] leading-tight">
            Upcoming Gym Classes
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages success -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            <!----------------------------------------------------------------------------------------->
            <!-- Flash Messages error -->
            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif
            <!----------------------------------------------------------------------------------------->
            <!-- Suspension Alert -->
            @if ($member->isSuspended())
                <div class="p-4 bg-rose-50 border-l-4 border-rose-600 text-rose-800 rounded-xl text-sm">
                    <strong>Account Suspended:</strong> You cannot book classes until {{ $member->suspended_until->format('Y-m-d H:i') }} due to 3 no-shows.
                </div>
            @endif
            <!----------------------------------------------------------------------------------------->
            <!-- Member Subscription & Quota Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Active Subscription</div>
                    @if ($subscription)
                        <div class="text-xl font-extrabold text-gray-900 mt-1.5">{{ $subscription->package->name }}</div>
                        <div class="text-xs text-gray-500 mt-1">Valid until {{ $subscription->ends_at->format('Y-m-d') }}</div>
                    @else
                        <div class="text-lg font-bold text-rose-600 mt-1.5">No Active Package</div>
                        <div class="text-xs text-gray-500 mt-1">Please visit the gym reception to subscribe.</div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Remaining Weekly Bookings</div>
                    <div class="text-3xl font-extrabold text-[#2e3192] mt-1">
                        {{ $remainingQuota }}
                        @if ($subscription)
                            <span class="text-sm font-medium text-gray-500">/ {{ $subscription->package->weekly_limit }} this week</span>
                        @endif
                    </div>
                </div>
            </div>
            <!----------------------------------------------------------------------------------------->
            <!-- Upcoming Classes Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6">
                    @if ($classes->isEmpty())
                        <p class="text-gray-500 text-center py-8 text-sm">No upcoming classes scheduled right now.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Class</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Trainer</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Date &amp; Time</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Available Spots</th>
                                        <th class="px-4 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($classes as $class)
                                        @php
                                            $existingBooking = $userBookings->get($class->id);
                                            $spotsLeft = $class->availableSpots();
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="px-4 py-4 font-bold text-gray-900">
                                                {{ $class->classType->name }}
                                                @if ($class->classType->description)
                                                    <p class="text-xs font-normal text-gray-500 mt-0.5">{{ Str::limit($class->classType->description, 50) }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-gray-700 font-medium">{{ $class->trainer->name }}</td>
                                            <td class="px-4 py-4 text-gray-600">
                                                {{ $class->starts_at->format('D, M d • H:i') }} - {{ $class->ends_at->format('H:i') }}
                                            </td>
                                            <td class="px-4 py-4">
                                                @if ($spotsLeft > 0)
                                                    <span class="px-3 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full">
                                                        {{ $spotsLeft }} / {{ $class->capacity }} left
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 rounded-full">
                                                        Full (Waitlist Open)
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                @if ($existingBooking)
                                                    <span class="px-3.5 py-1.5 text-xs font-bold rounded-full {{ $existingBooking->status->value === 'confirmed' ? 'bg-indigo-50 text-[#2e3192] border border-indigo-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                        {{ $existingBooking->status->label() }}
                                                    </span>
                                                @else
                                                    <form method="POST" action="{{ route('member.bookings.store', $class) }}">
                                                        @csrf
                                                        <button type="submit"
                                                            class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-white rounded-full shadow-sm transition {{ $spotsLeft > 0 ? 'bg-[#2e3192] hover:bg-[#232575]' : 'bg-amber-500 hover:bg-amber-600' }}">
                                                            {{ $spotsLeft > 0 ? 'Book Now' : 'Join Waitlist' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <!----------------------------------------------------------------------------------------->
        </div>
    </div>
</x-app-layout>