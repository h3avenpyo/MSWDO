<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case SocialWorker = 'social_worker';
    case EligibilityChecker = 'eligibility_checker';
    case Encoder = 'encoder';
    case Staff = 'staff';
    case SeniorCitizenOfficer = 'Senior Citizen officer';
    case FinancialAssistanceOfficer = 'Financial assistance officer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::SocialWorker => 'Social Worker',
            self::EligibilityChecker => 'Eligibility Checker',
            self::Encoder => 'Encoder',
            self::Staff => 'Staff',
            self::SeniorCitizenOfficer => 'Senior Citizen Officer',
            self::FinancialAssistanceOfficer => 'Financial Assistance Officer',
        };
    }

    public function canAccessSocialCase(): bool
    {
        return in_array($this, [self::Admin, self::SocialWorker, self::EligibilityChecker], true);
    }

    public function canEncodeCase(): bool
    {
        return in_array($this, [self::Admin, self::SocialWorker], true);
    }

    public function canCheckEligibility(): bool
    {
        return in_array($this, [self::Admin, self::EligibilityChecker], true);
    }
}
