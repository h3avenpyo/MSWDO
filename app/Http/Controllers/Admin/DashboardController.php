<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

        // Optimize query - only select needed fields
        $officers = User::on('mswdo_admin')
            ->where('email', '!=', 'admin@mswdo.test')
            ->select('id', 'name', 'email', 'role', 'created_at', 'is_active')
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
            ->select('id', 'control_number')
            ->orderByDesc('id')
            ->first();
        
        $nextSequence = $lastRecord ? intval(substr($lastRecord->control_number, -6)) + 1 : 1;
        
        return view('admin.senior-registration', compact('nextSequence', 'seniorCreated'));
    }

    public function seniorMasterlist(Request $request)
    {
        $query = SeniorCitizenRecord::on('mswdo_senior')
            ->select('id', 'control_number', 'full_name', 'address', 'barangay', 'birth_date', 'month', 'sex', 'status');

        if ($request->filled('barangay') && $request->barangay !== '') {
            $query->where('barangay', $request->barangay);
        }

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        $seniors = $query->orderByDesc('created_at')->paginate(15);

        // Calculate age dynamically for each senior
        $seniors->getCollection()->transform(function ($senior) {
            $birthDate = Carbon::parse($senior->birth_date);
            $senior->age = $birthDate->age;
            return $senior;
        });

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

        // Generate QR code locally
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

        // Generate avatar locally
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
            'qr_code' => $qrCodeData,
            'qr_code_image' => $qrCodePath,
            'avatar_image' => $avatarPath,
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

    public function showIdCard($id)
    {
        if (! session('admin_user_id')) {
            return redirect()->route('admin.login.form');
        }

        $senior = SeniorCitizenRecord::on('mswdo_senior')->findOrFail($id);

        return view('admin.id-card', compact('senior'));
    }

    public function generateIdCard(Request $request, $id)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $senior = SeniorCitizenRecord::on('mswdo_senior')->findOrFail($id);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'blood_type' => 'nullable|string|max:10',
            'civil_status' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
        ]);

        // Auto-generate unique senior_id_number if not already set
        if (!$senior->senior_id_number) {
            $senior->senior_id_number = $senior->generateSeniorIdNumber();
        }

        // Upload photo
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $senior->id . '.' . $file->getClientOriginalExtension();
            
            // Ensure uploads directory exists
            $uploadDir = public_path('uploads/senior_photos');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $file->move($uploadDir, $filename);
            $senior->photo = 'uploads/senior_photos/' . $filename;
        }

        // Generate QR code link
        $senior->qr_code = route('admin.senior.profile', $senior->id);
        $senior->date_issued = now()->toDateString();
        $senior->blood_type = $request->blood_type;
        $senior->civil_status = $request->civil_status;
        $senior->emergency_contact_name = $request->emergency_contact_name;
        $senior->emergency_contact_number = $request->emergency_contact_number;
        $senior->emergency_contact_relationship = $request->emergency_contact_relationship;

        $senior->save();

        return redirect()->route('admin.senior.id-card', $senior->id)->with('success', 'ID Card generated successfully.');
    }

    public function reprintIdCard($id)
    {
        if (! session('admin_user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $senior = SeniorCitizenRecord::on('mswdo_senior')->findOrFail($id);
        
        $senior->print_count = $senior->print_count + 1;
        $senior->last_printed_at = now();
        $senior->save();

        Log::info("Senior Citizen ID Card reprinted. Senior Name: {$senior->full_name}, ID Number: {$senior->senior_id_number}, Print Count: {$senior->print_count}, Printed By: " . (session('admin_user_name') ?? 'System Admin'));

        return response()->json([
            'success' => true,
            'print_count' => $senior->print_count,
            'last_printed_at' => $senior->last_printed_at->format('M d, Y h:i A')
        ]);
    }

    public function downloadIdCardPdf($id)
    {
        try {
            if (! session('admin_user_id')) {
                return redirect()->route('admin.login.form');
            }

            $senior = SeniorCitizenRecord::on('mswdo_senior')
                ->select('id', 'full_name', 'senior_id_number', 'control_number', 'birth_date', 'age', 'sex', 'barangay', 'address', 'photo', 'blood_type', 'civil_status', 'emergency_contact_name', 'emergency_contact_number', 'emergency_contact_relationship', 'date_issued', 'qr_code', 'qr_code_image', 'avatar_image')
                ->findOrFail($id);

            if (!$senior->senior_id_number) {
                return redirect()->route('admin.senior.id-card', $id)->with('error', 'Please generate the ID Card first before downloading.');
            }

            $pdf = Pdf::loadView('admin.id-card-pdf', compact('senior'));
            
            // Use standard A4 paper for better compatibility and speed
            $pdf->setPaper('a4', 'landscape');
            
            // Disable remote images - using local assets only for speed
            $pdf->setOption('isRemoteEnabled', false);
            
            // Disable PHP and JavaScript for speed
            $pdf->setOption('enable_php', false);
            $pdf->setOption('enable_javascript', false);

            return $pdf->download('senior-id-' . $senior->senior_id_number . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            
            return redirect()->route('admin.senior.id-card', $id)
                ->with('error', 'Failed to generate PDF. Please try again.');
        }
    }

    public function seniorProfile($id)
    {
        $senior = SeniorCitizenRecord::on('mswdo_senior')->findOrFail($id);

        return view('admin.senior-profile', compact('senior'));
    }
}
