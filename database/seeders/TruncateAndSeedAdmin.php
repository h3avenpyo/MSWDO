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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $tableObj) {
            $tableName = ((array)$tableObj)[key((array)$tableObj)];
            if ($tableName !== 'migrations') {
                DB::table($tableName)->truncate();
                $this->command->info("Truncated table: {$tableName}");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        // Seed the single administrator account
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@mswdo.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'position' => 'MSWDO Administrator',
            'signature_position' => 'mswdo_officer',
        ]);

        $this->command->info('Database truncated successfully.');
        $this->command->info('Administrator account created:');
        $this->command->info('  Email: admin@mswdo.test');
        $this->command->info('  Password: password');
    }
}
