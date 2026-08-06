<?php

namespace App\Services;

use App\Enums\UserRole;

class DashboardRedirector
{
    public static function routeFor(UserRole $role): string
    {
        return match ($role) {
            UserRole::SocialWorker => 'admin.social-case.dashboard',
            UserRole::SeniorCitizenOfficer, UserRole::Staff => 'admin.senior',
            UserRole::FinancialAssistanceOfficer => 'admin.financial',
            UserRole::Admin, UserRole::Encoder => 'admin.dashboard',
        };
    }
}
