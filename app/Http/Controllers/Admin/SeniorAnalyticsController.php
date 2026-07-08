<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Support\Facades\DB;

class SeniorAnalyticsController extends Controller
{
    public function index()
    {
        $barangayStats = SeniorCitizenRecord::on('mswdo_senior')
            ->whereNotNull('barangay')
            ->select('barangay', DB::raw('count(*) as total'))
            ->groupBy('barangay')
            ->orderByDesc('total')
            ->get();

        $totalSeniors = SeniorCitizenRecord::on('mswdo_senior')->count();
        $totalBarangays = $barangayStats->count();
        $activeSeniors = SeniorCitizenRecord::on('mswdo_senior')->where('status', 'active')->count();
        $avgPerBarangay = $totalBarangays > 0 ? round($totalSeniors / $totalBarangays) : 0;

        $topBarangay = $barangayStats->first()?->barangay ?? 'N/A';
        $topBarangayCount = $barangayStats->first()?->total ?? 0;

        // Gender distribution
        $genderStats = SeniorCitizenRecord::on('mswdo_senior')
            ->select('sex', DB::raw('count(*) as total'))
            ->whereNotNull('sex')
            ->groupBy('sex')
            ->get();

        // Age groups
        $ageGroups = SeniorCitizenRecord::on('mswdo_senior')
            ->select(DB::raw('
                CASE
                    WHEN age >= 60 AND age <= 69 THEN "60-69"
                    WHEN age >= 70 AND age <= 79 THEN "70-79"
                    WHEN age >= 80 AND age <= 89 THEN "80-89"
                    WHEN age >= 90 THEN "90+"
                    ELSE "Unknown"
                END as age_group,
                count(*) as total
            '))
            ->whereNotNull('age')
            ->groupBy('age_group')
            ->orderByRaw('FIELD(age_group, "60-69", "70-79", "80-89", "90+", "Unknown")')
            ->get();

        // Monthly registrations (current year)
        $monthlyRegistrations = SeniorCitizenRecord::on('mswdo_senior')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        // New seniors this month
        $newSeniorsThisMonth = SeniorCitizenRecord::on('mswdo_senior')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.senior-analytics', compact(
            'barangayStats', 'totalSeniors', 'totalBarangays',
            'activeSeniors', 'avgPerBarangay', 'topBarangay', 'topBarangayCount',
            'genderStats', 'ageGroups', 'monthlyRegistrations', 'newSeniorsThisMonth'
        ));
    }
}
