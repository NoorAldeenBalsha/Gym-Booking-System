<?php

namespace App\Enums;

enum UserRole: string {
    case Admin = 'admin';
    case Trainer = 'trainer';
    case Member = 'member';

    public function label(): string {
        return match ($this) {
            self::Admin => 'Admin',
            self::Trainer => 'Trainer',
            self::Member => 'Member',
        };
    }

    public function color(): string {
        return match ($this) {
            self::Admin => 'danger',
            self::Trainer => 'warning',
            self::Member => 'info',
        };
    }
}