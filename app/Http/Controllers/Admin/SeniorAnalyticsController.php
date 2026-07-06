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

        return view('admin.senior-analytics', compact(
            'barangayStats', 'totalSeniors', 'totalBarangays',
            'activeSeniors', 'avgPerBarangay', 'topBarangay', 'topBarangayCount'
        ));
    }
}
