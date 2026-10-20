<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        $this->call(FeaHeavenUserSeeder::class);
        $this->call(InBetweenEligibleSeeder::class);

        // Curated sample data for all modules (social case, senior citizen,
        // financial assistance, WCPU desk officers and WCPU admins).
        $this->call(ModuleSeeder::class);
    }
}
