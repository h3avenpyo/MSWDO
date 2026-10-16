<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Wcpu\User as WcpuUser;

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

    /**
     * Determine the destination URL for a WCPU user based on position/departments.
     */
    public static function wcpuRouteFor(WcpuUser $user): string
    {
        if ($user->position === 'Desk Officer') {
            $defaultDept = $user->defaultCaseDepartment();

            if ($defaultDept === 'BCPC' && ! $user->isMultiCaseDepartment()) {
                return route('desk-officer.bcpc.dashboard');
            }

            return route('desk-officer.dashboard');
        }

        // Administrator (SWD Admin and BCPC Admin)
        return route('admin-dashboard');
    }
}
