<?php

namespace App\Enums;

enum SignaturePosition: string
{
    case OscaHead = 'osca_head';
    case MswdoOfficer = 'mswdo_officer';
    case MswdoStaff = 'mswdo_staff';

    public function label(): string
    {
        return match ($this) {
            self::OscaHead => 'OSCA Head',
            self::MswdoOfficer => 'MSWDO Officer',
            self::MswdoStaff => 'MSWDO Staff',
        };
    }
}
