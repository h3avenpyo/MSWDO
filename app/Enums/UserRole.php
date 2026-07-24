<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case SocialWorker = 'social_worker';
    case Encoder = 'encoder';
    case Staff = 'staff';
    case SeniorCitizenOfficer = 'Senior Citizen officer';
    case FinancialAssistanceOfficer = 'Financial assistance officer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::SocialWorker => 'Social Worker',
            self::Encoder => 'Encoder',
            self::Staff => 'Staff',
            self::SeniorCitizenOfficer => 'Senior Citizen Officer',
            self::FinancialAssistanceOfficer => 'Financial Assistance Officer',
        };
    }
}
