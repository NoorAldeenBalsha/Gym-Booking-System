<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Gym Booking System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100 min-h-screen flex flex-col">

        {{-- Simple Top Navbar --}}
        <nav class="w-full bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <a href="{{ url('/') }}" class="text-lg font-extrabold text-[#2e3192] tracking-tight">
                    Gym Booking
                </a>

                <div class="flex items-center gap-4 text-sm font-medium">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-[#2e3192] transition">
                        Home
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="bg-[#2e3192] text-white px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#232575] transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="{{ request()->routeIs('login') ? 'text-[#2e3192] font-bold' : 'text-gray-600 hover:text-[#2e3192]' }} transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="bg-[#2e3192] text-white px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#232575] transition">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="flex-1 flex items-center justify-center p-4">
            @if (request()->routeIs('login', 'register'))
                {{ $slot }}
            @else
                <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-xl">
                    {{ $slot }}
                </div>
            @endif
        </main>

    </body>
</html>