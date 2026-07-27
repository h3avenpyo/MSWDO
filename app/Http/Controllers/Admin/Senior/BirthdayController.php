<?php

namespace App\Http\Controllers\Admin\Senior;

use App\Http\Controllers\Controller;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\Senior\BirthdayPayout;
use App\Models\Senior\BirthdayPayoutHistory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BirthdayController extends Controller
{
    public function index(Request $request)
    {
        $today = now();
        $todayCount = $this->birthdayQuery($today->format('m'), $today->format('d'))->count();
        $weekCount = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->where(function ($q) {
                $start = now();
                $end = now()->addDays(7);
                $sMD = $start->format('m-d');
                $eMD = $end->format('m-d');
                if ($sMD <= $eMD) {
                    $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$sMD, $eMD]);
                } else {
                    $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$sMD])
                      ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$eMD]);
                }
            })->count();

        $nextMonth = now()->addMonth();
        $nextMonthCount = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ?", [$nextMonth->format('n')])
            ->count();

        $total = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->where(function ($q) {
                $start = now();
                $end = now()->addDays(30);
                $sMD = $start->format('m-d');
                $eMD = $end->format('m-d');
                if ($sMD <= $eMD) {
                    $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$sMD, $eMD]);
                } else {
                    $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$sMD])
                      ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$eMD]);
                }
            })->count();

        $barangays = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('barangay')
            ->distinct()
            ->orderBy('barangay')
            ->pluck('barangay');

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        // Get selected month from request or default to current month
        $selectedMonth = $request->get('month', now()->format('n'));
        $selectedYear = $request->get('year', now()->year);
        $payoutAmount = 500.00;

        $barangayBreakdown = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereNotNull('barangay')
            ->whereRaw("MONTH(birth_date) = ?", [$selectedMonth])
            ->get()
            ->groupBy('barangay')
            ->map(function ($seniors, $barangay) use ($selectedYear, $payoutAmount) {
                $seniorIds = $seniors->pluck('id');
                $existingPayouts = BirthdayPayout::whereIn('senior_id', $seniorIds)
                    ->where('payout_year', $selectedYear)
                    ->get();

                $pendingCount = $existingPayouts->where('status', 'pending')->count();
                $releasedCount = $existingPayouts->where('status', 'released')->count();
                $totalCount = $seniors->count();
                $remainingCount = $totalCount - $existingPayouts->count();

                // Get all birthday celebrants for this barangay
                $celebrants = $seniors->map(function ($s) {
                    $controlNo = $s->control_number ?? $s->record_number ?? '-';
                    $fullName = trim("{$s->first_name} {$s->middle_name} {$s->last_name}");
                    return [
                        'control_number' => $controlNo,
                        'full_name' => $fullName,
                    ];
                })->sortBy('full_name')->values();

                return [
                    'barangay' => $barangay,
                    'total_seniors' => $totalCount,
                    'total_amount' => $totalCount * $payoutAmount,
                    'pending_count' => $pendingCount,
                    'pending_amount' => $pendingCount * $payoutAmount,
                    'released_count' => $releasedCount,
                    'released_amount' => $releasedCount * $payoutAmount,
                    'remaining_count' => $remainingCount,
                    'remaining_amount' => $remainingCount * $payoutAmount,
                    'celebrants' => $celebrants,
                ];
            })
            ->sortByDesc('total_seniors')
            ->values();

        $grandTotal = $barangayBreakdown->sum('total_amount');
        $grandRemaining = $barangayBreakdown->sum('remaining_amount');

        return view('admin.senior.birthdays', compact(
            'todayCount', 'weekCount', 'nextMonthCount', 'total',
            'barangays', 'months', 'barangayBreakdown',
            'grandTotal', 'grandRemaining', 'payoutAmount',
            'selectedMonth', 'selectedYear'
        ));
    }

    public function data(Request $request)
    {
        $query = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date');

        // Only apply 30-day range filter if no specific filter is set or filter is 'all'
        if (!$request->filled('filter') || $request->filter === 'all') {
            // Show birthdays for current month
            $query->whereRaw("MONTH(birth_date) = ?", [now()->format('n')]);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('middle_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('control_number', 'like', "%{$s}%")
                  ->orWhere('osca_id', 'like', "%{$s}%")
                  ->orWhere('barangay', 'like', "%{$s}%")
                  ->orWhere('contact_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('barangay')) {
            $query->where('barangay', $request->barangay);
        }

        if ($request->filled('month')) {
            $query->whereRaw("MONTH(birth_date) = ?", [$request->month]);
        }

        if ($request->filled('filter')) {
            $f = $request->filter;
            if ($f === 'today') {
                $query->whereRaw("MONTH(birth_date) = ? AND DAY(birth_date) = ?", [now()->format('n'), now()->format('j')]);
            } elseif ($f === 'week') {
                $ws = now(); $we = now()->addDays(7);
                $wsMD = $ws->format('m-d'); $weMD = $we->format('m-d');
                if ($wsMD <= $weMD) {
                    $query->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$wsMD, $weMD]);
                } else {
                    $query->where(function ($q) use ($wsMD, $weMD) {
                        $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$wsMD])
                          ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$weMD]);
                    });
                }
            } elseif ($f === 'nextmonth') {
                $nm = now()->addMonth();
                $query->whereRaw("MONTH(birth_date) = ?", [$nm->format('n')]);
            }
        }

        $sortField = $request->sort_field ?? 'birth_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['birth_date', 'first_name', 'barangay', 'control_number'];
        if (in_array($sortField, $allowedSorts)) {
            if ($sortField === 'birth_date') {
                $query->orderByRaw("MONTH(birth_date) $sortDir, DAY(birth_date) $sortDir");
            } else {
                $query->orderBy($sortField, $sortDir);
            }
        } else {
            $query->orderByRaw("MONTH(birth_date) ASC, DAY(birth_date) ASC");
        }

        $perPage = $request->per_page ?? 15;
        $seniors = $query->paginate($perPage);

        $seniors->getCollection()->transform(function ($s) {
            $bday = Carbon::parse($s->birth_date);
            $today = now()->startOfDay();
            $next = $bday->copy()->year($today->year)->startOfDay();
            if ($next->isBefore($today)) {
                $next->addYear();
            }
            $daysLeft = $today->diffInDays($next);
            $isToday = ($daysLeft == 0);
            $currentAge = $bday->age;
            $ageTurning = $isToday ? $currentAge : ($currentAge + 1);

            // Check payout status for current year
            $currentYear = now()->year;
            $payout = BirthdayPayout::where('senior_id', $s->id)
                ->where('payout_year', $currentYear)
                ->first();

            return (object) [
                'id' => $s->id,
                'control_number' => $s->control_number ?? $s->record_number ?? '-',
                'osca_id' => $s->osca_id ?? '-',
                'full_name' => $s->full_name,
                'birth_date' => $s->birth_date,
                'birth_date_formatted' => $bday->format('M d, Y'),
                'current_age' => $currentAge,
                'age_turning' => $ageTurning,
                'barangay' => $s->barangay ?? '-',
                'contact_number' => $s->contact_number ?? '-',
                'address' => $s->address ?? '-',
                'month' => $s->month ?? $bday->format('F'),
                'days_left' => $daysLeft,
                'is_today' => $isToday,
                'sex' => $s->sex,
                'philsys_number' => $s->philsys_number,
                'rrn_number' => $s->rrn_number,
                'remarks' => $s->remarks,
                'status' => $s->status,
                'payout_status' => $payout ? $payout->status : null,
                'payout_id' => $payout ? $payout->id : null,
                'payout_amount' => $payout ? $payout->amount : null,
            ];
        });

        return response()->json($seniors);
    }

    public function profile($id)
    {
        $senior = SeniorCitizenRecord::findOrFail($id);

        $bday = Carbon::parse($senior->birth_date);
        $today = now()->startOfDay();
        $next = $bday->copy()->year($today->year)->startOfDay();
        if ($next->isBefore($today)) {
            $next->addYear();
        }
        $daysLeft = $today->diffInDays($next);
        $isToday = ($daysLeft == 0);
        $currentAge = $bday->age;
        $ageTurning = $isToday ? $currentAge : ($currentAge + 1);

        return response()->json([
            'control_number' => $senior->control_number ?? $senior->record_number ?? '-',
            'osca_id' => $senior->osca_id ?? '-',
            'full_name' => $senior->full_name,
            'birth_date' => $bday->format('M d, Y'),
            'current_age' => $currentAge,
            'age_turning' => $ageTurning,
            'address' => $senior->address ?? '-',
            'barangay' => $senior->barangay ?? '-',
            'contact_number' => $senior->contact_number ?? '-',
            'sex' => $senior->sex ?? '-',
            'philsys_number' => $senior->philsys_number ?? '-',
            'rrn_number' => $senior->rrn_number ?? '-',
            'remarks' => $senior->remarks ?? '-',
            'month' => $senior->month ?? $bday->format('F'),
            'days_left' => $daysLeft,
            'is_today' => $isToday,
            'status' => $senior->status,
        ]);
    }

    public function dataByBarangay(Request $request)
    {
        $query = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereNotNull('barangay');

        $start = now();
        $end = now()->addDays(30);
        $sMD = $start->format('m-d');
        $eMD = $end->format('m-d');

        if ($sMD <= $eMD) {
            $query->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$sMD, $eMD]);
        } else {
            $query->where(function ($q) use ($sMD, $eMD) {
                $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$sMD])
                  ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$eMD]);
            });
        }

        $barangays = $query->get()->groupBy('barangay')->map(function ($seniors, $barangay) {
            return [
                'barangay' => $barangay,
                'count' => $seniors->count(),
                'seniors' => $seniors->map(function ($s) {
                    $bday = Carbon::parse($s->birth_date);
                    $today = now()->startOfDay();
                    $next = $bday->copy()->year($today->year)->startOfDay();
                    if ($next->isBefore($today)) {
                        $next->addYear();
                    }
                    $daysLeft = $today->diffInDays($next);
                    $isToday = ($daysLeft == 0);
                    return [
                        'id' => $s->id,
                        'full_name' => $s->full_name,
                        'birth_date' => $bday->format('M d, Y'),
                        'days_left' => $daysLeft,
                        'is_today' => $isToday,
                    ];
                })->sortBy('days_left')->values(),
            ];
        })->sortByDesc('count')->values();

        return response()->json($barangays);
    }

    public function exportPdf(Request $request)
    {
        $seniors = $this->getFilteredData($request);
        $dateGenerated = now()->format('F d, Y h:i A');
        $total = $seniors->count();

        $barangaySummary = $seniors->groupBy('barangay')->map(function ($group, $barangay) {
            return ['barangay' => $barangay, 'count' => $group->count()];
        })->sortByDesc('count');

        $pdf = Pdf::loadView('admin.senior.birthday-pdf', compact(
            'seniors', 'dateGenerated', 'total', 'barangaySummary'
        ));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('birthday-beneficiaries-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $seniors = $this->getFilteredData($request);
        $filename = 'birthday-beneficiaries-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($seniors) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Control Number', 'Senior Citizen ID', 'Full Name',
                'Birth Date', 'Current Age', 'Age Turning', 'Barangay',
                'Contact Number', 'Days Left', 'Status'
            ]);
            foreach ($seniors as $s) {
                fputcsv($file, [
                    $s['control_number'], $s['osca_id'], $s['full_name'],
                    $s['birth_date'], $s['current_age'], $s['age_turning'],
                    $s['barangay'], $s['contact_number'], $s['days_left'], $s['status']
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printView(Request $request)
    {
        $seniors = $this->getFilteredData($request);
        $dateGenerated = now()->format('F d, Y h:i A');
        $total = $seniors->count();

        $barangaySummary = $seniors->groupBy('barangay')->map(function ($group, $barangay) {
            return ['barangay' => $barangay, 'count' => $group->count()];
        })->sortByDesc('count');

        return view('admin.senior.birthday-print', compact(
            'seniors', 'dateGenerated', 'total', 'barangaySummary'
        ));
    }

    private function birthdayQuery($month, $day)
    {
        return SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ? AND DAY(birth_date) = ?", [$month, $day]);
    }

    private function getFilteredData(Request $request)
    {
        $query = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date');

        $start = now();
        $end = now()->addDays(30);
        $sMD = $start->format('m-d');
        $eMD = $end->format('m-d');

        if ($sMD <= $eMD) {
            $query->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$sMD, $eMD]);
        } else {
            $query->where(function ($q) use ($sMD, $eMD) {
                $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$sMD])
                  ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$eMD]);
            });
        }

        if ($request->filled('barangay')) {
            $query->where('barangay', $request->barangay);
        }
        if ($request->filled('month')) {
            $query->whereRaw("MONTH(birth_date) = ?", [$request->month]);
        }

        $all = $query->get();
        return $all->map(function ($s) {
            $bday = Carbon::parse($s->birth_date);
            $today = now()->startOfDay();
            $next = $bday->copy()->year($today->year)->startOfDay();
            if ($next->isBefore($today)) {
                $next->addYear();
            }
            $daysLeft = $today->diffInDays($next);
            $isToday = ($daysLeft == 0);
            $currentAge = $bday->age;
            $ageTurning = $isToday ? $currentAge : ($currentAge + 1);
            return [
                'control_number' => $s->control_number ?? $s->record_number ?? '-',
                'osca_id' => $s->osca_id ?? '-',
                'full_name' => $s->full_name,
                'birth_date' => $bday->format('M d, Y'),
                'current_age' => $currentAge,
                'age_turning' => $ageTurning,
                'barangay' => $s->barangay ?? '-',
                'contact_number' => $s->contact_number ?? '-',
                'address' => $s->address ?? '-',
                'days_left' => $daysLeft,
                'is_today' => $isToday,
                'status' => $s->status,
            ];
        })->sortBy('days_left')->values();
    }

    /**
     * Quick generate payouts for current month
     */
    public function generatePayouts(Request $request)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer',
            'barangay' => 'nullable|string',
        ]);

        $month = $request->month;
        $year = $request->year;
        $barangay = $request->barangay;
        $payoutAmount = 500.00;

        $monthNumber = date('m', strtotime($month));

        $query = SeniorCitizenRecord::query()
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ?", [$monthNumber]);

        if ($barangay) {
            $query->where('barangay', $barangay);
        }

        $seniors = $query->get();

        // Get barangay breakdown for preview
        $barangaySummary = $seniors->groupBy('barangay')->map(function ($group) use ($year, $payoutAmount) {
            $seniorIds = $group->pluck('id');
            $existingPayouts = BirthdayPayout::whereIn('senior_id', $seniorIds)
                ->where('payout_year', $year)
                ->count();
            $newCount = $group->count() - $existingPayouts;
            return [
                'barangay' => $group->first()->barangay,
                'total_seniors' => $group->count(),
                'new_payouts' => $newCount,
                'amount' => $newCount * $payoutAmount,
            ];
        })->sortByDesc('amount')->values();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($seniors as $senior) {
            $existingPayout = BirthdayPayout::query()
                ->where('senior_id', $senior->id)
                ->where('payout_year', $year)
                ->first();

            if (!$existingPayout) {
                $payout = BirthdayPayout::create([
                    'senior_id' => $senior->id,
                    'payout_year' => $year,
                    'amount' => $payoutAmount,
                    'status' => 'pending',
                ]);

                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $senior->id,
                    'generated',
                    "Payout generated for {$month} {$year}",
                    session('admin_user_id'),
                    $request->ip()
                );

                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Generated {$createdCount} payout records. Skipped {$skippedCount} existing records.",
            'created' => $createdCount,
            'skipped' => $skippedCount,
            'total_amount' => $createdCount * $payoutAmount,
            'barangay_summary' => $barangaySummary
        ]);
    }

    /**
     * Quick release payout for a senior
     */
    public function releasePayout(Request $request, $id)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        $payout = BirthdayPayout::findOrFail($id);

        if (!$payout->canBeReleased()) {
            return response()->json(['error' => 'This payout cannot be released'], 400);
        }

        $payout->markAsReleased(session('admin_user_id'), $request->remarks);

        BirthdayPayoutHistory::logAction(
            $payout->id,
            $payout->senior_id,
            'released',
            "Payout released. Amount: PHP {$payout->amount}. Remarks: " . ($request->remarks ?? 'None'),
            session('admin_user_id'),
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Payout marked as released successfully'
        ]);
    }

    /**
     * Bulk release payouts
     */
    public function bulkRelease(Request $request)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'payout_ids' => 'required|array',
            'payout_ids.*' => 'integer',
            'remarks' => 'nullable|string|max:500',
        ]);

        $payoutIds = $request->payout_ids;
        $releasedCount = 0;
        $releasedPayoutIds = [];

        foreach ($payoutIds as $id) {
            $payout = BirthdayPayout::find($id);
            if ($payout && $payout->canBeReleased()) {
                $payout->markAsReleased(session('admin_user_id'), $request->remarks);

                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $payout->senior_id,
                    'released',
                    "Bulk payout released. Amount: PHP {$payout->amount}. Remarks: " . ($request->remarks ?? 'None'),
                    session('admin_user_id'),
                    $request->ip()
                );

                $releasedCount++;
                $releasedPayoutIds[] = $payout->id;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Generated {$generatedCount} payouts and released {$releasedCount} payouts successfully for {$barangay}",
            'released_payout_ids' => $releasedPayoutIds
        ]);
    }

    /**
     * Print bulk released payouts
     */
    public function printBulkReleased(Request $request)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        $request->validate([
            'payout_ids' => 'required|array',
            'payout_ids.*' => 'integer',
        ]);

        $payoutIds = $request->payout_ids;

        $payouts = BirthdayPayout::whereIn('id', $payoutIds)
            ->with(['senior', 'releasedBy'])
            ->get();

        $dateGenerated = now()->format('F d, Y h:i A');
        $total = $payouts->count();
        $totalAmount = $payouts->sum('amount');
        $month = now()->format('F');
        $year = now()->year;

        // Group payouts by barangay
        $payoutsByBarangay = $payouts->groupBy(function($payout) {
            return $payout->senior->barangay ?? 'Unknown';
        })->sortKeys();

        // Determine barangay name for filename
        $romanMap = [' III' => ' 3', ' II' => ' 2', ' IV' => ' 4', ' I' => ' 1', ' V' => ' 5'];
        if ($payoutsByBarangay->count() === 1) {
            $barangayName = str_replace(' ', '-', trim(str_replace(array_keys($romanMap), array_values($romanMap), $payoutsByBarangay->keys()->first())));
        } else {
            $barangayName = 'all-barangays';
        }

        // Log to recent activities
        $barangayLabel = $payoutsByBarangay->count() === 1
            ? $payoutsByBarangay->keys()->first()
            : 'All Barangays';
        $this->logActivity('printed birthday payout PDF', "{$barangayLabel} - {$month} {$year}", count($payouts) . ' payout(s)');

        $pdf = Pdf::loadView('admin.senior.birthday-payout-print', compact(
            'payouts',
            'payoutsByBarangay',
            'dateGenerated',
            'total',
            'totalAmount',
            'month',
            'year',
            'barangayName'
        ))->setPaper('a4', 'portrait')
          ->setOptions([
              'defaultFont' => 'Times New Roman',
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled' => true,
              'margin-top' => '15mm',
              'margin-bottom' => '15mm',
              'margin-left' => '15mm',
              'margin-right' => '15mm',
          ]);

        $filename = strtolower($barangayName) . '-' . strtolower($month) . "-{$year}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Generate payouts for all barangays
     */
    public function generateAllPayouts(Request $request)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'month' => 'nullable|integer',
            'year' => 'nullable|integer',
        ]);

        $selectedMonth = $request->get('month', now()->format('n'));
        $selectedYear = $request->get('year', now()->year);
        $payoutAmount = 500.00;
        $currentMonthName = date('F', mktime(0, 0, 0, $selectedMonth, 1));

        $seniors = SeniorCitizenRecord::query()
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ?", [$selectedMonth])
            ->get();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($seniors as $senior) {
            $existingPayout = BirthdayPayout::query()
                ->where('senior_id', $senior->id)
                ->where('payout_year', $selectedYear)
                ->first();

            if (!$existingPayout) {
                $payout = BirthdayPayout::create([
                    'senior_id' => $senior->id,
                    'payout_year' => $selectedYear,
                    'amount' => $payoutAmount,
                    'status' => 'pending',
                ]);

                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $senior->id,
                    'generated',
                    "Bulk payout generated for {$currentMonthName} {$selectedYear}",
                    session('admin_user_id'),
                    $request->ip()
                );

                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Generated {$createdCount} payout records for all barangays. Skipped {$skippedCount} existing records.",
            'created' => $createdCount,
            'skipped' => $skippedCount,
            'total_amount' => $createdCount * $payoutAmount
        ]);
    }

    /**
     * Release all pending payouts
     */
    public function releaseAllPayouts(Request $request)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'month' => 'nullable|integer',
            'year' => 'nullable|integer',
        ]);

        $selectedMonth = $request->get('month', now()->format('n'));
        $selectedYear = $request->get('year', now()->year);
        $payoutAmount = 500.00;

        // Auto-generate payouts if none exist for the selected month/year
        $seniors = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ?", [$selectedMonth])
            ->get();

        $generatedCount = 0;
        foreach ($seniors as $senior) {
            $existingPayout = BirthdayPayout::query()
                ->where('senior_id', $senior->id)
                ->where('payout_year', $selectedYear)
                ->first();

            if (!$existingPayout) {
                $payout = BirthdayPayout::create([
                    'senior_id' => $senior->id,
                    'payout_year' => $selectedYear,
                    'amount' => $payoutAmount,
                    'status' => 'pending',
                ]);

                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $senior->id,
                    'generated',
                    "Auto-generated for release",
                    session('admin_user_id'),
                    $request->ip()
                );

                $generatedCount++;
            }
        }

        // Now release all pending payouts
        $pendingPayouts = BirthdayPayout::where('status', 'pending')
            ->where('payout_year', $selectedYear)
            ->get();

        $releasedCount = 0;
        $releasedPayoutIds = [];

        foreach ($pendingPayouts as $payout) {
            if ($payout->canBeReleased()) {
                $payout->markAsReleased(session('admin_user_id'), $request->remarks);

                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $payout->senior_id,
                    'released',
                    "Bulk release all payouts. Amount: PHP {$payout->amount}",
                    session('admin_user_id'),
                    $request->ip()
                );

                $releasedCount++;
                $releasedPayoutIds[] = $payout->id;
            }
        }

        // Log to recent activities
        $this->logActivity('bulk released birthday payouts', "{$releasedCount} payout(s)", 'Multiple');

        return response()->json([
            'success' => true,
            'message' => "Generated {$generatedCount} payouts and released {$releasedCount} payouts successfully",
            'released_payout_ids' => $releasedPayoutIds
        ]);
    }

    /**
     * Generate payouts for specific barangay
     */
    public function generateBarangayPayouts(Request $request)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'barangay' => 'required|string',
            'month' => 'nullable|integer',
            'year' => 'nullable|integer',
        ]);

        $barangay = $request->barangay;
        $selectedMonth = $request->get('month', now()->format('n'));
        $selectedYear = $request->get('year', now()->year);
        $payoutAmount = 500.00;
        $currentMonthName = date('F', mktime(0, 0, 0, $selectedMonth, 1));

        $seniors = SeniorCitizenRecord::query()
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->where('barangay', $barangay)
            ->whereRaw("MONTH(birth_date) = ?", [$selectedMonth])
            ->get();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($seniors as $senior) {
            $existingPayout = BirthdayPayout::query()
                ->where('senior_id', $senior->id)
                ->where('payout_year', $selectedYear)
                ->first();

            if (!$existingPayout) {
                $payout = BirthdayPayout::create([
                    'senior_id' => $senior->id,
                    'payout_year' => $selectedYear,
                    'amount' => $payoutAmount,
                    'status' => 'pending',
                ]);

                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $senior->id,
                    'generated',
                    "Payout generated for {$barangay} - {$currentMonthName} {$selectedYear}",
                    session('admin_user_id'),
                    $request->ip()
                );

                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Generated {$createdCount} payout records for {$barangay}. Skipped {$skippedCount} existing records.",
            'created' => $createdCount,
            'skipped' => $skippedCount,
            'total_amount' => $createdCount * $payoutAmount
        ]);
    }

    /**
     * Release payouts for specific barangay
     */
    public function releaseBarangayPayouts(Request $request)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'barangay' => 'required|string',
            'month' => 'nullable|integer',
            'year' => 'nullable|integer',
        ]);

        $barangay = $request->barangay;
        $selectedMonth = $request->get('month', now()->format('n'));
        $selectedYear = $request->get('year', now()->year);
        $payoutAmount = 500.00;

        // Auto-generate payouts for this barangay if none exist
        $seniors = SeniorCitizenRecord::where('status', 'active')
            ->where('barangay', $barangay)
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ?", [$selectedMonth])
            ->get();

        $generatedCount = 0;
        foreach ($seniors as $senior) {
            $existingPayout = BirthdayPayout::query()
                ->where('senior_id', $senior->id)
                ->where('payout_year', $selectedYear)
                ->first();

            if (!$existingPayout) {
                $payout = BirthdayPayout::create([
                    'senior_id' => $senior->id,
                    'payout_year' => $selectedYear,
                    'amount' => $payoutAmount,
                    'status' => 'pending',
                ]);

                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $senior->id,
                    'generated',
                    "Auto-generated for release - {$barangay}",
                    session('admin_user_id'),
                    $request->ip()
                );

                $generatedCount++;
            }
        }

        // Now release pending payouts for this barangay
        $pendingPayouts = BirthdayPayout::where('status', 'pending')
            ->where('payout_year', $selectedYear)
            ->whereHas('senior', function ($q) use ($barangay) {
                $q->where('barangay', $barangay);
            })
            ->get();

        $releasedCount = 0;
        $releasedPayoutIds = [];

        foreach ($pendingPayouts as $payout) {
            if ($payout->canBeReleased()) {
                $payout->markAsReleased(session('admin_user_id'), $request->remarks);

                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $payout->senior_id,
                    'released',
                    "Barangay release for {$barangay}. Amount: PHP {$payout->amount}",
                    session('admin_user_id'),
                    $request->ip()
                );

                $releasedCount++;
                $releasedPayoutIds[] = $payout->id;
            }
        }

        // Log to recent activities
        $this->logActivity('released birthday payouts', $barangay, $barangay);

        return response()->json([
            'success' => true,
            'message' => "Generated {$generatedCount} payouts and released {$releasedCount} payouts successfully for {$barangay}",
            'released_payout_ids' => $releasedPayoutIds
        ]);
    }

    /**
     * Log activity to session for recent actions display
     */
    private function logActivity($action, $name, $identifier)
    {
        $activities = session('recent_activities', []);

        array_unshift($activities, [
            'action' => $action,
            'name' => $name,
            'identifier' => $identifier,
            'timestamp' => now()->format('M d, Y h:i A'),
        ]);

        // Keep only last 10 activities
        $activities = array_slice($activities, 0, 10);

        session(['recent_activities' => $activities]);
    }
}
