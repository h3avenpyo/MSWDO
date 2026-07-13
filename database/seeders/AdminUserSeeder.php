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
        User::on('mswdo_admin')->updateOrCreate(
            ['email' => 'admin@mswdo.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('AdminPass123!'),
                'role' => 'Admin',
                'status' => 'active',
            ]
        );

        User::on('mswdo_admin')->updateOrCreate(
            ['email' => 'senior@mswdo.test'],
            [
                'name' => 'Senior Citizen Officer',
                'password' => Hash::make('password'),
                'role' => 'Senior Citizen officer',
                'status' => 'active',
            ]
        );
    }
}
