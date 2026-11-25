<?php

use App\Http\Controllers\Member\BookingController;
use App\Http\Controllers\Member\ScheduleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Models\ClassType;
use App\Models\Package;

Route::get('/', function () {
    return view('welcome', [ 'classTypes' => ClassType::all(),'packages' => Package::all(),]);
})->name('home');

Route::get('/dashboard', function () {  
    /** @var \App\Models\User|null $user */
        $user = Auth::user();

    if ($user->hasRole('admin') || $user->hasRole('trainer')) {return redirect('/admin');}

    return app(ScheduleController::class)->index();
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('member.bookings.index');
    Route::post('/classes/{gymClass}/book', [BookingController::class, 'store'])->name('member.bookings.store');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('member.bookings.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';