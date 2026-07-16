<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case SocialWorker = 'social_worker';
    case Encoder = 'encoder';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::SocialWorker => 'Social Worker',
            self::Encoder => 'Encoder',
            self::Staff => 'Staff',
        };
    }
}
