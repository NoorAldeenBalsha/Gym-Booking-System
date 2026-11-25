<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id', 'gym_class_id', 'status','cancelled_at','promoted_at',];

    protected function casts(): array {
        return [
            'status' => BookingStatus::class,
            'cancelled_at' => 'datetime',
            'promoted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function gymClass(): BelongsTo{
        return $this->belongsTo(GymClass::class);
    }

    // Scope bookings that hold an active spot or waitlist position.
    public function scopeActive(Builder $query): Builder {
        return $query->whereIn('status', [
            BookingStatus::Confirmed,
            BookingStatus::Waitlisted,
        ]);
    }

    // Determine whether the booking can still be cancelled (at least 3 hours before start).
    public function canBeCancelled(): bool  {
        if (! in_array($this->status, [BookingStatus::Confirmed, BookingStatus::Waitlisted], true)) {
            return false;
        }

        return now()->lte($this->gymClass->starts_at->copy()->subHours(3));
    }
}