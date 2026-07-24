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

    public function test_financial_step_roles_can_be_stored_and_cast(): void
    {
        $email1 = 'fin.step1.' . uniqid() . '@example.com';
        $email2 = 'fin.step2.' . uniqid() . '@example.com';

        // Test financialstep1
        $this->post(route('admin.officers.store'), [
            'name' => 'Fin Step 1 Officer ' . uniqid(),
            'email' => $email1,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'financialstep1',
            'phone' => '09170000002',
            'status' => 'active',
        ])->assertRedirect(route('admin.add-officers'));

        $user1 = User::where('email', $email1)->first();
        $this->assertNotNull($user1);
        $this->assertSame(\App\Enums\UserRole::FinancialStep1, $user1->role);
        $this->assertSame('Financial Assistance Step 1', $user1->role->label());

        // Test financialstep2
        $this->post(route('admin.officers.store'), [
            'name' => 'Fin Step 2 Officer ' . uniqid(),
            'email' => $email2,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'financialstep2',
            'phone' => '09170000003',
            'status' => 'active',
        ])->assertRedirect(route('admin.add-officers'));

        $user2 = User::where('email', $email2)->first();
        $this->assertNotNull($user2);
        $this->assertSame(\App\Enums\UserRole::FinancialStep2, $user2->role);
        $this->assertSame('Financial Assistance Step 2', $user2->role->label());
    }
}
