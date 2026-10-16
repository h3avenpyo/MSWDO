<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialCase\SocialCaseStudy;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\Senior\BirthdayPayout;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    /**
     * Resolve date range based on period type and request parameters.
     */
    public function resolveDateRange(Request $request): array
    {
        $period = $request->get('period', 'monthly');
        $now = Carbon::now();

        $year = (int) $request->get('year', $now->year);
        $month = (int) $request->get('month', $now->month);
        $quarter = (int) $request->get('quarter', ceil($now->month / 3));

        switch ($period) {
            case 'weekly':
                if ($request->filled('week_date')) {
                    $anchor = Carbon::parse($request->get('week_date'));
                } else {
                    $anchor = $now->copy();
                }
                $startDate = $anchor->copy()->startOfWeek();
                $endDate = $anchor->copy()->endOfWeek();
                $label = 'Week of ' . $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y');
                break;

            case 'quarterly':
                $startMonth = (($quarter - 1) * 3) + 1;
                $startDate = Carbon::createFromDate($year, $startMonth, 1)->startOfDay();
                $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
                $label = "Q{$quarter} {$year} (" . $startDate->format('M Y') . ' – ' . $endDate->format('M Y') . ')';
                break;

            case 'yearly':
                $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
                $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
                $label = "Full Year {$year}";
                break;

            case 'monthly':
            default:
                $period = 'monthly';
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
                $endDate = $startDate->copy()->endOfMonth();
                $label = $startDate->format('F Y');
                break;
        }

        return [
            'period' => $period,
            'year' => $year,
            'month' => $month,
            'quarter' => $quarter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'formattedRange' => $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y'),
            'label' => $label,
        ];
    }

    /**
     * Fetch Report Data for specified service & period
     */
    public function getReportData(Request $request)
    {
        $service = $request->get('service', 'all');
        $range = $this->resolveDateRange($request);
        $data = $this->buildReportPayload($service, $range);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Download or view PDF report
     */
    public function exportPdf(Request $request)
    {
        $service = $request->get('service', 'all');
        $range = $this->resolveDateRange($request);
        $data = $this->buildReportPayload($service, $range);

        $adminName = session('admin_user_name') ?: 'Fred Calos';
        $data['generatedBy'] = $adminName;
        $data['generatedAt'] = Carbon::now()->format('F d, Y h:i A');

        $filename = 'MSWDO_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $service))) . '_Report_' . $range['startDate']->format('Ymd') . '.pdf';

        $pdf = Pdf::loadView('admin.reports.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        if ($request->has('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Printable Browser View
     */
    public function printView(Request $request)
    {
        $service = $request->get('service', 'all');
        $range = $this->resolveDateRange($request);
        $data = $this->buildReportPayload($service, $range);

        $adminName = session('admin_user_name') ?: 'Fred Calos';
        $data['generatedBy'] = $adminName;
        $data['generatedAt'] = Carbon::now()->format('F d, Y h:i A');

        return view('admin.reports.print', $data);
    }

    /**
     * Build aggregated statistics and record rosters.
     */
    private function buildReportPayload(string $service, array $range): array
    {
        $start = $range['startDate'];
        $end = $range['endDate'];

        $payload = [
            'service' => $service,
            'serviceTitle' => $this->getServiceTitle($service),
            'range' => $range,
            'stats' => [],
            'breakdowns' => [],
            'records' => [],
        ];

        switch ($service) {
            case 'social_case':
                $payload = array_merge($payload, $this->aggregateSocialCaseData($start, $end));
                break;

            case 'senior':
                $payload = array_merge($payload, $this->aggregateSeniorData($start, $end));
                break;

            case 'financial':
                $payload = array_merge($payload, $this->aggregateFinancialData($start, $end));
                break;

            case 'all':
            default:
                $payload = array_merge($payload, $this->aggregateConsolidatedData($start, $end));
                break;
        }

        return $payload;
    }

    private function getServiceTitle(string $service): string
    {
        switch ($service) {
            case 'social_case': return 'Social Case Study';
            case 'senior': return 'Senior Citizen Affairs';
            case 'financial': return 'Financial Assistance Intake & Payroll';
            case 'all': default: return 'Consolidated Welfare Services';
        }
    }

    /**
     * Social Case Study Data Aggregation
     */
    private function aggregateSocialCaseData(Carbon $start, Carbon $end): array
    {
        $query = SocialCaseStudy::where(function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
              ->orWhereBetween('date_processed', [$start->toDateString(), $end->toDateString()]);
        });

        $totalCases = (clone $query)->count();
        $pendingCases = (clone $query)->whereIn('status', ['Draft', 'In Progress', 'Review', 'pending', 'in_progress', 'active'])->count();
        $resolvedCases = (clone $query)->whereIn('status', ['Completed', 'Printed', 'Released', 'Archived', 'resolved', 'closed', 'completed'])->count();
        $releasedReports = (clone $query)->where(function ($q) {
            $q->whereNotNull('released_at')->orWhere('status', 'Released');
        })->count();
        $totalAssistance = (clone $query)->where('assistance_released', true)->sum('assistance_amount')
            ?: (clone $query)->sum('assistance_amount') ?: 0;

        // Breakdown by purpose/service
        $purposeBreakdown = (clone $query)
            ->selectRaw("COALESCE(NULLIF(purpose, ''), NULLIF(service_provided, ''), 'General Assistance') as category, count(*) as count")
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'category')
            ->toArray();

        // Breakdown by Status
        $statusBreakdown = (clone $query)
            ->selectRaw("COALESCE(NULLIF(status, ''), 'pending') as status_val, count(*) as count")
            ->groupBy('status_val')
            ->pluck('count', 'status_val')
            ->toArray();

        // Barangay breakdown
        $barangayBreakdown = (clone $query)
            ->whereNotNull('intake_barangay')
            ->where('intake_barangay', '!=', '')
            ->selectRaw('intake_barangay, count(*) as count')
            ->groupBy('intake_barangay')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'intake_barangay')
            ->toArray();

        // Recent records roster
        $records = (clone $query)
            ->latest('created_at')
            ->take(25)
            ->get()
            ->map(function ($c) {
                $name = $c->intake_full_name ?: trim("{$c->intake_first_name} {$c->intake_last_name}");
                if (empty($name) && $c->client) {
                    $name = $c->client->full_name;
                }
                return [
                    'ref' => $c->case_number ?: ('#SC-' . str_pad($c->id, 5, '0', STR_PAD_LEFT)),
                    'name' => $name ?: 'Beneficiary',
                    'barangay' => $c->intake_barangay ?: 'N/A',
                    'category' => $c->purpose ?: ($c->service_provided ?: 'Social Assessment'),
                    'status' => ucfirst($c->status ?: 'Pending'),
                    'amount' => (float) ($c->assistance_amount ?? 0),
                    'date' => ($c->date_processed ? $c->date_processed->format('M d, Y') : ($c->created_at ? $c->created_at->format('M d, Y') : 'N/A')),
                ];
            })
            ->toArray();

        return [
            'stats' => [
                'totalVolume' => $totalCases,
                'pendingVolume' => $pendingCases,
                'completedVolume' => $resolvedCases,
                'releasedCount' => $releasedReports,
                'totalAmount' => (float) $totalAssistance,
            ],
            'breakdowns' => [
                'categories' => $purposeBreakdown,
                'statuses' => $statusBreakdown,
                'barangays' => $barangayBreakdown,
            ],
            'records' => $records,
        ];
    }

    /**
     * Senior Citizen Data Aggregation
     */
    private function aggregateSeniorData(Carbon $start, Carbon $end): array
    {
        $regQuery = SeniorCitizenRecord::whereBetween('created_at', [$start, $end]);
        $allSeniors = SeniorCitizenRecord::query();

        $newRegistrations = (clone $regQuery)->count();
        $totalActive = (clone $allSeniors)->where('status', 'active')->count();
        $totalArchived = (clone $allSeniors)->where('status', 'archived')->count();

        // Birthday cash gifts in period
        $payoutQuery = BirthdayPayout::where(function ($q) use ($start, $end) {
            $q->whereBetween('released_date', [$start, $end])
              ->orWhereBetween('created_at', [$start, $end]);
        });

        $payoutBeneficiaries = (clone $payoutQuery)->where('status', \App\Enums\PayoutStatus::Released->value)->count();
        $totalPayoutAmount = (clone $payoutQuery)->where('status', \App\Enums\PayoutStatus::Released->value)->sum('amount') ?? 0;

        // Gender breakdown of registrations or active seniors
        $genderBreakdown = (clone $regQuery)->count() > 0
            ? (clone $regQuery)->selectRaw('sex, count(*) as count')->groupBy('sex')->pluck('count', 'sex')->toArray()
            : (clone $allSeniors)->selectRaw('sex, count(*) as count')->groupBy('sex')->pluck('count', 'sex')->toArray();

        // Age bracket distribution
        $ageGroups = [
            '60-69' => (clone $allSeniors)->whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 60 AND 69')->count(),
            '70-79' => (clone $allSeniors)->whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 70 AND 79')->count(),
            '80-89' => (clone $allSeniors)->whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 80 AND 89')->count(),
            '90-99' => (clone $allSeniors)->whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 90 AND 99')->count(),
            '100+ (Centenarians)' => (clone $allSeniors)->whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 100')->count(),
        ];

        // Barangay distribution
        $barangayBreakdown = (clone $allSeniors)
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->selectRaw('barangay, count(*) as count')
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'barangay')
            ->toArray();

        // Records list
        $records = (clone $regQuery)->count() > 0
            ? (clone $regQuery)->latest('created_at')->take(25)->get()
            : (clone $allSeniors)->latest('updated_at')->take(25)->get();

        $formattedRecords = $records->map(function ($s) {
            $age = $s->birth_date ? Carbon::parse($s->birth_date)->age : 'N/A';
            return [
                'ref' => $s->senior_id_number ?: ($s->control_number ?: ('#SR-' . str_pad($s->id, 5, '0', STR_PAD_LEFT))),
                'name' => trim("{$s->first_name} {$s->middle_name} {$s->last_name}"),
                'barangay' => $s->barangay ?: 'N/A',
                'category' => "Age: {$age} • " . ucfirst($s->sex ?? 'Unspecified'),
                'status' => is_object($s->status) ? $s->status->label() : ucfirst((string) ($s->status ?? 'Active')),
                'amount' => 0.00,
                'date' => $s->created_at ? $s->created_at->format('M d, Y') : 'N/A',
            ];
        })->toArray();

        return [
            'stats' => [
                'totalVolume' => $newRegistrations,
                'activeSeniors' => $totalActive,
                'archivedSeniors' => $totalArchived,
                'payoutBeneficiaries' => $payoutBeneficiaries,
                'totalAmount' => (float) $totalPayoutAmount,
            ],
            'breakdowns' => [
                'categories' => $ageGroups,
                'statuses' => $genderBreakdown,
                'barangays' => $barangayBreakdown,
            ],
            'records' => $formattedRecords,
        ];
    }

    /**
     * Financial Assistance Data Aggregation
     */
    private function aggregateFinancialData(Carbon $start, Carbon $end): array
    {
        $query = BeneficiaryIntake::where(function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
              ->orWhereBetween('date_processed', [$start->toDateString(), $end->toDateString()]);
        });

        $totalIntakes = (clone $query)->count();
        $totalAmount = (clone $query)->sum('recommended_amount') ?: 0;
        $approvedCount = (clone $query)->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count();
        $pendingIntakes = (clone $query)->where(function ($q) {
            $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
        })->count();

        // Breakdown by assistance type
        $typeBreakdown = (clone $query)
            ->selectRaw("COALESCE(NULLIF(recommended_assistance_type, ''), NULLIF(purpose, ''), 'General Assistance') as category, count(*) as count")
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'category')
            ->toArray();

        // Breakdown by Barangay
        $barangayBreakdown = (clone $query)
            ->whereNotNull('beneficiary_barangay')
            ->where('beneficiary_barangay', '!=', '')
            ->selectRaw('beneficiary_barangay, count(*) as count')
            ->groupBy('beneficiary_barangay')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'beneficiary_barangay')
            ->toArray();

        // Status breakdown
        $statusBreakdown = [
            'Assessment Approved' => $approvedCount,
            'Pending Assessment' => $pendingIntakes,
        ];

        $records = (clone $query)
            ->latest('created_at')
            ->take(25)
            ->get()
            ->map(function ($intake) {
                $name = trim("{$intake->beneficiary_first_name} {$intake->beneficiary_middle_name} {$intake->beneficiary_last_name}");
                $status = ($intake->recommended_amount > 0) ? 'Approved' : 'Pending Review';

                return [
                    'ref' => $intake->control_number ?: ('#FA-' . str_pad($intake->id, 5, '0', STR_PAD_LEFT)),
                    'name' => $name ?: 'Beneficiary Client',
                    'barangay' => $intake->beneficiary_barangay ?: 'N/A',
                    'category' => $intake->recommended_assistance_type ?: ($intake->purpose ?: 'Financial Aid'),
                    'status' => $status,
                    'amount' => (float) ($intake->recommended_amount ?? 0),
                    'date' => $intake->date_processed ? Carbon::parse($intake->date_processed)->format('M d, Y') : ($intake->created_at ? $intake->created_at->format('M d, Y') : 'N/A'),
                ];
            })
            ->toArray();

        return [
            'stats' => [
                'totalVolume' => $totalIntakes,
                'pendingVolume' => $pendingIntakes,
                'completedVolume' => $approvedCount,
                'totalAmount' => (float) $totalAmount,
            ],
            'breakdowns' => [
                'categories' => $typeBreakdown,
                'statuses' => $statusBreakdown,
                'barangays' => $barangayBreakdown,
            ],
            'records' => $records,
        ];
    }

    /**
     * Consolidated (All Services) Aggregation
     */
    private function aggregateConsolidatedData(Carbon $start, Carbon $end): array
    {
        $sc = $this->aggregateSocialCaseData($start, $end);
        $sr = $this->aggregateSeniorData($start, $end);
        $fa = $this->aggregateFinancialData($start, $end);

        $totalBeneficiaries = $sc['stats']['totalVolume'] + $sr['stats']['totalVolume'] + $fa['stats']['totalVolume'];
        $totalDisbursed = $sc['stats']['totalAmount'] + $sr['stats']['totalAmount'] + $fa['stats']['totalAmount'];

        // Combined services breakdown
        $serviceBreakdown = [
            'Social Case Studies' => $sc['stats']['totalVolume'],
            'Senior Citizen Registrations' => $sr['stats']['totalVolume'],
            'Financial Assistance Intakes' => $fa['stats']['totalVolume'],
        ];

        // Combined records (sample from all 3)
        $combinedRecords = array_merge(
            array_slice($sc['records'], 0, 8),
            array_slice($fa['records'], 0, 8),
            array_slice($sr['records'], 0, 8)
        );

        return [
            'stats' => [
                'totalVolume' => $totalBeneficiaries,
                'socialCaseCases' => $sc['stats']['totalVolume'],
                'seniorRegistrations' => $sr['stats']['totalVolume'],
                'financialIntakes' => $fa['stats']['totalVolume'],
                'totalAmount' => (float) $totalDisbursed,
            ],
            'breakdowns' => [
                'categories' => $serviceBreakdown,
                'statuses' => [
                    'Social Cases Resolved' => $sc['stats']['completedVolume'],
                    'Seniors Paid Cash Gift' => $sr['stats']['payoutBeneficiaries'],
                    'FA Disbursed / Claimed' => $fa['stats']['completedVolume'],
                ],
                'barangays' => array_slice($fa['breakdowns']['barangays'] ?: $sc['breakdowns']['barangays'], 0, 8, true),
            ],
            'records' => $combinedRecords,
        ];
    }
}
