<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForcedLogoutTest extends TestCase
{
    #[Test]
    public function an_inactive_account_is_force_logged_out_by_the_status_poll(): void
    {
        $email = 'poll.user.' . uniqid() . '@example.com';
        $user = User::create([
            'name' => 'Poll User ' . uniqid(),
            'email' => $email,
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
            'password' => Hash::make('secret123'),
        ]);

        // Log the user in (mimics AuthController::login session setup).
        $this->withSession([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
        ]);

        // Active account -> not deactivated.
        $this->getJson(route('admin.check-account-status'))
            ->assertOk()
            ->assertJson(['deactivated' => false, 'authenticated' => true]);

        // Admin deactivates the account while the user is still "logged in".
        $user->update(['status' => UserStatus::Inactive]);

        // The poll now reports deactivated and invalidates the session.
        $this->getJson(route('admin.check-account-status'))
            ->assertOk()
            ->assertJson(['deactivated' => true, 'authenticated' => true]);

        // Session must be invalidated: protected page is now blocked.
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login.form'));

        // Authenticated AJAX/API requests are rejected with a 401.
        $this->getJson('/admin/social-case/api/cases')->assertStatus(401);

        $user->forceDelete();
    }

    #[Test]
    public function a_deactivated_session_is_rejected_with_json_for_api_requests(): void
    {
        $email = 'deactivated.user.' . uniqid() . '@example.com';
        $user = User::create([
            'name' => 'Deactivated User ' . uniqid(),
            'email' => $email,
            'role' => UserRole::Admin,
            'status' => UserStatus::Inactive,
            'password' => Hash::make('secret123'),
        ]);

        $this->withSession([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
        ]);

        $this->getJson('/admin/social-case/api/cases')
            ->assertStatus(401)
            ->assertJson(['error' => 'account_deactivated']);

        $user->forceDelete();
    }

    #[Test]
    public function a_deactivated_session_is_redirected_for_page_navigations(): void
    {
        $email = 'redirect.user.' . uniqid() . '@example.com';
        $user = User::create([
            'name' => 'Redirect User ' . uniqid(),
            'email' => $email,
            'role' => UserRole::Admin,
            'status' => UserStatus::Inactive,
            'password' => Hash::make('secret123'),
        ]);

        $this->withSession([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
        ]);

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login.form'))
            ->assertSessionHas('account_deactivated', true);

        $user->forceDelete();
    }

    #[Test]
    public function an_inactive_account_cannot_log_in_until_reactivated(): void
    {
        $email = 'blocked.user.' . uniqid() . '@example.com';
        $user = User::create([
            'name' => 'Blocked User ' . uniqid(),
            'email' => $email,
            'role' => UserRole::Admin,
            'status' => UserStatus::Inactive,
            'password' => Hash::make('secret123'),
        ]);

        $this->post(route('admin.login'), [
            'email' => $email,
            'password' => 'secret123',
        ])->assertSessionHas('account_deactivated', true);

        $user->forceDelete();
    }

    #[Test]
    public function a_reactivated_account_can_log_in_normally(): void
    {
        $email = 'reactivated.user.' . uniqid() . '@example.com';
        $user = User::create([
            'name' => 'Reactivated User ' . uniqid(),
            'email' => $email,
            'role' => UserRole::Admin,
            'status' => UserStatus::Inactive,
            'password' => Hash::make('secret123'),
        ]);

        // Blocked while inactive.
        $this->post(route('admin.login'), [
            'email' => $email,
            'password' => 'secret123',
        ])->assertSessionHas('account_deactivated', true);

        // Reactivation allows login.
        $user->update(['status' => UserStatus::Active]);
        $this->post(route('admin.login'), [
            'email' => $email,
            'password' => 'secret123',
        ])->assertSessionHas('admin_user_id', $user->id);

        $user->forceDelete();
    }
}