<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser {
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','phone','age','gender','password','role','suspended_until',];

    protected $hidden = ['password','remember_token',];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'suspended_until' => 'datetime',
        ];
    }

    public function hasRole(string $role): bool{
        return $this->role?->value === $role;
    }
    
    public function canAccessPanel(Panel $panel): bool {
        return in_array($this->role, [UserRole::Admin, UserRole::Trainer], true);
    }

    public function isAdmin(): bool {
        return $this->role === UserRole::Admin;
    }

    public function isTrainer(): bool {
        return $this->role === UserRole::Trainer;
    }

    public function isMember(): bool {
        return $this->role === UserRole::Member;
    }

    public function isSuspended(): bool {
        return $this->suspended_until !== null && $this->suspended_until->isFuture();
    }

    // --- Relationships ---
    public function classTypes(): BelongsToMany {
        return $this->belongsToMany(ClassType::class, 'class_type_trainer', 'trainer_id', 'class_type_id')
            ->withTimestamps();
    }

    public function taughtClasses(): HasMany {
        return $this->hasMany(GymClass::class, 'trainer_id');
    }

    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }

    public function bookings(): HasMany {
        return $this->hasMany(Booking::class);
    }

    // --- Domain Helpers ---

    // Retrieve the member's currently active subscription     
    public function activeSubscription(): ?Subscription {
        return $this->subscriptions()
            ->with('package')
            ->active()
            ->latest('ends_at')
            ->first();
    }

    // Count bookings consuming weekly quota for the week containing $date
    public function usedWeeklyBookingsCount(?Carbon $date = null): int {
        $target = ($date ?? now())->copy();
        $weekStart = $target->copy()->startOfWeek();
        $weekEnd = $target->copy()->endOfWeek();

        return $this->bookings()
            ->whereIn('status', [
                BookingStatus::Confirmed,
                BookingStatus::Attended,
                BookingStatus::NoShow,
            ])
            ->whereHas('gymClass', function ($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('starts_at', [$weekStart, $weekEnd]);
            })
            ->count();
    }

    // Calculate remaining weekly bookings based on the active subscription package.
    public function remainingWeeklyBookings(?Carbon $date = null): int {
        $subscription = $this->activeSubscription();

        if (! $subscription || ! $subscription->package) {return 0;}

        $used = $this->usedWeeklyBookingsCount($date);

        return max(0, $subscription->package->weekly_limit - $used);
    }
}