<?php

namespace App\Http\Controllers\Admin\Senior;

use App\Http\Controllers\Controller;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\Senior\SeniorActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SeniorController extends Controller
{
    public function senior()
    {
        $justLoggedIn = session('admin_just_logged_in', false);

        if ($justLoggedIn) {
            session()->forget('admin_just_logged_in');
        }

        $totalSeniors = SeniorCitizenRecord::whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')->count();
        $activeSeniors = SeniorCitizenRecord::where('status', 'active')->whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')->count();
        $pendingSeniors = SeniorCitizenRecord::where('status', 'pending')->whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')->count();
        $recentSeniors = SeniorCitizenRecord::
            whereNotNull('birth_date')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $allBarangays = $this->getAllBarangays();

        $barangayDistribution = SeniorCitizenRecord::
            where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')
            ->whereNotNull('barangay')
            ->selectRaw('barangay, COUNT(*) as count')
            ->groupBy('barangay')
            ->get()
            ->keyBy('barangay');

        $completeDistribution = [];
        foreach ($allBarangays as $barangay) {
            $completeDistribution[] = [
                'barangay' => $barangay,
                'count' => $barangayDistribution->has($barangay) ? $barangayDistribution[$barangay]->count : 0
            ];
        }

        usort($completeDistribution, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        $barangayDistribution = collect($completeDistribution);

        $recentActivities = SeniorActivityLog::recent(10);

        $data = [
            'totalSeniors' => $totalSeniors,
            'activeSeniors' => $activeSeniors,
            'pendingSeniors' => $pendingSeniors,
            'recentSeniors' => $recentSeniors,
            'barangayDistribution' => $barangayDistribution,
            'recentActivities' => $recentActivities,
            'justLoggedIn' => $justLoggedIn,
        ];

        return view('admin.senior.index', $data);
    }

    public function seniorRegistration()
    {
        $seniorCreated = session('senior_created', false);

        if ($seniorCreated) {
            session()->forget('senior_created');
        }

        $year = date('Y');
        $barangaySequences = [];
        $barangays = $this->getAllBarangays();
        $barangayCodes = $this->getBarangayCodes();

        foreach ($barangays as $barangay) {
            $lastRecord = SeniorCitizenRecord::
                where('barangay', $barangay)
                ->where('year_applied', $year)
                ->select('id', 'control_number')
                ->orderByDesc('id')
                ->first();

            $nextSequence = $lastRecord ? intval(substr($lastRecord->control_number, -6)) + 1 : 1;
            $barangaySequences[$barangay] = $nextSequence;
        }

        return view('admin.senior.registration', compact('barangaySequences', 'barangayCodes', 'seniorCreated'));
    }

    public function storeSeniorRegistration(Request $request)
    {
        $request->validate([
            'year_applied' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'control_number' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'barangay' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'sex' => ['required', 'in:Male,Female'],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{11}$/', 'max:11'],
            'philsys_number' => ['nullable', 'string', 'regex:/^[0-9]{12}$/', 'max:12'],
            'rrn_number' => ['nullable', 'string', 'regex:/^[0-9]{29}$/', 'max:29'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $nameParts = array_filter(array_map('trim', explode(' ', $request->full_name)));
        $firstName = array_shift($nameParts) ?? '';
        $lastName = array_pop($nameParts) ?? '';
        $middleName = implode(' ', $nameParts);

        $existingName = SeniorCitizenRecord::
            whereRaw('LOWER(first_name) = ? AND LOWER(last_name) = ?', [strtolower($firstName), strtolower($lastName)])
            ->first();

        if ($existingName) {
            return back()->withErrors(['full_name' => 'A senior citizen with this name already exists.'])->withInput();
        }

        $qrCodeData = $request->control_number;
        $qrCodePath = 'uploads/qr_codes/' . time() . '_qr.png';

        try {
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrCodeData);
            $qrCodeImage = file_get_contents($qrCodeUrl);
            if ($qrCodeImage !== false) {
                if (!file_exists(public_path('uploads/qr_codes'))) {
                    mkdir(public_path('uploads/qr_codes'), 0755, true);
                }
                file_put_contents(public_path($qrCodePath), $qrCodeImage);
            } else {
                $qrCodePath = null;
            }
        } catch (\Exception $e) {
            $qrCodePath = null;
        }

        $avatarPath = 'uploads/avatars/' . time() . '_avatar.png';
        try {
            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($request->full_name) . "&background=1A237E&color=fff&size=128";
            $avatarImage = file_get_contents($avatarUrl);
            if ($avatarImage !== false) {
                if (!file_exists(public_path('uploads/avatars'))) {
                    mkdir(public_path('uploads/avatars'), 0755, true);
                }
                file_put_contents(public_path($avatarPath), $avatarImage);
            } else {
                $avatarPath = null;
            }
        } catch (\Exception $e) {
            $avatarPath = null;
        }

        $senior = SeniorCitizenRecord::create([
            'year_applied' => $request->year_applied,
            'control_number' => $request->control_number,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'address' => $request->address,
            'barangay' => $request->barangay,
            'birth_date' => $request->birth_date,
            'sex' => $request->sex,
            'contact_number' => $request->contact_number,
            'philsys_number' => $request->philsys_number,
            'rrn_number' => $request->rrn_number,
            'remarks' => $request->remarks,
            'created_by' => session('admin_user_id'),
            'status' => 'active',
            'qr_code' => $qrCodeData,
            'qr_code_image' => $qrCodePath,
            'avatar_image' => $avatarPath,
        ]);

        $this->logActivity('registered', $senior->full_name, $senior->control_number);

        return redirect()->route('admin.senior.registration')->with('success', 'Senior citizen registered successfully.')->with('senior_created', true);
    }

    public function seniorMasterlist(Request $request)
    {
        $query = SeniorCitizenRecord::
            where('status', '!=', 'archived')
            ->whereNotNull('birth_date')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')
            ->select('id', 'control_number', 'first_name', 'middle_name', 'last_name', 'address', 'barangay', 'birth_date', 'sex', 'status');

        if ($request->filled('barangay') && $request->barangay !== '') {
            $query->where('barangay', $request->barangay);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('middle_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        $seniors = $query->orderByDesc('created_at')->paginate(15)->onEachSide(1);

        return view('admin.senior.masterlist', compact('seniors'));
    }

    public function seniorArchiveList(Request $request)
    {
        $query = SeniorCitizenRecord::
            where('status', 'archived')
            ->select('id', 'control_number', 'first_name', 'middle_name', 'last_name', 'address', 'barangay', 'birth_date', 'sex', 'status', 'created_at', 'updated_at');

        if ($request->filled('barangay') && $request->barangay !== '') {
            $query->where('barangay', $request->barangay);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('middle_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        $archivedSeniors = $query->orderByDesc('updated_at')->paginate(15);

        return view('admin.senior.archive', compact('archivedSeniors'));
    }

    public function archiveSenior($id)
    {
        $senior = SeniorCitizenRecord::find($id);

        if (!$senior) {
            return redirect()->route('admin.senior.masterlist')->with('error', 'Senior citizen not found.');
        }

        $senior->update([
            'status' => 'archived',
        ]);

        $this->logActivity('archived', $senior->full_name, $senior->control_number);

        return redirect()->route('admin.senior.masterlist')->with('success', 'Senior citizen archived successfully.');
    }

    public function unarchiveSenior($id)
    {
        $senior = SeniorCitizenRecord::find($id);

        if (!$senior) {
            return redirect()->route('admin.senior.archive.list')->with('error', 'Senior citizen not found.');
        }

        $senior->update([
            'status' => 'active',
        ]);

        $this->logActivity('restored', $senior->full_name, $senior->control_number);

        return redirect()->route('admin.senior.archive.list')->with('success', 'Senior citizen restored to active successfully.');
    }

    public function seniorProfileJson($id)
    {
        $senior = SeniorCitizenRecord::findOrFail($id);

        return response()->json([
            'id' => $senior->id,
            'control_number' => $senior->control_number ?? '-',
            'osca_id' => $senior->osca_id ?? '-',
            'full_name' => $senior->full_name ?? '-',
            'address' => $senior->address ?? '-',
            'barangay' => $senior->barangay ?? '-',
            'birth_date' => $senior->birth_date ? date('M d, Y', strtotime($senior->birth_date)) : '-',
            'current_age' => $senior->age ?? '-',
            'sex' => $senior->sex ?? '-',
            'month' => $senior->birth_month ?? '-',
            'contact_number' => $senior->contact_number ?? '-',
            'philsys_number' => $senior->philsys_number ?? '-',
            'rrn_number' => $senior->rrn_number ?? '-',
            'remarks' => $senior->remarks ?? '-',
            'status' => $senior->status ? ucfirst($senior->status->value) : 'pending',
            'year_applied' => $senior->year_applied ?? '-',
        ]);
    }

    public function clearRecentActivities()
    {
        SeniorActivityLog::truncate();
        return redirect()->back()->with('success', 'Recent activities cleared successfully.');
    }

    public function bulkArchive(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $ids = $request->ids;

        $updated = SeniorCitizenRecord::
            whereIn('id', $ids)
            ->update(['status' => 'archived']);

        if ($updated > 0) {
            $this->logActivity('bulk archived', "{$updated} senior(s)", 'Multiple');

            return response()->json([
                'success' => true,
                'message' => "Successfully archived {$updated} record(s)."
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No records were archived.'
        ], 400);
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $ids = $request->ids;

        $updated = SeniorCitizenRecord::
            whereIn('id', $ids)
            ->update(['status' => 'active']);

        if ($updated > 0) {
            $this->logActivity('bulk restored', "{$updated} senior(s)", 'Multiple');

            return response()->json([
                'success' => true,
                'message' => "Successfully restored {$updated} record(s)."
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No records were restored.'
        ], 400);
    }

    public function exportSeniors(Request $request)
    {
        $ids = $request->get('ids');

        if ($ids) {
            $idsArray = explode(',', $ids);
            $seniors = SeniorCitizenRecord::
                whereIn('id', $idsArray)
                ->get();
        } else {
            $seniors = SeniorCitizenRecord::
                where('status', 'active')
                ->whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')
                ->get();
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="senior_citizens_export_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($seniors) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Control Number',
                'Full Name',
                'Address',
                'Barangay',
                'Birth Date',
                'Sex',
                'Age',
                'Contact Number',
                'Status'
            ]);

            foreach ($seniors as $senior) {
                fputcsv($file, [
                    $senior->control_number ?? '',
                    $senior->full_name ?? '',
                    $senior->address ?? '',
                    $senior->barangay ?? '',
                    $senior->birth_date ?? '',
                    $senior->sex ?? '',
                    $senior->age ?? '',
                    $senior->contact_number ?? '',
                    $senior->status ? $senior->status->value : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSeniorsPdf(Request $request)
    {
        $ids = $request->get('ids');
        $barangay = $request->get('barangay');
        $search = $request->get('search');

        $query = SeniorCitizenRecord::where('status', 'active')
            ->whereNotNull('birth_date')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60');

        if ($ids) {
            $idsArray = explode(',', $ids);
            $query->whereIn('id', $idsArray);
        }

        if ($barangay) {
            $query->where('barangay', $barangay);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('middle_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        $seniors = $query->get();

        $data = [
            'seniors' => $seniors,
            'date' => now()->format('F d, Y'),
            'total' => $seniors->count(),
            'barangay' => $barangay,
            'search' => $search
        ];

        $pdf = PDF::loadView('admin.senior.seniors-pdf', $data);

        $filename = 'senior_citizens';
        if ($barangay) {
            $filename .= '_' . str_replace(' ', '_', strtolower($barangay));
        }
        $filename .= '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    private function logActivity($action, $name, $identifier)
    {
        SeniorActivityLog::log($action, $name, $identifier);
    }

    private function getAllBarangays(): array
    {
        return [
            'Acacia', 'Adlas', 'Anahaw I', 'Anahaw II', 'Balite I', 'Balite II', 'Balubad', 'Banaba', 'Batas',
            'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho', 'Bulihan', 'Cabangaan', 'Carmen', 'Hoyo', 'Hukay', 'Iba',
            'Inchican', 'Ipil I', 'Ipil II', 'Kalubkob', 'Kaong', 'Lalaan I', 'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil',
            'Maguyam', 'Malabag', 'Malaking Tatyao', 'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I', 'San Vicente II', 'Santol',
            'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II', 'Tubuan III', 'Ulat', 'Yakal'
        ];
    }

    private function getBarangayCodes(): array
    {
        return [
            'Acacia' => 'ACA', 'Adlas' => 'ADL', 'Anahaw I' => 'ANA1', 'Anahaw II' => 'ANA2',
            'Balite I' => 'BLT1', 'Balite II' => 'BLT2', 'Balubad' => 'BLB', 'Banaba' => 'BAN',
            'Batas' => 'BAT', 'Biga I' => 'BIG1', 'Biga II' => 'BIG2', 'Biluso' => 'BIL',
            'Bucal' => 'BUC', 'Buho' => 'BUH', 'Bulihan' => 'BUL', 'Cabangaan' => 'CAB',
            'Carmen' => 'CAR', 'Hoyo' => 'HOY', 'Hukay' => 'HUK', 'Iba' => 'IBA',
            'Inchican' => 'INC', 'Ipil I' => 'IPI1', 'Ipil II' => 'IPI2', 'Kalubkob' => 'KAL',
            'Kaong' => 'KAO', 'Lalaan I' => 'LAL1', 'Lalaan II' => 'LAL2', 'Litlit' => 'LIT',
            'Lucsuhin' => 'LUC', 'Lumil' => 'LUM', 'Maguyam' => 'MAG', 'Malabag' => 'MLB',
            'Malaking Tatyao' => 'MLK', 'Mataas na Burol' => 'MTA', 'Munting Ilog' => 'MUN',
            'Narra I' => 'NAR1', 'Narra II' => 'NAR2', 'Narra III' => 'NAR3', 'Paligawan' => 'PAL',
            'Pasong Langka' => 'PAS', 'Barangay I (Poblacion)' => 'POB1', 'Barangay II (Poblacion)' => 'POB2',
            'Barangay III (Poblacion)' => 'POB3', 'Barangay IV (Poblacion)' => 'POB4', 'Barangay V (Poblacion)' => 'POB5',
            'Pooc I' => 'POO1', 'Pooc II' => 'POO2', 'Pulong Bunga' => 'PLB', 'Pulong Saging' => 'PLS',
            'Puting Kahoy' => 'PUT', 'Sabutan' => 'SAB', 'San Miguel I' => 'SMI1', 'San Miguel II' => 'SMI2',
            'San Vicente I' => 'SVI1', 'San Vicente II' => 'SVI2', 'Santol' => 'SAN', 'Tartaria' => 'TAR',
            'Tibig' => 'TIB', 'Toledo' => 'TOL', 'Tubuan I' => 'TUB1', 'Tubuan II' => 'TUB2',
            'Tubuan III' => 'TUB3', 'Ulat' => 'ULA', 'Yakal' => 'YAK'
        ];
    }
}
