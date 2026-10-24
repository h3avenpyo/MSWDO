<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\Barangay;
use App\Models\Wcpu\BcpcCaseMonitoring;
use App\Mail\Wcpu\PortalActionNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BcpcCaseMonitoringController extends Controller
{
    public function edit(Request $request)
    {
        $this->ensureBcpcAccess();
        $barangay = $this->barangay();
        abort_unless($barangay, 404, 'Barangay not found for your account.');

        $viewId = $request->input('view');
        $editId = $request->input('edit');
        $fromArchived = $request->boolean('archived');
        $add = $request->input('add');
        $quarter = $request->input('quarter');
        $year = $request->input('year');

        // If viewing or editing a specific monitoring record
        if ($viewId || $editId) {
            $monitoring = BcpcCaseMonitoring::with('barangay')
                ->where('barangay_id', $barangay->id)
                ->findOrFail($viewId ?? $editId);
            $quarter = $monitoring->quarter;
            $year = $monitoring->year;
            $isView = $viewId !== null;

            return view('desk-officer.bcpc-case-monitoring-form', compact(
                'barangay',
                'quarter',
                'year',
                'monitoring',
                'isView',
                'fromArchived'
            ));
        }

        // If adding a new monitoring record
        if ($add) {
            if (!$quarter || !$year) {
                return redirect()->route('desk-officer.bcpc-case-monitoring.edit')
                    ->with('error', 'Please select quarter and year to add monitoring.');
            }

            if (BcpcCaseMonitoring::where('barangay_id', $barangay->id)
                ->where('quarter', $quarter)
                ->where('year', $year)
                ->whereNull('archived_at')
                ->exists()) {
                return redirect()->route('desk-officer.bcpc-case-monitoring.edit')
                    ->with('error', 'A BCPC monitoring report already exists for the selected quarter and year.');
            }

            $monitoring = new BcpcCaseMonitoring([
                'barangay_id' => $barangay->id,
                'quarter' => $quarter,
                'year' => $year,
            ]);

            $isView = false;

            return view('desk-officer.bcpc-case-monitoring-form', compact(
                'barangay',
                'quarter',
                'year',
                'monitoring',
                'isView'
            ));
        }

        // Get all submitted monitoring records for this barangay
        $query = BcpcCaseMonitoring::with('barangay', 'creator', 'updater')
            ->where('barangay_id', $barangay->id)->whereNull('archived_at');

        if ($quarter) {
            $query->where('quarter', $quarter);
        }
        if ($year) {
            $query->where('year', $year);
        }

        $monitorings = $query->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('year')
            ->orderByDesc('quarter')
            ->paginate(4)->withQueryString();

        $archivedCount = 0;

        if (Auth::user()->defaultCaseDepartment() === 'BCPC' && !Auth::user()->isMultiCaseDepartment()
            && $monitorings->currentPage() > $monitorings->lastPage()) {
            return redirect()->route('desk-officer.bcpc-case-monitoring.edit', array_merge($request->query(), ['page' => $monitorings->lastPage()]));
        }
        $existingMonitoringPeriods = BcpcCaseMonitoring::where('barangay_id', $barangay->id)
            ->whereNull('archived_at')->get(['quarter', 'year'])
            ->map(fn ($record) => $record->quarter . '-' . $record->year)->values();

        return view('desk-officer.bcpc-case-monitoring', compact(
            'barangay',
            'quarter',
            'year',
            'monitorings',
            'archivedCount',
            'existingMonitoringPeriods'
        ));
    }

    public function print(Request $request)
    {
        $this->ensureBcpcAccess();
        $barangay = $this->barangay();
        abort_unless($barangay, 404, 'Barangay not found for your account.');

        $quarter = $request->input('quarter');
        $year = $request->input('year');

        $monitoring = BcpcCaseMonitoring::where([
            'barangay_id' => $barangay->id,
            'quarter' => $quarter,
            'year' => $year,
        ])->first();

        $data = $monitoring->data ?? [];
        $yearGroups = ['year_2023' => 'Year 2023', 'year_2024' => 'Year 2024', 'total_2025' => 'Total 2025'];
        $sexColumns = ['male' => 'Male', 'female' => 'Female', 'total' => 'Total'];
        $tables = [
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

        return view('pdf.bcpc-case-monitoring', compact(
            'barangay',
            'quarter',
            'year',
            'tables',
            'data',
            'yearGroups',
            'sexColumns'
        ));
    }

    public function update(Request $request)
    {
        $this->ensureBcpcAccess();
        $data = $request->validate([
            'quarter' => 'required|integer|between:1,4',
            'year' => 'required|integer|between:2020,2100',
            'bcpc_crimes_against_children' => 'nullable|array',
            'bcpc_crimes_against_children.*.*.*' => 'nullable|integer|min:0',
            'bcpc_cicl_crimes' => 'nullable|array',
            'bcpc_cicl_crimes.*.*.*' => 'nullable|integer|min:0',
            'bcpc_cicl_status' => 'nullable|array',
            'bcpc_cicl_status.*.*.*' => 'nullable|integer|min:0',
            'bcpc_children_at_risk' => 'nullable|array',
            'bcpc_children_at_risk.*.*.*' => 'nullable|integer|min:0',
            'bcpc_special_protection' => 'nullable|array',
            'bcpc_special_protection.*.*.*' => 'nullable|integer|min:0',
            'bcpc_organizations' => 'nullable|array',
            'bcpc_organizations.*.name' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} ]+$/u"],
            'bcpc_organizations.*.responsible_person' => ['nullable', 'string', 'max:255', "regex:/^[\\p{L}\\p{M} ]+$/u"],
            'bcpc_organizations.*.phone' => ['nullable', 'regex:/^[0-9]+$/', new \App\Rules\ContactNumber(null)],
            'send_to_admin' => 'nullable|boolean',
        ], [
            'bcpc_organizations.*.name.regex' => 'The organization name may contain only letters and spaces.',
            'bcpc_organizations.*.responsible_person.regex' => 'The responsible person and designation may contain only letters and spaces.',
            'bcpc_organizations.*.phone.regex' => 'The phone or mobile number may contain numbers only.',
        ]);
        $barangay = $this->barangay();
        abort_unless($barangay, 404, 'Barangay not found for your account.');

        $monitoringData = collect($data)
            ->except(['quarter', 'year', 'send_to_admin'])
            ->map(fn ($value) => $value ?? [])
            ->all();

        if ($this->calculateBcpcTotalCases($monitoringData) === 0) {
            return back()->with('error', 'No monitoring data is available. Enter case data before saving a monitoring report.');
        }

        $monitoring = BcpcCaseMonitoring::whereNull('archived_at')->firstOrNew([
            'barangay_id' => $barangay->id,
            'quarter' => (int) $data['quarter'],
            'year' => (int) $data['year'],
        ]);
        if (!$monitoring->exists) {
            $monitoring->created_by = Auth::id();
        }
        $monitoring->updated_by = Auth::id();
        $monitoring->data = $monitoringData;
        $monitoring->archived_at = null;
        if ($request->boolean('send_to_admin')) {
            $monitoring->submitted_at = now();
            $monitoring->submitted_by = Auth::id();
        }
        $monitoring->save();

        $sent = $request->boolean('send_to_admin');
        if (($request->wantsJson() || $request->ajax()) && !$sent) {
            return response()->json([
                'success' => true,
                'monitoring_id' => $monitoring->id,
                'message' => 'BCPC case monitoring saved successfully.',
            ]);
        }

        if ($sent) {
            $this->notifyBcpcAdmin($monitoring, $barangay);
        }
        $msg = $sent
            ? "BCPC case monitoring (Q{$data['quarter']} {$data['year']}) saved and sent to Admin successfully."
            : "BCPC case monitoring (Q{$data['quarter']} {$data['year']}) saved successfully.";

        $redirect = redirect()->route('desk-officer.bcpc-case-monitoring.edit')
            ->with('success', $msg);

        if ($sent) {
            $redirect->with('bcpc_sent_to_admin', true);
        }

        return $redirect;
    }

    /**
     * Send BCPC Case Monitoring to Admin.
     */
    public function sendToAdmin(Request $request, $id)
    {
        $this->ensureBcpcAccess();
        $barangay = $this->barangay();
        abort_unless($barangay, 404, 'Barangay not found for your account.');

        $monitoring = BcpcCaseMonitoring::where('barangay_id', $barangay->id)->whereNull('archived_at')->findOrFail($id);

        $monitoring->updated_by = Auth::id();
        $monitoring->submitted_at = now();
        $monitoring->submitted_by = Auth::id();
        $monitoring->save();

        $this->notifyBcpcAdmin($monitoring, $barangay);

        \Log::info("BCPC Case Monitoring for {$barangay->name} (Q{$monitoring->quarter} {$monitoring->year}) sent to Admin by " . (Auth::user()->email ?? Auth::id()));

        $message = "BCPC Case Monitoring for {$barangay->name} (Q{$monitoring->quarter} {$monitoring->year}) has been sent to Admin successfully.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('desk-officer.bcpc-case-monitoring.edit')->with('success', $message);
    }

    private function notifyBcpcAdmin(BcpcCaseMonitoring $monitoring, Barangay $barangay): void
    {
        $submittedBy = trim((string) ((Auth::user()?->first_name ?? '') . ' ' . (Auth::user()?->last_name ?? '')))
            ?: (Auth::user()?->name ?? 'Desk Officer');
        $summary = "BCPC Case Monitoring for {$barangay->name} (Q{$monitoring->quarter} {$monitoring->year}) was sent to Admin. Submitted by: {$submittedBy}.";

        try {
            $reportView = $this->print(new Request([
                'quarter' => $monitoring->quarter,
                'year' => $monitoring->year,
            ]));
            $pdfContent = Pdf::loadHtml($reportView->render())->output();
            $pdfFilename = "BCPC-Monitoring-Q{$monitoring->quarter}-{$monitoring->year}.pdf";

            Mail::to('bcpcsilang@gmail.com')->send(new PortalActionNotification(
                'BCPC monitoring report submitted',
                $summary,
                $pdfContent,
                $pdfFilename,
            ));
        } catch (\Throwable $exception) {
            \Log::warning('BCPC monitoring notification email could not be sent.', [
                'email' => 'bcpcsilang@gmail.com',
                'monitoring_id' => $monitoring->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Display archived BCPC Case Monitoring records for Desk Officer.
     */
    public function archived(Request $request)
    {
        $this->ensureBcpcAccess();
        $barangay = $this->barangay();
        abort_unless($barangay, 404, 'Barangay not found for your account.');

        $quarter = $request->input('quarter');
        $year = $request->input('year');
        $search = trim((string) $request->input('search'));

        $query = BcpcCaseMonitoring::with(['barangay', 'creator'])->where('barangay_id', $barangay->id)->whereNotNull('archived_at');
        $instantArchiveSearch = ($request->user()->defaultCaseDepartment() === 'BCPC' || $request->user()->isMultiCaseDepartment());
        if ($quarter && !$instantArchiveSearch) $query->where('quarter', $quarter);
        if ($year && !$instantArchiveSearch) $query->where('year', $year);
        if ($search !== '' && !$instantArchiveSearch) {
            $searchQuarter = preg_match('/q([1-4])/i', $search, $m) ? (int)$m[1] : null;
            $query->where(function ($sub) use ($search, $searchQuarter) {
                if ($searchQuarter !== null) {
                    $sub->where('quarter', $searchQuarter);
                } else {
                    $sub->where('quarter', 'like', "%{$search}%");
                }
                $sub->orWhere('year', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT('Q', quarter, ' ', year) LIKE ?", ["%{$search}%"])
                    ->orWhereHas('creator', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"))
                    ->orWhereHas('barangay', fn ($b) => $b->where('name', 'like', "%{$search}%"));
            });
        }
        $monitorings = $query->latest('archived_at')->paginate($instantArchiveSearch ? PHP_INT_MAX : 5, ['*'], 'page', $instantArchiveSearch ? 1 : null)->withQueryString();
        $activePeriods = BcpcCaseMonitoring::where('barangay_id', $barangay->id)
            ->whereNull('archived_at')
            ->get(['quarter', 'year'])
            ->mapWithKeys(fn ($monitoring) => ["{$monitoring->quarter}-{$monitoring->year}" => true])
            ->all();
        $activeCount = BcpcCaseMonitoring::where('barangay_id', $barangay->id)->whereNull('archived_at')->count();
        $archivedCount = BcpcCaseMonitoring::where('barangay_id', $barangay->id)->whereNotNull('archived_at')->count();

        return view('desk-officer.bcpc-case-monitoring-archived', compact(
            'barangay',
            'quarter',
            'year',
            'monitorings',
            'activePeriods',
            'activeCount',
            'archivedCount'
        ));
    }

    /**
     * Archive a BCPC Case Monitoring record.
     */
    public function archive($id)
    {
        $this->ensureBcpcAccess();
        $barangay = $this->barangay();
        abort_unless($barangay, 404, 'Barangay not found for your account.');

        $monitoring = BcpcCaseMonitoring::where('barangay_id', $barangay->id)->findOrFail($id);
        $monitoring->archived_at = now();
        $monitoring->save();

        return response()->json([
            'success' => true,
            'message' => 'BCPC case monitoring record archived successfully.',
        ]);
    }

    /**
     * Restore an archived BCPC Case Monitoring record.
     */
    public function restore($id)
    {
        $this->ensureBcpcAccess();
        $barangay = $this->barangay();
        abort_unless($barangay, 404);
        $monitoring = BcpcCaseMonitoring::where('barangay_id', $barangay->id)->whereNotNull('archived_at')->findOrFail($id);
        if (BcpcCaseMonitoring::where('barangay_id', $barangay->id)
            ->where('quarter', $monitoring->quarter)
            ->where('year', $monitoring->year)
            ->whereNull('archived_at')
            ->exists()) {
            return response()->json(['success' => false, 'message' => 'An active monitoring report already exists for this quarter and year.'], 422);
        }
        $monitoring->archived_at = null;
        $monitoring->save();
        return response()->json(['success' => true, 'message' => 'Monitoring report restored.']);
    }

    public function forceDelete($id)
    {
        $this->ensureBcpcAccess();
        $barangay = $this->barangay();
        $monitoring = BcpcCaseMonitoring::where('barangay_id', $barangay->id)->whereNotNull('archived_at')->findOrFail($id);
        $monitoring->delete();

        return response()->json(['success' => true, 'message' => 'Archived BCPC monitoring report deleted successfully.']);
    }

    private function barangay(): ?Barangay
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        return Barangay::where('id', $user->barangay)->orWhere('name', $user->barangay)->first();
    }

    private function ensureBcpcAccess(): void
    {
        abort_unless(Auth::user()?->canAccessCaseDepartment('BCPC'), 403);
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

    private function monitoringTables(): array
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
