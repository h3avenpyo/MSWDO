<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRecord;
use App\Models\Client;
use App\Models\SocialCaseStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialCaseDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        // Get filter parameters
        $startDate = $request->input('start_date', now()->subMonths(6)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $barangay = $request->input('barangay');
        $status = $request->input('status');
        $assistanceType = $request->input('assistance_type');

        // Dashboard Cards
        $totalClients = Client::on('mswdo_social_case')->count();
        
        $activeCases = SocialCaseStudy::on('mswdo_social_case')
            ->where('status', 'Open')
            ->when($barangay, function ($query) use ($barangay) {
                return $query->whereHas('client', function ($q) use ($barangay) {
                    $q->where('address', 'like', "%{$barangay}%");
                });
            })
            ->count();

        $pendingAssessment = SocialCaseStudy::on('mswdo_social_case')
            ->where('status', 'Pending')
            ->when($barangay, function ($query) use ($barangay) {
                return $query->whereHas('client', function ($q) use ($barangay) {
                    $q->where('address', 'like', "%{$barangay}%");
                });
            })
            ->count();

        $approvedCases = AssistanceRecord::on('mswdo_social_case')
            ->where('status', 'Approved')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('release_date', [$startDate, $endDate]);
            })
            ->when($barangay, function ($query) use ($barangay) {
                return $query->whereHas('client', function ($q) use ($barangay) {
                    $q->where('address', 'like', "%{$barangay}%");
                });
            })
            ->when($assistanceType, function ($query) use ($assistanceType) {
                return $query->where('assistance_type', $assistanceType);
            })
            ->count();

        $releasedAssistance = AssistanceRecord::on('mswdo_social_case')
            ->where('status', 'Released')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('release_date', [$startDate, $endDate]);
            })
            ->when($barangay, function ($query) use ($barangay) {
                return $query->whereHas('client', function ($q) use ($barangay) {
                    $q->where('address', 'like', "%{$barangay}%");
                });
            })
            ->when($assistanceType, function ($query) use ($assistanceType) {
                return $query->where('assistance_type', $assistanceType);
            })
            ->count();

        $archivedCases = SocialCaseStudy::on('mswdo_social_case')
            ->where('status', 'Closed')
            ->when($barangay, function ($query) use ($barangay) {
                return $query->whereHas('client', function ($q) use ($barangay) {
                    $q->where('address', 'like', "%{$barangay}%");
                });
            })
            ->count();

        // Charts Data
        // Monthly Social Case Study Requests
        $monthlyRequests = SocialCaseStudy::on('mswdo_social_case')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Assistance by Purpose
        $assistanceByPurpose = AssistanceRecord::on('mswdo_social_case')
            ->select('assistance_type', DB::raw('COUNT(*) as count'))
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('release_date', [$startDate, $endDate]);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->groupBy('assistance_type')
            ->orderByDesc('count')
            ->get();

        // Assistance by Barangay
        $assistanceByBarangay = AssistanceRecord::on('mswdo_social_case')
            ->select(DB::raw('SUBSTRING_INDEX(clients.address, ",", 1) as barangay'), DB::raw('COUNT(*) as count'))
            ->join('clients', 'assistance_records.client_id', '=', 'clients.id')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('assistance_records.release_date', [$startDate, $endDate]);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('assistance_records.status', $status);
            })
            ->when($assistanceType, function ($query) use ($assistanceType) {
                return $query->where('assistance_records.assistance_type', $assistanceType);
            })
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->limit(15)
            ->get();

        // Recent Activities
        $latestEncodedCases = SocialCaseStudy::on('mswdo_social_case')
            ->with('client')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $latestApprovedCases = AssistanceRecord::on('mswdo_social_case')
            ->with('client')
            ->where('status', 'Approved')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $latestReleasedAssistance = AssistanceRecord::on('mswdo_social_case')
            ->with('client')
            ->where('status', 'Released')
            ->orderByDesc('release_date')
            ->limit(5)
            ->get();

        // Barangay list for filter
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

        // Assistance types for filter
        $assistanceTypes = AssistanceRecord::on('mswdo_social_case')
            ->select('assistance_type')
            ->distinct()
            ->orderBy('assistance_type')
            ->pluck('assistance_type');

        return view('admin.social-case-dashboard', compact(
            'totalClients',
            'activeCases',
            'pendingAssessment',
            'approvedCases',
            'releasedAssistance',
            'archivedCases',
            'monthlyRequests',
            'assistanceByPurpose',
            'assistanceByBarangay',
            'latestEncodedCases',
            'latestApprovedCases',
            'latestReleasedAssistance',
            'barangays',
            'assistanceTypes',
            'startDate',
            'endDate',
            'barangay',
            'status',
            'assistanceType'
        ));
    }
}
