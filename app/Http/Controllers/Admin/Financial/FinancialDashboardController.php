<?php

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Controller;
use App\Models\SocialCase\BeneficiaryIntake;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialDashboardController extends Controller
{
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

            if ($request->filled('date')) {
                $date = Carbon::parse($request->date);
                $query->where(function ($q) use ($date) {
                    $q->whereDate('created_at', $date)
                      ->orWhereDate('date_processed', $date);
                });
            } elseif (!$request->filled('all') && !$request->filled('search')) {
                $query->where(function ($q) use ($today) {
                    $q->whereDate('created_at', $today)
                      ->orWhereDate('date_processed', $today);
                });
            }

            $totalIntakes = BeneficiaryIntake::count();
            $todayIntakesCount = BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                  ->orWhereDate('date_processed', $today);
            })->count();

            $recentIntakes = $query->latest()->paginate(10)->withQueryString();
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

            $totalQueueCount = BeneficiaryIntake::count();
            $todayQueueCount = BeneficiaryIntake::where(function ($q) use ($today) {
                $q->whereDate('created_at', $today)
                  ->orWhereDate('date_processed', $today);
            })->count();

            $pendingPayoutCount = BeneficiaryIntake::where(function ($q) {
                $q->whereNotNull('recommended_amount')
                  ->where('recommended_amount', '>', 0);
            })->count();

            if ($pendingPayoutCount === 0 && $totalQueueCount > 0) {
                $pendingPayoutCount = $totalQueueCount;
            }

            $totalRecommendedAmount = BeneficiaryIntake::sum('recommended_amount') ?? 0;
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
     * Display the dedicated Financial Step 2 processing page for the selected client.
     */
    public function financialStep2Process(BeneficiaryIntake $intake)
    {
        $intake->loadMissing(['client', 'encoderUser']);

        return view('admin.financial.financialstep2-process', compact('intake'));
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
}
