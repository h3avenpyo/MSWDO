<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\SendLoginCodeRequest;
use App\Http\Requests\Admin\Auth\VerifyLoginCodeRequest;
use App\Mail\LoginCodeMail;
use App\Models\EmailLoginCode;
use App\Models\User;
use App\Models\Wcpu\User as WcpuUser;
use App\Services\DashboardRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailCodeController extends Controller
{
    public function send(SendLoginCodeRequest $request)
    {
        $data = $request->validated();

        [$user, $source] = $this->resolveAccount($data['email']) ?? [null, null];

        if (! $user) {
            return $this->fail($request, 'email', 'No active account found for this email.');
        }

        $code = (string) random_int(100000, 999999);

        EmailLoginCode::where('auth_source', $source)
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        EmailLoginCode::create([
            'user_id' => $user->id,
            'auth_source' => $source,
            'email' => $user->email,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new LoginCodeMail($code));

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Code sent to your email.']);
        }

        return back()->with([
            'code_sent' => true,
            'code_email' => $user->email,
        ]);
    }

    public function verify(VerifyLoginCodeRequest $request)
    {
        $data = $request->validated();

        [$user, $source] = $this->resolveAccount($data['email']) ?? [null, null];

        if (! $user) {
            return $this->fail($request, 'email', 'No active account found for this email.');
        }

        $record = EmailLoginCode::where('auth_source', $source)
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record || ! Hash::check($data['code'], $record->code)) {
            return $this->fail($request, 'code', 'Invalid or expired code.');
        }

        $record->update(['used_at' => now()]);

        $redirect = $source === 'wcpu'
            ? $this->loginWcpuUser($user)
            : $this->loginMswdoUser($user);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'redirect' => $redirect]);
        }

        return redirect($redirect);
    }

    /**
     * Resolve an active account by email across the MSWDO and WCPU databases.
     *
     * @return array{0: User|WcpuUser, 1: string}|null
     */
    protected function resolveAccount(string $email): ?array
    {
        $mswdoUser = User::where('email', $email)
            ->where('status', 'active')
            ->first();

        if ($mswdoUser) {
            return [$mswdoUser, 'mswdo'];
        }

        $wcpuUser = WcpuUser::where('email', $email)
            ->where('is_active', true)
            ->first();

        if ($wcpuUser) {
            return [$wcpuUser, 'wcpu'];
        }

        return null;
    }

    protected function loginMswdoUser(User $user): string
    {
        session([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
            'admin_just_logged_in' => true,
            'auth_source' => 'mswdo',
        ]);

        return route(DashboardRedirector::routeFor($user->role));
    }

    protected function loginWcpuUser(WcpuUser $user): string
    {
        Auth::guard('wcpu')->login($user);

        request()->session()->regenerate();
        request()->session()->put('last_user_activity_at', now()->timestamp);
        request()->session()->put('auth_source', 'wcpu');

        if (method_exists($user, 'updateLastLogin')) {
            $user->updateLastLogin();
        }

        return DashboardRedirector::wcpuRouteFor($user);
    }

    protected function fail(Request $request, string $field, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'message' => $message,
                'errors' => [$field => $message],
            ], 422);
        }

        return back()->withErrors([$field => $message])->withInput();
    }
}
