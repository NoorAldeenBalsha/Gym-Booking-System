<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model {
    use HasFactory;

    protected $fillable = [ 'user_id','package_id','starts_at','ends_at',];

    protected function casts(): array {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo {
        return $this->belongsTo(Package::class);
    }

    // Scope subscriptions currently active on today's date.
    public function scopeActive(Builder $query): Builder {
        $today = now()->toDateString();

        return $query->whereDate('starts_at', '<=', $today)
            ->whereDate('ends_at', '>=', $today);
    }

    public function isActive(): bool {
        $today = now()->startOfDay();

        return $this->starts_at->lte($today) && $this->ends_at->gte($today);
    }
}