<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model {
    use HasFactory;

    protected $fillable = ['name', 'weekly_limit', 'duration_days',];

    protected function casts(): array  {
        return [
            'weekly_limit' => 'integer',
            'duration_days' => 'integer',
        ];
    }

    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }
}