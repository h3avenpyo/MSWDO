<?php

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Controller;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        if (class_exists(BeneficiaryIntake::class)) {
            $query = BeneficiaryIntake::with(['client', 'encoderUser']);

            // STRICT FILTER: Display intake records processed or created on the current day
            $query->where(function ($q) use ($today) {
                $q->whereDate('date_processed', $today)
                  ->orWhereDate('created_at', $today);
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

            // Calculate overall today metrics (unaffected by search/filter so dashboard stats are accurate)
            $todayIntakesBase = BeneficiaryIntake::where(function ($q) use ($today) {
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
            'categories'
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
        $intake->recommended_amount = (float) $request->recommended_amount;
        $intake->save();

        $today = Carbon::today();
        $todayIntakesBase = BeneficiaryIntake::where(function ($q) use ($today) {
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
                    if ($intake) {
                        $intake->recommended_amount = (float) $amount;
                        $intake->save();
                        $updatedCount++;
                    }
                }
            }
        });

        $today = Carbon::today();
        $todayIntakesBase = BeneficiaryIntake::where(function ($q) use ($today) {
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
                'message' => "Successfully updated financial assistance amounts for {$updatedCount} intake records.",
                'updated_count' => $updatedCount,
                'total_today_count' => $totalTodayCount,
                'encoded_count' => $encodedCount,
                'pending_count' => $pendingCount,
                'total_payroll_amount' => (float) $totalPayrollAmount,
                'formatted_total_payroll_amount' => '₱' . number_format($totalPayrollAmount, 2),
                'all_amounts_encoded' => $allAmountsEncoded,
            ]);
        }

        return redirect()->back()->with('success', "Successfully updated financial assistance amounts for {$updatedCount} intakes.");
    }

    /**
     * Generate and display the printable Payroll document for today's intakes.
     */
    public function printPayroll(Request $request)
    {
        $today = Carbon::today();
        $targetDate = $request->filled('date') ? Carbon::parse($request->date) : $today;

        $query = BeneficiaryIntake::with(['client', 'encoderUser'])
            ->where(function ($q) use ($targetDate) {
                $q->whereDate('date_processed', $targetDate)
                  ->orWhere(function ($sq) use ($targetDate) {
                      $sq->whereNull('date_processed')->whereDate('created_at', $targetDate);
                  });
            });

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
            
            // Representative Name Rule:
            // If the intake has beneficiary only and no separate representative,
            // the beneficiary should automatically be treated as the representative
            // and their name should be placed in the Name of Representative field.
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

        $disbursingOfficer = session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Disbursing Officer';
        $payrollDate = $targetDate->format('F d, Y');
        $payrollRefNo = 'PAYROLL-' . $targetDate->format('Ymd') . '-' . str_pad((string) rand(100, 999), 3, '0', STR_PAD_LEFT);

        return view('admin.financial.financialstep2-payroll-print', compact(
            'payrollRows',
            'totalBeneficiaries',
            'totalAmount',
            'formattedTotalAmount',
            'missingAmountCount',
            'disbursingOfficer',
            'payrollDate',
            'payrollRefNo',
            'targetDate'
        ));
    }
}
