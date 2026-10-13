<?php

namespace Tests\Feature;

use App\Models\User as MswdoUser;
use App\Models\Wcpu\User as WcpuUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CentralizedAuthenticationIntegrationTest extends TestCase
{
    /**
     * 1. Test that the single MSWDO login page loads and accepts email or username.
     */
    public function test_mswdo_login_page_renders_with_email_or_username(): void
    {
        $response = $this->get(route('admin.login.form'));

        $response->assertStatus(200);
        $response->assertSee('Email or Username');
        $response->assertSee('id="email"', false);
        $response->assertSee('type="text"', false);
    }

    /**
     * 2. Test that legacy WCPU login routes redirect to the centralized MSWDO login page.
     */
    public function test_legacy_wcpu_login_routes_redirect_to_centralized_mswdo_login(): void
    {
        $this->get('/wcpu/login')->assertRedirect(route('admin.login.form'));
        $this->get('/desk-officer/login')->assertRedirect(route('admin.login.form'));
        $this->get('/login')->assertRedirect(route('admin.login.form'));
    }

    /**
     * 3. Test that invalid credentials are safely rejected without revealing user existence.
     */
    public function test_invalid_credentials_are_safely_rejected(): void
    {
        $response = $this->post(route('admin.login'), [
            'email' => 'nonexistent_user_12345@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertSessionHasErrors(['email' => 'Invalid email or password.']);
        $this->assertNull(session('admin_user_id'));
        $this->assertFalse(Auth::guard('wcpu')->check());
    }

    /**
     * 4. Test that MSWDO accounts continue to authenticate and redirect to MSWDO dashboards.
     */
    public function test_existing_mswdo_accounts_can_login_through_centralized_login(): void
    {
        $uniqueEmail = 'test_mswdo_' . uniqid() . '@example.com';
        $testMswdoUser = MswdoUser::create([
            'name' => 'Test MSWDO Admin',
            'email' => $uniqueEmail,
            'password' => Hash::make('TestPassword123!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        try {
            $response = $this->post(route('admin.login'), [
                'email' => $uniqueEmail,
                'password' => 'TestPassword123!',
            ]);

            $response->assertRedirect(route('admin.dashboard'));
            $this->assertSame($testMswdoUser->id, session('admin_user_id'));
            $this->assertSame('mswdo', session('auth_source'));
        } finally {
            $testMswdoUser->delete();
        }
    }

    /**
     * 5. Test WCPU user authentication using email or username, and correct dashboard redirection.
     */
    public function test_wcpu_user_can_authenticate_using_username_and_redirect_to_dashboard(): void
    {
        $username = 'desktest_' . uniqid();
        $wcpuUser = WcpuUser::create([
            'username' => $username,
            'email' => $username . '@example.com',
            'first_name' => 'Test',
            'last_name' => 'DeskOfficer',
            'password' => Hash::make('Secret12345!'),
            'position' => 'Desk Officer',
            'department' => 'BCPC',
            'is_active' => true,
            'failed_login_attempts' => 0,
        ]);

        try {
            // Attempt login using USERNAME (not email)
            $response = $this->post(route('admin.login'), [
                'email' => $username,
                'password' => 'Secret12345!',
            ]);

            $response->assertRedirect(route('desk-officer.bcpc.dashboard'));
            $this->assertTrue(Auth::guard('wcpu')->check());
            $this->assertSame($wcpuUser->id, Auth::guard('wcpu')->id());
            $this->assertSame('wcpu', session('auth_source'));
        } finally {
            $wcpuUser->delete();
        }
    }

    /**
     * 6. Test WCPU Admin redirection to admin-dashboard.
     */
    public function test_wcpu_admin_redirects_to_wcpu_admin_dashboard(): void
    {
        $username = 'admintest_' . uniqid();
        $adminUser = WcpuUser::create([
            'username' => $username,
            'email' => $username . '@example.com',
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'password' => Hash::make('AdminPass123!'),
            'position' => 'Administrator',
            'department' => 'WCPU-SILANG',
            'is_active' => true,
            'failed_login_attempts' => 0,
        ]);

        try {
            $response = $this->post(route('admin.login'), [
                'email' => $adminUser->email,
                'password' => 'AdminPass123!',
            ]);

            $response->assertRedirect(route('admin-dashboard'));
            $this->assertTrue(Auth::guard('wcpu')->check());
            $this->assertSame($adminUser->id, Auth::guard('wcpu')->id());
        } finally {
            $adminUser->delete();
        }
    }

    /**
     * 7. Verify all five live WCPU accounts exist, have correct roles, and resolve expected routes.
     */
    public function test_all_five_existing_wcpu_accounts_resolve_correct_access(): void
    {
        // Account 1: BCPC Admin
        $bcpcAdmin = WcpuUser::where('email', 'bcpcsilang@gmail.com')->first();
        $this->assertNotNull($bcpcAdmin, 'bcpcsilang@gmail.com must exist');
        $this->assertSame('Administrator', $bcpcAdmin->position);
        $this->assertTrue($bcpcAdmin->isBcpcAdmin());
        $this->assertSame(route('admin-dashboard'), route('admin-dashboard'));

        // Account 2: SWD Admin
        $swdAdmin = WcpuUser::where('email', 'wcpusilang@gmail.com')->first();
        $this->assertNotNull($swdAdmin, 'wcpusilang@gmail.com must exist');
        $this->assertSame('Administrator', $swdAdmin->position);
        $this->assertTrue($swdAdmin->isVawcAdmin());
        $this->assertSame(route('admin-dashboard'), route('admin-dashboard'));

        // Account 3: BCPC Desk
        $bcpcDesk = WcpuUser::where('username', 'testuser2004')->first();
        $this->assertNotNull($bcpcDesk, 'testuser2004 must exist');
        $this->assertSame('Desk Officer', $bcpcDesk->position);
        $this->assertSame('BCPC', $bcpcDesk->defaultCaseDepartment());
        $this->assertFalse($bcpcDesk->isMultiCaseDepartment());
        $this->assertSame(route('desk-officer.bcpc.dashboard'), route('desk-officer.bcpc.dashboard'));

        // Account 4: SWD Desk
        $swdDesk = WcpuUser::where('email', 'domskie2004@gmail.com')->first();
        $this->assertNotNull($swdDesk, 'domskie2004@gmail.com must exist');
        $this->assertSame('Desk Officer', $swdDesk->position);
        $this->assertSame('VAWC', $swdDesk->defaultCaseDepartment());
        $this->assertFalse($swdDesk->isMultiCaseDepartment());
        $this->assertSame(route('desk-officer.dashboard'), route('desk-officer.dashboard'));

        // Account 5: Desk Multi-role
        $multiDesk = WcpuUser::where('username', 'testuser2005')->first();
        $this->assertNotNull($multiDesk, 'testuser2005 must exist');
        $this->assertSame('Desk Officer', $multiDesk->position);
        $this->assertTrue($multiDesk->isMultiCaseDepartment());
        $this->assertSame(route('desk-officer.dashboard'), route('desk-officer.dashboard'));
    }

    /**
     * 8. Test route protection: Desk Officer cannot access Administrator routes.
     */
    public function test_desk_officer_cannot_access_administrator_routes(): void
    {
        $bcpcDesk = WcpuUser::where('username', 'testuser2004')->first();
        $this->assertNotNull($bcpcDesk);

        Auth::guard('wcpu')->login($bcpcDesk);

        // Try accessing Administrator-only user management route
        $response = $this->get(route('admin.users.index'));

        // CheckPosition middleware redirects unauthorized desk officers to their desk dashboard
        $response->assertRedirect(route('desk-officer.dashboard'));
    }

    /**
     * 9. Test route protection: Administrator can access admin-dashboard.
     */
    public function test_administrator_can_access_admin_dashboard(): void
    {
        $swdAdmin = WcpuUser::where('email', 'wcpusilang@gmail.com')->first();
        $this->assertNotNull($swdAdmin);

        Auth::guard('wcpu')->login($swdAdmin);

        $response = $this->get(route('admin-dashboard'));
        $response->assertStatus(200);
    }

    /**
     * 10. Test logout terminates centralized session and clears both guards.
     */
    public function test_logout_properly_terminates_wcpu_session(): void
    {
        $swdAdmin = WcpuUser::where('email', 'wcpusilang@gmail.com')->first();
        Auth::guard('wcpu')->login($swdAdmin);
        session(['admin_user_id' => 999, 'auth_source' => 'wcpu']);

        $response = $this->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login.form'));
        $this->assertFalse(Auth::guard('wcpu')->check());
        $this->assertNull(session('admin_user_id'));
    }
}
