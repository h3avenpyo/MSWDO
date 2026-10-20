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
        // 1. Truncate MSWDO1 tables (except migrations)
        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS = 0;');

        $mswdoTables = DB::connection('mysql')->select('SHOW TABLES');
        foreach ($mswdoTables as $tableObj) {
            $tableName = ((array)$tableObj)[key((array)$tableObj)];
            if ($tableName !== 'migrations') {
                DB::connection('mysql')->table($tableName)->truncate();
                if ($this->command) {
                    $this->command->info("Truncated MSWDO1 table: {$tableName}");
                }
            }
        }

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS = 1;');

        // 2. Truncate WCPU (u341236762_wcpu_silang2) preserving migrations, barangays, case_types
        DB::connection('wcpu')->statement('SET FOREIGN_KEY_CHECKS = 0;');

        $wcpuTables = DB::connection('wcpu')->select('SHOW TABLES');
        $wcpuExcluded = ['migrations', 'barangays', 'case_types'];
        foreach ($wcpuTables as $tableObj) {
            $tableName = ((array)$tableObj)[key((array)$tableObj)];
            if (!in_array($tableName, $wcpuExcluded)) {
                DB::connection('wcpu')->table($tableName)->truncate();
                if ($this->command) {
                    $this->command->info("Truncated WCPU table: {$tableName}");
                }
            }
        }

        DB::connection('wcpu')->statement('SET FOREIGN_KEY_CHECKS = 1;');

        // 3. Seed administrator account in MSWDO1
        User::create([
            'name' => 'Fredrick Calos',
            'email' => 'fredcalos@gmail.com',
            'password' => 'Password1!',
            'role' => 'admin',
            'status' => 'active',
            'position' => 'MSWDO Administrator',
            'signature_position' => 'mswdo_officer',
        ]);

        // 4. Seed default in-between benefit configuration
        DB::connection('mysql')->table('in_between_benefit_configurations')->insert([
            'id' => 1,
            'benefit_name' => 'In-Between Birthday Cash Gift',
            'description' => 'Municipality of Silang In-Between Birthday Cash Gift for Senior Citizens',
            'benefit_amount' => 1000.00,
            'milestone_ages' => json_encode([80, 85, 90, 95, 100]),
            'eligible_intervals' => json_encode(['81-84', '86-89', '91-94', '96-99']),
            'is_active' => 1,
            'effective_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($this->command) {
            $this->command->info('Databases truncated successfully.');
            $this->command->info('Administrator account created:');
            $this->command->info('  Name: Fredrick Calos');
            $this->command->info('  Email: fredcalos@gmail.com');
            $this->command->info('  Password: Password1!');
        }
    }
}
