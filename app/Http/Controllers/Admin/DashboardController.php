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

        // Fetch monthly social case data for analytics
        $monthlySocialCases = $this->getMonthlySocialCases();

        // Fetch reports summary & barangay distribution
        $reportsSummary = $this->getReportsSummary();
        $barangayStats = $this->getBarangayStats();

        // High-level executive KPI indicators
        $executiveSummary = [
            'totalBeneficiaries' => ($serviceBreakdown['Social Case Study']['total'] ?? 0) + ($serviceBreakdown['Senior Citizen']['total'] ?? 0),
            'activeCases' => ($serviceBreakdown['Social Case Study']['pending'] ?? 0) + ($serviceBreakdown['Financial Assistance']['pending'] ?? 0),
            'financialReleased' => $reportsSummary['financialReleased'] ?? 0,
            'casesThisMonth' => $reportsSummary['casesThisMonth'] ?? 0,
            'closedThisMonth' => $reportsSummary['closedThisMonth'] ?? 0,
        ];

        $data = [
            'staffPerformance' => [],
            'recentActivities' => [],
            'casesRequiringAttention' => [],
            'userOverview' => [
                'totalAdmins' => class_exists(User::class) ? User::where('role', 'admin')->count() : 0,
                'totalSocialWorkers' => class_exists(User::class) ? User::where('role', 'social_worker')->count() : 0,
                'totalStaff' => class_exists(User::class) ? User::where('role', 'staff')->count() : 0,
                'activeUsers' => class_exists(User::class) ? User::where('status', 'active')->count() : 0,
                'inactiveUsers' => class_exists(User::class) ? User::where('status', 'inactive')->count() : 0,
            ],
            'reportsSummary' => $reportsSummary,
            'executiveSummary' => $executiveSummary,
            'justLoggedIn' => $justLoggedIn,
            'serviceBreakdown' => $serviceBreakdown,
            'recentCases' => $recentCases,
            'monthlySocialCases' => $monthlySocialCases,
            'barangayStats' => $barangayStats,
            'caseDistribution' => [],
        ];

        return view('admin.admin-dashboard', $data);
    }

    private function getReportsSummary()
    {
        $casesThisMonth = 0;
        $closedThisMonth = 0;
        $pendingCases = 0;
        $generatedReports = 0;
        $financialReleased = 0;

        if (class_exists(\App\Models\SocialCase\SocialCaseStudy::class)) {
            $casesThisMonth = \App\Models\SocialCase\SocialCaseStudy::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            $closedThisMonth = \App\Models\SocialCase\SocialCaseStudy::whereIn('status', ['resolved', 'closed', 'completed'])
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count();

            $pendingCases = \App\Models\SocialCase\SocialCaseStudy::whereIn('status', ['pending', 'active', 'in_progress'])
                ->count();

            $generatedReports = \App\Models\SocialCase\SocialCaseStudy::whereNotNull('released_at')->count();
        }

        if (class_exists(\App\Models\SocialCase\BeneficiaryIntake::class)) {
            $financialReleased += (float) (\App\Models\SocialCase\BeneficiaryIntake::whereNotNull('recommended_amount')->sum('recommended_amount') ?? 0);
        }

        if (class_exists(\App\Models\Senior\BirthdayPayout::class)) {
            $financialReleased += (float) (\App\Models\Senior\BirthdayPayout::where('status', 'released')->sum('amount') ?? 0);
        }

        return [
            'casesThisMonth' => $casesThisMonth,
            'closedThisMonth' => $closedThisMonth,
            'pendingCases' => $pendingCases,
            'generatedReports' => $generatedReports,
            'financialReleased' => $financialReleased,
        ];
    }

    private function getBarangayStats()
    {
        if (!class_exists(\App\Models\Senior\SeniorCitizenRecord::class)) {
            return ['labels' => [], 'data' => []];
        }

        $records = \App\Models\Senior\SeniorCitizenRecord::selectRaw('barangay, count(*) as count')
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->take(6)
            ->get();

        return [
            'labels' => $records->pluck('barangay')->toArray(),
            'data' => $records->pluck('count')->toArray(),
        ];
    }

    private function getServiceBreakdown()
    {
        $services = [];

        // Social Case Study
        if (class_exists(\App\Models\SocialCase\SocialCaseStudy::class)) {
            $totalClients = class_exists(\App\Models\Client::class) ? \App\Models\Client::count() : 0;
            $casesThisMonth = \App\Models\SocialCase\SocialCaseStudy::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();
            $releasedToday = \App\Models\SocialCase\SocialCaseStudy::whereDate('released_at', today())->count();
            $totalReleased = \App\Models\SocialCase\SocialCaseStudy::whereNotNull('released_at')->count();

            $services['Social Case Study'] = [
                'active' => $totalClients,
                'pending' => $casesThisMonth,
                'overdue' => $releasedToday,
                'completed' => $totalReleased,
                'total' => $totalClients,
                'icon' => 'folder-heart',
                'color' => 'indigo',
                'route' => route('admin.social-case.dashboard'),
                'metrics' => [
                    ['label' => 'Total Clients', 'val' => number_format($totalClients), 'badge' => 'neutral'],
                    ['label' => 'Cases This Month', 'val' => number_format($casesThisMonth), 'badge' => 'info'],
                    ['label' => 'Released Today', 'val' => number_format($releasedToday), 'badge' => 'success'],
                    ['label' => 'Total Released', 'val' => number_format($totalReleased), 'badge' => 'success'],
                ],
            ];
        }

        // Financial Assistance
        if (class_exists(\App\Models\SocialCase\BeneficiaryIntake::class)) {
            $activeIntakes = \App\Models\SocialCase\BeneficiaryIntake::where(function ($q) {
                $q->where('is_archived', false)->orWhereNull('is_archived');
            });
            $totalIntakes = (clone $activeIntakes)->count();
            $pendingAssessments = (clone $activeIntakes)->whereHas('socialCaseStudy', function($query) {
                $query->where('status', 'pending');
            })->count();
            $step1Approved = (clone $activeIntakes)->whereHas('socialCaseStudy', function($query) {
                $query->where('status', 'active');
            })->count();
            $readyForStep2 = (clone $activeIntakes)->whereHas('socialCaseStudy', function($query) {
                $query->where('status', 'resolved');
            })->count();

            $services['Financial Assistance'] = [
                'active' => $totalIntakes,
                'pending' => $pendingAssessments,
                'overdue' => $step1Approved,
                'completed' => $readyForStep2,
                'total' => $totalIntakes,
                'icon' => 'banknote',
                'color' => 'emerald',
                'route' => url('/admin/financial/dashboard'),
                'metrics' => [
                    ['label' => 'Total Intakes', 'val' => number_format($totalIntakes), 'badge' => 'neutral'],
                    ['label' => 'Pending Review', 'val' => number_format($pendingAssessments), 'badge' => 'warning'],
                    ['label' => 'Step 1 Approved', 'val' => number_format($step1Approved), 'badge' => 'info'],
                    ['label' => 'Ready Step 2', 'val' => number_format($readyForStep2), 'badge' => 'success'],
                ],
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
                'active' => $totalSeniors,
                'pending' => $activeSeniors,
                'overdue' => $archived,
                'completed' => $totalPayout,
                'total' => $totalSeniors,
                'icon' => 'heart-handshake',
                'color' => 'amber',
                'route' => route('admin.senior'),
                'metrics' => [
                    ['label' => 'Total Seniors', 'val' => number_format($totalSeniors), 'badge' => 'neutral'],
                    ['label' => 'Active Seniors', 'val' => number_format($activeSeniors), 'badge' => 'success'],
                    ['label' => 'Archived', 'val' => number_format($archived), 'badge' => 'neutral'],
                    ['label' => 'Total Payout', 'val' => '₱' . number_format($totalPayout, 2), 'badge' => 'amber'],
                ],
            ];
        }

        // VAWC (Violence Against Women and Children)
        $services['VAWC'] = [
            'active' => 0,
            'pending' => 0,
            'overdue' => 0,
            'completed' => 0,
            'total' => 0,
            'icon' => 'shield-alert',
            'color' => 'rose',
            'status' => 'standby',
            'route' => '#',
            'metrics' => [
                ['label' => 'Total Cases', 'val' => 0, 'badge' => 'neutral'],
                ['label' => 'Active Cases', 'val' => 0, 'badge' => 'info'],
                ['label' => 'Pending Action', 'val' => 0, 'badge' => 'warning'],
                ['label' => 'Resolved', 'val' => 0, 'badge' => 'success'],
            ],
        ];

        // BCPC (Barangay Council for the Protection of Children)
        $services['BCPC'] = [
            'active' => 0,
            'pending' => 0,
            'overdue' => 0,
            'completed' => 0,
            'total' => 0,
            'icon' => 'smile',
            'color' => 'cyan',
            'status' => 'standby',
            'route' => '#',
            'metrics' => [
                ['label' => 'Total Cases', 'val' => 0, 'badge' => 'neutral'],
                ['label' => 'Active Cases', 'val' => 0, 'badge' => 'info'],
                ['label' => 'Overdue', 'val' => 0, 'badge' => 'danger'],
                ['label' => 'Resolved', 'val' => 0, 'badge' => 'success'],
            ],
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
                        'id' => $case->id,
                        'client' => $case->client ? ($case->client->full_name ?? 'Unknown') : 'Unknown',
                        'service' => 'Social Case Study',
                        'officer' => $officerName,
                        'status' => ucfirst($case->status ?? 'Pending'),
                        'updated' => $case->updated_at ? $case->updated_at->format('M d, Y') : 'N/A',
                        'url' => route('admin.social-case.cases'),
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
                    $status = 'Pending';
                    $officerName = 'Not assigned';
                    if ($intake->socialCaseStudy) {
                        $status = ucfirst($intake->socialCaseStudy->status ?? 'Pending');
                        if ($intake->socialCaseStudy->officer) {
                            $officerName = $intake->socialCaseStudy->officer->name ?? 'Not assigned';
                        } elseif ($intake->socialCaseStudy->social_worker_name) {
                            $officerName = $intake->socialCaseStudy->social_worker_name;
                        }
                    }
                    return [
                        'id' => $intake->id,
                        'client' => $intake->client_name ?? 'Unknown',
                        'service' => 'Financial Assistance',
                        'officer' => $officerName,
                        'status' => $status,
                        'updated' => $intake->updated_at ? $intake->updated_at->format('M d, Y') : 'N/A',
                        'url' => url('/admin/financial/dashboard'),
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
                        'id' => $senior->id,
                        'client' => $senior->full_name ?? 'Unknown',
                        'service' => 'Senior Citizen',
                        'officer' => $officerName,
                        'status' => $senior->status instanceof \App\Enums\SeniorStatus
                            ? $senior->status->label()
                            : ucfirst((string) ($senior->status ?? 'Active')),
                        'updated' => $senior->updated_at ? $senior->updated_at->format('M d, Y') : 'N/A',
                        'url' => route('admin.senior'),
                    ];
                });
            $recentCases = $recentCases->concat($seniorCases);
        }

        return $recentCases->take(10);
    }

    private function getMonthlySocialCases()
    {
        if (!class_exists(\App\Models\SocialCase\SocialCaseStudy::class)) {
            return [];
        }

        $monthlyData = [];
        $months = [];
        
        // Get data for the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;
            
            $count = \App\Models\SocialCase\SocialCaseStudy::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $monthlyData[] = $count;
        }

        return [
            'labels' => $months,
            'data' => $monthlyData
        ];
    }

    public function financialDashboard()
    {
        $activeQuery = class_exists(\App\Models\SocialCase\BeneficiaryIntake::class) 
            ? \App\Models\SocialCase\BeneficiaryIntake::where(function ($q) {
                $q->where('is_archived', false)->orWhereNull('is_archived');
            })
            : null;

        $totalIntakes = $activeQuery ? (clone $activeQuery)->count() : 0;
        $recentIntakes = $activeQuery 
            ? (clone $activeQuery)->with(['client', 'encoderUser'])->latest()->take(6)->get() 
            : collect();

        $today = \Carbon\Carbon::today();
        $todayIntakes = $activeQuery
            ? (clone $activeQuery)->whereDate('date_processed', $today)->count()
            : 0;
        $step1Approved = $totalIntakes;
        $readyForStep2 = $activeQuery
            ? ((clone $activeQuery)->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count() ?: $totalIntakes)
            : 0;
        $totalAmount = $activeQuery
            ? ((clone $activeQuery)->sum('recommended_amount') ?? 0)
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
