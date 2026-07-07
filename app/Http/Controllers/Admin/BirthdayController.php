<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BirthdayController extends Controller
{
    public function index()
    {
        $today = now();
        $todayCount = $this->birthdayQuery($today->format('m'), $today->format('d'))->count();
        $weekCount = SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->where(function ($q) {
                $start = now();
                $end = now()->addDays(7);
                $sMD = $start->format('m-d');
                $eMD = $end->format('m-d');
                if ($sMD <= $eMD) {
                    $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$sMD, $eMD]);
                } else {
                    $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$sMD])
                      ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$eMD]);
                }
            })->count();

        $nextMonth = now()->addMonth();
        $nextMonthCount = SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ?", [$nextMonth->format('n')])
            ->count();

        $total = SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->where(function ($q) {
                $start = now();
                $end = now()->addDays(30);
                $sMD = $start->format('m-d');
                $eMD = $end->format('m-d');
                if ($sMD <= $eMD) {
                    $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$sMD, $eMD]);
                } else {
                    $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$sMD])
                      ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$eMD]);
                }
            })->count();

        $barangays = SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('barangay')
            ->distinct()
            ->orderBy('barangay')
            ->pluck('barangay');

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        return view('admin.senior-birthdays', compact(
            'todayCount', 'weekCount', 'nextMonthCount', 'total',
            'barangays', 'months'
        ));
    }

    public function data(Request $request)
    {
        $query = SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('birth_date');

        // Only apply 30-day range filter if no specific filter is set or filter is 'all'
        if (!$request->filled('filter') || $request->filter === 'all') {
            // Show birthdays for current month
            $query->whereRaw("MONTH(birth_date) = ?", [now()->format('n')]);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('control_number', 'like', "%{$s}%")
                  ->orWhere('osca_id', 'like', "%{$s}%")
                  ->orWhere('barangay', 'like', "%{$s}%")
                  ->orWhere('contact_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('barangay')) {
            $query->where('barangay', $request->barangay);
        }

        if ($request->filled('month')) {
            $query->whereRaw("MONTH(birth_date) = ?", [$request->month]);
        }

        if ($request->filled('filter')) {
            $f = $request->filter;
            if ($f === 'today') {
                $query->whereRaw("MONTH(birth_date) = ? AND DAY(birth_date) = ?", [now()->format('n'), now()->format('j')]);
            } elseif ($f === 'week') {
                $ws = now(); $we = now()->addDays(7);
                $wsMD = $ws->format('m-d'); $weMD = $we->format('m-d');
                if ($wsMD <= $weMD) {
                    $query->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$wsMD, $weMD]);
                } else {
                    $query->where(function ($q) use ($wsMD, $weMD) {
                        $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$wsMD])
                          ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$weMD]);
                    });
                }
            } elseif ($f === 'nextmonth') {
                $nm = now()->addMonth();
                $query->whereRaw("MONTH(birth_date) = ?", [$nm->format('n')]);
            }
        }

        $sortField = $request->sort_field ?? 'birth_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['birth_date', 'full_name', 'barangay', 'control_number', 'age'];
        if (in_array($sortField, $allowedSorts)) {
            if ($sortField === 'birth_date') {
                $query->orderByRaw("MONTH(birth_date) $sortDir, DAY(birth_date) $sortDir");
            } else {
                $query->orderBy($sortField, $sortDir);
            }
        } else {
            $query->orderByRaw("MONTH(birth_date) ASC, DAY(birth_date) ASC");
        }

        $perPage = $request->per_page ?? 15;
        $seniors = $query->paginate($perPage);

        $seniors->getCollection()->transform(function ($s) {
            $bday = Carbon::parse($s->birth_date);
            $today = now()->startOfDay();
            $next = $bday->copy()->year($today->year)->startOfDay();
            if ($next->isBefore($today)) {
                $next->addYear();
            }
            $daysLeft = $today->diffInDays($next);
            $isToday = ($daysLeft == 0);
            $currentAge = $bday->age;
            $ageTurning = $isToday ? $currentAge : ($currentAge + 1);

            return (object) [
                'id' => $s->id,
                'control_number' => $s->control_number ?? $s->record_number ?? '-',
                'osca_id' => $s->osca_id ?? '-',
                'full_name' => $s->full_name,
                'birth_date' => $s->birth_date,
                'birth_date_formatted' => $bday->format('M d, Y'),
                'current_age' => $currentAge,
                'age_turning' => $ageTurning,
                'barangay' => $s->barangay ?? '-',
                'contact_number' => $s->contact_number ?? '-',
                'address' => $s->address ?? '-',
                'month' => $s->month ?? $bday->format('F'),
                'days_left' => $daysLeft,
                'is_today' => $isToday,
                'sex' => $s->sex,
                'philsys_number' => $s->philsys_number,
                'rrn_number' => $s->rrn_number,
                'remarks' => $s->remarks,
                'status' => $s->status,
            ];
        });

        return response()->json($seniors);
    }

    public function profile($id)
    {
        $senior = SeniorCitizenRecord::on('mswdo_senior')->findOrFail($id);

        $bday = Carbon::parse($senior->birth_date);
        $today = now()->startOfDay();
        $next = $bday->copy()->year($today->year)->startOfDay();
        if ($next->isBefore($today)) {
            $next->addYear();
        }
        $daysLeft = $today->diffInDays($next);
        $isToday = ($daysLeft == 0);
        $currentAge = $bday->age;
        $ageTurning = $isToday ? $currentAge : ($currentAge + 1);

        return response()->json([
            'control_number' => $senior->control_number ?? $senior->record_number ?? '-',
            'osca_id' => $senior->osca_id ?? '-',
            'full_name' => $senior->full_name,
            'birth_date' => $bday->format('M d, Y'),
            'current_age' => $currentAge,
            'age_turning' => $ageTurning,
            'address' => $senior->address ?? '-',
            'barangay' => $senior->barangay ?? '-',
            'contact_number' => $senior->contact_number ?? '-',
            'sex' => $senior->sex ?? '-',
            'philsys_number' => $senior->philsys_number ?? '-',
            'rrn_number' => $senior->rrn_number ?? '-',
            'remarks' => $senior->remarks ?? '-',
            'month' => $senior->month ?? $bday->format('F'),
            'days_left' => $daysLeft,
            'is_today' => $isToday,
            'status' => $senior->status,
        ]);
    }

    public function dataByBarangay(Request $request)
    {
        $query = SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereNotNull('barangay');

        $start = now();
        $end = now()->addDays(30);
        $sMD = $start->format('m-d');
        $eMD = $end->format('m-d');

        if ($sMD <= $eMD) {
            $query->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$sMD, $eMD]);
        } else {
            $query->where(function ($q) use ($sMD, $eMD) {
                $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$sMD])
                  ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$eMD]);
            });
        }

        $barangays = $query->get()->groupBy('barangay')->map(function ($seniors, $barangay) {
            return [
                'barangay' => $barangay,
                'count' => $seniors->count(),
                'seniors' => $seniors->map(function ($s) {
                    $bday = Carbon::parse($s->birth_date);
                    $today = now()->startOfDay();
                    $next = $bday->copy()->year($today->year)->startOfDay();
                    if ($next->isBefore($today)) {
                        $next->addYear();
                    }
                    $daysLeft = $today->diffInDays($next);
                    $isToday = ($daysLeft == 0);
                    return [
                        'id' => $s->id,
                        'full_name' => $s->full_name,
                        'birth_date' => $bday->format('M d, Y'),
                        'days_left' => $daysLeft,
                        'is_today' => $isToday,
                    ];
                })->sortBy('days_left')->values(),
            ];
        })->sortByDesc('count')->values();

        return response()->json($barangays);
    }

    public function exportPdf(Request $request)
    {
        $seniors = $this->getFilteredData($request);
        $dateGenerated = now()->format('F d, Y h:i A');
        $total = $seniors->count();

        $barangaySummary = $seniors->groupBy('barangay')->map(function ($group, $barangay) {
            return ['barangay' => $barangay, 'count' => $group->count()];
        })->sortByDesc('count');

        $pdf = Pdf::loadView('admin.birthday-pdf', compact(
            'seniors', 'dateGenerated', 'total', 'barangaySummary'
        ));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('birthday-beneficiaries-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $seniors = $this->getFilteredData($request);
        $filename = 'birthday-beneficiaries-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($seniors) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Control Number', 'Senior Citizen ID', 'Full Name',
                'Birth Date', 'Current Age', 'Age Turning', 'Barangay',
                'Contact Number', 'Days Left', 'Status'
            ]);
            foreach ($seniors as $s) {
                fputcsv($file, [
                    $s['control_number'], $s['osca_id'], $s['full_name'],
                    $s['birth_date'], $s['current_age'], $s['age_turning'],
                    $s['barangay'], $s['contact_number'], $s['days_left'], $s['status']
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printView(Request $request)
    {
        $seniors = $this->getFilteredData($request);
        $dateGenerated = now()->format('F d, Y h:i A');
        $total = $seniors->count();

        $barangaySummary = $seniors->groupBy('barangay')->map(function ($group, $barangay) {
            return ['barangay' => $barangay, 'count' => $group->count()];
        })->sortByDesc('count');

        return view('admin.birthday-print', compact(
            'seniors', 'dateGenerated', 'total', 'barangaySummary'
        ));
    }

    private function birthdayQuery($month, $day)
    {
        return SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw("MONTH(birth_date) = ? AND DAY(birth_date) = ?", [$month, $day]);
    }

    private function getFilteredData(Request $request)
    {
        $query = SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('birth_date');

        $start = now();
        $end = now()->addDays(30);
        $sMD = $start->format('m-d');
        $eMD = $end->format('m-d');

        if ($sMD <= $eMD) {
            $query->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$sMD, $eMD]);
        } else {
            $query->where(function ($q) use ($sMD, $eMD) {
                $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$sMD])
                  ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$eMD]);
            });
        }

        if ($request->filled('barangay')) {
            $query->where('barangay', $request->barangay);
        }
        if ($request->filled('month')) {
            $query->whereRaw("MONTH(birth_date) = ?", [$request->month]);
        }

        $all = $query->get();
        return $all->map(function ($s) {
            $bday = Carbon::parse($s->birth_date);
            $today = now()->startOfDay();
            $next = $bday->copy()->year($today->year)->startOfDay();
            if ($next->isBefore($today)) {
                $next->addYear();
            }
            $daysLeft = $today->diffInDays($next);
            $isToday = ($daysLeft == 0);
            $currentAge = $bday->age;
            $ageTurning = $isToday ? $currentAge : ($currentAge + 1);
            return [
                'control_number' => $s->control_number ?? $s->record_number ?? '-',
                'osca_id' => $s->osca_id ?? '-',
                'full_name' => $s->full_name,
                'birth_date' => $bday->format('M d, Y'),
                'current_age' => $currentAge,
                'age_turning' => $ageTurning,
                'barangay' => $s->barangay ?? '-',
                'contact_number' => $s->contact_number ?? '-',
                'address' => $s->address ?? '-',
                'days_left' => $daysLeft,
                'is_today' => $isToday,
                'status' => $s->status,
            ];
        })->sortBy('days_left')->values();
    }
}
