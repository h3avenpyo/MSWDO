<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SecureSeedAccount::updateOrCreate(User::class,
            ['email' => 'admin@mswdo.test'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        SecureSeedAccount::updateOrCreate(User::class,
            ['email' => 'social@mswdo.test'],
            [
                'name' => 'Social Case Officer',
                'role' => 'social_worker',
                'status' => 'active',
            ]
        );

        SecureSeedAccount::updateOrCreate(User::class,
            ['email' => 'social2@mwsdo.test'],
            [
                'name' => 'Social Case Eligibility Checker',
                'role' => 'eligibility_checker',
                'status' => 'active',
            ]
        );

        SecureSeedAccount::updateOrCreate(User::class,
            ['email' => 'senior@mswdo.test'],
            [
                'name' => 'Senior Citizen Officer',
                'role' => 'staff',
                'status' => 'active',
            ]
        );

        SecureSeedAccount::updateOrCreate(User::class,
            ['email' => 'financial@mswdo.test'],
            [
                'name' => 'Financial Assistance Officer',
                'role' => 'Financial assistance officer',
                'status' => 'active',
            ]
        );
    }
}
