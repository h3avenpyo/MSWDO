<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function statistics()
    {
        $data = [
            'caseDistribution' => [],
            'barangayStats' => [],
        ];

        return view('admin.statistics', $data);
    }

    public function financial()
    {
        return view('admin.financial');
    }

    public function senior()
    {
        return view('admin.senior');
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
