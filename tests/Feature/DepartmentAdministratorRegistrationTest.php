<?php

namespace Tests\Feature;

use App\Models\Wcpu\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DepartmentAdministratorRegistrationTest extends TestCase
{
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'officer_test',
            'database.connections.officer_test' => ['driver' => 'sqlite', 'database' => ':memory:'],
            'database.connections.wcpu' => ['driver' => 'sqlite', 'database' => ':memory:'],
        ]);
        app('db')->purge('wcpu');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
        });
        Schema::connection('wcpu')->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('position');
            $table->string('department');
            $table->string('barangay')->nullable();
            $table->boolean('is_active');
            $table->boolean('bcpc_is_active');
            $table->boolean('vawc_is_active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function test_department_administrators_are_created_in_the_correct_portal(): void
    {
        foreach (['bcpc_admin' => 'BCPC', 'swd_admin' => 'VAWC'] as $role => $department) {
            $email = $role.'@example.com';
            $this->post(route('admin.officers.store'), [
                'name' => 'Jane Department Admin',
                'email' => $email,
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => $role,
                'status' => 'active',
            ])->assertSessionHasNoErrors()->assertRedirect(route('admin.add-officers'));

            $user = User::where('email', $email)->firstOrFail();
            $this->assertSame('Administrator', $user->position);
            $this->assertSame($department, $user->department);
            $this->assertNull($user->barangay);
            $this->assertTrue($department === 'BCPC' ? $user->isBcpcAdmin() : $user->isVawcAdmin());
            $this->assertTrue(Hash::check('Password123!', $user->password));
        }
        $this->assertDatabaseCount('users', 0, 'officer_test');
    }
}
