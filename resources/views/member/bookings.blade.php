<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-[#2e3192] leading-tight">
            {{ __('My Bookings') }}
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
            <!-- Bookings Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6">
                    @if ($bookings->isEmpty())
                        <p class="text-gray-500 text-center py-8 text-sm">
                            You have no bookings yet. <a href="{{ route('dashboard') }}" class="text-[#2e3192] font-bold hover:underline">Browse classes</a>.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Class</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Trainer</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Schedule</th>
                                        <th class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                                        <th class="px-4 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($bookings as $booking)
                                        @php
                                            $badgeClass = match ($booking->status->value) {
                                                'confirmed' => 'bg-indigo-50 text-[#2e3192] border-indigo-200',
                                                'waitlisted' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'attended' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                default => 'bg-rose-50 text-rose-700 border-rose-200',
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="px-4 py-4 font-bold text-gray-900">
                                                {{ $booking->gymClass->classType->name }}
                                            </td>
                                            <td class="px-4 py-4 text-gray-700 font-medium">
                                                {{ $booking->gymClass->trainer->name }}
                                            </td>
                                            <td class="px-4 py-4 text-gray-600">
                                                {{ $booking->gymClass->starts_at->format('D, M d • H:i') }}
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-3 py-1 text-xs font-bold border rounded-full {{ $badgeClass }}">
                                                    {{ $booking->status->label() }}
                                                </span>
                                                @if ($booking->promoted_at)
                                                    <span class="block text-xs text-emerald-600 font-medium mt-1">
                                                        Promoted from waitlist
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                @if ($booking->canBeCancelled())
                                                    <form method="POST" action="{{ route('member.bookings.destroy', $booking) }}"
                                                        onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-white bg-rose-600 hover:bg-rose-700 rounded-full shadow-sm transition">
                                                            Cancel Booking
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-gray-400">—</span>
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

        </div>
    </div>
</x-app-layout>