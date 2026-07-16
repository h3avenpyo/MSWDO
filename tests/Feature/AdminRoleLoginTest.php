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
}
