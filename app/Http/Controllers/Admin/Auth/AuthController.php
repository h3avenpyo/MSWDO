<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Services\DashboardRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        // Check if account is deactivated
        $status = is_object($user->status) ? $user->status->value : $user->status;
        if ($status === 'inactive') {
            return back()->with('account_deactivated', true)
                ->with('message', 'Your account has been deactivated. Please contact the administrator.')
                ->withInput();
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        session([
            'admin_user_id' => $user->id,
            'admin_user_name' => $user->name,
            'admin_user_role' => $user->role->value,
            'admin_just_logged_in' => true,
        ]);

        return redirect()->route(DashboardRedirector::routeFor($user->role));
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_user_id', 'admin_user_name', 'admin_user_role']);

        return redirect()->route('admin.login.form');
    }

    public function clearWelcome(Request $request)
    {
        $request->session()->forget('admin_just_logged_in');
        return response()->json(['ok' => true]);
    }

    /**
     * Lightweight account-status poll used by authenticated pages to detect
     * forced logout. If the session account is inactive (or no longer exists),
     * the session is invalidated server-side so the user can no longer access
     * protected pages or perform authenticated actions.
     */
    public function checkAccountStatus(Request $request)
    {
        if (! session('admin_user_id')) {
            return response()->json(['deactivated' => false, 'authenticated' => false]);
        }

        $user = User::find(session('admin_user_id'));

        if (! $user) {
            $this->invalidateAdminSession($request);

            return response()->json(['deactivated' => true, 'authenticated' => true]);
        }

        $status = is_object($user->status) ? $user->status->value : $user->status;
        $isDeactivated = $status === 'inactive';

        if ($isDeactivated) {
            $this->invalidateAdminSession($request);
        }

        return response()->json([
            'deactivated' => $isDeactivated,
            'authenticated' => true,
        ]);
    }

    /**
     * Clear the admin session keys, regenerate the session id and CSRF token.
     */
    protected function invalidateAdminSession(Request $request): void
    {
        $request->session()->forget([
            'admin_user_id',
            'admin_user_name',
            'admin_user_role',
            'admin_just_logged_in',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    // Forgot Password
    public function showForgotPassword()
    {
        return view('admin.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'If an account exists with this email, a password reset request has been submitted for admin approval.');
        }

        // Check if there's already a pending request
        $existingRequest = PasswordResetRequest::where('email', $request->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingRequest) {
            return back()->with('info', 'A password reset request is already pending for this email. Please wait for admin approval. An email will be sent once approved.');
        }

        // Create new password reset request
        $token = Str::random(64);
        PasswordResetRequest::create([
            'email' => $request->email,
            'token' => $token,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);

        return back()->with('success', 'Password reset request submitted. Please wait for admin approval. An email with the reset link will be sent to your email once approved.');
    }

    public function showResetPassword(Request $request, $token)
    {
        $resetRequest = PasswordResetRequest::where('token', $token)
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRequest) {
            return redirect()->route('admin.login.form')
                ->with('error', 'Invalid or expired password reset token.');
        }

        return view('admin.reset-password', [
            'token' => $token,
            'email' => $resetRequest->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $resetRequest = PasswordResetRequest::where('token', $request->token)
            ->where('email', $request->email)
            ->where('status', 'approved')
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRequest) {
            return back()->with('error', 'Invalid or expired password reset token.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Mark request as completed
        $resetRequest->status = 'completed';
        $resetRequest->processed_at = now();
        $resetRequest->save();

        return redirect()->route('admin.login.form')
            ->with('success', 'Password has been reset successfully. You can now log in with your new password.');
    }
}
