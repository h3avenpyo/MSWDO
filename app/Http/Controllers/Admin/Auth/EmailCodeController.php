<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCodeMail;
use App\Models\EmailLoginCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailCodeController extends Controller
{
    protected array $moduleRoleMap = [
        'Social Case Study' => ['admin', 'social_worker'],
        'Senior Citizen' => ['admin', 'staff', 'Senior Citizen officer'],
        'Financial Assistance Officer' => ['admin', 'staff', 'Financial assistance officer'],
        'Admin' => ['admin'],
    ];

    protected array $moduleRedirects = [
        'Social Case Study' => 'admin.social-case.dashboard',
        'Senior Citizen' => 'admin.senior',
        'Financial Assistance Officer' => 'admin.financial',
        'Admin' => 'admin.dashboard',
    ];

    public function send(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])
            ->where('status', 'active')
            ->first();

        if (! $user) {
            return $this->fail($request, 'email', 'No active account found for this email.');
        }

        $allowedRoles = (array) ($this->moduleRoleMap[$data['role']] ?? []);

        if (! empty($allowedRoles) && ! in_array($user->role->value, $allowedRoles, true)) {
            return $this->fail($request, 'role', 'This account is not authorized for the selected role.');
        }

        $code = (string) random_int(100000, 999999);

        EmailLoginCode::where('user_id', $user->id)->whereNull('used_at')->delete();

        EmailLoginCode::create([
            'user_id' => $user->id,
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
            'code_role' => $data['role'],
        ]);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $data['email'])
            ->where('status', 'active')
            ->first();

        if (! $user) {
            return $this->fail($request, 'email', 'No active account found for this email.');
        }

        $record = EmailLoginCode::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record || ! Hash::check($data['code'], $record->code)) {
            return $this->fail($request, 'code', 'Invalid or expired code.');
        }

        $allowedRoles = (array) ($this->moduleRoleMap[$data['role']] ?? []);

        if (! empty($allowedRoles) && ! in_array($user->role->value, $allowedRoles, true)) {
            return $this->fail($request, 'role', 'This account is not authorized for the selected role.');
        }

        $record->update(['used_at' => now()]);

        session([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
            'admin_just_logged_in' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'redirect' => route($this->moduleRedirects[$data['role']] ?? 'admin.dashboard')]);
        }

        return redirect()->route($this->moduleRedirects[$data['role']] ?? 'admin.dashboard');
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
