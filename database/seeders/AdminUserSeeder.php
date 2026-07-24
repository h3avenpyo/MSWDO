<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mswdo.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('AdminPass123!'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'social@mswdo.test'],
            [
                'name' => 'Social Case Officer',
                'password' => Hash::make('password'),
                'role' => 'social_worker',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'senior@mswdo.test'],
            [
                'name' => 'Senior Citizen Officer',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'financial@mswdo.test'],
            [
                'name' => 'Financial Assistance Officer',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'status' => 'active',
            ]
        );
    }
}
