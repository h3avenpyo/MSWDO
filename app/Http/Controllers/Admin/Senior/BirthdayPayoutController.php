<?php

namespace App\Http\Controllers\Admin\Senior;

use App\Http\Controllers\Controller;
use App\Models\Senior\BirthdayPayout;
use App\Models\Senior\BirthdayPayoutHistory;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\Senior\SeniorActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BirthdayPayoutController extends Controller
{
    /**
     * Display the birthday payout list page
     */
    public function index(Request $request)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $currentYear = now()->year;
        $currentMonth = now()->format('F');

        $selectedMonth = $request->get('month', $currentMonth);
        $selectedYear = $request->get('year', $currentYear);
        $selectedBarangay = $request->get('barangay', '');
        $search = $request->get('search', '');

        // Get month number from month name
        $monthNumber = date('m', strtotime($selectedMonth));

        // Get barangays for filter
        $barangays = [
            'Acacia', 'Adlas', 'Anahaw I', 'Anahaw II', 'Balite I', 'Balite II', 'Balubad', 'Banaba', 'Batas',
            'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho', 'Bulihan', 'Cabangaan', 'Carmen', 'Hoyo', 'Hukay', 'Iba',
            'Inchican', 'Ipil I', 'Ipil II', 'Kalubkob', 'Kaong', 'Lalaan I', 'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil',
            'Maguyam', 'Malabag', 'Malaking Tatyao', 'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I', 'San Vicente II', 'Santol',
            'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II', 'Tubuan III', 'Ulat', 'Yakal'
        ];

        // Get existing payouts for the selected month and year
        $query = BirthdayPayout::query()
            ->where('payout_year', $selectedYear)
            ->whereHas('senior', function ($q) use ($monthNumber) {
                $q->whereRaw("MONTH(birth_date) = ?", [$monthNumber]);
            })
            ->with(['senior', 'releasedBy']);

        // Apply barangay filter
        if ($selectedBarangay) {
            $query->whereHas('senior', function ($q) use ($selectedBarangay) {
                $q->where('barangay', $selectedBarangay);
            });
        }

        // Apply search filter
        if ($search) {
            $query->whereHas('senior', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('middle_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            });
        }

        $existingPayouts = $query->paginate(25)->appends($request->query());

        // Calculate summary
        $totalBeneficiaries = $existingPayouts->count();
        $totalReleased = $existingPayouts->where('status', 'released')->count();
        $totalPending = $existingPayouts->where('status', 'pending')->count();
        $payoutAmount = 500.00; // Default payout amount
        $totalBudget = $totalBeneficiaries * $payoutAmount;

        return view('admin.senior.birthday-payouts', compact(
            'months',
            'currentYear',
            'currentMonth',
            'selectedMonth',
            'selectedYear',
            'selectedBarangay',
            'search',
            'barangays',
            'existingPayouts',
            'totalBeneficiaries',
            'totalReleased',
            'totalPending',
            'payoutAmount',
            'totalBudget'
        ));
    }

    /**
     * Display payout history/audit log
     */
    public function history(Request $request)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        $query = BirthdayPayoutHistory::query()
            ->with(['payout', 'senior', 'performedBy'])
            ->where('action', 'released')
            ->orderBy('created_at', 'desc');

        // Apply barangay filter
        $barangay = $request->get('barangay', '');
        if ($barangay) {
            $query->whereHas('senior', function($q) use ($barangay) {
                $q->where('barangay', $barangay);
            });
        }

        // Apply date filters
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        $history = $query->paginate(15);

        $actions = ['released'];

        return view('admin.senior.birthday-payout-history', compact(
            'history',
            'actions',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Generate payout list based on filters
     */
    public function generate(Request $request)
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

        // Get month number from month name
        $monthNumber = date('m', strtotime($month));

        // Query eligible seniors
        $query = SeniorCitizenRecord::query()
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ?", [$monthNumber]);

        if ($barangay) {
            $query->where('barangay', $barangay);
        }

        $seniors = $query->get();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($seniors as $senior) {
            // Check if payout already exists for this senior in this year
            $existingPayout = BirthdayPayout::query()
                ->where('senior_id', $senior->id)
                ->where('payout_year', $year)
                ->first();

            if (!$existingPayout) {
                $payout = BirthdayPayout::create([
                    'senior_id' => $senior->id,
                    'payout_year' => $year,
                    'amount' => 500.00,
                    'status' => 'pending',
                ]);

                // Log the action
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
            'skipped' => $skippedCount
        ]);
    }

    /**
     * Mark a single payout as released
     */
    public function release(Request $request, $id)
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

        // Log the action to history
        BirthdayPayoutHistory::logAction(
            $payout->id,
            $payout->senior_id,
            'released',
            "Payout released. Amount: PHP {$payout->amount}. Remarks: " . ($request->remarks ?? 'None'),
            session('admin_user_id'),
            $request->ip()
        );

        // Log to recent activities
        $this->logActivity('released birthday payout', $payout->senior->full_name ?? 'Unknown', $payout->senior->control_number ?? 'N/A');

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

        foreach ($payoutIds as $id) {
            $payout = BirthdayPayout::find($id);
            if ($payout && $payout->canBeReleased()) {
                $payout->markAsReleased(session('admin_user_id'), $request->remarks);

                // Log the action to history
                BirthdayPayoutHistory::logAction(
                    $payout->id,
                    $payout->senior_id,
                    'released',
                    "Bulk payout released. Amount: PHP {$payout->amount}. Remarks: " . ($request->remarks ?? 'None'),
                    session('admin_user_id'),
                    $request->ip()
                );

                $releasedCount++;
            }
        }

        // Log to recent activities
        $this->logActivity('bulk released birthday payouts', "{$releasedCount} payout(s)", 'Multiple');

        return response()->json([
            'success' => true,
            'message' => "Released {$releasedCount} payouts successfully"
        ]);
    }

    /**
     * Cancel a payout
     */
    public function cancel(Request $request, $id)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        $payout = BirthdayPayout::findOrFail($id);
        $payout->cancel($request->remarks);

        // Log the action
        BirthdayPayoutHistory::logAction(
            $payout->id,
            $payout->senior_id,
            'cancelled',
            "Payout cancelled. Remarks: " . $request->remarks,
            session('admin_user_id'),
            $request->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Payout cancelled successfully'
        ]);
    }

    /**
     * Reset payout list - delete all payouts for selected month and year or all records
     */
    public function reset(Request $request)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $resetAll = $request->get('reset_all', false);

        if ($resetAll) {
            // Delete all payout records
            $deletedCount = BirthdayPayout::query()->delete();
            $message = "Deleted {$deletedCount} payout records from the database successfully";
        } else {
            // Delete only for selected month and year
            $request->validate([
                'month' => 'required|string',
                'year' => 'required|integer',
            ]);

            $month = $request->month;
            $year = $request->year;

            $monthNumber = date('m', strtotime($month));

            $deletedCount = BirthdayPayout::query()
                ->where('payout_year', $year)
                ->whereHas('senior', function ($q) use ($monthNumber) {
                    $q->whereRaw("MONTH(birth_date) = ?", [$monthNumber]);
                })
                ->delete();

            $message = "Deleted {$deletedCount} payout records for {$month} {$year} successfully";
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Print payout list
     */
    public function print(Request $request)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        $month = $request->get('month', now()->format('F'));
        $year = $request->get('year', now()->year);
        $barangay = $request->get('barangay', '');

        $monthNumber = date('m', strtotime($month));

        $payouts = BirthdayPayout::query()
            ->where('payout_year', $year)
            ->whereHas('senior', function ($q) use ($monthNumber) {
                $q->whereRaw("MONTH(birth_date) = ?", [$monthNumber]);
            })
            ->with(['senior', 'releasedBy'])
            ->get();

        if ($barangay) {
            $payouts = $payouts->filter(function ($payout) use ($barangay) {
                return $payout->senior->barangay === $barangay;
            });
        }

        // Log to recent activities
        $this->logActivity('printed birthday payout list', "{$month} {$year}", count($payouts) . ' payout(s)');

        return view('admin.senior.birthday-payout-print', compact(
            'payouts',
            'month',
            'year',
            'barangay'
        ));
    }

    /**
     * Export payout list to PDF
     */
    public function exportPdf(Request $request)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        $month = $request->get('month', now()->format('F'));
        $year = $request->get('year', now()->year);
        $barangay = $request->get('barangay', '');

        $monthNumber = date('m', strtotime($month));

        $payouts = BirthdayPayout::query()
            ->where('payout_year', $year)
            ->whereHas('senior', function ($q) use ($monthNumber) {
                $q->whereRaw("MONTH(birth_date) = ?", [$monthNumber]);
            })
            ->with(['senior', 'releasedBy'])
            ->get();

        if ($barangay) {
            $payouts = $payouts->filter(function ($payout) use ($barangay) {
                return $payout->senior->barangay === $barangay;
            });
        }

        $pdf = Pdf::loadView('admin.senior.birthday-payout-pdf', compact(
            'payouts',
            'month',
            'year',
            'barangay'
        ));

        // Log to recent activities
        $this->logActivity('exported birthday payout PDF', "{$month} {$year}", count($payouts) . ' payout(s)');

        return $pdf->download("birthday-payout-{$month}-{$year}.pdf");
    }

    /**
     * Export payout list to Excel
     */
    public function exportExcel(Request $request)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        // Excel export not available - use PDF export instead
        return redirect()->back()->with('error', 'Excel export is not available. Please use PDF export.');
    }

    /**
     * Print individual acknowledgement receipt
     */
    public function receipt($id)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        $payout = BirthdayPayout::query()
            ->with(['senior', 'releasedBy'])
            ->findOrFail($id);

        // Log to recent activities
        $this->logActivity('printed birthday payout receipt', $payout->senior->full_name ?? 'Unknown', $payout->senior->control_number ?? 'N/A');

        return view('admin.senior.birthday-payout-receipt', compact('payout'));
    }

    /**
     * Log activity to session for recent actions display
     */
    private function logActivity($action, $name, $identifier)
    {
        SeniorActivityLog::log($action, $name, $identifier);
    }
}
