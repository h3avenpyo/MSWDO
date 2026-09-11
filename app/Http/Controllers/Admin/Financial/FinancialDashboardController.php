<?php

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\FinancialPayrollRecord;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialDashboardController extends Controller
{
    /**
     * Authenticate and authorize Step 2 access from Step 1.
     */
    public function authenticateStep2(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim($request->input('email'));
        $user = User::where('email', $identifier)
            ->orWhere('name', $identifier)
            ->first();

        if (! $user) {
            return redirect()->route('admin.financial.financialstep1')
                ->with('step2_auth_error', 'Invalid credentials. Account not found.')
                ->with('step2_auth_required', true)
                ->withInput();
        }

        // Check account status
        $status = is_object($user->status) ? $user->status->value : $user->status;
        if ($status === 'inactive') {
            return redirect()->route('admin.financial.financialstep1')
                ->with('step2_auth_error', 'This account has been deactivated. Please contact an administrator.')
                ->with('step2_auth_required', true)
                ->withInput();
        }

        if (! Hash::check($request->password, $user->password)) {
            return redirect()->route('admin.financial.financialstep1')
                ->with('step2_auth_error', 'Invalid email or password.')
                ->with('step2_auth_required', true)
                ->withInput();
        }

        // Check if user has permission for Step 2
        $roleValue = is_object($user->role) ? $user->role->value : $user->role;
        $roleValueLower = strtolower((string) $roleValue);
        $allowedRoles = ['admin', 'financialstep2', 'financial assistance officer'];

        if (! in_array($roleValueLower, $allowedRoles, true)) {
            return redirect()->route('admin.financial.financialstep1')
                ->with('step2_auth_error', 'Access denied. The provided account is not authorized for Step 2 Verification & Disbursement.')
                ->with('step2_auth_required', true)
                ->withInput();
        }

        // Authorize session for Step 2
        session([
            'financial_step2_authorized' => true,
            'financial_step2_authorized_user' => $user->name,
            'financial_step2_authorized_role' => $roleValue,
            'financial_step2_authorized_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('admin.financial.financialstep2')
            ->with('success', 'Step 2 Financial Masterlist access successfully authorized.');
    }

    public function financialDashboard()
    {
        $totalIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::count() 
            : 0;

        $today = Carbon::today();
        $todayIntakes = class_exists(BeneficiaryIntake::class)
            ? BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                  ->orWhereDate('date_processed', $today);
            })->count()
            : 0;

        $step1Approved = $totalIntakes;
        $readyForStep2 = class_exists(BeneficiaryIntake::class)
            ? (BeneficiaryIntake::whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count() ?: $totalIntakes)
            : 0;

        $totalAmount = class_exists(BeneficiaryIntake::class)
            ? (BeneficiaryIntake::sum('recommended_amount') ?? 0)
            : 0;

        $recentIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::with(['client', 'encoderUser'])->latest()->take(6)->get() 
            : collect();

        return view('admin.financial.financial-dashboard', compact(
            'totalIntakes',
            'todayIntakes',
            'step1Approved',
            'readyForStep2',
            'totalAmount',
            'recentIntakes'
        ));
    }

    public function financialStep1(Request $request)
    {
        $today = Carbon::today();

        if (class_exists(BeneficiaryIntake::class)) {
            $query = BeneficiaryIntake::with(['client', 'encoderUser']);

            // STRICT SERVER-SIDE FILTER: Only display intake records processed on the current day
            $query->whereDate('date_processed', $today);

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('control_number', 'like', "%{$search}%")
                      ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                      ->orWhere('rep_first_name', 'like', "%{$search}%")
                      ->orWhere('rep_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_barangay', 'like', "%{$search}%");
                });
            }

            $todayIntakesCount = BeneficiaryIntake::whereDate('date_processed', $today)->count();
            $totalIntakes = $todayIntakesCount;

            $recentIntakes = $query->latest('id')->paginate(10)->withQueryString();
        } else {
            $totalIntakes = 0;
            $todayIntakesCount = 0;
            $recentIntakes = collect();
        }

        return view('admin.financial.financialstep1', compact('totalIntakes', 'todayIntakesCount', 'recentIntakes'));
    }

    public function financialStep2(Request $request)
    {
        $today = Carbon::today();

        if (class_exists(BeneficiaryIntake::class)) {
            $query = BeneficiaryIntake::with(['client', 'encoderUser']);

            // STRICT FILTER: Only display intake records processed today (or created today if date_processed is null)
            $query->where(function ($q) use ($today) {
                $q->whereDate('date_processed', $today)
                  ->orWhere(function ($sq) use ($today) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $today);
                  });
            });

            // Search by control number, beneficiary name, representative name, or barangay
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('control_number', 'like', "%{$search}%")
                      ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                      ->orWhere('rep_first_name', 'like', "%{$search}%")
                      ->orWhere('rep_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_barangay', 'like', "%{$search}%");
                });
            }

            // Filter by Barangay
            if ($request->filled('barangay') && $request->barangay !== 'All') {
                $query->where('beneficiary_barangay', $request->barangay);
            }

            // Filter by Beneficiary Category
            if ($request->filled('category') && $request->category !== 'All') {
                $cat = $request->category;
                $query->where(function ($q) use ($cat) {
                    $q->where('beneficiary_category', $cat)
                      ->orWhereJsonContains('beneficiary_categories', $cat);
                });
            }

            // Filter by Status
            if ($request->filled('status') && $request->status !== 'All') {
                $status = $request->status;
                if ($status === 'unprocessed') {
                    $query->where(function ($q) {
                        $q->where('is_payroll_generated', false)
                          ->orWhereNull('is_payroll_generated');
                    })->whereNull('payroll_record_id');
                } elseif ($status === 'pending_amount' || $status === 'for_assessment') {
                    $query->where(function ($q) {
                        $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
                    });
                } elseif ($status === 'unclaimed') {
                    $query->where('is_payroll_generated', true)
                          ->where(function ($q) {
                              $q->where('claim_status', '!=', 'Claimed')
                                ->orWhereNull('claim_status');
                          });
                } elseif ($status === 'claimed') {
                    $query->where('claim_status', 'Claimed');
                } elseif ($status === 'amount_assigned' || $status === 'ready_payout') {
                    $query->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0);
                }
            }

            // Sorting
            $sort = $request->input('sort', 'date_desc');
            switch ($sort) {
                case 'date_asc':
                    $query->orderBy('date_processed', 'asc')->orderBy('created_at', 'asc');
                    break;
                case 'name_asc':
                    $query->orderBy('beneficiary_last_name', 'asc')->orderBy('beneficiary_first_name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('beneficiary_last_name', 'desc')->orderBy('beneficiary_first_name', 'desc');
                    break;
                case 'amount_desc':
                    $query->orderBy('recommended_amount', 'desc');
                    break;
                case 'amount_asc':
                    $query->orderBy('recommended_amount', 'asc');
                    break;
                case 'control_asc':
                    $query->orderBy('control_number', 'asc');
                    break;
                case 'control_desc':
                    $query->orderBy('control_number', 'desc');
                    break;
                case 'date_desc':
                default:
                    $query->orderBy('date_processed', 'desc')->orderBy('created_at', 'desc');
                    break;
            }

            $todayQueueBase = BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('date_processed', $today)
                  ->orWhere(function ($sq) use ($today) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $today);
                  });
            });

            $todayQueueCount = (clone $todayQueueBase)->count();
            $totalQueueCount = BeneficiaryIntake::count();

            $pendingAmountCount = (clone $todayQueueBase)->where(function ($q) {
                $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
            })->count();

            $unprocessedCount = (clone $todayQueueBase)->where(function ($q) {
                $q->where('is_payroll_generated', false)
                  ->orWhereNull('is_payroll_generated');
            })->whereNull('payroll_record_id')->count();
            $unprocessedIntakesCount = $unprocessedCount;

            $unclaimedCount = (clone $todayQueueBase)->where('is_payroll_generated', true)
                ->where(function ($q) {
                    $q->where('claim_status', '!=', 'Claimed')
                      ->orWhereNull('claim_status');
                })->count();

            $claimedCount = (clone $todayQueueBase)->where('claim_status', 'Claimed')->count();

            $pendingPayoutCount = (clone $todayQueueBase)->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count();

            $totalRecommendedAmount = (clone $todayQueueBase)->sum('recommended_amount') ?? 0;

            $intakes = $query->paginate(15)->withQueryString();
        } else {
            $totalQueueCount = 0;
            $todayQueueCount = 0;
            $pendingAmountCount = 0;
            $unprocessedCount = 0;
            $unprocessedIntakesCount = 0;
            $unclaimedCount = 0;
            $claimedCount = 0;
            $pendingPayoutCount = 0;
            $totalRecommendedAmount = 0;
            $intakes = collect();
        }

        $barangays = [
            'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Acacia', 'Anabu',
            'Balite I', 'Balite II', 'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho',
            'Caballero', 'Carmen', 'Hukay', 'Iba', 'Kalubkob', 'Kaong', 'Lalaan I',
            'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil', 'Maguyam', 'Malabag', 'Malaking Tatyao',
            'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I',
            'San Vicente II', 'Santol', 'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II',
            'Tubuan III', 'Ulat', 'Yakal'
        ];

        $categories = [
            'Solo Parents',
            'Indigenous People',
            'PWD',
            '4PS DSWD Beneficiary',
            'LGBTQIA+',
            'Psychosocial/Mental/Learning Disability',
            'Stateless Person/Asylum Seekers/Refugees',
            'Senior Citizen',
            'Indigent Resident',
            'Others',
        ];

        return view('admin.financial.financialstep2', compact(
            'intakes',
            'totalQueueCount',
            'todayQueueCount',
            'pendingAmountCount',
            'unprocessedCount',
            'unprocessedIntakesCount',
            'unclaimedCount',
            'claimedCount',
            'pendingPayoutCount',
            'totalRecommendedAmount',
            'barangays',
            'categories'
        ));
    }

    /**
     * Dedicated All Intakes page for Step 2 users to view and review all General Intakes from Step 1.
     */
    public function financialStep2AllIntakes(Request $request)
    {
        $today = Carbon::today();

        if (class_exists(BeneficiaryIntake::class)) {
            $query = BeneficiaryIntake::with(['client', 'encoderUser']);

            // Search by control number, beneficiary name, representative name, or barangay
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('control_number', 'like', "%{$search}%")
                      ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                      ->orWhere('rep_first_name', 'like', "%{$search}%")
                      ->orWhere('rep_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_barangay', 'like', "%{$search}%");
                });
            }

            // Filter by Barangay
            if ($request->filled('barangay') && $request->barangay !== 'All') {
                $query->where('beneficiary_barangay', $request->barangay);
            }

            // Filter by Beneficiary Category
            if ($request->filled('category') && $request->category !== 'All') {
                $cat = $request->category;
                $query->where(function ($q) use ($cat) {
                    $q->where('beneficiary_category', $cat)
                      ->orWhereJsonContains('beneficiary_categories', $cat);
                });
            }

            // Filter by Status
            if ($request->filled('status') && $request->status !== 'All') {
                $status = $request->status;
                if ($status === 'pending_amount' || $status === 'for_assessment') {
                    $query->where(function ($q) {
                        $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
                    });
                } elseif ($status === 'unclaimed') {
                    $query->where('is_payroll_generated', true)
                          ->where(function ($q) {
                              $q->where('claim_status', '!=', 'Claimed')
                                ->orWhereNull('claim_status');
                          });
                } elseif ($status === 'claimed') {
                    $query->where('claim_status', 'Claimed');
                } elseif ($status === 'amount_assigned' || $status === 'ready_payout') {
                    $query->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0);
                }
            }

            // Filter by Month & Year (e.g. 'YYYY-MM') or fallback Exact Date
            if ($request->filled('month')) {
                $monthInput = trim($request->month);
                $parts = explode('-', $monthInput);
                if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    $year = (int) $parts[0];
                    $monthNum = (int) $parts[1];
                    $query->where(function ($q) use ($year, $monthNum) {
                        $q->where(function ($sq) use ($year, $monthNum) {
                            $sq->whereNotNull('date_processed')
                               ->whereYear('date_processed', $year)
                               ->whereMonth('date_processed', $monthNum);
                        })->orWhere(function ($sq) use ($year, $monthNum) {
                            $sq->whereNull('date_processed')
                               ->whereYear('created_at', $year)
                               ->whereMonth('created_at', $monthNum);
                        });
                    });
                }
            } elseif ($request->filled('date')) {
                try {
                    $filterDate = Carbon::parse($request->date)->toDateString();
                    $query->where(function ($q) use ($filterDate) {
                        $q->where(function ($sq) use ($filterDate) {
                            $sq->whereNotNull('date_processed')->whereDate('date_processed', $filterDate);
                        })->orWhere(function ($sq) use ($filterDate) {
                            $sq->whereNull('date_processed')->whereDate('created_at', $filterDate);
                        });
                    });
                } catch (\Exception $e) {
                    // Ignore invalid date string
                }
            }

            // Sorting
            $sort = $request->input('sort', 'date_desc');
            switch ($sort) {
                case 'date_asc':
                    $query->orderBy('date_processed', 'asc')->orderBy('created_at', 'asc');
                    break;
                case 'name_asc':
                    $query->orderBy('beneficiary_last_name', 'asc')->orderBy('beneficiary_first_name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('beneficiary_last_name', 'desc')->orderBy('beneficiary_first_name', 'desc');
                    break;
                case 'control_asc':
                    $query->orderBy('control_number', 'asc');
                    break;
                case 'control_desc':
                    $query->orderBy('control_number', 'desc');
                    break;
                case 'date_desc':
                default:
                    $query->orderBy('date_processed', 'desc')->orderBy('created_at', 'desc');
                    break;
            }

            $totalIntakesCount = BeneficiaryIntake::count();
            $todayIntakesCount = BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                  ->orWhereDate('date_processed', $today);
            })->count();

            $pendingAmountCount = BeneficiaryIntake::where(function ($q) {
                $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
            })->count();

            $unclaimedCount = BeneficiaryIntake::where('is_payroll_generated', true)
                ->where(function ($q) {
                    $q->where('claim_status', '!=', 'Claimed')
                      ->orWhereNull('claim_status');
                })->count();

            $claimedCount = BeneficiaryIntake::where('claim_status', 'Claimed')->count();

            $indigentCount = BeneficiaryIntake::where(function ($q) {
                $q->where('beneficiary_category', 'Indigent Resident')
                  ->orWhereJsonContains('beneficiary_categories', 'Indigent Resident');
            })->count();

            $specialSectorsCount = BeneficiaryIntake::where(function ($q) {
                $q->whereNotNull('beneficiary_category')
                  ->where('beneficiary_category', '!=', 'Indigent Resident');
            })->count();

            $intakes = $query->paginate(15)->withQueryString();
        } else {
            $totalIntakesCount = 0;
            $todayIntakesCount = 0;
            $pendingAmountCount = 0;
            $unclaimedCount = 0;
            $claimedCount = 0;
            $indigentCount = 0;
            $specialSectorsCount = 0;
            $intakes = collect();
        }

        $barangays = [
            'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Acacia', 'Anabu',
            'Balite I', 'Balite II', 'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho',
            'Caballero', 'Carmen', 'Hukay', 'Iba', 'Kalubkob', 'Kaong', 'Lalaan I',
            'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil', 'Maguyam', 'Malabag', 'Malaking Tatyao',
            'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I',
            'San Vicente II', 'Santol', 'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II',
            'Tubuan III', 'Ulat', 'Yakal'
        ];

        $categories = [
            'Solo Parents',
            'Indigenous People',
            'PWD',
            '4PS DSWD Beneficiary',
            'LGBTQIA+',
            'Psychosocial/Mental/Learning Disability',
            'Stateless Person/Asylum Seekers/Refugees',
            'Senior Citizen',
            'Indigent Resident',
            'Others',
        ];

        return view('admin.financial.financialstep2-all-intakes', compact(
            'intakes',
            'totalIntakesCount',
            'todayIntakesCount',
            'pendingAmountCount',
            'unclaimedCount',
            'claimedCount',
            'indigentCount',
            'specialSectorsCount',
            'barangays',
            'categories'
        ));
    }

    public function statistics()
    {
        $totalIntakes = BeneficiaryIntake::count();

        // 1. Monthly Intake Cases (Last 12 Months)
        $monthlyIntakes = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthKey = $month->format('M Y');
            
            $count = BeneficiaryIntake::whereYear('date_processed', $month->year)
                ->whereMonth('date_processed', $month->month)
                ->count();

            if ($count === 0) {
                $count = BeneficiaryIntake::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
            }

            $monthlyIntakes[$monthKey] = $count;
        }

        // 2. Beneficiaries Breakdown by Barangay
        $barangayBreakdown = BeneficiaryIntake::select('beneficiary_barangay', DB::raw('count(*) as count'))
            ->whereNotNull('beneficiary_barangay')
            ->where('beneficiary_barangay', '!=', '')
            ->groupBy('beneficiary_barangay')
            ->orderByDesc('count')
            ->limit(12)
            ->pluck('count', 'beneficiary_barangay')
            ->toArray();

        // 3. Comparison of Male and Female Beneficiaries
        $maleCount = BeneficiaryIntake::where('beneficiary_sex', 'Male')->count();
        $femaleCount = BeneficiaryIntake::where('beneficiary_sex', 'Female')->count();
        $otherGenderCount = BeneficiaryIntake::whereNotIn('beneficiary_sex', ['Male', 'Female'])
            ->whereNotNull('beneficiary_sex')
            ->count();

        $genderBreakdown = [
            'Male' => $maleCount,
            'Female' => $femaleCount,
        ];
        if ($otherGenderCount > 0) {
            $genderBreakdown['Other / Unspecified'] = $otherGenderCount;
        }

        // 4. Summary of Types of Medical Concerns / Issues
        $medicalConcernsSummary = BeneficiaryIntake::select('assistance_purpose', DB::raw('count(*) as count'))
            ->whereNotNull('assistance_purpose')
            ->where('assistance_purpose', '!=', '')
            ->groupBy('assistance_purpose')
            ->orderByDesc('count')
            ->pluck('count', 'assistance_purpose')
            ->toArray();

        // 5. "Dahilan ng Paghingi ng Tulong" (Reasons for Seeking Assistance)
        $reasonsAssistance = BeneficiaryIntake::select('assistance_purpose', DB::raw('count(*) as total_cases'))
            ->whereNotNull('assistance_purpose')
            ->where('assistance_purpose', '!=', '')
            ->groupBy('assistance_purpose')
            ->orderByDesc('total_cases')
            ->get();

        return view('admin.financial.financialstep1statistics', compact(
            'totalIntakes',
            'monthlyIntakes',
            'barangayBreakdown',
            'genderBreakdown',
            'medicalConcernsSummary',
            'reasonsAssistance'
        ));
    }

    /**
     * Dedicated Step 2 Statistics & Analytics Page.
     * Computes real-time metrics, barangay distributions, gender breakdown,
     * and ranked sector financial assistance statistics.
     */
    public function financialStep2Statistics(Request $request)
    {
        $totalBeneficiaries = BeneficiaryIntake::count();
        $totalAmount = (float) BeneficiaryIntake::sum('recommended_amount');
        $totalClaimed = BeneficiaryIntake::where('claim_status', 'Claimed')->count();
        $totalClaimedAmount = (float) BeneficiaryIntake::where('claim_status', 'Claimed')->sum('recommended_amount');
        $totalUnclaimed = BeneficiaryIntake::where('is_payroll_generated', true)
            ->where(function ($q) {
                $q->where('claim_status', '!=', 'Claimed')->orWhereNull('claim_status');
            })->count();
        $totalInPayroll = BeneficiaryIntake::where('is_payroll_generated', true)->count();
        $totalPayrollBatches = FinancialPayrollRecord::count();

        // 1. Number of beneficiaries who received financial assistance per Barangay
        $barangayRaw = BeneficiaryIntake::select(
            'beneficiary_barangay',
            DB::raw('count(*) as total_beneficiaries'),
            DB::raw('SUM(CASE WHEN recommended_amount > 0 THEN recommended_amount ELSE 0 END) as total_amount'),
            DB::raw('SUM(CASE WHEN claim_status = "Claimed" THEN 1 ELSE 0 END) as claimed_count')
        )
        ->whereNotNull('beneficiary_barangay')
        ->where('beneficiary_barangay', '!=', '')
        ->groupBy('beneficiary_barangay')
        ->orderByDesc('total_beneficiaries')
        ->get();

        $barangayStats = [];
        $topBarangay = null;
        $topBarangayCount = 0;
        foreach ($barangayRaw as $b) {
            $bCount = (int) $b->total_beneficiaries;
            $barangayStats[$b->beneficiary_barangay] = [
                'name' => $b->beneficiary_barangay,
                'beneficiaries' => $bCount,
                'amount' => (float) $b->total_amount,
                'formatted_amount' => '₱' . number_format((float) $b->total_amount, 2),
                'claimed' => (int) $b->claimed_count,
                'percentage' => $totalBeneficiaries > 0 ? round(($bCount / $totalBeneficiaries) * 100, 1) : 0,
            ];
            if (!$topBarangay) {
                $topBarangay = $b->beneficiary_barangay;
                $topBarangayCount = $bCount;
            }
        }

        // 2. Male vs. Female beneficiaries
        $maleCount = BeneficiaryIntake::where('beneficiary_sex', 'Male')->count();
        $femaleCount = BeneficiaryIntake::where('beneficiary_sex', 'Female')->count();
        $otherGenderCount = BeneficiaryIntake::whereNotIn('beneficiary_sex', ['Male', 'Female'])
            ->whereNotNull('beneficiary_sex')
            ->where('beneficiary_sex', '!=', '')
            ->count();

        $genderBreakdown = [
            'Male' => $maleCount,
            'Female' => $femaleCount,
        ];
        if ($otherGenderCount > 0) {
            $genderBreakdown['Other / Unspecified'] = $otherGenderCount;
        }

        $malePercentage = $totalBeneficiaries > 0 ? round(($maleCount / $totalBeneficiaries) * 100, 1) : 0;
        $femalePercentage = $totalBeneficiaries > 0 ? round(($femaleCount / $totalBeneficiaries) * 100, 1) : 0;

        // 3. Financial assistance by sector (arranged from highest to lowest)
        $intakes = BeneficiaryIntake::select('beneficiary_category', 'beneficiary_categories', 'recommended_amount')->cursor();

        $sectorCounts = [];
        $sectorAmounts = [];

        foreach ($intakes as $intake) {
            $amount = (float) ($intake->recommended_amount ?? 0);
            $cats = [];

            if (!empty(trim($intake->beneficiary_category ?? ''))) {
                $cats[] = trim($intake->beneficiary_category);
            }
            if (is_array($intake->beneficiary_categories)) {
                foreach ($intake->beneficiary_categories as $c) {
                    $trimmed = trim($c);
                    if (!empty($trimmed) && !in_array($trimmed, $cats)) {
                        $cats[] = $trimmed;
                    }
                }
            }
            if (empty($cats)) {
                $cats[] = 'Indigent Resident';
            }

            foreach ($cats as $cat) {
                if (!isset($sectorCounts[$cat])) {
                    $sectorCounts[$cat] = 0;
                    $sectorAmounts[$cat] = 0.0;
                }
                $sectorCounts[$cat]++;
                $sectorAmounts[$cat] += $amount;
            }
        }

        // Arrange sectors strictly from highest to lowest by beneficiary count
        arsort($sectorCounts);

        $sectorRanked = [];
        $topSector = null;
        $topSectorCount = 0;
        foreach ($sectorCounts as $sec => $cnt) {
            $sectorRanked[] = [
                'sector' => $sec,
                'beneficiaries' => $cnt,
                'amount' => $sectorAmounts[$sec] ?? 0.0,
                'formatted_amount' => '₱' . number_format($sectorAmounts[$sec] ?? 0.0, 2),
                'percentage' => $totalBeneficiaries > 0 ? round(($cnt / $totalBeneficiaries) * 100, 1) : 0,
            ];
            if (!$topSector) {
                $topSector = $sec;
                $topSectorCount = $cnt;
            }
        }

        // 4. Most common medical concerns & assistance reasons (arranged from highest to lowest)
        $medicalCounts = [];
        $medicalAmounts = [];

        $medicalIntakes = BeneficiaryIntake::select(
            'medical_conditions',
            'medical_condition_other',
            'assistance_purpose',
            'purpose',
            'purpose_other',
            'recommended_amount'
        )->cursor();

        foreach ($medicalIntakes as $intake) {
            $amount = (float) ($intake->recommended_amount ?? 0);
            $concerns = [];

            // Conditions array from checkboxes
            if (is_array($intake->medical_conditions)) {
                foreach ($intake->medical_conditions as $c) {
                    $trimmed = trim($c);
                    if (!empty($trimmed) && strtolower($trimmed) !== 'other' && !in_array($trimmed, $concerns)) {
                        $concerns[] = $trimmed;
                    }
                }
            }

            // Other medical condition specified
            if (!empty(trim($intake->medical_condition_other ?? ''))) {
                $otherCond = trim($intake->medical_condition_other);
                if (!in_array($otherCond, $concerns)) {
                    $concerns[] = $otherCond;
                }
            }

            // Fallback or complement with assistance_purpose / purpose if conditions array is empty
            if (empty($concerns)) {
                if (!empty(trim($intake->assistance_purpose ?? '')) && !in_array(trim($intake->assistance_purpose), ['Others', 'Other Medical Conditions'])) {
                    $concerns[] = trim($intake->assistance_purpose);
                } elseif (!empty(trim($intake->purpose_other ?? ''))) {
                    $concerns[] = trim($intake->purpose_other);
                } elseif (!empty(trim($intake->purpose ?? ''))) {
                    $concerns[] = trim($intake->purpose);
                }
            }

            if (empty($concerns)) {
                $concerns[] = 'General Medical Assistance';
            }

            foreach ($concerns as $concern) {
                if (!isset($medicalCounts[$concern])) {
                    $medicalCounts[$concern] = 0;
                    $medicalAmounts[$concern] = 0.0;
                }
                $medicalCounts[$concern]++;
                $medicalAmounts[$concern] += $amount;
            }
        }

        // Arrange medical concerns strictly from highest to lowest by beneficiary count
        arsort($medicalCounts);

        $medicalRanked = [];
        $topMedicalConcern = null;
        $topMedicalConcernCount = 0;
        foreach ($medicalCounts as $concern => $cnt) {
            $medicalRanked[] = [
                'concern' => $concern,
                'beneficiaries' => $cnt,
                'amount' => $medicalAmounts[$concern] ?? 0.0,
                'formatted_amount' => '₱' . number_format($medicalAmounts[$concern] ?? 0.0, 2),
                'percentage' => $totalBeneficiaries > 0 ? round(($cnt / $totalBeneficiaries) * 100, 1) : 0,
            ];
            if (!$topMedicalConcern) {
                $topMedicalConcern = $concern;
                $topMedicalConcernCount = $cnt;
            }
        }

        return view('admin.financial.financialstep2-statistics', compact(
            'totalBeneficiaries',
            'totalAmount',
            'totalClaimed',
            'totalClaimedAmount',
            'totalUnclaimed',
            'totalInPayroll',
            'totalPayrollBatches',
            'barangayStats',
            'topBarangay',
            'topBarangayCount',
            'genderBreakdown',
            'maleCount',
            'femaleCount',
            'otherGenderCount',
            'malePercentage',
            'femalePercentage',
            'sectorRanked',
            'topSector',
            'topSectorCount',
            'medicalRanked',
            'topMedicalConcern',
            'topMedicalConcernCount'
        ));
    }

    /**
     * Dedicated Payroll Generation page for Step 2 users to encode financial assistance amounts
     * and verify/generate printable payroll for all intakes recorded for the current day.
     */
    public function financialStep2Payroll(Request $request)
    {
        $today = Carbon::today();
        $targetDate = $request->filled('date') ? Carbon::parse($request->date) : $today;

        if (class_exists(BeneficiaryIntake::class)) {
            $query = BeneficiaryIntake::with(['client', 'encoderUser']);

            // DUPLICATE PAYROLL PREVENTION: Only display ungenerated / new unprocessed intakes
            $query->where(function ($q) {
                $q->where('is_payroll_generated', false)
                  ->orWhereNull('is_payroll_generated');
            })->whereNull('payroll_record_id');

            // STRICT FILTER: Display intake records processed or created on the current day (or filtered date)
            $query->where(function ($q) use ($targetDate) {
                $q->whereDate('date_processed', $targetDate)
                  ->orWhere(function ($sq) use ($targetDate) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $targetDate);
                  });
            });

            // Search filter
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('control_number', 'like', "%{$search}%")
                      ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                      ->orWhere('rep_first_name', 'like', "%{$search}%")
                      ->orWhere('rep_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_barangay', 'like', "%{$search}%");
                });
            }

            // Filter by Barangay
            if ($request->filled('barangay') && $request->barangay !== 'All') {
                $query->where('beneficiary_barangay', $request->barangay);
            }

            // Filter by Beneficiary Category
            if ($request->filled('category') && $request->category !== 'All') {
                $cat = $request->category;
                $query->where(function ($q) use ($cat) {
                    $q->where('beneficiary_category', $cat)
                      ->orWhereJsonContains('beneficiary_categories', $cat);
                });
            }

            // Filter by Encoding Status
            if ($request->filled('status') && $request->status !== 'All') {
                if ($request->status === 'encoded') {
                    $query->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0);
                } elseif ($request->status === 'pending' || $request->status === 'pending_amount') {
                    $query->where(function ($q) {
                        $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
                    });
                }
            }

            // Sorting
            $sort = $request->input('sort', 'control_asc');
            switch ($sort) {
                case 'date_asc':
                    $query->orderBy('date_processed', 'asc')->orderBy('created_at', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('date_processed', 'desc')->orderBy('created_at', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('beneficiary_last_name', 'asc')->orderBy('beneficiary_first_name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('beneficiary_last_name', 'desc')->orderBy('beneficiary_first_name', 'desc');
                    break;
                case 'amount_desc':
                    $query->orderBy('recommended_amount', 'desc');
                    break;
                case 'amount_asc':
                    $query->orderBy('recommended_amount', 'asc');
                    break;
                case 'control_desc':
                    $query->orderBy('control_number', 'desc');
                    break;
                case 'control_asc':
                default:
                    $query->orderBy('control_number', 'asc')->orderBy('id', 'asc');
                    break;
            }

            // Calculate overall ungenerated metrics for the target date
            $targetIntakesBase = BeneficiaryIntake::where(function ($q) {
                $q->where('is_payroll_generated', false)
                  ->orWhereNull('is_payroll_generated');
            })->whereNull('payroll_record_id')->where(function ($q) use ($targetDate) {
                $q->whereDate('date_processed', $targetDate)
                  ->orWhere(function ($sq) use ($targetDate) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $targetDate);
                  });
            });

            $totalTodayCount = (clone $targetIntakesBase)->count();
            $encodedCount = (clone $targetIntakesBase)->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count();
            $pendingCount = (clone $targetIntakesBase)->where(function ($q) {
                $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
            })->count();
            $totalPayrollAmount = (clone $targetIntakesBase)->whereNotNull('recommended_amount')->sum('recommended_amount') ?? 0;
            $allAmountsEncoded = ($totalTodayCount > 0 && $pendingCount === 0);

            $intakes = $query->get();
        } else {
            $totalTodayCount = 0;
            $encodedCount = 0;
            $pendingCount = 0;
            $totalPayrollAmount = 0;
            $allAmountsEncoded = false;
            $intakes = collect();
        }

        $barangays = [
            'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Acacia', 'Anabu',
            'Balite I', 'Balite II', 'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho',
            'Caballero', 'Carmen', 'Hukay', 'Iba', 'Kalubkob', 'Kaong', 'Lalaan I',
            'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil', 'Maguyam', 'Malabag', 'Malaking Tatyao',
            'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I',
            'San Vicente II', 'Santol', 'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II',
            'Tubuan III', 'Ulat', 'Yakal'
        ];

        $categories = [
            'Solo Parents',
            'Indigenous People',
            'PWD',
            '4PS DSWD Beneficiary',
            'LGBTQIA+',
            'Psychosocial/Mental/Learning Disability',
            'Stateless Person/Asylum Seekers/Refugees',
            'Senior Citizen',
            'Indigent Resident',
            'Others',
        ];

        return view('admin.financial.financialstep2-payroll', compact(
            'intakes',
            'totalTodayCount',
            'encodedCount',
            'pendingCount',
            'totalPayrollAmount',
            'allAmountsEncoded',
            'barangays',
            'categories',
            'targetDate'
        ));
    }

    /**
     * Update financial assistance amount for a single intake.
     */
    public function updateIntakeAmount(Request $request)
    {
        $request->validate([
            'intake_id' => ['required', 'integer', 'exists:beneficiary_intakes,id'],
            'recommended_amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
        ]);

        $intake = BeneficiaryIntake::findOrFail($request->intake_id);
        
        // Prevent modifying an already generated intake
        if ($intake->is_payroll_generated || $intake->payroll_record_id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "This intake record ({$intake->control_number}) has already been generated into a payroll and cannot be modified.",
                ], 422);
            }
            return redirect()->back()->with('error', "Intake {$intake->control_number} has already been generated into a payroll.");
        }

        $intake->recommended_amount = (float) $request->recommended_amount;
        $intake->save();

        $today = Carbon::today();
        $todayIntakesBase = BeneficiaryIntake::where(function ($q) {
            $q->where('is_payroll_generated', false)
              ->orWhereNull('is_payroll_generated');
        })->whereNull('payroll_record_id')->where(function ($q) use ($today) {
            $q->whereDate('date_processed', $today)
              ->orWhere(function ($sq) use ($today) {
                  $sq->whereNull('date_processed')->whereDate('created_at', $today);
              });
        });

        $totalTodayCount = (clone $todayIntakesBase)->count();
        $encodedCount = (clone $todayIntakesBase)->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count();
        $pendingCount = (clone $todayIntakesBase)->where(function ($q) {
            $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
        })->count();
        $totalPayrollAmount = (clone $todayIntakesBase)->whereNotNull('recommended_amount')->sum('recommended_amount') ?? 0;
        $allAmountsEncoded = ($totalTodayCount > 0 && $pendingCount === 0);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Financial assistance amount of ₱" . number_format($intake->recommended_amount, 2) . " saved for {$intake->control_number}.",
                'intake_id' => $intake->id,
                'control_number' => $intake->control_number,
                'recommended_amount' => (float) $intake->recommended_amount,
                'formatted_amount' => '₱' . number_format($intake->recommended_amount, 2),
                'total_today_count' => $totalTodayCount,
                'encoded_count' => $encodedCount,
                'pending_count' => $pendingCount,
                'total_payroll_amount' => (float) $totalPayrollAmount,
                'formatted_total_payroll_amount' => '₱' . number_format($totalPayrollAmount, 2),
                'all_amounts_encoded' => $allAmountsEncoded,
            ]);
        }

        return redirect()->back()->with('success', "Assistance amount of ₱" . number_format($intake->recommended_amount, 2) . " saved for {$intake->control_number}.");
    }

    /**
     * Bulk update financial assistance amounts for multiple intakes.
     */
    public function bulkUpdateIntakeAmounts(Request $request)
    {
        $request->validate([
            'amounts' => ['required', 'array'],
            'amounts.*' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
        ]);

        $updatedCount = 0;
        DB::transaction(function () use ($request, &$updatedCount) {
            foreach ($request->amounts as $intakeId => $amount) {
                if ($amount !== null && $amount !== '') {
                    $intake = BeneficiaryIntake::find($intakeId);
                    if ($intake && !$intake->is_payroll_generated && !$intake->payroll_record_id) {
                        $intake->recommended_amount = (float) $amount;
                        $intake->save();
                        $updatedCount++;
                    }
                }
            }
        });

        $today = Carbon::today();
        $todayIntakesBase = BeneficiaryIntake::where(function ($q) {
            $q->where('is_payroll_generated', false)
              ->orWhereNull('is_payroll_generated');
        })->whereNull('payroll_record_id')->where(function ($q) use ($today) {
            $q->whereDate('date_processed', $today)
              ->orWhere(function ($sq) use ($today) {
                  $sq->whereNull('date_processed')->whereDate('created_at', $today);
              });
        });

        $totalTodayCount = (clone $todayIntakesBase)->count();
        $encodedCount = (clone $todayIntakesBase)->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count();
        $pendingCount = (clone $todayIntakesBase)->where(function ($q) {
            $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
        })->count();
        $totalPayrollAmount = (clone $todayIntakesBase)->whereNotNull('recommended_amount')->sum('recommended_amount') ?? 0;
        $allAmountsEncoded = ($totalTodayCount > 0 && $pendingCount === 0);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully saved financial assistance amounts for {$updatedCount} intake records.",
                'updated_count' => $updatedCount,
                'total_today_count' => $totalTodayCount,
                'encoded_count' => $encodedCount,
                'pending_count' => $pendingCount,
                'total_payroll_amount' => (float) $totalPayrollAmount,
                'formatted_total_payroll_amount' => '₱' . number_format($totalPayrollAmount, 2),
                'all_amounts_encoded' => $allAmountsEncoded,
            ]);
        }

        return redirect()->back()->with('success', "Successfully updated financial assistance amounts for {$updatedCount} intake records.");
    }

    /**
     * Generate Official Payroll, mark intakes as processed, and create a separate individual Payroll Record.
     * Multiple separate payrolls can be generated on the same day.
     */
    public function generatePayroll(Request $request)
    {
        $today = Carbon::today();
        $targetDate = $request->filled('date') ? Carbon::parse($request->date) : $today;

        // Query eligible intakes for target date that have NOT been generated
        $intakes = BeneficiaryIntake::where(function ($q) {
                $q->where('is_payroll_generated', false)
                  ->orWhereNull('is_payroll_generated');
            })
            ->whereNull('payroll_record_id')
            ->where(function ($q) use ($targetDate) {
                $q->whereDate('date_processed', $targetDate)
                  ->orWhere(function ($sq) use ($targetDate) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $targetDate);
                  });
            })
            ->whereNotNull('recommended_amount')
            ->where('recommended_amount', '>', 0)
            ->get();

        if ($intakes->isEmpty()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eligible ungenerated intakes with encoded amounts were found for ' . $targetDate->format('F d, Y') . '. Either all intakes have already been generated into a payroll or amounts are pending.',
                ], 422);
            }
            return redirect()->back()->with('error', 'No eligible ungenerated intakes found to generate payroll for ' . $targetDate->format('F d, Y') . '.');
        }

        // Duplicate prevention validation: ensure none of the selected intakes are already generated
        $alreadyGenerated = BeneficiaryIntake::whereIn('id', $intakes->pluck('id'))
            ->where(function ($q) {
                $q->where('is_payroll_generated', true)
                  ->orWhereNotNull('payroll_record_id');
            })
            ->exists();

        if ($alreadyGenerated) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplicate payroll error: One or more intakes have already been processed into a payroll.',
                ], 422);
            }
            return redirect()->back()->with('error', 'Duplicate payroll error: One or more intakes have already been processed.');
        }

        // Calculate sequence number for this target date
        $existingCount = FinancialPayrollRecord::whereDate('payroll_date', $targetDate)->count();
        $seqNumber = $existingCount + 1;
        $payrollNumber = 'PAYROLL-' . $targetDate->format('Ymd') . '-' . str_pad((string) $seqNumber, 3, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(uniqid(), -4));

        $officerName = session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Disbursing Officer';
        $userId = session('admin_user_id');

        // Prepare structured snapshot data
        $payrollData = $intakes->map(function ($intake, $index) {
            $beneficiaryName = $intake->beneficiary_full_name ?? 'N/A';
            $hasSepRep = $intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A';
            $repName = $hasSepRep ? $intake->representative_full_name : $beneficiaryName;
            $contactNumber = $hasSepRep ? ($intake->rep_contact_number ?: ($intake->beneficiary_contact_number ?: 'N/A')) : ($intake->beneficiary_contact_number ?: 'N/A');

            return [
                'item_no' => $index + 1,
                'intake_id' => $intake->id,
                'control_number' => $intake->control_number,
                'representative_name' => $repName,
                'beneficiary_name' => $beneficiaryName,
                'barangay' => $intake->beneficiary_barangay ?: 'Silang, Cavite',
                'contact_number' => $contactNumber,
                'amount' => (float) ($intake->recommended_amount ?? 0),
                'formatted_amount' => '₱' . number_format((float) ($intake->recommended_amount ?? 0), 2),
                'is_separate_rep' => $hasSepRep,
                'claim_status' => 'Unclaimed',
                'service_provided' => $intake->service_provided,
                'purpose' => $intake->purpose,
            ];
        })->toArray();

        $payrollRecord = null;

        // Create individual Payroll Record and link intakes in an atomic transaction
        DB::transaction(function () use ($intakes, $targetDate, $seqNumber, $payrollNumber, $officerName, $userId, $payrollData, &$payrollRecord) {
            $payrollRecord = FinancialPayrollRecord::create([
                'payroll_number' => $payrollNumber,
                'payroll_date' => $targetDate->format('Y-m-d'),
                'batch_number' => $seqNumber,
                'generated_by_id' => $userId,
                'generated_by_name' => $officerName,
                'disbursing_officer' => $officerName,
                'total_beneficiaries' => $intakes->count(),
                'total_amount' => $intakes->sum('recommended_amount'),
                'status' => 'Completed',
                'payroll_data' => $payrollData,
            ]);

            BeneficiaryIntake::whereIn('id', $intakes->pluck('id'))
                ->update([
                    'is_payroll_generated' => true,
                    'payroll_generated_at' => Carbon::now(),
                    'payroll_date' => $targetDate->format('Y-m-d'),
                    'payroll_record_id' => $payrollRecord->id,
                    'claim_status' => 'Unclaimed',
                    'claimed_at' => null,
                    'claimed_by' => null,
                ]);
        });

        $count = $intakes->count();
        $totalAmt = $intakes->sum('recommended_amount');
        $formattedAmt = '₱' . number_format($totalAmt, 2);

        $successMsg = "Payroll successfully generated for {$count} beneficiaries ({$formattedAmt}). Processed intakes have been archived to Payroll Records.";
        $redirectUrl = route('admin.financial.financialstep2.payroll-records', [
            'payroll_id' => $payrollRecord ? $payrollRecord->id : null,
            'date' => $targetDate->format('Y-m-d'),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'payroll_id' => $payrollRecord ? $payrollRecord->id : null,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', $successMsg);
    }

    /**
     * Generate and display the printable Payroll document.
     */
    public function printPayroll(Request $request)
    {
        $selectedPayrollRecord = null;
        if ($request->filled('payroll_id')) {
            $selectedPayrollRecord = FinancialPayrollRecord::find($request->payroll_id);
        } elseif ($request->filled('date') && $request->filled('batch')) {
            $selectedPayrollRecord = FinancialPayrollRecord::whereDate('payroll_date', $request->date)
                ->where('batch_number', (int) $request->batch)
                ->first();
        } elseif ($request->filled('date')) {
            $selectedPayrollRecord = FinancialPayrollRecord::whereDate('payroll_date', $request->date)
                ->latest('created_at')
                ->first();
        }

        if ($selectedPayrollRecord) {
            $targetDate = Carbon::parse($selectedPayrollRecord->payroll_date);
            $payrollRefNo = $selectedPayrollRecord->payroll_number;
            $disbursingOfficer = $selectedPayrollRecord->disbursing_officer ?: (session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Disbursing Officer');
            $generatedTime = $selectedPayrollRecord->created_at ? $selectedPayrollRecord->created_at->format('h:i A') : null;
        } else {
            $today = Carbon::today();
            $targetDate = $request->filled('date') ? Carbon::parse($request->date) : $today;
            $disbursingOfficer = session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Disbursing Officer';
            $payrollRefNo = 'PAYROLL-' . $targetDate->format('Ymd') . '-' . str_pad((string) rand(100, 999), 3, '0', STR_PAD_LEFT);
            $generatedTime = null;
        }

        $query = BeneficiaryIntake::with(['client', 'encoderUser', 'payrollRecord']);

        if ($selectedPayrollRecord && $request->filled('payroll_id')) {
            $query->where('is_payroll_generated', true)
                  ->where(function ($q) use ($selectedPayrollRecord, $targetDate) {
                      $q->where('payroll_record_id', $selectedPayrollRecord->id)
                        ->orWhere(function ($sq) use ($targetDate) {
                            $sq->whereNull('payroll_record_id')
                               ->where(function ($dq) use ($targetDate) {
                                   $dq->whereDate('payroll_date', $targetDate)
                                      ->orWhereDate('date_processed', $targetDate);
                               });
                        });
                  });
        } elseif ($selectedPayrollRecord) {
            $query->where(function ($q) use ($selectedPayrollRecord, $targetDate) {
                $q->where(function ($sq) use ($selectedPayrollRecord, $targetDate) {
                    $sq->where('is_payroll_generated', true)
                       ->where(function ($ssq) use ($selectedPayrollRecord, $targetDate) {
                           $ssq->where('payroll_record_id', $selectedPayrollRecord->id)
                               ->orWhere(function ($dq) use ($targetDate) {
                                   $dq->whereNull('payroll_record_id')
                                      ->where(function ($ddq) use ($targetDate) {
                                          $ddq->whereDate('payroll_date', $targetDate)
                                              ->orWhereDate('date_processed', $targetDate);
                                      });
                               });
                       });
                })->orWhere(function ($uq) use ($targetDate) {
                    $uq->where(function ($ssq) {
                        $ssq->where('is_payroll_generated', false)
                            ->orWhereNull('is_payroll_generated');
                    })->where(function ($dq) use ($targetDate) {
                        $dq->whereDate('date_processed', $targetDate)
                           ->orWhere(function ($sq) use ($targetDate) {
                               $sq->whereNull('date_processed')->whereDate('created_at', $targetDate);
                           });
                    })->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0);
                });
            });
        } else {
            // When printing from Payroll Generator (either before or during generation):
            $query->where(function ($q) use ($targetDate) {
                $q->whereDate('date_processed', $targetDate)
                  ->orWhere(function ($sq) use ($targetDate) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $targetDate);
                  });
            })->where(function ($q) {
                $q->where('is_payroll_generated', true)
                  ->orWhere(function ($sq) {
                      $sq->where(function ($ssq) {
                          $ssq->where('is_payroll_generated', false)
                              ->orWhereNull('is_payroll_generated');
                      })->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0);
                  });
            });
        }

        // Filter by Barangay if requested
        if ($request->filled('barangay') && $request->barangay !== 'All') {
            $query->where('beneficiary_barangay', $request->barangay);
        }

        // Sorting
        $sort = $request->input('sort', 'control_asc');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('beneficiary_last_name', 'asc')->orderBy('beneficiary_first_name', 'asc');
                break;
            case 'control_desc':
                $query->orderBy('control_number', 'desc');
                break;
            case 'control_asc':
            default:
                $query->orderBy('control_number', 'asc')->orderBy('id', 'asc');
                break;
        }

        $intakes = $query->get();

        // Prepare payroll rows with strict adherence to Representative Name Rule
        $payrollRows = $intakes->map(function ($intake, $index) {
            $beneficiaryName = $intake->beneficiary_full_name ?? 'N/A';
            
            if ($intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A') {
                $representativeName = $intake->representative_full_name;
                $contactNumber = $intake->rep_contact_number ?: ($intake->beneficiary_contact_number ?: 'N/A');
                $isSeparateRep = true;
            } else {
                $representativeName = $beneficiaryName;
                $contactNumber = $intake->beneficiary_contact_number ?: 'N/A';
                $isSeparateRep = false;
            }

            return (object) [
                'item_no' => $index + 1,
                'control_number' => $intake->control_number,
                'representative_name' => $representativeName,
                'beneficiary_name' => $beneficiaryName,
                'barangay' => $intake->beneficiary_barangay ?: 'Silang, Cavite',
                'contact_number' => $contactNumber,
                'amount' => (float) ($intake->recommended_amount ?? 0),
                'formatted_amount' => '₱' . number_format((float) ($intake->recommended_amount ?? 0), 2),
                'is_separate_rep' => $isSeparateRep,
                'raw_intake' => $intake,
            ];
        });

        $totalBeneficiaries = $payrollRows->count();
        $totalAmount = $payrollRows->sum('amount');
        $formattedTotalAmount = '₱' . number_format($totalAmount, 2);
        
        $missingAmountCount = $payrollRows->where('amount', '<=', 0)->count();
        $payrollDate = $targetDate->format('F d, Y');

        return view('admin.financial.financialstep2-payroll-print', compact(
            'payrollRows',
            'totalBeneficiaries',
            'totalAmount',
            'formattedTotalAmount',
            'missingAmountCount',
            'disbursingOfficer',
            'payrollDate',
            'payrollRefNo',
            'targetDate',
            'selectedPayrollRecord',
            'generatedTime'
        ));
    }

    /**
     * Dedicated Step 2 Payroll Records Page.
     * Displays all generated payroll records on the page itself with collapsible tables.
     */
    public function financialStep2PayrollRecords(Request $request)
    {
        $today = Carbon::today();

        // 1. Ensure any unlinked generated intakes are linked to a FinancialPayrollRecord for that date
        $unlinkedIntakes = BeneficiaryIntake::where('is_payroll_generated', true)
            ->whereNull('payroll_record_id')
            ->get();

        if ($unlinkedIntakes->isNotEmpty()) {
            $groupedByDate = $unlinkedIntakes->groupBy(function ($intake) {
                return $intake->payroll_date 
                    ? $intake->payroll_date->format('Y-m-d') 
                    : ($intake->date_processed ? $intake->date_processed->format('Y-m-d') : $intake->created_at->format('Y-m-d'));
            });

            foreach ($groupedByDate as $pDate => $dateIntakes) {
                $payrollRecord = FinancialPayrollRecord::whereDate('payroll_date', $pDate)->first();
                if (!$payrollRecord) {
                    $payrollRecord = FinancialPayrollRecord::create([
                        'payroll_number' => 'PAYROLL-' . str_replace('-', '', $pDate) . '-001-' . strtoupper(substr(uniqid(), -4)),
                        'payroll_date' => $pDate,
                        'batch_number' => 1,
                        'disbursing_officer' => session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Disbursing Officer',
                        'total_beneficiaries' => $dateIntakes->count(),
                        'total_amount' => $dateIntakes->sum('recommended_amount'),
                        'status' => 'Completed',
                    ]);
                }
                BeneficiaryIntake::whereIn('id', $dateIntakes->pluck('id'))->update([
                    'payroll_record_id' => $payrollRecord->id,
                    'payroll_date' => $pDate,
                ]);
            }
        }

        // Determine if this is a Date-Specific View (when 'date' parameter or route is provided, or payroll_id)
        $filterDateInput = $request->route('date') ?? $request->date;
        $isDateView = !empty($filterDateInput) || $request->filled('payroll_id');
        $selectedDate = !empty($filterDateInput) ? Carbon::parse($filterDateInput) : null;

        if (!$selectedDate && $request->filled('payroll_id')) {
            $pRec = FinancialPayrollRecord::find($request->payroll_id);
            if ($pRec && $pRec->payroll_date) {
                $selectedDate = Carbon::parse($pRec->payroll_date);
            }
        }
        $filterDateStr = $selectedDate ? $selectedDate->format('Y-m-d') : null;

        $barangays = [
            'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Acacia', 'Anabu',
            'Balite I', 'Balite II', 'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho',
            'Caballero', 'Carmen', 'Hukay', 'Iba', 'Kalubkob', 'Kaong', 'Lalaan I',
            'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil', 'Maguyam', 'Malabag', 'Malaking Tatyao',
            'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I',
            'San Vicente II', 'Santol', 'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II',
            'Tubuan III', 'Ulat', 'Yakal'
        ];

        if ($isDateView) {
            // Dedicated Date View: Load and format records specifically for this selected date
            $recordsQuery = FinancialPayrollRecord::orderBy('created_at', 'asc');

            if ($filterDateStr && !$request->filled('payroll_id')) {
                $recordsQuery->whereDate('payroll_date', $filterDateStr);
            }

            if ($request->filled('payroll_id')) {
                $recordsQuery->where('id', (int) $request->payroll_id);
            }

            $rawRecords = $recordsQuery->get();
            $allDatePayrolls = FinancialPayrollRecord::whereDate('payroll_date', $filterDateStr)->orderBy('created_at', 'asc')->get();

            $payrollRecords = collect();
            $grandTotalBeneficiaries = 0;
            $grandTotalAmount = 0.0;
            $grandTotalClaimedCount = 0;
            $grandTotalUnclaimedCount = 0;
            $grandTotalClaimedAmount = 0.0;
            $grandTotalUnclaimedAmount = 0.0;

            foreach ($rawRecords as $record) {
                $intakesQuery = BeneficiaryIntake::with(['client', 'encoderUser', 'payrollRecord', 'latestMessage'])
                    ->where('is_payroll_generated', true)
                    ->where(function ($q) use ($record) {
                        $q->where('payroll_record_id', $record->id)
                          ->orWhere(function ($sq) use ($record) {
                              $sq->whereNull('payroll_record_id')->whereDate('payroll_date', $record->payroll_date);
                          });
                    });

                if ($request->filled('search')) {
                    $search = trim($request->search);
                    $intakesQuery->where(function ($q) use ($search) {
                        $q->where('control_number', 'like', "%{$search}%")
                          ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                          ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                          ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                          ->orWhere('rep_first_name', 'like', "%{$search}%")
                          ->orWhere('rep_last_name', 'like', "%{$search}%")
                          ->orWhere('beneficiary_barangay', 'like', "%{$search}%");
                    });
                }

                if ($request->filled('barangay') && $request->barangay !== 'All') {
                    $intakesQuery->where('beneficiary_barangay', $request->barangay);
                }

                $claimStatusInput = $request->claim_status ?? ($request->status === 'claimed' ? 'Claimed' : ($request->status === 'unclaimed' ? 'Unclaimed' : null));
                if (!empty($claimStatusInput) && in_array($claimStatusInput, ['Claimed', 'Unclaimed'])) {
                    if ($claimStatusInput === 'Claimed') {
                        $intakesQuery->where('claim_status', 'Claimed');
                    } else {
                        $intakesQuery->where(function ($q) {
                            $q->where('claim_status', 'Unclaimed')
                              ->orWhereNull('claim_status');
                        });
                    }
                }

                // Apply Sorting to Beneficiaries
                $sort = $request->get('sort', 'control_asc');
                switch ($sort) {
                    case 'name_asc':
                        $intakesQuery->orderBy('beneficiary_last_name', 'asc')->orderBy('beneficiary_first_name', 'asc');
                        break;
                    case 'name_desc':
                        $intakesQuery->orderBy('beneficiary_last_name', 'desc')->orderBy('beneficiary_first_name', 'desc');
                        break;
                    case 'amount_desc':
                        $intakesQuery->orderBy('recommended_amount', 'desc');
                        break;
                    case 'amount_asc':
                        $intakesQuery->orderBy('recommended_amount', 'asc');
                        break;
                    case 'control_desc':
                        $intakesQuery->orderBy('control_number', 'desc');
                        break;
                    case 'control_asc':
                    default:
                        $intakesQuery->orderBy('control_number', 'asc');
                        break;
                }

                $intakes = $intakesQuery->get();

                if (($request->filled('search') || ($request->filled('barangay') && $request->barangay !== 'All') || $request->filled('claim_status')) && $intakes->isEmpty()) {
                    continue;
                }

                $payrollRows = $intakes->map(function ($intake, $index) use ($record) {
                    $beneficiaryName = $intake->beneficiary_full_name ?? 'N/A';

                    if ($intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A') {
                        $representativeName = $intake->representative_full_name;
                        $contactNumber = $intake->rep_contact_number ?: ($intake->beneficiary_contact_number ?: 'N/A');
                        $isSeparateRep = true;
                    } else {
                        $representativeName = $beneficiaryName;
                        $contactNumber = $intake->beneficiary_contact_number ?: 'N/A';
                        $isSeparateRep = false;
                    }

                    $claimStatus = ($intake->claim_status === 'Claimed') ? 'Claimed' : 'Unclaimed';
                    $claimingDate = $intake->effective_claiming_date;
                    $formattedClaimingDate = $claimingDate ? $claimingDate->format('F d, Y') : ($record->payroll_date ? $record->payroll_date->format('F d, Y') : null);
                    $lastMessageSentDate = $intake->last_message_sent_at_formatted;

                    return (object) [
                        'id' => $intake->id,
                        'item_no' => $index + 1,
                        'control_number' => $intake->control_number,
                        'representative_name' => $representativeName,
                        'beneficiary_name' => $beneficiaryName,
                        'barangay' => $intake->beneficiary_barangay ?: 'Silang, Cavite',
                        'contact_number' => $contactNumber,
                        'amount' => (float) ($intake->recommended_amount ?? 0),
                        'formatted_amount' => '₱' . number_format((float) ($intake->recommended_amount ?? 0), 2),
                        'is_separate_rep' => $isSeparateRep,
                        'payroll_date' => $record->payroll_date ? $record->payroll_date->format('F d, Y') : 'N/A',
                        'payroll_number' => $record->payroll_number ?? 'N/A',
                        'claim_status' => $claimStatus,
                        'claimed_at' => $intake->claimed_at ? $intake->claimed_at->format('M d, Y h:i A') : null,
                        'claimed_by' => $intake->claimed_by,
                        'claiming_date' => $formattedClaimingDate,
                        'raw_claiming_date' => $claimingDate ? $claimingDate->format('Y-m-d') : ($record->payroll_date ? $record->payroll_date->format('Y-m-d') : null),
                        'purpose' => $intake->display_assistance_purpose,
                        'assistance_type' => $intake->recommended_assistance_type ?? 'Financial Assistance',
                        'last_message_date' => $lastMessageSentDate,
                        'raw_intake' => $intake,
                    ];
                });

                $recBeneficiaries = $payrollRows->count();
                $recAmount = (float) $payrollRows->sum('amount');
                $recClaimed = $payrollRows->where('claim_status', 'Claimed')->count();
                $recUnclaimed = $payrollRows->where('claim_status', '!=', 'Claimed')->count();
                $recClaimedAmount = (float) $payrollRows->where('claim_status', 'Claimed')->sum('amount');
                $recUnclaimedAmount = (float) $payrollRows->where('claim_status', '!=', 'Claimed')->sum('amount');

                $record->payrollRows = $payrollRows;
                $record->recordBeneficiariesCount = $recBeneficiaries;
                $record->recordTotalAmount = $recAmount;
                $record->formattedRecordAmount = '₱' . number_format($recAmount, 2);
                $record->claimedCount = $recClaimed;
                $record->unclaimedCount = $recUnclaimed;
                $record->recordClaimedAmount = $recClaimedAmount;
                $record->recordUnclaimedAmount = $recUnclaimedAmount;
                $record->formattedRecordClaimedAmount = '₱' . number_format($recClaimedAmount, 2);
                $record->formattedRecordUnclaimedAmount = '₱' . number_format($recUnclaimedAmount, 2);

                $grandTotalBeneficiaries += $recBeneficiaries;
                $grandTotalAmount += $recAmount;
                $grandTotalClaimedCount += $recClaimed;
                $grandTotalUnclaimedCount += $recUnclaimed;
                $grandTotalClaimedAmount += $recClaimedAmount;
                $grandTotalUnclaimedAmount += $recUnclaimedAmount;

                $payrollRecords->push($record);
            }

            $formattedGrandTotalAmount = '₱' . number_format($grandTotalAmount, 2);
            $formattedGrandTotalClaimedAmount = '₱' . number_format($grandTotalClaimedAmount, 2);
            $formattedGrandTotalUnclaimedAmount = '₱' . number_format($grandTotalUnclaimedAmount, 2);
            $paginatedDateGroups = null;
            $totalDatesCount = 1;
            $totalRecordsCount = $payrollRecords->count();

            return view('admin.financial.financialstep2-payroll-records', compact(
                'payrollRecords',
                'allDatePayrolls',
                'paginatedDateGroups',
                'totalDatesCount',
                'totalRecordsCount',
                'isDateView',
                'selectedDate',
                'grandTotalBeneficiaries',
                'grandTotalAmount',
                'formattedGrandTotalAmount',
                'grandTotalClaimedCount',
                'grandTotalUnclaimedCount',
                'grandTotalClaimedAmount',
                'grandTotalUnclaimedAmount',
                'formattedGrandTotalClaimedAmount',
                'formattedGrandTotalUnclaimedAmount',
                'barangays'
            ));
        }

        // Scalable, High-Performance Main Directory View for large volumes of records
        $totalDatesCount = (int) (FinancialPayrollRecord::selectRaw('COUNT(DISTINCT payroll_date) as agg')->value('agg') ?? 0);
        $totalRecordsCount = FinancialPayrollRecord::count();
        $grandTotalBeneficiaries = (int) (FinancialPayrollRecord::sum('total_beneficiaries') ?? 0);
        $grandTotalAmount = (float) (FinancialPayrollRecord::sum('total_amount') ?? 0);
        $formattedGrandTotalAmount = '₱' . number_format($grandTotalAmount, 2);

        // Filter matching dates based on search or barangay
        $datesQuery = FinancialPayrollRecord::query();

        // Month and Year filtering on directory
        if ($request->filled('month')) {
            $parts = explode('-', trim($request->month));
            if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                $datesQuery->whereYear('payroll_date', (int) $parts[0])
                           ->whereMonth('payroll_date', (int) $parts[1]);
            }
        }

        // Date range filtering on directory
        if ($request->filled('date_from')) {
            $datesQuery->whereDate('payroll_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $datesQuery->whereDate('payroll_date', '<=', $request->date_to);
        }

        if ($request->filled('search') || ($request->filled('barangay') && $request->barangay !== 'All')) {
            $matchingIntakesQuery = BeneficiaryIntake::where('is_payroll_generated', true);

            if ($request->filled('search')) {
                $search = trim($request->search);
                $matchingIntakesQuery->where(function ($q) use ($search) {
                    $q->where('control_number', 'like', "%{$search}%")
                      ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                      ->orWhere('rep_first_name', 'like', "%{$search}%")
                      ->orWhere('rep_last_name', 'like', "%{$search}%")
                      ->orWhere('beneficiary_barangay', 'like', "%{$search}%");
                });
            }

            if ($request->filled('barangay') && $request->barangay !== 'All') {
                $matchingIntakesQuery->where('beneficiary_barangay', $request->barangay);
            }

            $matchingPayrollIds = $matchingIntakesQuery->pluck('payroll_record_id')->filter()->unique();

            if ($request->filled('search')) {
                $search = trim($request->search);
                $matchingPayrollNumIds = FinancialPayrollRecord::where('payroll_number', 'like', "%{$search}%")->pluck('id');
                $matchingPayrollIds = $matchingPayrollIds->merge($matchingPayrollNumIds)->unique();
            }

            $datesQuery->whereIn('id', $matchingPayrollIds);
        }

        // Database-level aggregation & dynamic sorting
        $datesQuery->selectRaw('payroll_date, COUNT(*) as records_count, SUM(total_beneficiaries) as total_beneficiaries, SUM(total_amount) as total_amount')
            ->groupBy('payroll_date');

        $sort = $request->get('sort', 'date_desc');
        switch ($sort) {
            case 'date_asc':
                $datesQuery->orderBy('payroll_date', 'asc');
                break;
            case 'beneficiaries_desc':
                $datesQuery->orderByRaw('SUM(total_beneficiaries) DESC');
                break;
            case 'amount_desc':
                $datesQuery->orderByRaw('SUM(total_amount) DESC');
                break;
            case 'records_desc':
                $datesQuery->orderByRaw('COUNT(*) DESC');
                break;
            case 'date_desc':
            default:
                $datesQuery->orderBy('payroll_date', 'desc');
                break;
        }

        $paginatedDateGroups = $datesQuery->paginate(15)->withQueryString();

        $paginatedDateGroups->getCollection()->transform(function ($item) {
            $dateKey = $item->payroll_date ? Carbon::parse($item->payroll_date)->format('Y-m-d') : 'Unknown';
            $parsedDate = $dateKey !== 'Unknown' ? Carbon::parse($dateKey) : null;
            $totAmount = (float) ($item->total_amount ?? 0);

            return (object) [
                'payroll_date' => $dateKey,
                'date_str' => $dateKey,
                'parsed_date' => $parsedDate,
                'formatted_date' => $parsedDate ? $parsedDate->format('F d, Y') : 'Unknown Date',
                'relative_date' => $parsedDate ? ($parsedDate->isToday() ? 'Today' : ($parsedDate->isYesterday() ? 'Yesterday' : $parsedDate->diffForHumans())) : null,
                'records_count' => (int) $item->records_count,
                'total_beneficiaries' => (int) $item->total_beneficiaries,
                'total_amount' => $totAmount,
                'formatted_total_amount' => '₱' . number_format($totAmount, 2),
            ];
        });

        $payrollRecords = collect();

        return view('admin.financial.financialstep2-payroll-records', compact(
            'payrollRecords',
            'paginatedDateGroups',
            'totalDatesCount',
            'totalRecordsCount',
            'isDateView',
            'selectedDate',
            'grandTotalBeneficiaries',
            'grandTotalAmount',
            'formattedGrandTotalAmount',
            'barangays'
        ));
    }

    /**
     * Update claim status of an intake in payroll.
     */
    public function updateIntakeClaimStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Claimed,Unclaimed',
        ]);

        $intake = BeneficiaryIntake::findOrFail($id);

        $newStatus = $request->status;
        $officerName = session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'Step 2 Staff';

        if ($newStatus === 'Claimed') {
            $intake->claim_status = 'Claimed';
            $intake->claimed_at = Carbon::now();
            $intake->claimed_by = $officerName;
        } else {
            $intake->claim_status = 'Unclaimed';
            $intake->claimed_at = null;
            $intake->claimed_by = null;
        }

        $intake->save();

        $beneficiaryName = $intake->beneficiary_full_name ?? 'Beneficiary';
        $msg = $newStatus === 'Claimed'
            ? "Assistance for {$beneficiaryName} has been marked as Claimed."
            : "Assistance for {$beneficiaryName} has been reverted to Unclaimed.";

        $intakeAmount = (float) ($intake->recommended_amount ?? 0);
        $payrollDate = $intake->payroll_date ? $intake->payroll_date->format('Y-m-d') : ($intake->date_processed ? $intake->date_processed->format('Y-m-d') : null);

        $dateUnclaimedAmount = null;
        $dateClaimedAmount = null;
        $dateUnclaimedCount = null;
        $dateClaimedCount = null;

        if ($payrollDate) {
            $dateBaseQuery = BeneficiaryIntake::where('is_payroll_generated', true)
                ->where(function ($q) use ($payrollDate) {
                    $q->whereDate('payroll_date', $payrollDate)
                      ->orWhere(function ($sq) use ($payrollDate) {
                          $sq->whereNull('payroll_date')->whereDate('date_processed', $payrollDate);
                      });
                });

            $dateUnclaimedAmount = (float) (clone $dateBaseQuery)->where(function ($q) {
                $q->where('claim_status', '!=', 'Claimed')
                  ->orWhereNull('claim_status');
            })->sum('recommended_amount');

            $dateClaimedAmount = (float) (clone $dateBaseQuery)->where('claim_status', 'Claimed')->sum('recommended_amount');

            $dateUnclaimedCount = (int) (clone $dateBaseQuery)->where(function ($q) {
                $q->where('claim_status', '!=', 'Claimed')
                  ->orWhereNull('claim_status');
            })->count();

            $dateClaimedCount = (int) (clone $dateBaseQuery)->where('claim_status', 'Claimed')->count();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'intake_id' => $intake->id,
                'claim_status' => $intake->claim_status,
                'claimed_at' => $intake->claimed_at ? $intake->claimed_at->format('M d, Y h:i A') : null,
                'claimed_by' => $intake->claimed_by,
                'recommended_amount' => $intakeAmount,
                'formatted_amount' => '₱' . number_format($intakeAmount, 2),
                'date_unclaimed_amount' => $dateUnclaimedAmount,
                'formatted_date_unclaimed_amount' => $dateUnclaimedAmount !== null ? '₱' . number_format($dateUnclaimedAmount, 2) : null,
                'date_claimed_amount' => $dateClaimedAmount,
                'formatted_date_claimed_amount' => $dateClaimedAmount !== null ? '₱' . number_format($dateClaimedAmount, 2) : null,
                'date_unclaimed_count' => $dateUnclaimedCount,
                'date_claimed_count' => $dateClaimedCount,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Step 2: Monthly Payroll Liquidation & Financial Monitoring.
     * Displays financial liquidation summarized PER MONTH with Total Allocated, Total Claimed, Total Unclaimed, and Remaining Balance.
     */
    public function financialStep2Liquidation(Request $request)
    {
        $today = Carbon::today();

        // 1. Ensure any unlinked generated intakes are linked to a FinancialPayrollRecord
        $unlinkedIntakes = BeneficiaryIntake::where('is_payroll_generated', true)
            ->whereNull('payroll_record_id')
            ->get();

        if ($unlinkedIntakes->isNotEmpty()) {
            $groupedByDate = $unlinkedIntakes->groupBy(function ($intake) {
                return $intake->payroll_date 
                    ? $intake->payroll_date->format('Y-m-d') 
                    : ($intake->date_processed ? $intake->date_processed->format('Y-m-d') : $intake->created_at->format('Y-m-d'));
            });

            foreach ($groupedByDate as $pDate => $dateIntakes) {
                $payrollRecord = FinancialPayrollRecord::whereDate('payroll_date', $pDate)->first();
                if (!$payrollRecord) {
                    $payrollRecord = FinancialPayrollRecord::create([
                        'payroll_number' => 'PAYROLL-' . str_replace('-', '', $pDate) . '-001-' . strtoupper(substr(uniqid(), -4)),
                        'payroll_date' => $pDate,
                        'batch_number' => 1,
                        'disbursing_officer' => session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Disbursing Officer',
                        'total_beneficiaries' => $dateIntakes->count(),
                        'total_amount' => $dateIntakes->sum('recommended_amount'),
                        'status' => 'Completed',
                    ]);
                }
                BeneficiaryIntake::whereIn('id', $dateIntakes->pluck('id'))->update([
                    'payroll_record_id' => $payrollRecord->id,
                    'payroll_date' => $pDate,
                ]);
            }
        }

        // Available distinct months for filter dropdown
        $availableMonths = FinancialPayrollRecord::selectRaw('DATE_FORMAT(payroll_date, "%Y-%m") as month_key, DATE_FORMAT(payroll_date, "%M %Y") as month_label')
            ->whereNotNull('payroll_date')
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key', 'desc')
            ->get()
            ->pluck('month_label', 'month_key');

        $query = FinancialPayrollRecord::with(['beneficiaryIntakes' => function ($q) {
            $q->orderBy('id', 'asc');
        }])->orderBy('payroll_date', 'desc')->orderBy('created_at', 'desc');

        // Filter by Month (e.g. '2026-09')
        if ($request->filled('month') && $request->month !== 'All') {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->whereYear('payroll_date', $parts[0])->whereMonth('payroll_date', $parts[1]);
            }
        }

        // Search by payroll number, disbursing officer, or beneficiary/rep/control no inside payroll
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('payroll_number', 'like', "%{$search}%")
                  ->orWhere('disbursing_officer', 'like', "%{$search}%")
                  ->orWhereHas('beneficiaryIntakes', function ($bq) use ($search) {
                      $bq->where('control_number', 'like', "%{$search}%")
                         ->orWhere('beneficiary_first_name', 'like', "%{$search}%")
                         ->orWhere('beneficiary_last_name', 'like', "%{$search}%")
                         ->orWhere('beneficiary_middle_name', 'like', "%{$search}%")
                         ->orWhere('rep_first_name', 'like', "%{$search}%")
                         ->orWhere('rep_last_name', 'like', "%{$search}%")
                         ->orWhere('beneficiary_barangay', 'like', "%{$search}%");
                  });
            });
        }

        $allPayrolls = $query->get();

        // Group payroll records by Year-Month (e.g., '2026-09')
        $groupedPayrolls = $allPayrolls->groupBy(function ($item) {
            return $item->payroll_date ? $item->payroll_date->format('Y-m') : Carbon::parse($item->created_at)->format('Y-m');
        });

        $monthlyRecords = collect();
        $globalTotalAllocated = 0.0;
        $globalTotalClaimed = 0.0;
        $globalTotalUnclaimed = 0.0;
        $globalTotalBeneficiaries = 0;
        $globalClaimedCount = 0;
        $globalUnclaimedCount = 0;

        foreach ($groupedPayrolls as $yearMonth => $payrollsInMonth) {
            try {
                $monthCarbon = Carbon::createFromFormat('Y-m', $yearMonth);
                $monthLabel = $monthCarbon->format('F Y');
            } catch (\Exception $e) {
                $monthLabel = $yearMonth;
            }

            $monthTotalAllocated = 0.0;
            $monthTotalClaimed = 0.0;
            $monthTotalUnclaimed = 0.0;
            $monthTotalBeneficiaries = 0;
            $monthClaimedCount = 0;
            $monthUnclaimedCount = 0;

            // Process each payroll in this month
            foreach ($payrollsInMonth as $payroll) {
                $intakes = $payroll->beneficiaryIntakes;
                $pBeneficiaries = $intakes->count();

                $pAllocated = (float) $intakes->sum('recommended_amount');
                if ($pAllocated <= 0 && $payroll->total_amount > 0) {
                    $pAllocated = (float) $payroll->total_amount;
                }

                $pClaimedIntakes = $intakes->where('claim_status', 'Claimed');
                $pUnclaimedIntakes = $intakes->where('claim_status', '!=', 'Claimed');

                $pClaimedCount = $pClaimedIntakes->count();
                $pUnclaimedCount = $pUnclaimedIntakes->count();

                $pClaimedAmount = (float) $pClaimedIntakes->sum('recommended_amount');
                $pUnclaimedAmount = (float) $pUnclaimedIntakes->sum('recommended_amount');
                $pRemainingBalance = $pUnclaimedAmount;

                $pLiquidationRate = $pAllocated > 0 ? round(($pClaimedAmount / $pAllocated) * 100, 1) : 0;
                if ($pLiquidationRate >= 100 && $pBeneficiaries > 0) {
                    $pStatus = 'Fully Liquidated';
                    $pStatusClass = 'success';
                } elseif ($pClaimedAmount > 0) {
                    $pStatus = 'Partially Liquidated';
                    $pStatusClass = 'warning';
                } else {
                    $pStatus = 'Unreleased';
                    $pStatusClass = 'secondary';
                }

                $payroll->totalAllocated = $pAllocated;
                $payroll->totalClaimed = $pClaimedAmount;
                $payroll->totalUnclaimed = $pUnclaimedAmount;
                $payroll->remainingBalance = $pRemainingBalance;
                $payroll->claimedCount = $pClaimedCount;
                $payroll->unclaimedCount = $pUnclaimedCount;
                $payroll->totalBeneficiariesCount = $pBeneficiaries;
                $payroll->liquidationRate = $pLiquidationRate;
                $payroll->liquidationStatus = $pStatus;
                $payroll->liquidationStatusClass = $pStatusClass;

                $payroll->formattedAllocated = '₱' . number_format($pAllocated, 2);
                $payroll->formattedClaimed = '₱' . number_format($pClaimedAmount, 2);
                $payroll->formattedUnclaimed = '₱' . number_format($pUnclaimedAmount, 2);
                $payroll->formattedRemaining = '₱' . number_format($pRemainingBalance, 2);

                // Accumulate month totals
                $monthTotalAllocated += $pAllocated;
                $monthTotalClaimed += $pClaimedAmount;
                $monthTotalUnclaimed += $pUnclaimedAmount;
                $monthTotalBeneficiaries += $pBeneficiaries;
                $monthClaimedCount += $pClaimedCount;
                $monthUnclaimedCount += $pUnclaimedCount;
            }

            $monthRemainingBalance = $monthTotalUnclaimed;
            $monthLiquidationRate = $monthTotalAllocated > 0 ? round(($monthTotalClaimed / $monthTotalAllocated) * 100, 1) : 0;

            if ($monthLiquidationRate >= 100 && $monthTotalBeneficiaries > 0) {
                $monthStatus = 'Fully Liquidated';
                $monthStatusClass = 'success';
            } elseif ($monthTotalClaimed > 0) {
                $monthStatus = 'Partially Liquidated';
                $monthStatusClass = 'warning';
            } else {
                $monthStatus = 'Unreleased';
                $monthStatusClass = 'secondary';
            }

            // Filter by Status if requested
            if ($request->filled('status') && $request->status !== 'All') {
                if ($request->status === 'fully_liquidated' && $monthStatus !== 'Fully Liquidated') {
                    continue;
                }
                if ($request->status === 'partially_liquidated' && $monthStatus !== 'Partially Liquidated') {
                    continue;
                }
                if ($request->status === 'unreleased' && $monthStatus !== 'Unreleased') {
                    continue;
                }
            }

            // Month object
            $monthObj = (object) [
                'month_key' => $yearMonth,
                'month_label' => $monthLabel,
                'payrolls_count' => $payrollsInMonth->count(),
                'payrolls' => $payrollsInMonth,
                'totalAllocated' => $monthTotalAllocated,
                'totalClaimed' => $monthTotalClaimed,
                'totalUnclaimed' => $monthTotalUnclaimed,
                'remainingBalance' => $monthRemainingBalance,
                'totalBeneficiariesCount' => $monthTotalBeneficiaries,
                'claimedCount' => $monthClaimedCount,
                'unclaimedCount' => $monthUnclaimedCount,
                'liquidationRate' => $monthLiquidationRate,
                'liquidationStatus' => $monthStatus,
                'liquidationStatusClass' => $monthStatusClass,
                'formattedAllocated' => '₱' . number_format($monthTotalAllocated, 2),
                'formattedClaimed' => '₱' . number_format($monthTotalClaimed, 2),
                'formattedUnclaimed' => '₱' . number_format($monthTotalUnclaimed, 2),
                'formattedRemaining' => '₱' . number_format($monthRemainingBalance, 2),
            ];

            // Accumulate globals
            $globalTotalAllocated += $monthTotalAllocated;
            $globalTotalClaimed += $monthTotalClaimed;
            $globalTotalUnclaimed += $monthTotalUnclaimed;
            $globalTotalBeneficiaries += $monthTotalBeneficiaries;
            $globalClaimedCount += $monthClaimedCount;
            $globalUnclaimedCount += $monthUnclaimedCount;

            $monthlyRecords->push($monthObj);
        }

        $globalRemainingBalance = $globalTotalUnclaimed;
        $globalLiquidationRate = $globalTotalAllocated > 0 ? round(($globalTotalClaimed / $globalTotalAllocated) * 100, 1) : 0;

        $formattedGlobalAllocated = '₱' . number_format($globalTotalAllocated, 2);
        $formattedGlobalClaimed = '₱' . number_format($globalTotalClaimed, 2);
        $formattedGlobalUnclaimed = '₱' . number_format($globalTotalUnclaimed, 2);
        $formattedGlobalRemaining = '₱' . number_format($globalRemainingBalance, 2);

        $totalMonthsCount = $monthlyRecords->count();

        return view('admin.financial.financialstep2-liquidation', compact(
            'monthlyRecords',
            'availableMonths',
            'globalTotalAllocated',
            'globalTotalClaimed',
            'globalTotalUnclaimed',
            'globalRemainingBalance',
            'globalTotalBeneficiaries',
            'globalClaimedCount',
            'globalUnclaimedCount',
            'globalLiquidationRate',
            'formattedGlobalAllocated',
            'formattedGlobalClaimed',
            'formattedGlobalUnclaimed',
            'formattedGlobalRemaining',
            'totalMonthsCount'
        ));
    }

    /**
     * Generate official print-ready Liquidation Report for a specific month.
     */
    public function financialStep2LiquidationReportMonthly(Request $request, $yearMonth)
    {
        $parts = explode('-', $yearMonth);
        if (count($parts) !== 2) {
            return redirect()->route('admin.financial.financialstep2.liquidation')
                ->with('error', 'Invalid month format specified.');
        }

        $year = (int) $parts[0];
        $month = (int) $parts[1];

        try {
            $monthCarbon = Carbon::createFromDate($year, $month, 1);
            $monthLabel = $monthCarbon->format('F Y');
        } catch (\Exception $e) {
            $monthLabel = $yearMonth;
        }

        $payrolls = FinancialPayrollRecord::with(['beneficiaryIntakes' => function ($q) {
            $q->orderBy('payroll_date', 'asc')->orderBy('id', 'asc');
        }])
        ->whereYear('payroll_date', $year)
        ->whereMonth('payroll_date', $month)
        ->orderBy('payroll_date', 'asc')
        ->orderBy('batch_number', 'asc')
        ->get();

        $allIntakes = collect();
        $officers = [];

        $totalAllocated = 0.0;
        $totalClaimed = 0.0;
        $totalUnclaimed = 0.0;
        $claimedCount = 0;
        $unclaimedCount = 0;

        $payrollBatches = [];

        foreach ($payrolls as $pIndex => $payroll) {
            if (!empty($payroll->disbursing_officer) && !in_array($payroll->disbursing_officer, $officers)) {
                $officers[] = $payroll->disbursing_officer;
            }

            $intakes = $payroll->beneficiaryIntakes;
            $pBeneficiaries = $intakes->count();

            $pAllocated = (float) $intakes->sum('recommended_amount');
            if ($pAllocated <= 0 && $payroll->total_amount > 0) {
                $pAllocated = (float) $payroll->total_amount;
            }

            $pClaimedIntakes = $intakes->where('claim_status', 'Claimed');
            $pUnclaimedIntakes = $intakes->where('claim_status', '!=', 'Claimed');

            $pClaimedCount = $pClaimedIntakes->count();
            $pUnclaimedCount = $pUnclaimedIntakes->count();

            $pClaimedAmount = (float) $pClaimedIntakes->sum('recommended_amount');
            $pUnclaimedAmount = (float) $pUnclaimedIntakes->sum('recommended_amount');
            $pRemainingBalance = $pUnclaimedAmount;

            $pRate = $pAllocated > 0 ? round(($pClaimedAmount / $pAllocated) * 100, 1) : 0;
            $pStatus = ($pRate >= 100 && $pBeneficiaries > 0) ? 'Fully Liquidated' : ($pClaimedAmount > 0 ? 'Partially Liquidated' : 'Unreleased');

            $payrollBatches[] = (object) [
                'batch_no' => $pIndex + 1,
                'payroll_number' => $payroll->payroll_number,
                'payroll_date' => $payroll->payroll_date ? $payroll->payroll_date->format('M d, Y') : 'N/A',
                'disbursing_officer' => $payroll->disbursing_officer ?: 'MSWDO Disbursing Officer',
                'total_beneficiaries' => $pBeneficiaries,
                'allocated' => $pAllocated,
                'claimed' => $pClaimedAmount,
                'unclaimed' => $pUnclaimedAmount,
                'remaining' => $pRemainingBalance,
                'liquidation_rate' => $pRate,
                'status' => $pStatus,
            ];

            $totalAllocated += $pAllocated;
            $totalClaimed += $pClaimedAmount;
            $totalUnclaimed += $pUnclaimedAmount;
            $claimedCount += $pClaimedCount;
            $unclaimedCount += $pUnclaimedCount;

            foreach ($intakes as $intake) {
                $allIntakes->push($intake);
            }
        }

        $totalBeneficiaries = $allIntakes->count();
        $remainingBalance = $totalUnclaimed;
        $liquidationRate = $totalAllocated > 0 ? round(($totalClaimed / $totalAllocated) * 100, 1) : 0;

        if ($liquidationRate >= 100 && $totalBeneficiaries > 0) {
            $liquidationStatus = 'Fully Liquidated';
        } elseif ($totalClaimed > 0) {
            $liquidationStatus = 'Partially Liquidated';
        } else {
            $liquidationStatus = 'Unreleased';
        }

        // Map beneficiaries
        $beneficiaries = $allIntakes->map(function ($intake, $index) {
            $beneficiaryName = $intake->beneficiary_full_name ?? 'N/A';
            $repName = ($intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A')
                ? $intake->representative_full_name
                : $beneficiaryName;

            return (object) [
                'item_no' => $index + 1,
                'control_number' => $intake->control_number,
                'payroll_date' => $intake->payroll_date ? $intake->payroll_date->format('M d, Y') : ($intake->date_processed ? $intake->date_processed->format('M d, Y') : '--'),
                'representative_name' => $repName,
                'beneficiary_name' => $beneficiaryName,
                'barangay' => $intake->beneficiary_barangay ?: 'Silang, Cavite',
                'contact_number' => $intake->beneficiary_contact_number ?: ($intake->rep_contact_number ?: 'N/A'),
                'amount' => (float) ($intake->recommended_amount ?? 0),
                'formatted_amount' => '₱' . number_format((float) ($intake->recommended_amount ?? 0), 2),
                'claim_status' => ($intake->claim_status === 'Claimed') ? 'Claimed' : 'Unclaimed',
                'claimed_at' => $intake->claimed_at ? $intake->claimed_at->format('M d, Y h:i A') : '--',
                'claimed_by' => $intake->claimed_by ?: '--',
            ];
        });

        $disbursingOfficer = !empty($officers) ? implode(', ', $officers) : 'MSWDO Disbursing Officer';
        $reportDate = Carbon::now()->format('F d, Y');
        $isMonthlyReport = true;
        $payrollsCount = count($payrollBatches);

        return view('admin.financial.financialstep2-liquidation-report', compact(
            'isMonthlyReport',
            'monthLabel',
            'yearMonth',
            'payrollBatches',
            'payrollsCount',
            'beneficiaries',
            'totalBeneficiaries',
            'totalAllocated',
            'totalClaimed',
            'totalUnclaimed',
            'remainingBalance',
            'claimedCount',
            'unclaimedCount',
            'liquidationRate',
            'liquidationStatus',
            'disbursingOfficer',
            'reportDate'
        ));
    }

    /**
     * Generate official print-ready Liquidation Report for a specific payroll record.
     */
    public function financialStep2LiquidationReport(Request $request, $id)
    {
        $payroll = FinancialPayrollRecord::with(['beneficiaryIntakes' => function ($q) {
            $q->orderBy('id', 'asc');
        }])->findOrFail($id);

        $intakes = $payroll->beneficiaryIntakes;
        $totalBeneficiaries = $intakes->count();

        $totalAllocated = (float) $intakes->sum('recommended_amount');
        if ($totalAllocated <= 0 && $payroll->total_amount > 0) {
            $totalAllocated = (float) $payroll->total_amount;
        }

        $claimedIntakes = $intakes->where('claim_status', 'Claimed');
        $unclaimedIntakes = $intakes->where('claim_status', '!=', 'Claimed');

        $claimedCount = $claimedIntakes->count();
        $unclaimedCount = $unclaimedIntakes->count();

        $totalClaimed = (float) $claimedIntakes->sum('recommended_amount');
        $totalUnclaimed = (float) $unclaimedIntakes->sum('recommended_amount');
        $remainingBalance = $totalUnclaimed;

        $liquidationRate = $totalAllocated > 0 ? round(($totalClaimed / $totalAllocated) * 100, 1) : 0;

        if ($liquidationRate >= 100 && $totalBeneficiaries > 0) {
            $liquidationStatus = 'Fully Liquidated';
        } elseif ($totalClaimed > 0) {
            $liquidationStatus = 'Partially Liquidated';
        } else {
            $liquidationStatus = 'Unreleased';
        }

        // Map beneficiary list for reporting
        $beneficiaries = $intakes->map(function ($intake, $index) {
            $beneficiaryName = $intake->beneficiary_full_name ?? 'N/A';
            $repName = ($intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A')
                ? $intake->representative_full_name
                : $beneficiaryName;

            return (object) [
                'item_no' => $index + 1,
                'control_number' => $intake->control_number,
                'payroll_date' => $intake->payroll_date ? $intake->payroll_date->format('M d, Y') : ($intake->date_processed ? $intake->date_processed->format('M d, Y') : '--'),
                'representative_name' => $repName,
                'beneficiary_name' => $beneficiaryName,
                'barangay' => $intake->beneficiary_barangay ?: 'Silang, Cavite',
                'contact_number' => $intake->beneficiary_contact_number ?: ($intake->rep_contact_number ?: 'N/A'),
                'amount' => (float) ($intake->recommended_amount ?? 0),
                'formatted_amount' => '₱' . number_format((float) ($intake->recommended_amount ?? 0), 2),
                'claim_status' => ($intake->claim_status === 'Claimed') ? 'Claimed' : 'Unclaimed',
                'claimed_at' => $intake->claimed_at ? $intake->claimed_at->format('M d, Y h:i A') : '--',
                'claimed_by' => $intake->claimed_by ?: '--',
            ];
        });

        $disbursingOfficer = $payroll->disbursing_officer ?: 'MSWDO Disbursing Officer';
        $reportDate = Carbon::now()->format('F d, Y');
        $payrollDate = $payroll->payroll_date ? $payroll->payroll_date->format('F d, Y') : 'N/A';
        $isMonthlyReport = false;
        $monthLabel = $payroll->payroll_date ? $payroll->payroll_date->format('F Y') : 'N/A';

        return view('admin.financial.financialstep2-liquidation-report', compact(
            'payroll',
            'isMonthlyReport',
            'monthLabel',
            'beneficiaries',
            'totalBeneficiaries',
            'totalAllocated',
            'totalClaimed',
            'totalUnclaimed',
            'remainingBalance',
            'claimedCount',
            'unclaimedCount',
            'liquidationRate',
            'liquidationStatus',
            'disbursingOfficer',
            'reportDate',
            'payrollDate'
        ));
    }
}
