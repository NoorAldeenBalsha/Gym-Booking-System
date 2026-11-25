<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Gym Booking System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 flex flex-col min-h-screen">

        {{-- Top Navbar (Matches Login & Register Pages) --}}
        <nav class="w-full bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xl font-extrabold text-[#2e3192] tracking-tight">
                    Gym Booking
                </a>

                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                    <a href="#features" class="hover:text-[#2e3192] transition">Features</a>
                    <a href="#classes" class="hover:text-[#2e3192] transition">Classes</a>
                    <a href="#packages" class="hover:text-[#2e3192] transition">Packages</a>
                </div>

                <div class="flex items-center gap-4 text-sm font-medium">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="bg-[#2e3192] text-white px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#232575] transition shadow-sm">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-gray-700 hover:text-[#2e3192] font-semibold transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="bg-[#2e3192] text-white px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#232575] transition shadow-sm">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Hero Section --}}
        <section class="bg-white border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-20 md:py-28 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-[#2e3192] mb-4">
                        Smart Gym Management &amp; Booking
                    </span>
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
                        Book Your Next Workout <span class="text-[#2e3192]">In Seconds.</span>
                    </h1>
                    <p class="text-base text-gray-600 leading-relaxed mb-8 max-w-lg">
                        Browse live class schedules, train with certified coaches, manage your weekly subscription quota, and get instant waitlist updates — all in one place.
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="bg-[#2e3192] hover:bg-[#232575] text-white text-xs font-bold uppercase tracking-wider px-8 py-4 rounded-full shadow-md transition">
                                Go to Class Schedule
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="bg-[#2e3192] hover:bg-[#232575] text-white text-xs font-bold uppercase tracking-wider px-8 py-4 rounded-full shadow-md transition">
                                Create Free Account
                            </a>
                            <a href="{{ route('login') }}"
                               class="border border-gray-300 hover:border-[#2e3192] text-gray-700 hover:text-[#2e3192] text-xs font-bold uppercase tracking-wider px-8 py-4 rounded-full transition">
                                Member Login
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Right Visual Card --}}
                <div class="bg-[#2e3192] rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
                    <div class="space-y-6 relative z-10">
                        <div class="flex items-center justify-between border-b border-white/15 pb-4">
                            <div>
                                <p class="text-xs uppercase tracking-wider text-indigo-200">Live Schedule</p>
                                <h3 class="text-xl font-bold mt-0.5">Today's Training Sessions</h3>
                            </div>
                            <span class="px-3 py-1 bg-white/15 rounded-full text-xs font-semibold">Real-Time</span>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="bg-white/10 rounded-xl p-4 flex items-center justify-between">
                                <div>
                                    <p class="font-bold">CrossFit &amp; Strength</p>
                                    <p class="text-xs text-indigo-200">Coach Ahmad • 60 Mins</p>
                                </div>
                                <span class="px-3 py-1 bg-emerald-400/20 text-emerald-200 rounded-full text-xs font-semibold">Open Spots</span>
                            </div>

                            <div class="bg-white/10 rounded-xl p-4 flex items-center justify-between">
                                <div>
                                    <p class="font-bold">Yoga &amp; Mobility</p>
                                    <p class="text-xs text-indigo-200">Coach Ahmad • 60 Mins</p>
                                </div>
                                <span class="px-3 py-1 bg-amber-400/20 text-amber-200 rounded-full text-xs font-semibold">Waitlist Ready</span>
                            </div>

                            <div class="bg-white/10 rounded-xl p-4 flex items-center justify-between">
                                <div>
                                    <p class="font-bold">Cardio Boxing</p>
                                    <p class="text-xs text-indigo-200">Coach Sara • 60 Mins</p>
                                </div>
                                <span class="px-3 py-1 bg-emerald-400/20 text-emerald-200 rounded-full text-xs font-semibold">Open Spots</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Features Section --}}
        <section id="features" class="py-20 max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="text-3xl font-extrabold text-gray-900">Why Train With Us?</h2>
                <p class="text-sm text-gray-500 mt-2">Everything designed to keep your fitness routine organized and fair for every member.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-[#2e3192] flex items-center justify-center font-extrabold text-lg mb-4">
                        01
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Instant Spot Reservation</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        See exact seat availability before booking and reserve your place in any upcoming class with one click.
                    </p>
                </div>

                <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-[#2e3192] flex items-center justify-center font-extrabold text-lg mb-4">
                        02
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Smart Auto-Waitlist</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        If a class reaches full capacity, join the waitlist and get automatically promoted the moment a spot opens up.
                    </p>
                </div>

                <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-[#2e3192] flex items-center justify-center font-extrabold text-lg mb-4">
                        03
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Flexible Weekly Plans</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Choose a monthly package tailored to your weekly schedule and track your remaining bookings live from your dashboard.
                    </p>
                </div>
            </div>
        </section>

        {{-- Dynamic Classes Section --}}
        @if (isset($classTypes) && $classTypes->isNotEmpty())
            <section id="classes" class="py-16 bg-white border-y border-gray-100">
                <div class="max-w-6xl mx-auto px-4 sm:px-6">
                    <div class="text-center max-w-2xl mx-auto mb-12">
                        <h2 class="text-3xl font-extrabold text-gray-900">Our Training Programs</h2>
                        <p class="text-sm text-gray-500 mt-2">Specialized sessions led by qualified coaches.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($classTypes as $type)
                            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100">
                                <h3 class="text-lg font-bold text-[#2e3192] mb-2">{{ $type->name }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $type->description ?: 'High-energy guided group session suitable for all fitness levels.' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Dynamic Packages Section --}}
        @if (isset($packages) && $packages->isNotEmpty())
            <section id="packages" class="py-20 max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-3xl font-extrabold text-gray-900">Membership Packages</h2>
                    <p class="text-sm text-gray-500 mt-2">Register online and activate your preferred plan at the gym reception.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    @foreach ($packages as $package)
                        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm flex flex-col justify-between">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-[#2e3192] bg-indigo-50 px-3 py-1 rounded-full">
                                    {{ $package->duration_days }} Days Validity
                                </span>
                                <h3 class="text-2xl font-extrabold text-gray-900 mt-4">{{ $package->name }}</h3>
                                <p class="text-4xl font-extrabold text-[#2e3192] mt-4">
                                    {{ $package->weekly_limit }} <span class="text-sm font-medium text-gray-500">classes / week</span>
                                </p>
                                <ul class="mt-6 space-y-2.5 text-sm text-gray-600">
                                    <li>• Access to all scheduled group classes</li>
                                    <li>• Automatic waitlist priority</li>
                                    <li>• Free cancellation up to 3 hours before class</li>
                                </ul>
                            </div>

                            <div class="mt-8">
                                <a href="{{ route('register') }}"
                                   class="block text-center w-full bg-[#2e3192] hover:bg-[#232575] text-white text-xs font-bold uppercase tracking-wider py-3.5 rounded-full transition">
                                    Get Started
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Bottom CTA Banner --}}
        <section class="bg-[#2e3192] text-white py-16 mt-auto">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
                <h2 class="text-3xl font-extrabold mb-3">Ready to Start Training?</h2>
                <p class="text-sm text-indigo-100 mb-8 max-w-md mx-auto">
                    Create your member account today and view the upcoming weekly schedule immediately.
                </p>
                <a href="{{ auth()->check() ? route('dashboard') : route('register') }}"
                   class="inline-block bg-white text-[#2e3192] hover:bg-indigo-50 text-xs font-bold uppercase tracking-wider px-10 py-3.5 rounded-full shadow-md transition">
                    {{ auth()->check() ? 'Open Dashboard' : 'Join Now' }}
                </a>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} Gym Booking System. All rights reserved.
        </footer>

    </body>
</html>