<?php

namespace Tests\Feature;

use App\Mail\LoginCodeMail;
use App\Models\EmailLoginCode;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailCodeLoginTest extends TestCase
{
    use WithoutMiddleware;

    public function test_user_can_send_and_verify_email_code(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Fea Heaven',
            'email' => 'feaheaven.' . uniqid() . '@example.com',
            'password' => Hash::make('feaheaven'),
            'role' => 'admin',
            'phone' => '09170000000',
            'status' => 'active',
        ]);

        $response = $this->post(route('admin.login.code.send'), [
            'email' => $user->email,
            'role' => 'Admin',
        ]);

        $response->assertSessionHas('code_sent');

        Mail::assertSent(LoginCodeMail::class, function (LoginCodeMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseHas('email_login_codes', [
            'user_id' => $user->id,
            'email' => $user->email,
            'used_at' => null,
        ]);

        $code = '';
        Mail::assertSent(LoginCodeMail::class, function (LoginCodeMail $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $verify = $this->post(route('admin.login.code.verify'), [
            'email' => $user->email,
            'role' => 'Admin',
            'code' => $code,
        ]);

        $verify->assertRedirect(route('admin.dashboard'));
        $this->assertSame($user->id, session('admin_user_id'));
        $this->assertSame('admin', session('admin_user_role'));

        $this->assertNotNull(EmailLoginCode::where('user_id', $user->id)->first()->used_at);
    }

    public function test_wrong_code_is_rejected(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Fea Heaven',
            'email' => 'feaheaven.' . uniqid() . '@example.com',
            'password' => Hash::make('feaheaven'),
            'role' => 'admin',
            'phone' => '09170000000',
            'status' => 'active',
        ]);

        $this->post(route('admin.login.code.send'), [
            'email' => $user->email,
            'role' => 'Admin',
        ]);

        $verify = $this->post(route('admin.login.code.verify'), [
            'email' => $user->email,
            'role' => 'Admin',
            'code' => '000000',
        ]);

        $verify->assertSessionHasErrors('code');
        $this->assertNull(session('admin_user_id'));
    }

    public function test_send_and_verify_work_via_ajax_json(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Fea Heaven',
            'email' => 'feaheaven.' . uniqid() . '@example.com',
            'password' => Hash::make('feaheaven'),
            'role' => 'admin',
            'phone' => '09170000000',
            'status' => 'active',
        ]);

        $send = $this->postJson(route('admin.login.code.send'), [
            'email' => $user->email,
            'role' => 'Admin',
        ]);

        $send->assertOk()->assertJson(['ok' => true]);

        $code = '';
        Mail::assertSent(LoginCodeMail::class, function (LoginCodeMail $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $verify = $this->postJson(route('admin.login.code.verify'), [
            'email' => $user->email,
            'role' => 'Admin',
            'code' => $code,
        ]);

        $verify->assertOk()->assertJsonPath('redirect', route('admin.dashboard'));
        $this->assertSame($user->id, session('admin_user_id'));
    }
}

