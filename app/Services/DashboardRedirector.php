<?php

namespace App\Services;

use App\Enums\UserRole;

class DashboardRedirector
{
    public static function routeFor(UserRole $role): string
    {
        return match ($role) {
            UserRole::SocialWorker, UserRole::EligibilityChecker => 'admin.social-case.dashboard',
            UserRole::SeniorCitizenOfficer, UserRole::Staff => 'admin.senior',
            UserRole::FinancialStep2 => 'admin.financial.financialstep2',
            UserRole::FinancialAssistanceOfficer, UserRole::FinancialStep1 => 'admin.financial.dashboard',
            UserRole::Admin, UserRole::Encoder => 'admin.dashboard',
        };
    }
}
