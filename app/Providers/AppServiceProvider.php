<?php

namespace App\Providers;

use App\Contracts\AttendanceServiceInterface;
use App\Contracts\BookingServiceInterface;
use App\Services\BookingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {
        $this->app->bind(BookingServiceInterface::class, BookingService::class);
        $this->app->bind(AttendanceServiceInterface::class, BookingService::class);
    }

    public function boot(): void {
        //
    }
}