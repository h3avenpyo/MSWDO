<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCodeMail;
use App\Models\EmailLoginCode;
use App\Models\User;
use App\Services\DashboardRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailCodeController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])
            ->where('status', 'active')
            ->first();

        if (! $user) {
            return $this->fail($request, 'email', 'No active account found for this email.');
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
        ]);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
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

        $record->update(['used_at' => now()]);

        session([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
            'admin_just_logged_in' => true,
        ]);

        $redirect = route(DashboardRedirector::routeFor($user->role));

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'redirect' => $redirect]);
        }

        return redirect($redirect);
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
