<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SeniorAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        $barangay = $request->get('barangay');
        if ($barangay === null || $barangay === '') {
            $barangay = null;
        }
        $gender = $request->get('gender');
        $ageGroup = $request->get('age_group');

        // Build base query with filters
        $baseQuery = SeniorCitizenRecord::where('status', 'active');

        if ($barangay) {
            $baseQuery->where('barangay', $barangay);
        }
        if ($gender) {
            $baseQuery->where('sex', $gender);
        }
        if ($ageGroup) {
            $ageExpr = DB::raw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE())');
            switch ($ageGroup) {
                case '60-69':
                    $baseQuery->whereBetween($ageExpr, [60, 69]);
                    break;
                case '70-79':
                    $baseQuery->whereBetween($ageExpr, [70, 79]);
                    break;
                case '80-89':
                    $baseQuery->whereBetween($ageExpr, [80, 89]);
                    break;
                case '90-99':
                    $baseQuery->whereBetween($ageExpr, [90, 99]);
                    break;
                case '100+':
                    $baseQuery->where($ageExpr, '>=', 100);
                    break;
            }
        }

        // Barangay statistics
        $barangayQuery = clone $baseQuery;
        $barangayStats = $barangayQuery
            ->whereNotNull('barangay')
            ->select('barangay', DB::raw('count(*) as total'))
            ->groupBy('barangay')
            ->orderByDesc('total')
            ->get()
            ->keyBy('barangay');

        // All barangays list
        $allBarangays = [
            'Acacia', 'Adlas', 'Anahaw I', 'Anahaw II', 'Balite I', 'Balite II', 'Balubad', 'Banaba', 'Batas',
            'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho', 'Bulihan', 'Cabangaan', 'Carmen', 'Hoyo', 'Hukay', 'Iba',
            'Inchican', 'Ipil I', 'Ipil II', 'Kalubkob', 'Kaong', 'Lalaan I', 'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil',
            'Maguyam', 'Malabag', 'Malaking Tatyao', 'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I', 'San Vicente II', 'Santol',
            'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II', 'Tubuan III', 'Ulat', 'Yakal'
        ];

        // Build complete stats including barangays with 0 count
        $completeStats = [];
        foreach ($allBarangays as $barangay) {
            $completeStats[] = [
                'barangay' => $barangay,
                'total' => $barangayStats->has($barangay) ? $barangayStats[$barangay]->total : 0
            ];
        }

        // Sort by count (highest to lowest)
        usort($completeStats, function($a, $b) {
            return $b['total'] - $a['total'];
        });

        $barangayStats = collect($completeStats);

        // Total statistics
        $totalSeniors = $baseQuery->count();
        $totalBarangays = count($allBarangays);
        $activeSeniors = $baseQuery->count();
        $inactiveSeniors = SeniorCitizenRecord::where('status', '!=', 'active')->count();
        $avgPerBarangay = $totalBarangays > 0 ? round($totalSeniors / $totalBarangays) : 0;

        $topBarangay = $barangayStats->first()?->barangay ?? 'N/A';
        $topBarangayCount = $barangayStats->first()?->total ?? 0;

        // Gender distribution
        $genderQuery = clone $baseQuery;
        $genderStats = $genderQuery
            ->select('sex', DB::raw('count(*) as total'))
            ->whereNotNull('sex')
            ->groupBy('sex')
            ->get();

        $maleCount = $genderStats->where('sex', 'Male')->first()?->total ?? 0;
        $femaleCount = $genderStats->where('sex', 'Female')->first()?->total ?? 0;

        // Age groups
        $ageQuery = clone $baseQuery;
        $ageGroups = $ageQuery
            ->select(DB::raw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60 AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= 69 THEN "60-69"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 70 AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= 79 THEN "70-79"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 80 AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= 89 THEN "80-89"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 90 AND TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= 99 THEN "90-99"
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 100 THEN "100+"
                    ELSE "Unknown"
                END as age_group,
                count(*) as total
            '))
            ->whereNotNull('birth_date')
            ->groupBy('age_group')
            ->orderByRaw('FIELD(age_group, "60-69", "70-79", "80-89", "90-99", "100+", "Unknown")')
            ->get();

        // Monthly registrations for selected year
        $registrationQuery = clone $baseQuery;
        $monthlyRegistrations = $registrationQuery
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('month')
            ->get();

        return view('admin.senior-analytics', compact(
            'barangayStats', 'totalSeniors', 'totalBarangays',
            'activeSeniors', 'inactiveSeniors', 'avgPerBarangay', 'topBarangay', 'topBarangayCount',
            'genderStats', 'ageGroups', 'monthlyRegistrations',
            'maleCount', 'femaleCount',
            'allBarangays',
            'year', 'month', 'barangay', 'gender', 'ageGroup'
        ));
    }
}
