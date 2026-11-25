<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassType extends Model
{
    use HasFactory;

    protected $fillable = ['name','description',];

    public function trainers(): BelongsToMany {
        return $this->belongsToMany(User::class, 'class_type_trainer', 'class_type_id', 'trainer_id')
            ->withTimestamps();
    }

    public function gymClasses(): HasMany {
        return $this->hasMany(GymClass::class);
    }
}