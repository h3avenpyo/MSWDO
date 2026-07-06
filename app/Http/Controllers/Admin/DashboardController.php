<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if just logged in and pass to view
        $justLoggedIn = session('admin_just_logged_in', false);
        
        // Clear the just logged in flag after first dashboard visit
        if ($justLoggedIn) {
            session()->forget('admin_just_logged_in');
        }

        $officers = User::on('mswdo_admin')
            ->where('email', '!=', 'admin@mswdo.test')
            ->orderByDesc('created_at')
            ->get();

        $data = [
            'totalCases' => 0,
            'pendingCases' => 0,
            'resolvedCases' => 0,
            'totalUsers' => $officers->count(),
            'staffPerformance' => [],
            'recentActivities' => [],
            'casesRequiringAttention' => [],
            'userOverview' => [
                'totalAdmins' => 0,
                'totalSocialWorkers' => 0,
                'totalStaff' => 0,
                'activeUsers' => 0,
                'inactiveUsers' => 0,
            ],
            'reportsSummary' => [
                'casesThisMonth' => 0,
                'closedThisMonth' => 0,
                'pendingCases' => 0,
                'generatedReports' => 0,
                'financialReleased' => 0,
            ],
            'officers' => $officers,
            'justLoggedIn' => $justLoggedIn,
        ];

        $data['caseDistribution'] = [];
        $data['barangayStats'] = [];

        return view('admin.dashboard', $data);
    }

    public function financial()
    {
        return view('admin.financial');
    }

    public function senior()
    {
        // Check if just logged in and pass to view
        $justLoggedIn = session('admin_just_logged_in', false);
        
        // Clear the just logged in flag after first visit
        if ($justLoggedIn) {
            session()->forget('admin_just_logged_in');
        }

        // Fetch senior citizen records
        $totalSeniors = SeniorCitizenRecord::on('mswdo_senior')->count();
        $activeSeniors = SeniorCitizenRecord::on('mswdo_senior')->where('status', 'active')->count();
        $pendingSeniors = SeniorCitizenRecord::on('mswdo_senior')->where('status', 'pending')->count();
        $recentSeniors = SeniorCitizenRecord::on('mswdo_senior')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $data = [
            'totalSeniors' => $totalSeniors,
            'activeSeniors' => $activeSeniors,
            'pendingSeniors' => $pendingSeniors,
            'recentSeniors' => $recentSeniors,
            'justLoggedIn' => $justLoggedIn,
        ];

        return view('admin.senior', $data);
    }

    public function seniorBirthdays()
    {
        $today = now();
        $endDate = now()->addDays(30);
        $todayMD = $today->format('m-d');
        $endMD = $endDate->format('m-d');

        $query = SeniorCitizenRecord::on('mswdo_senior')
            ->where('status', 'active')
            ->whereNotNull('birth_date')
            ->orderByRaw("MONTH(birth_date), DAY(birth_date)");

        if ($todayMD <= $endMD) {
            $query->whereRaw("DATE_FORMAT(birth_date, '%m-%d') BETWEEN ? AND ?", [$todayMD, $endMD]);
        } else {
            $query->where(function ($q) use ($todayMD, $endMD) {
                $q->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$todayMD])
                  ->orWhereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$endMD]);
            });
        }

        $celebrants = $query->get();

        $todayFormatted = $today->format('M d, Y');
        $endFormatted = $endDate->format('M d, Y');

        return view('admin.senior-birthdays', compact('celebrants', 'todayFormatted', 'endFormatted'));
    }

    public function seniorRegistration()
    {
        // Check if senior was just created and pass to view
        $seniorCreated = session('senior_created', false);
        
        // Clear the flag after first visit
        if ($seniorCreated) {
            session()->forget('senior_created');
        }

        // Get the next sequence number for control number generation
        $year = date('Y');
        $lastRecord = SeniorCitizenRecord::on('mswdo_senior')
            ->where('year_applied', $year)
            ->orderByDesc('id')
            ->first();
        
        $nextSequence = $lastRecord ? intval(substr($lastRecord->control_number, -6)) + 1 : 1;
        
        return view('admin.senior-registration', compact('nextSequence', 'seniorCreated'));
    }

    public function seniorMasterlist(Request $request)
    {
        $query = SeniorCitizenRecord::on('mswdo_senior');

        if ($request->filled('barangay') && $request->barangay !== '') {
            $query->where('barangay', $request->barangay);
        }

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        $seniors = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.senior-masterlist', compact('seniors'));
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
            'month' => ['required', 'string'],
            'sex' => ['required', 'in:Male,Female'],
            'age' => ['required', 'integer', 'min:60', 'max:150'],
            'contact_number' => ['required', 'string', 'max:20'],
            'philsys_number' => ['nullable', 'string', 'max:255'],
            'rrn_number' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check for duplicate name (case-insensitive)
        $existingName = SeniorCitizenRecord::on('mswdo_senior')
            ->whereRaw('LOWER(full_name) = ?', [strtolower($request->full_name)])
            ->first();

        if ($existingName) {
            return back()->withErrors(['full_name' => 'A senior citizen with this name already exists.'])->withInput();
        }

        SeniorCitizenRecord::on('mswdo_senior')->create([
            'year_applied' => $request->year_applied,
            'control_number' => $request->control_number,
            'full_name' => $request->full_name,
            'address' => $request->address,
            'barangay' => $request->barangay,
            'birth_date' => $request->birth_date,
            'month' => $request->month,
            'sex' => $request->sex,
            'age' => $request->age,
            'contact_number' => $request->contact_number,
            'philsys_number' => $request->philsys_number,
            'rrn_number' => $request->rrn_number,
            'remarks' => $request->remarks,
            'created_by' => session('admin_user_id'),
            'status' => 'active',
        ]);

        return redirect()->route('admin.senior.registration')->with('success', 'Senior citizen registered successfully.')->with('senior_created', true);
    }

    public function archiveSenior($id)
    {
        $senior = SeniorCitizenRecord::on('mswdo_senior')->find($id);

        if (!$senior) {
            return redirect()->route('admin.senior.masterlist')->with('error', 'Senior citizen not found.');
        }

        $senior->update([
            'status' => 'archived',
        ]);

        return redirect()->route('admin.senior.masterlist')->with('success', 'Senior citizen archived successfully.');
    }

    public function addOfficers()
    {
        // Check if officer was just created and pass to view
        $officerCreated = session('officer_created', false);
        
        // Clear the flag after first visit
        if ($officerCreated) {
            session()->forget('officer_created');
        }

        $officers = User::on('mswdo_admin')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.add-officers', compact('officers', 'officerCreated'));
    }

    public function storeOfficer(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $exists = User::on('mswdo_admin')
                    ->whereRaw('LOWER(name) = ?', [strtolower($value)])
                    ->exists();
                if ($exists) {
                    $fail('An officer with this name already exists.');
                }
            }],
            'email' => ['required', 'email', 'unique:mswdo_admin.users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        User::on('mswdo_admin')->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.add-officers')->with('success', 'Officer created successfully.')->with('officer_created', true);
    }
}
