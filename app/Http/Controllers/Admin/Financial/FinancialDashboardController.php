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
                if ($status === 'ready_payout') {
                    $query->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0);
                } elseif ($status === 'for_assessment') {
                    $query->where(function ($q) {
                        $q->whereNull('recommended_amount')->orWhere('recommended_amount', '<=', 0);
                    });
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

            $todayQueueCount = BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('date_processed', $today)
                  ->orWhere(function ($sq) use ($today) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $today);
                  });
            })->count();

            $totalQueueCount = $todayQueueCount;

            $pendingPayoutCount = BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('date_processed', $today)
                  ->orWhere(function ($sq) use ($today) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $today);
                  });
            })->whereNotNull('recommended_amount')->where('recommended_amount', '>', 0)->count();

            $totalRecommendedAmount = BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('date_processed', $today)
                  ->orWhere(function ($sq) use ($today) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $today);
                  });
            })->sum('recommended_amount') ?? 0;

            $intakes = $query->paginate(15)->withQueryString();
        } else {
            $totalQueueCount = 0;
            $todayQueueCount = 0;
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

            // Filter by Date
            if ($request->filled('date')) {
                $filterDate = Carbon::parse($request->date);
                $query->where(function ($q) use ($filterDate) {
                    $q->whereDate('created_at', $filterDate)
                      ->orWhereDate('date_processed', $filterDate);
                });
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
                } elseif ($request->status === 'pending') {
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

            foreach ($rawRecords as $record) {
                $intakesQuery = BeneficiaryIntake::with(['client', 'encoderUser', 'payrollRecord'])
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

                if (($request->filled('search') || ($request->filled('barangay') && $request->barangay !== 'All')) && $intakes->isEmpty()) {
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
                        'payroll_date' => $record->payroll_date ? $record->payroll_date->format('F d, Y') : 'N/A',
                        'payroll_number' => $record->payroll_number ?? 'N/A',
                        'raw_intake' => $intake,
                    ];
                });

                $recBeneficiaries = $payrollRows->count();
                $recAmount = $payrollRows->sum('amount');

                $record->payrollRows = $payrollRows;
                $record->recordBeneficiariesCount = $recBeneficiaries;
                $record->recordTotalAmount = $recAmount;
                $record->formattedRecordAmount = '₱' . number_format($recAmount, 2);

                $grandTotalBeneficiaries += $recBeneficiaries;
                $grandTotalAmount += $recAmount;

                $payrollRecords->push($record);
            }

            $formattedGrandTotalAmount = '₱' . number_format($grandTotalAmount, 2);
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

        $paginatedDateGroups = $datesQuery->paginate(10)->withQueryString();

        $paginatedDateGroups->getCollection()->transform(function ($item) {
            $dateKey = $item->payroll_date ? Carbon::parse($item->payroll_date)->format('Y-m-d') : 'Unknown';
            $parsedDate = $dateKey !== 'Unknown' ? Carbon::parse($dateKey) : null;
            $totAmount = (float) ($item->total_amount ?? 0);

            return (object) [
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
}
