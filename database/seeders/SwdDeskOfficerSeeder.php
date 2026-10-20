<?php

namespace Database\Seeders;

use App\Models\Wcpu\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SwdDeskOfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userData = [
            'first_name' => 'GERALD',
            'last_name' => 'SUMAYLO',
            'username' => 'louissumaylo',
            'password' => Hash::make('Password1!'),
            'position' => 'Desk Officer',
            'department' => 'VAWC',
            'barangay' => 'Biga II',
            'is_active' => true,
            'bcpc_is_active' => false,
            'vawc_is_active' => true,
            'failed_login_attempts' => 0,
            'account_locked' => false,
            'locked_until' => null,
        ];

        $user = User::withTrashed()->where('email', 'louissumaylo@gmail.com')->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }
            $user->update($userData);
        } else {
            User::create(array_merge($userData, [
                'email' => 'louissumaylo@gmail.com',
            ]));
        }

        // Clean up temporary gewaosumaylo test account if present
        User::withTrashed()->where('email', 'gewaosumaylo@gmail.com')->forceDelete();
    }
}
