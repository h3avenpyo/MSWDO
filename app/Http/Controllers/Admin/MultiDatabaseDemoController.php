<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminProfile;
use App\Models\Financial\FinancialAssistanceApplication;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultiDatabaseDemoController extends Controller
{
    public function index()
    {
        $adminUsers = User::query()->count();
        $adminProfiles = AdminProfile::query()->count();
        $financialApplications = FinancialAssistanceApplication::query()->count();
        $seniorRecords = SeniorCitizenRecord::query()->count();

        return view('admin.multi-database-demo', compact(
            'adminUsers',
            'adminProfiles',
            'financialApplications',
            'seniorRecords',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'module' => ['required', 'in:admin,financial,senior'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        match ($request->module) {
            'admin' => AdminProfile::create([
                'user_id' => 1,
                'position' => $request->title,
                'employee_id' => 'EMP-001',
                'phone' => '09170000000',
                'address' => 'MSWDO',
                'status' => 'active',
            ]),
            'financial' => FinancialAssistanceApplication::create([
                'application_number' => 'FA-' . now()->timestamp,
                'applicant_name' => $request->title,
                'assistance_type' => 'Emergency',
                'amount_requested' => 5000,
                'created_by' => 1,
                'status' => 'pending',
            ]),
            'senior' => SeniorCitizenRecord::create([
                'record_number' => 'SR-' . now()->timestamp,
                'first_name' => $request->title,
                'last_name' => '',
                'birth_date' => now()->subYears(70)->toDateString(),
                'osca_id' => 'OSCA-' . now()->timestamp,
                'created_by' => 1,
                'status' => 'active',
            ]),
        };

        return back()->with('success', 'Dummy record created successfully.');
    }
}
