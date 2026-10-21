<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeds a curated sample dataset for every module of the system:
 *
 *  - Social Case Study module (MSWDO1)
 *  - Senior Citizen module (MSWDO1)
 *  - Financial Assistance module (MSWDO1)
 *  - WCPU desk officers + sample cases (WCPU database)
 *  - WCPU SWD / BCPC administrators (WCPU database)
 *
 * Run with: php artisan db:seed --class=Database\\Seeders\\ModuleSeeder
 */
class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // MSWDO1 database
            SocialCaseModuleSeeder::class,
            SeniorCitizenModuleSeeder::class,
            FinancialModuleSeeder::class,

            // WCPU database (u341236762_wcpu_silang2)
            WcpuAdminSeeder::class,
            WcpuDeskOfficerSeeder::class,
        ]);
    }
}
