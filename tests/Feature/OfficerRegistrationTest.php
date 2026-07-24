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
            'role' => 'encoder',
            'phone' => '09170000000',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.add-officers'));

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertSame('encoder', $user->role->value);
        $this->assertSame('09170000000', $user->phone);
    }

    public function test_senior_citizen_officer_role_can_be_stored_and_cast(): void
    {
        $email = 'senior.officer.' . uniqid() . '@example.com';

        $response = $this->post(route('admin.officers.store'), [
            'name' => 'Senior Officer ' . uniqid(),
            'email' => $email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'Senior Citizen officer',
            'phone' => '09170000001',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.add-officers'));

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertSame(\App\Enums\UserRole::SeniorCitizenOfficer, $user->role);
        $this->assertSame('Senior Citizen Officer', $user->role->label());
    }
}
