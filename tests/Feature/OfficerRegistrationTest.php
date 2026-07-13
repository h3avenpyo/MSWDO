<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class OfficerRegistrationTest extends TestCase
{
    use WithoutMiddleware;

    public function test_officer_registration_can_store_password_role_and_phone(): void
    {
        $email = 'jane.' . uniqid() . '@example.com';

        $response = $this->post(route('admin.officers.store'), [
            'name' => 'Jane Officer ' . uniqid(),
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'Financial assistance officer',
            'phone' => '09170000000',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.add-officers'));

        $user = User::on('mswdo_admin')->where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertSame('Financial assistance officer', $user->role);
        $this->assertSame('09170000000', $user->phone);
    }
}
