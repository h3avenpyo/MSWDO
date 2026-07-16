<?php

namespace Database\Seeders;

use App\Models\Admin\AdminProfile;
use App\Models\Financial\FinancialAssistanceApplication;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class MultiDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password123'),
            ]
        );

        AdminProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'position' => 'Administrator',
                'employee_id' => 'EMP-001',
                'phone' => '09170000000',
                'address' => 'MSWDO Office',
                'status' => 'active',
            ]
        );

        FinancialAssistanceApplication::firstOrCreate(
            ['application_number' => 'FA-1001'],
            [
                'applicant_name' => 'Sample Applicant',
                'assistance_type' => 'Emergency',
                'amount_requested' => 5000,
                'created_by' => $user->id,
                'status' => 'pending',
            ]
        );

        SeniorCitizenRecord::firstOrCreate(
            ['record_number' => 'SR-1001'],
            [
                'first_name' => 'Sample',
                'middle_name' => null,
                'last_name' => 'Senior Citizen',
                'birth_date' => '1950-01-01',
                'osca_id' => 'OSCA-1001',
                'created_by' => $user->id,
                'status' => 'active',
            ]
        );
    }
}
