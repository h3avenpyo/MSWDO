<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\QuarterlyReport;
use App\Models\Wcpu\BcpcCaseMonitoring;
use App\Models\Wcpu\CaseModel;
use App\Models\Wcpu\CaseType;
use App\Models\Wcpu\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class QuarterlyReportController extends Controller
{
    public function printAllDeskOfficer(Request $request)
    {
        $user = Auth::user();
        abort_unless($user?->position === 'Desk Officer' && $user->isMultiCaseDepartment(), 403);
        $request->validate(['type' => 'required|in:BCPC,VAWC']);
        $type = $this->requestedReportType($request->input('type'));
        $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->firstOrFail();
        $reports = QuarterlyReport::where('barangay_id', $barangay->id)->where('type', $type)
            ->whereNull('archived_at')->orderByDesc('year')->orderByDesc('quarter')->get();
        if ($reports->isEmpty()) {
            return back()->with('error', 'There are no quarterly reports to print.');
        }
        $caseTypes = CaseType::all()->pluck('name', 'republic_act')->toArray();
        $pages = [];
        $styles = '';
        foreach ($reports as $report) {
            $quarter = $report->quarter;
            $year = $report->year;
            $startDate = Carbon::create($year, ($quarter - 1) * 3 + 1, 1)->startOfDay();
            $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
            $html = view('pdf.quarterly-report', [
                'barangay' => $barangay, 'quarter' => $quarter, 'year' => $year,
                'period' => "Q{$quarter} {$year}", 'startDate' => $startDate, 'endDate' => $endDate,
                'reportData' => $this->freshQuarterlyReportData($report), 'caseTypes' => $caseTypes,
                'generatedAt' => now()->format('F j, Y'), 'user' => $user, 'printEditControls' => false,
            ])->render();
            if ($styles === '') {
                preg_match_all('/<style[^>]*>.*?<\/style>/s', $html, $matches);
                $styles = implode('', $matches[0]);
            }
            preg_match('/<body[^>]*>(.*)<\/body>/s', $html, $body);
            $pages[] = $body[1];
        }
        $html = '<html><head><meta charset="UTF-8">' . $styles . '</head><body>'
            . implode('<div style="page-break-before:always"></div>', $pages) . '</body></html>';
        $html = str_replace('</body>', view('print.edit-controls')->render() . '</body>', $html);
        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function freshQuarterlyReportData(QuarterlyReport $report): array
    {
        $reportData = $report->report_data ?? [];
        $reportData['is_bcpc_report'] = $this->normalizeReportType($report->type ?? null) === 'BCPC';
        $caseRows = collect($reportData['cases'] ?? []);
        $caseIds = $caseRows->pluck('id')->filter()->unique()->values();

        if ($caseIds->isNotEmpty()) {
            $currentStatuses = CaseModel::withTrashed()->whereIn('id', $caseIds)->pluck('status', 'id');
            $reportData['cases'] = $caseRows->map(function ($caseRow) use ($currentStatuses) {
                $caseRow = (array) $caseRow;
                if (isset($caseRow['id']) && $currentStatuses->has($caseRow['id'])) {
                    $caseRow['status'] = $currentStatuses->get($caseRow['id']);
                }
                return $caseRow;
            })->values()->all();
        }

        return $reportData;
    }

    private function quarterlyReportStatuses($reports): array
    {
        $normalize = fn ($status) => strcasecmp(trim((string) $status), 'Case closed') === 0
            ? 'Case Closed'
            : trim((string) $status);

        return collect(['Case Filed', 'Case Referred', 'Case Closed'])
            ->merge(collect($reports)->flatMap(fn ($report) => collect($report->report_data['cases'] ?? [])
                ->pluck('status')->filter()->map($normalize)))
            ->unique()->values()->all();
    }

    private function normalizeReportType(?string $type): string
    {
        return strtolower((string) $type) === 'bcpc' ? 'BCPC' : 'VAWC';
    }

    private function requestedReportType(?string $requested = null): string
    {
        $user = Auth::user();
        $reportType = $this->normalizeReportType($requested ?? request('type', $user?->defaultCaseDepartment() ?? 'VAWC'));

        abort_unless($user && $user->caseDepartments(), 403);

        $defaultDepartment = $user->defaultCaseDepartment();

        if (!$user->isMultiCaseDepartment()) {
            if ($requested !== null || request()->has('type')) {
                abort_unless($reportType === $defaultDepartment, 403);
            }

            return $defaultDepartment;
        }

        abort_unless($user->canAccessCaseDepartment($reportType), 403);

        return $reportType;
    }

    private function applyReportTypeScope($query, ?string $type = null)
    {
        $user = Auth::user();

        if ($type) {
            $normalized = $this->normalizeReportType($type);

            if (!$user || !$user->canAccessCaseDepartment($normalized)) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('type', $normalized);
        }

        if ($user) {
            return $query->whereIn('type', $user->caseDepartments());
        }

        return $query;
    }

    /**
     * Display a listing of quarterly reports.
     * Desk Officer sees their own reports, Admin sees all.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = strtolower($user->position) === 'administrator';
        $barangays = $isAdmin ? Barangay::orderBy('name')->get() : collect();

        if ($isAdmin && $request->has('barangay_name')) {
            $request->validate(['barangay_name' => 'nullable|string|max:255']);
            $name = trim((string) $request->input('barangay_name'));
            if ($name !== '') {
                $matchingBarangayIds = $barangays
                    ->filter(fn ($barangay) => str_contains(mb_strtolower($barangay->name), mb_strtolower($name)))
                    ->pluck('id')
                    ->all();
                $request->merge(['barangay_ids' => $matchingBarangayIds]);
            }
        }
        
        $query = QuarterlyReport::with(['user', 'barangay'])
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('year')
            ->orderByDesc('quarter');
        if ($user->isDepartmentAdmin() || !$isAdmin) {
            $query->whereNull('archived_at');
        }
        
        if (!$isAdmin) {
            // Desk officers see reports from their barangay (shared among all officers)
            $barangay = Barangay::where('id', $user->barangay)
                ->orWhere('name', $user->barangay)
                ->first();
            if ($barangay) {
                $query->where('barangay_id', $barangay->id);
            } else {
                // Fallback: no barangay found, show nothing
                $query->where('barangay_id', 0);
            }
        }
        
        $this->applyReportTypeScope($query);
        if ($request->filled('type')) {
            $query->where('type', $this->requestedReportType($request->type));
        }
        
        // Filters
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('quarter')) {
            $query->where('quarter', $request->quarter);
        }
        if ($isAdmin && $request->has('barangay_ids')) {
            $query->whereIn('barangay_id', $request->input('barangay_ids'));
        } elseif ($isAdmin && $request->filled('barangay_id')) {
            $query->where('barangay_id', $request->barangay_id);
        }
        
        $isMultiCaseDepartment = $user ? $user->isMultiCaseDepartment() : false;
        
        if (!$isAdmin && $isMultiCaseDepartment) {
            $reports = $query->get();
        } else {
            $reports = $query->paginate($isAdmin && $user->isDepartmentAdmin() && $request->boolean('portrait_cards') ? 4 : 5)->withQueryString();
        }
        
        // Count archived reports for the badge
        $archivedCount = $user->isBcpcAdmin()
            ? QuarterlyReport::where('type', 'BCPC')->whereNotNull('archived_at')->count()
                + \App\Models\Wcpu\BcpcCaseMonitoring::whereNotNull('archived_at')->count()
            : 0;
        
        $view = $isAdmin 
            ? 'admin-tailwind.quarterly-reports.index' 
            : 'desk-officer.quarterly-reports.index';
        
        $monitorings = null;
        if ($isAdmin && $user->canAccessCaseDepartment('BCPC')) {
            $monitoringQuery = \App\Models\Wcpu\BcpcCaseMonitoring::with(['barangay', 'creator'])
                ->when($user->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin($user->id))
                ->whereNull('archived_at');
            foreach (['barangay_id', 'quarter', 'year'] as $filter) {
                if ($request->filled($filter)) {
                    $monitoringQuery->where($filter, $request->input($filter));
                }
            }
            $monitorings = $monitoringQuery->orderByDesc('year')->orderByDesc('quarter')
                ->paginate(10, ['*'], 'monitoring_page')->withQueryString();
        }

        if (!$isAdmin) {
            $reportGroups = [];
            foreach ($user->caseDepartments() as $department) {
                $reportGroups[$department] = (clone $query)->where('type', $department)->paginate(5, ['*'], strtolower($department).'_page')->withQueryString();
            }
            return view('desk-officer.quarterly-reports.index', compact('reportGroups', 'isMultiCaseDepartment'));
        }
        $activeBcpcReportPeriods = $user->isBcpcAdmin()
            ? QuarterlyReport::where('type', 'BCPC')->whereNull('archived_at')
                ->get(['barangay_id', 'quarter', 'year'])
                ->map(fn ($report) => "{$report->barangay_id}-{$report->quarter}-{$report->year}")
                ->values()
                ->all()
            : [];
        return view($view, compact('reports', 'barangays', 'isMultiCaseDepartment', 'archivedCount', 'monitorings', 'activeBcpcReportPeriods'));
    }

    /**
     * Show the form for creating a new quarterly report (Desk Officer).
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get barangay for the desk officer
        $barangay = Barangay::where('id', $user->barangay)
            ->orWhere('name', $user->barangay)
            ->first();
        
        if (!$barangay) {
            return redirect()->route('desk-officer.quarterly-reports.index')
                ->with('error', 'Barangay not found for your account.');
        }

        // If user has only one role, proceed directly with their role's report
        if (!$user->isMultiCaseDepartment()) {
            $requestedType = $this->normalizeReportType($user->defaultCaseDepartment());
        } else {
            // If user has both roles and didn't specify type, redirect to index with popup
            if (!request()->filled('type')) {
                return redirect()->route('desk-officer.quarterly-reports.index', ['select_type' => 1]);
            }
            $requestedType = $this->requestedReportType(request('type'));
        }

        $reportsQuery = QuarterlyReport::where('barangay_id', $barangay->id)
            ->where('type', $requestedType)
            ->whereNull('archived_at');

        // Get existing reports to show which quarters are already submitted
        $existingReports = $reportsQuery
            ->select('quarter', 'year')
            ->get()
            ->map(fn($r) => $r->quarter . '-' . $r->year)
            ->toArray();
        
        $currentYear = now()->year;
        $years = range($currentYear - 2, $currentYear + 1);
        
        return view('desk-officer.quarterly-reports.create', compact('barangay', 'existingReports', 'years', 'requestedType'));
    }

    /**
     * Generate and store a new quarterly report.
     */
    public function store(Request $request)
    {
        $request->validate([
            'quarter' => 'required|integer|min:1|max:4',
            'year' => 'required|integer|min:2020|max:2100',
        ]);
        
        $user = Auth::user();
        
        // Get barangay
        $isAdmin = strtolower($user->position) === 'administrator';
        if ($isAdmin) {
            $request->validate(['barangay_id' => ['required', Rule::exists(Barangay::class, 'id')], 'type' => 'required|in:VAWC,BCPC']);
            $barangay = Barangay::findOrFail($request->barangay_id);
        } else {
            $barangay = Barangay::where('id', $user->barangay)
                ->orWhere('name', $user->barangay)->first();
        }
        
        if (!$barangay) {
            return back()->with('error', 'Barangay not found for your account.');
        }
        
        // Calculate quarter date range
        $quarter = $request->quarter;
        $year = $request->year;
        $startMonth = ($quarter - 1) * 3 + 1;
        $startDate = Carbon::create($year, $startMonth, 1)->startOfDay();
        $endDate = Carbon::create($year, $startMonth + 2, 1)->endOfMonth()->endOfDay();
        
        // Get all case types for mapping RA numbers to descriptive names
        $caseTypes = CaseType::all()->pluck('name', 'republic_act')->toArray();
        
        // Determine which report types to generate based on user's case departments or requested type
        if ($request->filled('type')) {
            $requestedType = $this->requestedReportType($request->type);
            $reportTypes = [$requestedType];
        } else {
            $reportTypes = $user->caseDepartments();
        }

        if ($isAdmin && $user->isDepartmentAdmin() && QuarterlyReport::where('type', $reportTypes[0])
            ->where('barangay_id', $barangay->id)
            ->where('quarter', $quarter)
            ->where('year', $year)
            ->whereNull('archived_at')
            ->exists()) {
            return redirect()->route('admin.quarterly-reports.index')
                ->with('duplicate_quarterly_report', true);
        }
        
        $generatedCount = 0;
        $skippedCount = 0;
        $noDataCount = 0;
        
        foreach ($reportTypes as $reportType) {
            // Check for existing report of this type
            $existing = QuarterlyReport::where('barangay_id', $barangay->id)
                ->where('type', $reportType)
                ->where('quarter', $quarter)
                ->where('year', $year)
                ->whereNull('archived_at')
                ->exists();
            
            if ($existing) {
                $skippedCount++;
                continue;
            }

            // Get all actual cases for this barangay in this quarter (filtered by type)
            $cases = CaseModel::where('case_designation', $barangay->id)
                ->where('type', $reportType)
            ->whereBetween('date_reported', [$startDate, $endDate])
            ->orderBy('id')
            ->get();

            if ($cases->isEmpty()) {
                $noDataCount++;
                continue;
            }

            $reportData = $this->generateReportData($cases, $caseTypes, $reportType);
            
            // Generate PDF
            $pdfData = [
                'barangay' => $barangay,
                'quarter' => $quarter,
                'year' => $year,
                'period' => "Q{$quarter} {$year}",
                'startDate' => $startDate,
                'endDate' => $endDate,
                'reportData' => $reportData,
                'caseTypes' => $caseTypes,
                'generatedAt' => now()->format('F j, Y'),
                'user' => $user,
            ];
            
            $pdf = Pdf::loadView('pdf.quarterly-report', $pdfData)
                ->setPaper('legal', 'landscape')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                ]);
            
            // Save PDF
            $filename = "quarterly_reports/{$barangay->id}/{$reportType}_Q{$quarter}_{$year}.pdf";
            Storage::disk('local')->put($filename, $pdf->output());
            
            // Create report record
            QuarterlyReport::create([
                'user_id' => $user->id,
                'barangay_id' => $barangay->id,
                'type' => $reportType,
                'quarter' => $quarter,
                'year' => $year,
                'report_data' => $reportData,
                'pdf_path' => $filename,
            ]);
            
            $generatedCount++;
        }
        
        if ($generatedCount === 0 && $noDataCount > 0) {
            if ($request->expectsJson() && $user->position === 'Desk Officer' && (in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) || $user->isMultiCaseDepartment())) {
                return response()->json(['success' => false, 'message' => 'No case data is available for the selected report period.'], 422);
            }

            if ($isAdmin && $user->isDepartmentAdmin()) {
                return redirect()->route('admin.quarterly-reports.index')
                    ->with('no_cases_for_quarterly_report', "Q{$quarter} {$year}");
            }

            return back()->with('error', 'No case data is available for the selected report period.');
        }

        if ($generatedCount === 0 && $skippedCount > 0) {
            if ($request->expectsJson() && $user->position === 'Desk Officer' && (in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) || $user->isMultiCaseDepartment())) {
                return response()->json(['success' => false, 'message' => 'Reports for this quarter already exist.'], 422);
            }
            return back()->with('error', 'Reports for this quarter already exist.');
        }

        if ($request->expectsJson() && $user->position === 'Desk Officer' && (in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) || $user->isMultiCaseDepartment())) {
            return response()->json([
                'success' => $generatedCount > 0,
                'message' => $generatedCount > 0 ? 'Quarterly report generated successfully.' : 'No report was generated.',
                'redirect_url' => route('desk-officer.quarterly-reports.index'),
            ]);
        }
        
        $response = redirect()
            ->route($isAdmin ? 'admin.quarterly-reports.index' : 'desk-officer.quarterly-reports.index')
            ->with('quarterly_report_completed', $generatedCount > 0)
            ->with('success', "Generated {$generatedCount} report(s) for Q{$quarter} {$year}." . ($skippedCount > 0 ? " ({$skippedCount} already existed)" : ""));

        if ($isAdmin && $user->isBcpcAdmin()) {
            return $response->with('generated_bcpc_quarterly_report', true);
        }

        if ($isAdmin && $user->isVawcAdmin()) {
            return $response->with('generated_vawc_quarterly_report', true);
        }

        return $response;
    }

    /**
     * Generate report data from actual cases only.
     */
    private function generateReportData($cases, $caseTypes, string $reportType)
    {
        $isBCPCReport = $reportType === 'BCPC';

        // Count actual cases per RA-based abuse type
        $abuseTypeCounts = [];
        foreach ($cases as $case) {
            $ra = $caseTypes[$case->abuse_type] ?? $case->abuse_type ?? 'Unknown';
            $abuseTypeCounts[$ra] = ($abuseTypeCounts[$ra] ?? 0) + 1;
        }
        
        // Count actual cases per status
        $statusCounts = [];
        foreach ($cases as $case) {
            $status = $case->status ?? 'Unknown';
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        // Only actual case records are included in the Quarterly Report
        $totalCases = $cases->count();

        return [
            'total_cases' => $totalCases,
            'abuse_type_counts' => $abuseTypeCounts,
            'status_counts' => $statusCounts,
            'cases' => $cases->map(fn($c) => [
                'id' => $c->id,
                'case_number' => $c->case_number,
                'victim_name' => $c->victim_name,
                'victim_age' => $c->victim_age,
                'victim_gender' => $c->victim_gender,
                'abuse_type' => $c->abuse_type,
                'abuse_type_name' => $caseTypes[$c->abuse_type] ?? $c->abuse_type,
                'status' => $c->status,
                'date_reported' => $c->date_reported?->format('M d, Y'),
            ])->toArray(),
            'is_bcpc_report' => $isBCPCReport,
        ];
    }

    /**
     * Calculate total BCPC cases from monitoring data.
     */
    public function calculateBcpcTotalCases(array $monitoringData): int
    {
        $total = 0;
        
        if (!empty($monitoringData['bcpc_crimes_against_children'])) {
            $table = $monitoringData['bcpc_crimes_against_children'];
            $tableSum = 0;
            if (isset($table['total']) && is_array($table['total'])) {
                foreach ($table['total'] as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $tableSum += $val;
                    }
                }
            }
            if ($tableSum === 0) {
                foreach ($table as $rowKey => $rowYears) {
                    if ($rowKey === 'total' || !is_array($rowYears)) continue;
                    foreach ($rowYears as $yearCol) {
                        if (is_array($yearCol)) {
                            $val = (int) ($yearCol['total'] ?? 0);
                            if ($val === 0) {
                                $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                            }
                            $tableSum += $val;
                        }
                    }
                }
            }
            $total += $tableSum;
        }

        if (!empty($monitoringData['bcpc_cicl_crimes'])) {
            $table = $monitoringData['bcpc_cicl_crimes'];
            $ciclSum = 0;
            foreach ($table as $rowKey => $rowYears) {
                if ($rowKey === 'total' || !is_array($rowYears)) continue;
                foreach ($rowYears as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $ciclSum += $val;
                    }
                }
            }
            $total += $ciclSum;
        }

        if (!empty($monitoringData['bcpc_children_at_risk'])) {
            $table = $monitoringData['bcpc_children_at_risk'];
            $carSum = 0;
            foreach ($table as $rowKey => $rowYears) {
                if ($rowKey === 'total' || !is_array($rowYears)) continue;
                foreach ($rowYears as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $carSum += $val;
                    }
                }
            }
            $total += $carSum;
        }

        if (!empty($monitoringData['bcpc_special_protection'])) {
            $table = $monitoringData['bcpc_special_protection'];
            $spSum = 0;
            if (isset($table['total']) && is_array($table['total'])) {
                foreach ($table['total'] as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $spSum += $val;
                    }
                }
            }
            if ($spSum === 0) {
                foreach ($table as $rowKey => $rowYears) {
                    if ($rowKey === 'total' || !is_array($rowYears)) continue;
                    foreach ($rowYears as $yearCol) {
                        if (is_array($yearCol)) {
                            $val = (int) ($yearCol['total'] ?? 0);
                            if ($val === 0) {
                                $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                            }
                            $spSum += $val;
                        }
                    }
                }
            }
            $total += $spSum;
        }

        if ($total === 0 && !empty($monitoringData['bcpc_cicl_status'])) {
            $table = $monitoringData['bcpc_cicl_status'];
            if (isset($table['total']) && is_array($table['total'])) {
                foreach ($table['total'] as $yearCol) {
                    if (is_array($yearCol)) {
                        $val = (int) ($yearCol['total'] ?? 0);
                        if ($val === 0) {
                            $val = (int) ($yearCol['male'] ?? 0) + (int) ($yearCol['female'] ?? 0);
                        }
                        $total += $val;
                    }
                }
            }
        }

        return $total;
    }

    /**
     * Display the specified report.
     */
    public function show(Request $request, $id)
    {
        $report = QuarterlyReport::with(['user', 'barangay'])->findOrFail($id);
        
        // Authorization check
        $user = Auth::user();
        $isAdmin = strtolower($user->position) === 'administrator';
        abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);
        
        if (!$isAdmin) {
            // Desk officers can view any report from their barangay
            $barangay = Barangay::where('id', $user->barangay)
                ->orWhere('name', $user->barangay)
                ->first();
            if (!$barangay || $report->barangay_id !== $barangay->id) {
                abort(403);
            }
            abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);
        } else {
            $barangay = $report->barangay ?? Barangay::find($report->barangay_id);
        }

        $allCaseRecords = collect($report->report_data['cases'] ?? [])->values();
        $perPage = $isAdmin && $request->boolean('archive_report_preview') ? max(1, $allCaseRecords->count()) : 5;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('case_records_page');
        $caseRecords = new \Illuminate\Pagination\LengthAwarePaginator(
            $allCaseRecords->forPage($currentPage, $perPage)->values(),
            $allCaseRecords->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'case_records_page', 'fragment' => 'case-records']
        );
        $caseRecords->appends($request->query());

        return view('desk-officer.quarterly-reports.show', compact('report', 'barangay', 'isAdmin', 'caseRecords'));
    }

    /**
     * Show the edit form / options for a quarterly report.
     */
    public function edit($id)
    {
        $report = QuarterlyReport::with('barangay')->findOrFail($id);
        $user = Auth::user();
        $isAdmin = strtolower($user->position) === 'administrator';
        abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);

        if (!$isAdmin) {
            $barangay = Barangay::where('id', $user->barangay)
                ->orWhere('name', $user->barangay)
                ->first();
            if (!$barangay || $report->barangay_id !== $barangay->id) {
                abort(403);
            }
            abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);
        } else {
            $barangay = $report->barangay;
        }

        return view('desk-officer.quarterly-reports.edit', compact('report', 'barangay'));
    }

    /**
     * Regenerate / update a quarterly report from current cases.
     */
    public function regenerate($id)
    {
        $report = QuarterlyReport::with('barangay')->findOrFail($id);
        $user = Auth::user();
        $isAdmin = strtolower($user->position) === 'administrator';
        abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);

        if (!$isAdmin) {
            $barangay = Barangay::where('id', $user->barangay)
                ->orWhere('name', $user->barangay)
                ->first();
            if (!$barangay || $report->barangay_id !== $barangay->id) {
                abort(403);
            }
            abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);
        } else {
            $barangay = $report->barangay;
        }

        $quarter = (int) $report->quarter;
        $year = (int) $report->year;
        $startMonth = ($quarter - 1) * 3 + 1;
        $startDate = Carbon::create($year, $startMonth, 1)->startOfDay();
        $endDate = Carbon::create($year, $startMonth + 2, 1)->endOfMonth()->endOfDay();

        $caseTypes = CaseType::all()->pluck('name', 'republic_act')->toArray();

        $cases = CaseModel::where('case_designation', $barangay->id)
            ->where('type', $report->type)
            ->whereBetween('date_reported', [$startDate, $endDate])
            ->orderBy('id')
            ->get();

        $reportData = $this->generateReportData($cases, $caseTypes, $report->type);

        $existingReportData = $report->report_data ?? [];
        foreach (['abuse_type_counts', 'status_counts'] as $key) {
            if (isset($existingReportData[$key]) && is_array($existingReportData[$key])) {
                ksort($existingReportData[$key]);
            }
            if (isset($reportData[$key]) && is_array($reportData[$key])) {
                ksort($reportData[$key]);
            }
        }
        $existingReportData['cases'] = collect($existingReportData['cases'] ?? [])->sortBy('id')->values()->all();
        $reportData['cases'] = collect($reportData['cases'] ?? [])->sortBy('id')->values()->all();

        if (($report->type === 'BCPC' || $user->isMultiCaseDepartment() || ($user->position === 'Desk Officer' && $user->defaultCaseDepartment() === 'VAWC' && !$user->isMultiCaseDepartment())) && $existingReportData == $reportData) {
            if (request()->expectsJson() && $user->position === 'Desk Officer' && ($user->defaultCaseDepartment() === 'BCPC' || $user->isMultiCaseDepartment())) {
                return response()->json(['success' => false, 'message' => 'Nothing to update. This quarterly report is already up to date.'], 422);
            }
            return back()->with('error', "No changes were found for Q{$quarter} {$year}. The quarterly report is already up to date.");
        }

        // Generate PDF
        $pdfData = [
            'barangay' => $barangay,
            'quarter' => $quarter,
            'year' => $year,
            'period' => "Q{$quarter} {$year}",
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportData' => $reportData,
            'caseTypes' => $caseTypes,
            'generatedAt' => now()->format('F j, Y'),
            'user' => $user,
        ];

        $pdf = Pdf::loadView('pdf.quarterly-report', $pdfData)
            ->setPaper('legal', 'landscape')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        $filename = "quarterly_reports/{$barangay->id}/{$report->type}_Q{$quarter}_{$year}.pdf";
        Storage::disk('local')->put($filename, $pdf->output());

        $report->report_data = $reportData;
        $report->pdf_path = $filename;
        $report->archived_at = null;
        $report->save();

        if (request()->expectsJson() && $user->position === 'Desk Officer' && ($user->defaultCaseDepartment() === 'BCPC' || $user->isMultiCaseDepartment())) {
            return response()->json(['success' => true, 'message' => 'Quarterly report regenerated successfully.', 'redirect_url' => route('desk-officer.quarterly-reports.index')]);
        }

        return redirect()->route($isAdmin ? 'admin.quarterly-reports.index' : 'desk-officer.quarterly-reports.index')
            ->with('quarterly_report_completed', true)
            ->with('success', "Quarterly report for Q{$quarter} {$year} ({$report->type}) has been successfully updated with latest cases.");
    }

    /**
     * Display the report in an editable print preview.
     */
    public function download(Request $request, $id)
    {
        $report = QuarterlyReport::with('barangay')->findOrFail($id);
        
        // Authorization check
        $user = Auth::user();
        $isAdmin = strtolower($user->position) === 'administrator';
        abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);
        
        if (!$isAdmin) {
            // Desk officers can download any report from their barangay
            $barangay = Barangay::where('id', $user->barangay)
                ->orWhere('name', $user->barangay)
                ->first();
            if (!$barangay || $report->barangay_id !== $barangay->id) {
                abort(403);
            }
            abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);
        }
        
        $printView = $user->isBcpcAdmin()
            ? 'pdf.quarterly-report-bcpc-admin'
            : 'pdf.quarterly-report';

        return view($printView, [
            'barangay' => $report->barangay,
            'quarter' => $report->quarter,
            'year' => $report->year,
            'period' => $report->period_label,
            'startDate' => $report->start_date,
            'endDate' => $report->end_date,
            'reportData' => $this->freshQuarterlyReportData($report),
            'caseTypes' => CaseType::all()->pluck('name', 'republic_act')->toArray(),
            'generatedAt' => $report->created_at->format('F j, Y'),
            'user' => \App\Models\Wcpu\User::withTrashed()->find($report->user_id),
        ]);
    }

    /**
     * Display the summary of all quarterly reports for a given quarter/year.
     */
    public function summary(Request $request)
    {
        $quarter = $request->input('quarter', ceil(now()->month / 3));
        $year = $request->input('year', now()->year);
        
        // Get all case types for column headers
        $caseTypes = CaseType::all()->pluck('name', 'republic_act')->toArray();
        // Get all submitted reports for this quarter/year
        $reportType = $this->requestedReportType();
        $reports = QuarterlyReport::with(['barangay', 'user'])->where('type', $reportType)
            ->when(Auth::user()->isDepartmentAdmin(), fn ($query) => $query->whereNull('archived_at'))
            ->where('quarter', $quarter)
            ->where('year', $year)
            ->orderBy('barangay_id')
            ->get();
        $reports = $reports->map(function ($report) {
            $report->report_data = $this->freshQuarterlyReportData($report);
            return $report;
        });
        $allStatuses = $this->quarterlyReportStatuses($reports);
        
        // Get all barangays to show compliance
        $allBarangays = Barangay::orderBy('name')->get();
        
        // Build per-barangay summary
        $barangaySummary = [];
        $grandTotals = [
            'total_cases' => 0,
            'abuse_types' => array_fill_keys(array_keys($caseTypes), 0),
            'statuses' => array_fill_keys($allStatuses, 0),
        ];
        
        foreach ($allBarangays as $brgy) {
            $report = $reports->firstWhere('barangay_id', $brgy->id);
            
            $row = [
                'barangay' => $brgy->name,
                'submitted' => $report !== null,
                'submitted_by' => $report ? ($report->user->first_name ?? '') . ' ' . ($report->user->last_name ?? '') : null,
                'submitted_at' => $report ? $report->created_at->format('M d, Y') : null,
                'total_cases' => 0,
                'abuse_types' => array_fill_keys(array_keys($caseTypes), 0),
                'statuses' => array_fill_keys($allStatuses, 0),
            ];
            
            if ($report && isset($report->report_data['cases'])) {
                $row['total_cases'] = $report->report_data['total_cases'] ?? 0;
                $grandTotals['total_cases'] += $row['total_cases'];
                
                foreach ($report->report_data['cases'] as $case) {
                    $abuseType = $case['abuse_type'] ?? '';
                    $status = strcasecmp(trim((string) ($case['status'] ?? '')), 'Case closed') === 0 ? 'Case Closed' : trim((string) ($case['status'] ?? ''));
                    
                    if (isset($row['abuse_types'][$abuseType])) {
                        $row['abuse_types'][$abuseType]++;
                        $grandTotals['abuse_types'][$abuseType]++;
                    }
                    if (isset($row['statuses'][$status])) {
                        $row['statuses'][$status]++;
                        $grandTotals['statuses'][$status]++;
                    }
                }
            }
            
            $barangaySummary[] = $row;
        }
        
        $submittedCount = $reports->count();
        $totalBarangays = $allBarangays->count();
        
        return view('admin-tailwind.quarterly-reports.summary', compact(
            'quarter', 'year', 'caseTypes', 'allStatuses',
            'barangaySummary', 'grandTotals',
            'submittedCount', 'totalBarangays', 'reportType'
        ));
    }

    /**
     * Display the summary in an editable print preview.
     */
    public function summaryPdf(Request $request)
    {
        $quarter = $request->input('quarter', ceil(now()->month / 3));
        $year = $request->input('year', now()->year);
        
        // Same aggregation logic as summary()
        $caseTypes = CaseType::all()->pluck('name', 'republic_act')->toArray();
        $reportType = $this->requestedReportType();
        $reports = QuarterlyReport::with(['barangay', 'user'])->where('type', $reportType)
            ->when(Auth::user()->isDepartmentAdmin(), fn ($query) => $query->whereNull('archived_at'))
            ->where('quarter', $quarter)
            ->where('year', $year)
            ->orderBy('barangay_id')
            ->get();
        $reports = $reports->map(function ($report) {
            $report->report_data = $this->freshQuarterlyReportData($report);
            return $report;
        });
        $allStatuses = $this->quarterlyReportStatuses($reports);
        
        $allBarangays = Barangay::orderBy('name')->get();
        
        $barangaySummary = [];
        $grandTotals = [
            'total_cases' => 0,
            'abuse_types' => array_fill_keys(array_keys($caseTypes), 0),
            'statuses' => array_fill_keys($allStatuses, 0),
        ];
        
        foreach ($allBarangays as $brgy) {
            $report = $reports->firstWhere('barangay_id', $brgy->id);
            
            $row = [
                'barangay' => $brgy->name,
                'submitted' => $report !== null,
                'total_cases' => 0,
                'abuse_types' => array_fill_keys(array_keys($caseTypes), 0),
                'statuses' => array_fill_keys($allStatuses, 0),
            ];
            
            if ($report && isset($report->report_data['cases'])) {
                $row['total_cases'] = $report->report_data['total_cases'] ?? 0;
                $grandTotals['total_cases'] += $row['total_cases'];
                
                foreach ($report->report_data['cases'] as $case) {
                    $abuseType = $case['abuse_type'] ?? '';
                    $status = strcasecmp(trim((string) ($case['status'] ?? '')), 'Case closed') === 0 ? 'Case Closed' : trim((string) ($case['status'] ?? ''));
                    
                    if (isset($row['abuse_types'][$abuseType])) {
                        $row['abuse_types'][$abuseType]++;
                        $grandTotals['abuse_types'][$abuseType]++;
                    }
                    if (isset($row['statuses'][$status])) {
                        $row['statuses'][$status]++;
                        $grandTotals['statuses'][$status]++;
                    }
                }
            }
            
            $barangaySummary[] = $row;
        }
        
        $submittedCount = $reports->count();
        $totalBarangays = $allBarangays->count();
        
        $startMonth = ($quarter - 1) * 3 + 1;
        $startDate = \Carbon\Carbon::create($year, $startMonth, 1)->startOfDay();
        $endDate = \Carbon\Carbon::create($year, $startMonth + 2, 1)->endOfMonth();
        
        return view('pdf.quarterly-summary', compact(
            'quarter', 'year', 'caseTypes', 'allStatuses',
            'barangaySummary', 'grandTotals',
            'submittedCount', 'totalBarangays',
            'startDate', 'endDate', 'reportType'
        ));
    }

    /**
     * Admin index - same as index but explicitly for admin layout.
     */
    public function adminIndex(Request $request)
    {
        return $this->index($request);
    }

    public function printAll(Request $request)
    {
        abort_unless($request->user()->isDepartmentAdmin(), 403);
        $reportType = $request->user()->isBcpcAdmin() ? 'BCPC' : 'VAWC';

        $reports = QuarterlyReport::with(['barangay', 'user'])
            ->where('type', $reportType)->whereNull('archived_at')
            ->when($request->filled('quarter'), fn ($query) => $query->where('quarter', $request->quarter))
            ->when($request->filled('year'), fn ($query) => $query->where('year', $request->year))
            ->when($request->filled('barangay_name'), function ($query) use ($request) {
                $query->whereHas('barangay', fn ($barangayQuery) => $barangayQuery->where('name', 'like', '%' . trim($request->barangay_name) . '%'));
            })
            ->orderByDesc('year')->orderByDesc('quarter')->orderBy('barangay_id')->get();

        return view('pdf.quarterly-reports-list', compact('reports', 'reportType'));
    }

    public function generateAdminCases(Request $request)
    {
        abort_unless($request->user()->isBcpcAdmin(), 403);
        $validated = $request->validate([
            'barangay_id' => ['required', Rule::exists(Barangay::class, 'id')],
            'quarter' => 'required|integer|between:1,4',
            'year' => 'required|integer|between:2020,2100',
        ]);
        $user = $request->user();
        $barangay = Barangay::findOrFail($validated['barangay_id']);
        $quarter = (int) $validated['quarter'];
        $year = (int) $validated['year'];

        if (QuarterlyReport::where('type', 'BCPC')
            ->where('quarter', $quarter)
            ->where('year', $year)
            ->whereNull('archived_at')
            ->exists()) {
            return redirect()->route('admin.quarterly-reports.index')
                ->with('duplicate_quarterly_report', true);
        }

        $startDate = Carbon::create($year, ($quarter - 1) * 3 + 1, 1)->startOfDay();
        $endDate = $startDate->copy()->addMonths(2)->endOfMonth()->endOfDay();
        $cases = CaseModel::where('case_designation', $barangay->id)->where('type', 'BCPC')
            ->whereBetween('date_reported', [$startDate, $endDate])->get();

        if ($cases->isEmpty()) {
            return redirect()->route('admin.quarterly-reports.index')
                ->with('no_cases_for_quarterly_report', "Q{$quarter} {$year}");
        }

        $caseTypes = CaseType::all()->pluck('name', 'republic_act')->toArray();
        $reportData = $this->generateReportData($cases, $caseTypes, 'BCPC');
        $period = "Q{$quarter} {$year}";
        $generatedAt = now()->format('F j, Y');
        $pdf = Pdf::loadView('pdf.quarterly-report', compact(
            'barangay', 'quarter', 'year', 'startDate', 'endDate', 'caseTypes', 'reportData', 'period', 'generatedAt', 'user'
        ))->setPaper('legal', 'landscape')->setOptions([
            'isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true, 'isFontSubsettingEnabled' => true,
        ]);

        $filename = "quarterly_reports/{$barangay->id}/BCPC_Q{$quarter}_{$year}.pdf";
        Storage::disk('local')->put($filename, $pdf->output());

        QuarterlyReport::create([
            'user_id' => $user->id,
            'barangay_id' => $barangay->id,
            'type' => 'BCPC',
            'quarter' => $quarter,
            'year' => $year,
            'report_data' => $reportData,
            'pdf_path' => $filename,
        ]);

        return redirect()->route('admin.quarterly-reports.index')
            ->with('success', "BCPC quarterly report for {$barangay->name}, Q{$quarter} {$year} saved successfully.")
            ->with('generated_bcpc_quarterly_report', true);
    }

    /**
     * Archive a quarterly report (soft delete).
     */
    public function archive($id)
    {
        $report = QuarterlyReport::findOrFail($id);
        
        $user = Auth::user();
        $isAdmin = strtolower($user->position) === 'administrator';
        abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);
        
        if (!$isAdmin) {
            $barangay = Barangay::where('id', $user->barangay)
                ->orWhere('name', $user->barangay)
                ->first();
            if (!$barangay || $report->barangay_id !== $barangay->id) {
                abort(403);
            }
            abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);
        }
        
        $quarter = $report->quarter;
        $year = $report->year;
        $type = $report->type ?? 'VAWC';
        
        if ($user->isDepartmentAdmin() || !$isAdmin) {
            $report->archived_at = now();
            $report->save();
        } else {
            $report->delete();
        }
        
        $reportLabel = $user->isVawcAdmin() ? 'SWD' : $type;
        $message = "Q{$quarter} {$year} ({$reportLabel}) report has been archived successfully.";
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        
        return back()->with('success', $message)
            ->with('archived_quarterly_report', $message);
    }

    /**
     * Legacy destroy route - archives the report rather than permanently deleting.
     */
    public function destroy($id)
    {
        return $this->archive($id);
    }

    /**
     * Display a listing of archived quarterly reports.
     */
    public function archived(Request $request)
    {
        $archivePageSize = $request->user()->isDepartmentAdmin() && $request->boolean('portrait_cards') ? 4 : 5;
        $user = $request->user();
        if (strtolower($request->user()->position) === 'desk officer') {
            $user = $request->user();
            $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->firstOrFail();
            $reportGroups = [];
            $activeReportPeriods = [];
            $search = trim((string) $request->input('search'));
            $quarter = $request->input('quarter');
            $year = $request->input('year');
            $instantArchiveSearch = in_array($user->defaultCaseDepartment(), ['VAWC', 'BCPC'], true);
            foreach ($user->caseDepartments() as $department) {
                $q = QuarterlyReport::with(['barangay', 'user'])->where('barangay_id', $barangay->id)->where('type', $department)->whereNotNull('archived_at');
                if ($quarter && !$instantArchiveSearch) {
                    $q->where('quarter', $quarter);
                }
                if ($year && !$instantArchiveSearch) {
                    $q->where('year', $year);
                }
                if ($search !== '' && !$instantArchiveSearch) {
                    $searchQuarter = preg_match('/q([1-4])/i', $search, $m) ? (int)$m[1] : null;
                    $q->where(function ($sub) use ($search, $searchQuarter) {
                        if ($searchQuarter !== null) {
                            $sub->where('quarter', $searchQuarter);
                        } else {
                            $sub->where('quarter', 'like', "%{$search}%");
                        }
                        $sub->orWhere('year', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT('Q', quarter, ' ', year) LIKE ?", ["%{$search}%"])
                            ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"))
                            ->orWhereHas('barangay', fn($b) => $b->where('name', 'like', "%{$search}%"));
                    });
                }
                $reportGroups[$department] = $q->latest('archived_at')->paginate($instantArchiveSearch ? PHP_INT_MAX : 5, ['*'], strtolower($department).'_page', $instantArchiveSearch ? 1 : null)->withQueryString();
                $activeReportPeriods[$department] = QuarterlyReport::where('barangay_id', $barangay->id)
                    ->where('type', $department)
                    ->whereNull('archived_at')
                    ->get(['quarter', 'year'])
                    ->mapWithKeys(fn ($report) => ["{$report->quarter}-{$report->year}" => true])
                    ->all();
            }
            $archivedMonitorings = null;
            $activeMonitoringPeriods = [];
            if ($user->canAccessCaseDepartment('BCPC')) {
                $archivedMonitorings = BcpcCaseMonitoring::with(['barangay', 'creator'])
                    ->where('barangay_id', $barangay->id)->whereNotNull('archived_at')->latest('archived_at')->get();
                $activeMonitoringPeriods = BcpcCaseMonitoring::where('barangay_id', $barangay->id)
                    ->whereNull('archived_at')->get(['quarter', 'year'])
                    ->mapWithKeys(fn ($monitoring) => ["{$monitoring->quarter}-{$monitoring->year}" => true])->all();
            }
            return view('desk-officer.quarterly-reports.archived', compact('reportGroups', 'activeReportPeriods', 'quarter', 'year', 'search', 'archivedMonitorings', 'activeMonitoringPeriods'));
        }
        if ($user->isDepartmentAdmin() && $request->input('section') === 'quarterly') {
            $department = $user->isVawcAdmin() ? 'VAWC' : 'BCPC';
            $search = trim((string) $request->input('search'));
            $reportsQuery = QuarterlyReport::with(['barangay', 'user'])
                ->where('type', $department)
                ->whereNotNull('archived_at');

            foreach (['barangay_id', 'quarter', 'year'] as $filter) {
                if ($request->filled($filter)) {
                    $reportsQuery->where($filter, $request->input($filter));
                }
            }

            if ($search !== '') {
                $searchQuarter = preg_match('/q([1-4])/i', $search, $matches) ? (int) $matches[1] : null;
                $reportsQuery->where(function ($query) use ($search, $searchQuarter) {
                    if ($searchQuarter !== null) {
                        $query->where('quarter', $searchQuarter);
                    } else {
                        $query->where('quarter', 'like', "%{$search}%");
                    }

                    $query->orWhere('year', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT('Q', quarter, ' ', year) LIKE ?", ["%{$search}%"])
                        ->orWhereHas('barangay', fn ($barangay) => $barangay->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($reportUser) => $reportUser
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%"));
                });
            }

            $reports = $reportsQuery->latest('archived_at')->paginate($archivePageSize)->withQueryString();
            $barangays = Barangay::orderBy('name')->get();
            $activeReportPeriods = QuarterlyReport::where('type', $department)
                ->whereNull('archived_at')
                ->get(['barangay_id', 'quarter', 'year'])
                ->mapWithKeys(fn ($report) => [$report->barangay_id . '-' . $report->quarter . '-' . $report->year => true])
                ->all();

            return view('admin-tailwind.quarterly-reports.archived', compact('reports', 'barangays', 'activeReportPeriods'));
        }
        if ($request->user()->isVawcAdmin()) {
            $archivedCaseSearch = trim((string) $request->input('search'));
            $archivedCaseBarangayId = $request->input('barangay_id');
            $archivedCaseQuarter = $request->input('quarter');
            $archivedCaseYear = $request->input('year');
            $reports = QuarterlyReport::with(['barangay', 'user'])->where('type', 'VAWC')->whereNotNull('archived_at')
                ->orderByDesc('archived_at')->paginate($archivePageSize, ['*'], 'cases_page')->withQueryString();
            $monitorings = null;
            $barangayCases = \App\Models\Wcpu\CaseModel::with(['barangay'])
                ->withTrashed()
                ->where('type', 'VAWC')
                ->whereNotNull('deleted_at')
                ->when($archivedCaseBarangayId, fn ($query) => $query->where('case_designation', $archivedCaseBarangayId))
                ->when($archivedCaseQuarter, fn ($query) => $query->whereRaw('QUARTER(COALESCE(date_reported, incident_date, created_at)) = ?', [$archivedCaseQuarter]))
                ->when($archivedCaseYear, fn ($query) => $query->whereYear(\DB::raw('COALESCE(date_reported, incident_date, created_at)'), $archivedCaseYear))
                ->when($archivedCaseSearch !== '', function ($query) use ($archivedCaseSearch) {
                    $query->where(function ($searchQuery) use ($archivedCaseSearch) {
                        $searchQuery->where('case_number', 'like', "%{$archivedCaseSearch}%")
                            ->orWhere('victim_name', 'like', "%{$archivedCaseSearch}%")
                            ->orWhereHas('barangay', fn ($barangay) => $barangay->where('name', 'like', "%{$archivedCaseSearch}%"));
                    });
                })
                ->orderByDesc('deleted_at')
                ->paginate($archivePageSize, ['*'], 'barangay_cases_page')
                ->withQueryString();
            $activeReportPeriods = QuarterlyReport::where('type', 'VAWC')
                ->whereNull('archived_at')
                ->get(['quarter', 'year'])
                ->mapWithKeys(fn ($report) => ["{$report->quarter}-{$report->year}" => true])
                ->all();
            $barangays = Barangay::orderBy('name')->get();
            return view('admin-tailwind.quarterly-reports.bcpc-archived', compact('reports', 'monitorings', 'barangayCases', 'activeReportPeriods', 'barangays', 'archivedCaseSearch', 'archivedCaseBarangayId', 'archivedCaseQuarter', 'archivedCaseYear'));
        }
        if ($request->user()->isBcpcAdmin()) {
            $isArchivedBarangayCasesPage = $request->input('section') === 'barangay-cases';
            $archivedCaseSearch = trim((string) $request->input('search'));
            $archivedCaseBarangayId = $request->input('barangay_id');
            $archivedCaseQuarter = $request->input('quarter');
            $archivedCaseYear = $request->input('year');
            $reportsQuery = QuarterlyReport::with(['barangay', 'user'])->where('type', 'BCPC')->whereNotNull('archived_at');
            $monitoringQuery = \App\Models\Wcpu\BcpcCaseMonitoring::with(['barangay', 'creator'])->whereNotNull('archived_at')
                ->when($user->isBcpcAdmin(), fn ($query) => $query->visibleToAdmin($user->id));
            $barangayCasesQuery = \App\Models\Wcpu\CaseModel::with(['barangay'])
                ->where('type', 'BCPC')
                ->onlyTrashed();
            if ($isArchivedBarangayCasesPage) {
                $barangayCasesQuery
                    ->when($archivedCaseBarangayId, fn ($query) => $query->where('case_designation', $archivedCaseBarangayId))
                    ->when($archivedCaseQuarter, fn ($query) => $query->whereRaw('QUARTER(COALESCE(date_reported, incident_date, created_at)) = ?', [$archivedCaseQuarter]))
                    ->when($archivedCaseYear, fn ($query) => $query->whereYear(\DB::raw('COALESCE(date_reported, incident_date, created_at)'), $archivedCaseYear))
                    ->when($archivedCaseSearch !== '', function ($query) use ($archivedCaseSearch) {
                        $query->where(function ($searchQuery) use ($archivedCaseSearch) {
                            $searchQuery->where('case_number', 'like', "%{$archivedCaseSearch}%")
                                ->orWhere('victim_name', 'like', "%{$archivedCaseSearch}%")
                                ->orWhereHas('barangay', fn ($barangay) => $barangay->where('name', 'like', "%{$archivedCaseSearch}%"));
                        });
                    });
            }
            $barangayCases = $barangayCasesQuery
                ->latest('deleted_at')
                ->paginate($archivePageSize, ['*'], 'barangay_cases_page')
                ->withQueryString();
            $reports = $reportsQuery->orderByDesc('archived_at')->paginate($archivePageSize, ['*'], 'cases_page')->withQueryString();
            $monitorings = $monitoringQuery->orderByDesc('archived_at')->paginate($archivePageSize, ['*'], 'monitoring_page')->withQueryString();
            $activeReportPeriods = QuarterlyReport::where('type', 'BCPC')
                ->whereNull('archived_at')
                ->get(['quarter', 'year'])
                ->mapWithKeys(fn ($report) => ["{$report->quarter}-{$report->year}" => true])
                ->all();
            $activeMonitoringPeriods = \App\Models\Wcpu\BcpcCaseMonitoring::whereNull('archived_at')
                ->get(['quarter', 'year'])
                ->mapWithKeys(fn ($monitoring) => ["{$monitoring->quarter}-{$monitoring->year}" => true])
                ->all();
            $barangays = Barangay::orderBy('name')->get();
            return view('admin-tailwind.quarterly-reports.bcpc-archived', compact('reports', 'monitorings', 'barangayCases', 'activeReportPeriods', 'activeMonitoringPeriods', 'barangays', 'archivedCaseSearch', 'archivedCaseBarangayId', 'archivedCaseQuarter', 'archivedCaseYear'));
        }
        // Soft deletes not enabled - return empty collection
        $reports = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $barangays = collect();
        $isMultiCaseDepartment = false;
        
        $view = 'admin-tailwind.quarterly-reports.archived';
        
        return view($view, compact('reports', 'barangays', 'isMultiCaseDepartment'));
    }

    /**
     * Admin view for archived reports.
     */
    public function adminArchived(Request $request)
    {
        return $this->archived($request);
    }

    /**
     * Restore an archived quarterly report.
     */
    public function restore($id)
    {
        if (strtolower(Auth::user()->position) === 'desk officer') {
            $user = Auth::user();
            $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->firstOrFail();
            $report = QuarterlyReport::where('barangay_id', $barangay->id)->whereIn('type', $user->caseDepartments())->whereNotNull('archived_at')->findOrFail($id);
            $activeMatchExists = QuarterlyReport::where('barangay_id', $barangay->id)
                ->where('type', $report->type)
                ->where('quarter', $report->quarter)
                ->where('year', $report->year)
                ->whereNull('archived_at')
                ->exists();
            if ($activeMatchExists) {
                if (request()->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'An active quarterly report already exists for this quarter and year.'], 422);
                }
                return back()->with('error', 'An active quarterly report already exists for this quarter and year.');
            }
            $report->archived_at = null;
            $report->save();
            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Quarterly case report restored.']);
            }
            return redirect()->route(
                ($user->defaultCaseDepartment() === 'VAWC' || $user->isMultiCaseDepartment())
                    ? 'desk-officer.quarterly-reports.archived'
                    : 'desk-officer.quarterly-reports.index'
            )->with('success', 'Quarterly case report restored.');
        }
        if (Auth::user()->isDepartmentAdmin()) {
            $report = QuarterlyReport::where('type', Auth::user()->isVawcAdmin() ? 'VAWC' : 'BCPC')->whereNotNull('archived_at')->findOrFail($id);

            if (QuarterlyReport::where('type', $report->type)
                ->where('barangay_id', $report->barangay_id)
                ->where('quarter', $report->quarter)
                ->where('year', $report->year)
                ->whereNull('archived_at')
                ->exists()) {
                return back()->with('error', 'An active quarterly report already exists for this quarter and year.');
            }

            $report->archived_at = null;
            $report->save();
            return redirect()->route('admin.quarterly-reports.archived', ['section' => 'quarterly'])
                ->with('restored_report', 'Quarterly report restored successfully.');
        }
        // Soft deletes not enabled - return error
        if (request()->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Restore functionality not available'], 422);
        }
        return back()->with('error', 'Restore functionality not available');
    }

    /**
     * Permanently delete an archived quarterly report and its PDF file.
     */
    public function forceDelete($id)
    {
        $user = Auth::user();
        $isDeskOfficer = strtolower((string) $user->position) === 'desk officer'
            && in_array($user->defaultCaseDepartment(), ['BCPC', 'VAWC'], true);
        abort_unless($user->isDepartmentAdmin() || $isDeskOfficer, 403);
        $reportQuery = QuarterlyReport::whereNotNull('archived_at');
        if ($isDeskOfficer) {
            $barangay = Barangay::where('name', $user->barangay)->orWhere('id', $user->barangay)->firstOrFail();
            $reportQuery->where('barangay_id', $barangay->id);
            if ($user->isMultiCaseDepartment()) {
                $reportQuery->whereIn('type', $user->caseDepartments());
            } else {
                $reportQuery->where('type', $user->defaultCaseDepartment());
            }
        }
        $report = $reportQuery->findOrFail($id);
        abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);

        if ($report->pdf_path) {
            Storage::disk('local')->delete($report->pdf_path);
        }
        $report->delete();

        $message = 'Archived quarterly report deleted successfully.';
        return request()->wantsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : back()->with('success', $message);
    }

    public function sendToAdmin(Request $request, $id)
    {
        $user = Auth::user();
        $barangay = Barangay::where('id', $user->barangay)
            ->orWhere('name', $user->barangay)
            ->first();
        abort_unless($barangay, 404, 'Barangay not found for your account.');

        $report = QuarterlyReport::where('barangay_id', $barangay->id)->findOrFail($id);
        abort_if(strtoupper((string) $report->type) === 'BCPC', 403, 'BCPC case reports do not require submission.');
        abort_unless($user->canAccessCaseDepartment($report->type ?? 'VAWC'), 403);

        $report->touch();

        \Log::info("Quarterly Report ({$report->type}) for {$barangay->name} (Q{$report->quarter} {$report->year}) sent to Admin by " . ($user->email ?? $user->id));

        $message = "Quarterly Report ({$report->type}) for {$barangay->name} (Q{$report->quarter} {$report->year}) has been sent to Admin successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Get BCPC monitoring table structure.
     */
    public function getBcpcMonitoringTables(): array
    {
        return [
            [
                'title' => 'Crimes Committed Against Children',
                'note' => 'What types of crime have been committed against children in the past 2 years?',
                'name' => 'bcpc_crimes_against_children',
                'label' => 'Crimes Against Children',
                'rows' => [
                    'rape' => 'Rape',
                    'incestuous_rape' => 'Incestuous Rape',
                    'attempted_frustrated_rape' => 'Attempted/Frustrated Rape',
                    'acts_of_lasciviousness' => 'Acts of Lasciviousness',
                    'physical_injuries' => 'Physical Injuries',
                    'kidnapping_illegal_detention' => 'Kidnapping/Illegal Detention',
                    'trafficking' => 'RA 9208 amended by RA 10634 (Trafficking in persons)',
                    'drugs_substance_abuse' => 'Drugs/Substance Abuse',
                    'others' => 'Others, specify',
                    'total' => 'TOTAL'
                ]
            ],
            [
                'title' => 'Crimes Committed by Children in Conflict with the Law (CICL)',
                'note' => 'What types of crime have been committed by children in conflict with the law (CICL) in the past 2 years?',
                'name' => 'bcpc_cicl_crimes',
                'label' => 'Crimes Against Children',
                'rows' => [
                    'rape' => 'Rape',
                    'incestuous_rape' => 'Incestuous Rape',
                    'attempted_frustrated_rape' => 'Attempted/Frustrated Rape',
                    'acts_of_lasciviousness' => 'Acts of Lasciviousness',
                    'physical_injuries' => 'Physical Injuries'
                ]
            ],
            [
                'title' => 'Children in Conflict with the Law (CICL) and Status',
                'note' => 'Summary: Number of Children in Conflict with the Law (CICL) and their status. Choose one category and avoid double counting.',
                'name' => 'bcpc_cicl_status',
                'label' => 'Categories of CICL',
                'rows' => [
                    'diversion_program' => 'Number of CICL undergoing Diversion Program',
                    'jail_detention_center' => 'Number of CICL in Jail/Detention Center',
                    'bahay_pagasa' => 'Number of CICL in Bahay Pag-asa',
                    'ijisc' => 'Number of CICL under Intensive Juvenile Intervention and Support for Center (IJISC)',
                    'rrcy' => 'Number of CICL in RRCY',
                    'after_care_program' => 'Number of CICL undergoing After-Care Program',
                    're_offended' => 'Number of CICL that have re-offended',
                    'fully_integrated' => 'Number of CICL that have been fully integrated',
                    'community_based' => 'Number of CICL undergoing community-based',
                    'cbdr' => 'Number of CICL undergoing Community-Based Rehabilitation Program (CBDR)',
                    'others' => 'Others, please specify:',
                    'total' => 'Total'
                ]
            ],
            [
                'title' => 'Children at Risk',
                'note' => 'Number of Children at Risk as defined in RA 9344 as amended. Choose only one category for the child and avoid double counting.',
                'name' => 'bcpc_children_at_risk',
                'label' => 'Categories of CAR',
                'rows' => [
                    'curfew_hour' => 'Curfew Hour',
                    'truancy' => 'Truancy',
                    'parental_disobedience' => 'Parental Disobedience',
                    'anti_smoking' => 'Anti-Smoking',
                    'anti_drinking' => 'Anti-Drinking',
                    'disorderly_conduct' => 'Disorderly conduct',
                    'public_scandal' => 'Public scandal',
                    'harassment' => 'Harassment',
                    'public_intoxication' => 'Public intoxication',
                    'criminal_nuisance' => 'Criminal nuisance',
                    'vandalism' => 'Vandalism',
                    'gambling' => 'Gambling',
                    'mendicancy' => 'Mendicancy (Presidential Decree No. 1563)'
                ]
            ],
            [
                'title' => 'Children Needing Special Protection',
                'note' => 'Children needing special protection and other child protection categories.',
                'name' => 'bcpc_special_protection',
                'label' => 'Category',
                'rows' => [
                    'parents_lost_jobs_covid' => 'Children whose parents lost jobs due to COVID-19',
                    'children_with_disabilities' => 'Children with disabilities',
                    'indigenous_families' => 'Children of indigenous families',
                    'four_ps_families' => '4Ps families',
                    'child_surrenderers_drug_campaign' => 'Child-surrenderers in relation to the Philippine Campaign Against Illegal Drugs',
                    'total' => 'TOTAL'
                ]
            ],
        ];
    }
}
