<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRoleLoginTest extends TestCase
{
    use WithoutMiddleware;

    public function test_social_case_officer_can_login_only_to_social_case_module(): void
    {
        $user = User::create([
            'name' => 'Social Case Officer',
            'email' => 'social.' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'social_worker',
            'phone' => '09170000000',
            'status' => 'active',
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'Password123!',
            'role' => 'Social Case Study',
        ]);

        $response->assertRedirect(route('admin.social-case.dashboard'));
        $this->assertSame($user->id, session('admin_user_id'));
        $this->assertSame('social_worker', session('admin_user_role'));
    }

    public function test_financial_step_officers_can_login_via_single_financial_form(): void
    {
        $step1User = User::create([
            'name' => 'Step 1 Officer',
            'email' => 'step1.' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'financialstep1',
            'phone' => '09170000001',
            'status' => 'active',
        ]);

        $step2User = User::create([
            'name' => 'Step 2 Officer',
            'email' => 'step2.' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'financialstep2',
            'phone' => '09170000002',
            'status' => 'active',
        ]);

        // Login Step 1 user using single "Financial Assistance Officer" form option
        $response1 = $this->post(route('admin.login'), [
            'email' => $step1User->email,
            'password' => 'Password123!',
            'role' => 'Financial Assistance Officer',
        ]);
        $response1->assertRedirect(route('admin.financial.dashboard'));
        $this->assertSame($step1User->id, session('admin_user_id'));
        $this->assertSame('financialstep1', session('admin_user_role'));

        // Login Step 2 user using single "Financial Assistance Officer" form option
        $response2 = $this->post(route('admin.login'), [
            'email' => $step2User->email,
            'password' => 'Password123!',
            'role' => 'Financial Assistance Officer',
        ]);
        $response2->assertRedirect(route('admin.financial.financialstep2'));
        $this->assertSame($step2User->id, session('admin_user_id'));
        $this->assertSame('financialstep2', session('admin_user_role'));
    }
}
