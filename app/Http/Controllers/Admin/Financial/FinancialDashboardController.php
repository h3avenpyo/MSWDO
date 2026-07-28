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
        $recentIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::latest()->take(5)->get() 
            : collect();

        return view('admin.financial.financial-dashboard', compact('totalIntakes', 'recentIntakes'));
    }

    public function financialStep1()
    {
        $totalIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::count() 
            : 0;
        $recentIntakes = class_exists(BeneficiaryIntake::class) 
            ? BeneficiaryIntake::latest()->paginate(10)
            : collect();

        return view('admin.financial.financialstep1', compact('totalIntakes', 'recentIntakes'));
    }

    public function financialStep2()
    {
        return view('admin.financial.financialstep2');
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
