<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Senior\BirthdayPayout;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $justLoggedIn = session('admin_just_logged_in', false);

        if ($justLoggedIn) {
            session()->forget('admin_just_logged_in');
        }

        // Fetch service breakdown statistics
        $serviceBreakdown = $this->getServiceBreakdown();

        // Fetch recent cases
        $recentCases = $this->getRecentCases();

        $data = [
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
            'justLoggedIn' => $justLoggedIn,
            'serviceBreakdown' => $serviceBreakdown,
            'recentCases' => $recentCases,
        ];

        $data['caseDistribution'] = [];
        $data['barangayStats'] = [];

        return view('admin.dashboard', $data);
    }

    private function getServiceBreakdown()
    {
        $services = [];

        // Social Case Study
        if (class_exists(\App\Models\SocialCase\SocialCaseStudy::class)) {
            $totalClients = class_exists(\App\Models\Client::class) ? \App\Models\Client::count() : 0;
            $casesThisMonth = \App\Models\SocialCase\SocialCaseStudy::whereMonth('created_at', now()->month)->count();
            $releasedToday = \App\Models\SocialCase\SocialCaseStudy::whereDate('released_at', today())->count();
            $totalReleased = \App\Models\SocialCase\SocialCaseStudy::whereNotNull('released_at')->count();

            $services['Social Case Study'] = [
                'active' => $totalClients,        // Total Clients
                'pending' => $casesThisMonth,    // Cases This Month
                'overdue' => $releasedToday,     // Released Today
                'completed' => $totalReleased,  // Total Released
                'total' => $totalClients, // Total shows total clients
            ];
        }

        // Financial Assistance
        if (class_exists(\App\Models\SocialCase\BeneficiaryIntake::class)) {
            $totalIntakes = \App\Models\SocialCase\BeneficiaryIntake::count();
            $pendingAssessments = \App\Models\SocialCase\BeneficiaryIntake::whereHas('socialCaseStudy', function($query) {
                $query->where('status', 'pending');
            })->count();
            $step1Approved = \App\Models\SocialCase\BeneficiaryIntake::whereHas('socialCaseStudy', function($query) {
                $query->where('status', 'active');
            })->count();
            $readyForStep2 = \App\Models\SocialCase\BeneficiaryIntake::whereHas('socialCaseStudy', function($query) {
                $query->where('status', 'resolved');
            })->count();

            $services['Financial Assistance'] = [
                'active' => $totalIntakes,         // Total Intakes
                'pending' => $pendingAssessments, // Pending Assessments
                'overdue' => $step1Approved,     // Step 1 Approved
                'completed' => $readyForStep2,  // Ready for Step 2
                'total' => $totalIntakes, // Total shows total intakes
            ];
        }

        // Senior Citizen
        if (class_exists(\App\Models\Senior\SeniorCitizenRecord::class)) {
            $totalSeniors = \App\Models\Senior\SeniorCitizenRecord::whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')->count();
            $activeSeniors = \App\Models\Senior\SeniorCitizenRecord::where('status', 'active')
                ->whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')->count();
            $archived = \App\Models\Senior\SeniorCitizenRecord::where('status', 'archived')
                ->whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')->count();
            $totalPayout = class_exists(\App\Models\Senior\BirthdayPayout::class) 
                ? \App\Models\Senior\BirthdayPayout::where('status', 'released')->sum('amount') 
                : 0;

            $services['Senior Citizen'] = [
                'active' => $totalSeniors,    // Total Seniors
                'pending' => $activeSeniors,  // Active Seniors
                'overdue' => $archived,      // Archived
                'completed' => $totalPayout, // Total Payout
                'total' => $totalSeniors, // Total shows total seniors
            ];
        }

        // VAWC (Violence Against Women and Children) - Placeholder for now
        $services['VAWC'] = [
            'active' => 0,
            'pending' => 0,
            'overdue' => 0,
            'completed' => 0,
            'total' => 0,
        ];

        // BCPC (Barangay Council for the Protection of Children) - Placeholder for now
        $services['BCPC'] = [
            'active' => 0,
            'pending' => 0,
            'overdue' => 0,
            'completed' => 0,
            'total' => 0,
        ];

        return $services;
    }

    private function getRecentCases()
    {
        $recentCases = collect();

        // Get recent Social Case Studies
        if (class_exists(\App\Models\SocialCase\SocialCaseStudy::class)) {
            $socialCases = \App\Models\SocialCase\SocialCaseStudy::with('client', 'officer')
                ->latest('updated_at')
                ->take(5)
                ->get()
                ->map(function ($case) {
                    $officerName = 'Not assigned';
                    if ($case->officer) {
                        $officerName = $case->officer->name ?? 'Not assigned';
                    } elseif ($case->social_worker_name) {
                        $officerName = $case->social_worker_name;
                    }
                    return [
                        'client' => $case->client ? $case->client->full_name ?? 'Unknown' : 'Unknown',
                        'service' => 'Social Case Study',
                        'officer' => $officerName,
                        'status' => ucfirst($case->status),
                        'updated' => $case->updated_at->format('M d, Y'),
                    ];
                });
            $recentCases = $recentCases->concat($socialCases);
        }

        // Get recent Financial Assistance cases
        if (class_exists(\App\Models\SocialCase\BeneficiaryIntake::class)) {
            $financialCases = \App\Models\SocialCase\BeneficiaryIntake::with('socialCaseStudy.officer')
                ->latest('updated_at')
                ->take(5)
                ->get()
                ->map(function ($intake) {
                    $status = 'Unknown';
                    $officerName = 'Not assigned';
                    if ($intake->socialCaseStudy) {
                        $status = ucfirst($intake->socialCaseStudy->status);
                        if ($intake->socialCaseStudy->officer) {
                            $officerName = $intake->socialCaseStudy->officer->name ?? 'Not assigned';
                        } elseif ($intake->socialCaseStudy->social_worker_name) {
                            $officerName = $intake->socialCaseStudy->social_worker_name;
                        }
                    }
                    return [
                        'client' => $intake->client_name ?? 'Unknown',
                        'service' => 'Financial Assistance',
                        'officer' => $officerName,
                        'status' => $status,
                        'updated' => $intake->updated_at->format('M d, Y'),
                    ];
                });
            $recentCases = $recentCases->concat($financialCases);
        }

        // Get recent Senior Citizen records
        if (class_exists(\App\Models\Senior\SeniorCitizenRecord::class)) {
            $seniorCases = \App\Models\Senior\SeniorCitizenRecord::with('createdBy')
                ->latest('updated_at')
                ->take(5)
                ->get()
                ->map(function ($senior) {
                    $officerName = 'N/A';
                    if ($senior->createdBy) {
                        $officerName = $senior->createdBy->name ?? 'Unknown';
                    }
                    return [
                        'client' => $senior->full_name ?? 'Unknown',
                        'service' => 'Senior Citizen',
                        'officer' => $officerName,
                        'status' => $senior->status instanceof \App\Enums\SeniorStatus
                            ? $senior->status->label()
                            : ucfirst((string) $senior->status),
                        'updated' => $senior->updated_at ? $senior->updated_at->format('F j, Y') : 'N/A',
                    ];
                });
            $recentCases = $recentCases->concat($seniorCases);
        }

        // Sort by updated date and take top 10
        return $recentCases->take(10);
    }

    public function financialDashboard()
    {
        $totalIntakes = class_exists(\App\Models\SocialCase\BeneficiaryIntake::class) 
            ? \App\Models\SocialCase\BeneficiaryIntake::count() 
            : 0;
        $recentIntakes = class_exists(\App\Models\SocialCase\BeneficiaryIntake::class) 
            ? \App\Models\SocialCase\BeneficiaryIntake::with(['client', 'encoderUser'])->latest()->take(6)->get() 
            : collect();

        $today = \Carbon\Carbon::today();
        $todayIntakes = class_exists(\App\Models\SocialCase\BeneficiaryIntake::class)
            ? \App\Models\SocialCase\BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('created_at', $today)->orWhereDate('date_processed', $today);
            })->count()
            : 0;
        $step1Approved = $totalIntakes;
        $readyForStep2 = class_exists(\App\Models\SocialCase\BeneficiaryIntake::class)
            ? (\App\Models\SocialCase\BeneficiaryIntake::whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count() ?: $totalIntakes)
            : 0;
        $totalAmount = class_exists(\App\Models\SocialCase\BeneficiaryIntake::class)
            ? (\App\Models\SocialCase\BeneficiaryIntake::sum('recommended_amount') ?? 0)
            : 0;

        return view('admin.financial.financial-dashboard', compact('totalIntakes', 'todayIntakes', 'step1Approved', 'readyForStep2', 'totalAmount', 'recentIntakes'));
    }

    public function financialStep1(\Illuminate\Http\Request $request)
    {
        return app(\App\Http\Controllers\Admin\Financial\FinancialDashboardController::class)->financialStep1($request);
    }

    public function financialStep2(\Illuminate\Http\Request $request)
    {
        return app(\App\Http\Controllers\Admin\Financial\FinancialDashboardController::class)->financialStep2($request);
    }
}
