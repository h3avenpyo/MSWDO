<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SocialCaseStudyOfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::on('mswdo_admin')->updateOrCreate(
            ['email' => 'social.case@mswdo.test'],
            [
                'name' => 'Social Case Study Officer',
                'password' => Hash::make('SocialCase123!'),
                'role' => 'Social Case Study officer',
                'phone' => '09170000000',
                'status' => 'active',
            ]
        );
    }
}
