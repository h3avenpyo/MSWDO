<?php

namespace App\Http\Controllers\Admin\Senior;

use App\Http\Controllers\Controller;
use App\Models\Senior\BirthdayPayoutHistory;
use App\Models\Senior\SeniorActivityLog;
use App\Models\Senior\InBetweenBenefitHistory;
use Illuminate\Http\Request;

use Carbon\Carbon;

class BirthdayPayoutController extends Controller
{
    /**
     * Display payout history/audit log
     */
    public function history(Request $request)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        $activeTab = $request->get('tab', $request->get('type', 'birthday'));
        if ($request->has('in_between_page')) {
            $activeTab = 'in-between';
        }

        // Get birthday payout history
        $birthdayQuery = BirthdayPayoutHistory::query()
            ->with(['payout', 'senior', 'performedBy'])
            ->where('action', 'released')
            ->orderBy('created_at', 'desc');

        // Apply barangay filter
        $barangay = $request->get('barangay', '');
        if ($barangay) {
            $birthdayQuery->whereHas('senior', function($q) use ($barangay) {
                $q->where('barangay', $barangay);
            });
        }

        // Apply date filters
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        if ($dateFrom && $dateTo) {
            $birthdayQuery->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
        } elseif ($dateFrom) {
            $birthdayQuery->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        } elseif ($dateTo) {
            $birthdayQuery->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $history = $birthdayQuery->paginate(15, ['*'], 'birthday_page')->withQueryString();

        // Get In-Between Benefits history (includes processed claims immediately)
        $inBetweenQuery = InBetweenBenefitHistory::with(['senior', 'processedBy', 'approvedBy'])
            ->whereIn('status', ['approved', 'released'])
            ->orderBy('payout_date', 'desc')
            ->orderBy('id', 'desc');

        // Apply barangay filter for in-between
        if ($barangay) {
            $inBetweenQuery->whereHas('senior', function($q) use ($barangay) {
                $q->where('barangay', $barangay);
            });
        }

        // Apply date filters for in-between
        if ($dateFrom && $dateTo) {
            $inBetweenQuery->whereBetween('payout_date', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
        } elseif ($dateFrom) {
            $inBetweenQuery->where('payout_date', '>=', Carbon::parse($dateFrom)->startOfDay());
        } elseif ($dateTo) {
            $inBetweenQuery->where('payout_date', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $inBetweenHistory = $inBetweenQuery->paginate(15, ['*'], 'in_between_page')->withQueryString();

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

        $actions = ['released'];

        return view('admin.senior.birthday-payout-history', compact(
            'history',
            'inBetweenHistory',
            'actions',
            'barangays',
            'dateFrom',
            'dateTo',
            'activeTab'
        ));
    }

    /**
     * Log activity to session for recent actions display
     */
    private function logActivity($action, $name, $identifier)
    {
        SeniorActivityLog::log($action, $name, $identifier);
    }
}
