<?php

namespace App\Http\Controllers\Admin\Senior;

use App\Http\Controllers\Controller;
use App\Models\Senior\BirthdayPayoutHistory;
use App\Models\Senior\SeniorActivityLog;
use Illuminate\Http\Request;

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

        $query = BirthdayPayoutHistory::query()
            ->with(['payout', 'senior', 'performedBy'])
            ->where('action', 'released')
            ->orderBy('created_at', 'desc');

        // Apply barangay filter
        $barangay = $request->get('barangay', '');
        if ($barangay) {
            $query->whereHas('senior', function($q) use ($barangay) {
                $q->where('barangay', $barangay);
            });
        }

        // Apply date filters
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

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

        $history = $query->paginate(15);

        $actions = ['released'];

        return view('admin.senior.birthday-payout-history', compact(
            'history',
            'actions',
            'barangays',
            'dateFrom',
            'dateTo'
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
