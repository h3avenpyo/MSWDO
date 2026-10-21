<?php

namespace Database\Seeders;

use App\Models\Wcpu\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Sample WCPU administrator accounts for the SWD (VAWC) and BCPC departments.
 *
 * All dummy accounts share the password: Password1!
 *
 * | Email                 | Position      | Department |
 * |-----------------------|---------------|------------|
 * | swd.admin@wcpu.test   | Administrator | VAWC       |
 * | bcpc.admin@wcpu.test  | Administrator | BCPC       |
 */
class WcpuAdminSeeder extends Seeder
{
    public const PASSWORD = 'Password1!';

    public function run(): void
    {
        $admins = [
            [
                'email' => 'swd.admin@wcpu.test',
                'username' => 'swd.admin',
                'first_name' => 'Lara',
                'last_name' => 'Canales',
                'department' => 'VAWC',
            ],
            [
                'email' => 'bcpc.admin@wcpu.test',
                'username' => 'bcpc.admin',
                'first_name' => 'Charlotte',
                'last_name' => 'Bacor',
                'department' => 'BCPC',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'first_name' => $admin['first_name'],
                    'last_name' => $admin['last_name'],
                    'username' => $admin['username'],
                    'password' => Hash::make(self::PASSWORD),
                    'phone_number' => '0918' . str_pad((string) abs(crc32($admin['email'])) % 10000000, 7, '0', STR_PAD_LEFT),
                    'position' => 'Administrator',
                    'department' => $admin['department'],
                    'barangay' => null,
                    'is_active' => true,
                    'bcpc_is_active' => $admin['department'] === 'BCPC',
                    'vawc_is_active' => $admin['department'] === 'VAWC',
                    'failed_login_attempts' => 0,
                    'account_locked' => false,
                ]
            );
        }

        $this->command->info('WCPU Admin seed complete. 2 administrator accounts created (password: ' . self::PASSWORD . ').');
        $this->command->info('  SWD (VAWC) administrator: swd.admin@wcpu.test');
        $this->command->info('  BCPC administrator:       bcpc.admin@wcpu.test');
    }
}
