<?php

namespace Database\Seeders;

use App\Models\Wcpu\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BcpcDeskOfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userData = [
            'first_name' => 'BCPC',
            'last_name' => 'Desk Officer',
            'username' => 'bcpcdesk',
            'password' => Hash::make('Password1!'),
            'position' => 'Desk Officer',
            'department' => 'BCPC',
            'barangay' => 'Biga II',
            'is_active' => true,
            'bcpc_is_active' => true,
            'vawc_is_active' => false,
            'failed_login_attempts' => 0,
            'account_locked' => false,
            'locked_until' => null,
        ];

        $user = User::withTrashed()->where('email', 'bcpcdesk@gmail.com')->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }
            $user->update($userData);
        } else {
            User::create(array_merge($userData, [
                'email' => 'bcpcdesk@gmail.com',
            ]));
        }
    }
}
