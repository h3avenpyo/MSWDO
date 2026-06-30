<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mock data for dashboard - replace with actual database queries
        $data = [
            'totalCases' => 1247,
            'pendingCases' => 89,
            'resolvedCases' => 1158,
            'totalUsers' => 45,
            'staffPerformance' => [
                ['name' => 'Maria Santos', 'assigned' => 45, 'completed' => 42, 'pending' => 3, 'rate' => 93, 'avgTime' => '2.5 days'],
                ['name' => 'Juan Reyes', 'assigned' => 38, 'completed' => 35, 'pending' => 3, 'rate' => 92, 'avgTime' => '2.8 days'],
                ['name' => 'Ana Cruz', 'assigned' => 52, 'completed' => 48, 'pending' => 4, 'rate' => 92, 'avgTime' => '2.3 days'],
                ['name' => 'Carlos Mendoza', 'assigned' => 41, 'completed' => 37, 'pending' => 4, 'rate' => 90, 'avgTime' => '3.1 days'],
                ['name' => 'Liza Garcia', 'assigned' => 35, 'completed' => 32, 'pending' => 3, 'rate' => 91, 'avgTime' => '2.6 days'],
            ],
            'recentActivities' => [
                ['user' => 'Maria Santos', 'action' => 'Approved a case', 'time' => '2 minutes ago', 'photo' => 'MS'],
                ['user' => 'Juan Reyes', 'action' => 'Registered new beneficiary', 'time' => '15 minutes ago', 'photo' => 'JR'],
                ['user' => 'System', 'action' => 'Financial assistance released', 'time' => '1 hour ago', 'photo' => 'SY'],
                ['user' => 'Ana Cruz', 'action' => 'Updated Social Case Study', 'time' => '2 hours ago', 'photo' => 'AC'],
                ['user' => 'System', 'action' => 'Senior Citizen case closed', 'time' => '3 hours ago', 'photo' => 'SY'],
            ],
            'casesRequiringAttention' => [
                ['title' => 'Overdue: Case #1234', 'priority' => 'high', 'type' => 'Financial Assistance'],
                ['title' => 'Pending approval: Case #1235', 'priority' => 'medium', 'type' => 'Social Case Study'],
                ['title' => 'Incomplete docs: Case #1236', 'priority' => 'medium', 'type' => 'Senior Citizen'],
                ['title' => 'Upcoming interview: Case #1237', 'priority' => 'low', 'type' => 'Financial Assistance'],
                ['title' => 'Home visit scheduled: Case #1238', 'priority' => 'low', 'type' => 'Social Case Study'],
            ],
            'userOverview' => [
                'totalAdmins' => 5,
                'totalSocialWorkers' => 32,
                'totalStaff' => 8,
                'activeUsers' => 42,
                'inactiveUsers' => 3,
            ],
            'reportsSummary' => [
                'casesThisMonth' => 98,
                'closedThisMonth' => 85,
                'pendingCases' => 89,
                'generatedReports' => 24,
                'financialReleased' => 125000,
            ],
        ];

        // Also needed by the dashboard charts
        $data['caseDistribution'] = [
            'Financial Assistance' => 456,
            'Social Case Study'    => 312,
            'Senior Citizen'       => 56,
        ];
        $data['barangayStats'] = [
            'Poblacion I'   => 145,
            'Poblacion II'  => 132,
            'Poblacion III' => 98,
            'Poblacion IV'  => 87,
            'Poblacion V'   => 76,
            'Adlas'         => 54,
            'Anahaw I'      => 43,
            'Biluso'        => 23,
        ];

        return view('admin.dashboard', $data);
    }

    public function statistics()
    {
        $data = [
            'caseDistribution' => [
                'Financial Assistance' => 456,
                'Social Case Study' => 312,
                'Senior Citizen' => 56
            ],
            'barangayStats' => [
                'Poblacion I' => 145,
                'Poblacion II' => 132,
                'Poblacion III' => 98,
                'Poblacion IV' => 87,
                'Poblacion V' => 76,
                'Poblacion VI' => 65,
                'Adlas' => 54,
                'Anahaw I' => 43,
                'Anahaw II' => 38,
                'Balite I' => 35,
                'Balite II' => 32,
                'Balubad' => 29,
                'Banaba' => 27,
                'Batas' => 25,
                'Biluso' => 23,
                'Bucal' => 21,
                'Buho' => 19,
                'Bulihan' => 18,
                'Cabangaan' => 17,
                'Carmen' => 16,
                'Hukay' => 15,
                'Inchican' => 14,
                'Ipil I' => 13,
                'Ipil II' => 12,
                'Kalubkob' => 11,
                'Kaong' => 10,
                'Lalaan I' => 9,
                'Lalaan II' => 8,
                'Litlit' => 7,
                'Lucsuhin' => 6,
                'Lumil' => 5,
                'Maguyam' => 4,
                'Malabag' => 3,
                'Mataas na Burol' => 2,
                'Munting Ilog' => 1,
                'Narra I' => 45,
                'Narra II' => 42,
                'Narra III' => 39,
                'Paligawan' => 36,
                'Pasong Langka' => 33,
                'Pooc I' => 30,
                'Pooc II' => 28,
                'Pulong Bunga' => 26,
                'Punong-Guihan' => 24,
                'Sabutan' => 22,
                'San Miguel I' => 20,
                'San Miguel II' => 18,
                'Santol' => 16,
                'Tartaria' => 14,
                'Tibig' => 12,
                'Tubuan I' => 10,
                'Tubuan II' => 8,
                'Ulat' => 6,
                'Acacia' => 4,
                'Biga I' => 2,
                'Biga II' => 1,
                'Buasao' => 1,
                'Dila' => 1,
                'Luksuhin Ilaya' => 1,
                'Malaking Tatyao' => 1,
                'Matangtubig' => 1,
                'Puting Kahoy' => 1,
                'Narra IV' => 1,
                'Batas Dako' => 1,
            ],
        ];

        return view('admin.statistics', $data);
    }

    public function addOfficers()
    {
        // Mock data or whatever is needed for adding officers page
        return view('admin.add-officers');
    }
}
