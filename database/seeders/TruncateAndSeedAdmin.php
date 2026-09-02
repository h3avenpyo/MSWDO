<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TruncateAndSeedAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate social case study related tables in correct order (respecting dependencies)
        $tables = [
            'social_case_report_release_logs',
            'social_case_reports',
            'online_request_attachments',
            'online_requests',
            'eligibility_audit_logs',
            'case_rejections',
            'assistance_records',
            'beneficiary_intakes',
            'family_members',
            'case_interviews',
            'social_case_studies',
            'clients',
            'password_reset_requests',
            'users',
        ];

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed the administrator account
        User::create([
            'name' => 'Fred Calos',
            'email' => 'fred.calos@mswdo.test',
            'password' => Hash::make('Password1!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->command->info('Social case study tables truncated successfully.');
        $this->command->info('Administrator account created: Fred Calos (fred.calos@mswdo.test) with password: Password1!');
    }
}
