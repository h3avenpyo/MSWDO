<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $justLoggedIn = session('admin_just_logged_in', false);

        if ($justLoggedIn) {
            session()->forget('admin_just_logged_in');
        }

        $officers = User::
            where('email', '!=', 'admin@mswdo.test')
            ->select('id', 'name', 'email', 'role', 'created_at', 'status')
            ->orderByDesc('created_at')
            ->get();

        $data = [
            'totalCases' => 0,
            'pendingCases' => 0,
            'resolvedCases' => 0,
            'totalUsers' => $officers->count(),
            'staffPerformance' => [],
            'recentActivities' => [],
            'casesRequiringAttention' => [],
            'userOverview' => [
                'totalAdmins' => 0,
                'totalSocialWorkers' => 0,
                'totalStaff' => 0,
                'activeUsers' => 0,
                'inactiveUsers' => 0,
            ],
            'reportsSummary' => [
                'casesThisMonth' => 0,
                'closedThisMonth' => 0,
                'pendingCases' => 0,
                'generatedReports' => 0,
                'financialReleased' => 0,
            ],
            'officers' => $officers,
            'justLoggedIn' => $justLoggedIn,
        ];

        $data['caseDistribution'] = [];
        $data['barangayStats'] = [];

        return view('admin.dashboard', $data);
    }

    public function financial()
    {
        return view('admin.financial');
    }
}
