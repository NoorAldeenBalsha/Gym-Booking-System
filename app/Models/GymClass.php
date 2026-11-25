<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymClass extends Model
{
    use HasFactory;

    protected $fillable = ['class_type_id','trainer_id','starts_at','ends_at','capacity',];

    protected function casts(): array {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'capacity' => 'integer',
        ];
    }

    public function classType(): BelongsTo {
        return $this->belongsTo(ClassType::class);
    }

    public function trainer(): BelongsTo {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function bookings(): HasMany {
        return $this->hasMany(Booking::class);
    }

    public function confirmedBookings(): HasMany {
        return $this->hasMany(Booking::class)->where('status', BookingStatus::Confirmed);
    }

    public function waitlistedBookings(): HasMany {
        return $this->hasMany(Booking::class)
            ->where('status', BookingStatus::Waitlisted)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    // Scope classes scheduled in the future.
    public function scopeUpcoming(Builder $query): Builder {
        return $query->where('starts_at', '>', now())->orderBy('starts_at');
    }

    // Calculate remaining available spots (uses eager-loaded count if present).
    public function availableSpots(): int {
        $confirmedCount = $this->confirmed_bookings_count ?? $this->confirmedBookings()->count();

        return max(0, $this->capacity - $confirmedCount);
    }

    public function isFull(): bool {
        return $this->availableSpots() === 0;
    }

    public function hasStarted(): bool {
        return $this->starts_at->lte(now());
    }

    public function hasEnded(): bool {
        return $this->ends_at->lt(now());
    }
}