<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FeaHeavenUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'feaheaven@gmail.com'],
            [
                'name' => 'Fea Heaven',
                'password' => Hash::make('feaheaven'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
