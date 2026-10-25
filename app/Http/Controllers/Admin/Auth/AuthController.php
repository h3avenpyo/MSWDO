<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;
use App\Http\Requests\Admin\Auth\SendResetLinkRequest;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Models\Wcpu\User as WcpuUser;
use App\Services\DashboardRedirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.mswdo-admin.login');
    }

    public function login(LoginRequest $request)
    {
        $login = trim((string) ($request->input('email') ?? $request->input('login')));
        $password = (string) $request->input('password');

        // 1. Attempt authentication against MSWDO accounts
        $mswdoUser = User::where('email', $login)->first();

        if ($mswdoUser && Hash::check($password, $mswdoUser->password)) {
            // Check if MSWDO account is deactivated
            $status = is_object($mswdoUser->status) ? $mswdoUser->status->value : $mswdoUser->status;
            if ($status === 'inactive') {
                return back()->with('account_deactivated', true)
                    ->with('message', 'Your account has been deactivated. Please contact the administrator.')
                    ->withInput();
            }

            session([
                'admin_user_id' => $mswdoUser->id,
                'admin_user_name' => $mswdoUser->name,
                'admin_user_role' => is_object($mswdoUser->role) ? $mswdoUser->role->value : $mswdoUser->role,
                'admin_just_logged_in' => true,
                'auth_source' => 'mswdo',
            ]);

            return redirect()->route(DashboardRedirector::routeFor($mswdoUser->role));
        }

        // 2. Attempt authentication against WCPU accounts (email or username)
        $wcpuUserQuery = WcpuUser::where('email', $login);
        if (Schema::connection('wcpu')->hasColumn('vawc_users', 'username') || Schema::connection('wcpu')->hasColumn('users', 'username')) {
            $wcpuUserQuery->orWhere('username', $login);
        }
        $wcpuUser = $wcpuUserQuery
            ->first();

        if ($wcpuUser) {
            // Check for permanent lockout
            if ($wcpuUser->account_locked) {
                $this->logWcpuFailedLogin($wcpuUser, $request, 'Account is permanently locked');
                return back()->withErrors([
                    'email' => 'Account permanently locked due to too many failed login attempts. Please reset your password to unlock your account.',
                ])->withInput();
            }

            // Check if currently in temporary lockout period
            if ($wcpuUser->locked_until && now()->lessThan($wcpuUser->locked_until)) {
                $this->logWcpuFailedLogin($wcpuUser, $request, 'Account is temporarily locked');
                $remainingTime = now()->diffInSeconds($wcpuUser->locked_until);
                $minutes = floor($remainingTime / 60);
                $seconds = $remainingTime % 60;
                $timeMessage = $minutes > 0
                    ? "{$minutes} minute(s) and {$seconds} second(s)"
                    : "{$seconds} second(s)";

                return back()->withErrors([
                    'email' => "Too many login attempts. Please try again in {$timeMessage}.",
                ])->withInput();
            }

            // Clear temporary lockout if it has expired
            if ($wcpuUser->locked_until && now()->greaterThanOrEqualTo($wcpuUser->locked_until)) {
                $wcpuUser->locked_until = null;
                $wcpuUser->save();
            }

            // Check if account is active
            if (!$wcpuUser->is_active) {
                $this->logWcpuFailedLogin($wcpuUser, $request, 'Account is inactive');
                return back()->with('account_deactivated', true)
                    ->with('message', 'Your account has been deactivated. Please contact the administrator.')
                    ->withInput();
            }

            // Verify password
            if (!Hash::check($password, $wcpuUser->password)) {
                $this->logWcpuFailedLogin($wcpuUser, $request, 'Invalid password');

                $currentAttempts = ($wcpuUser->failed_login_attempts ?? 0) + 1;
                $wcpuUser->failed_login_attempts = $currentAttempts;

                // Permanent lock at 8 failed attempts
                if ($wcpuUser->failed_login_attempts >= 8) {
                    $wcpuUser->account_locked = true;
                    $wcpuUser->locked_until = null;
                    $wcpuUser->save();

                    return back()->withErrors([
                        'email' => 'Account permanently locked due to too many failed login attempts. Please reset your password to unlock your account.',
                    ])->withInput();
                }
                // Temporary 3-minute lock at 5 failed attempts
                elseif ($wcpuUser->failed_login_attempts == 5) {
                    $wcpuUser->locked_until = now()->addMinutes(3);
                    $wcpuUser->save();

                    return back()->withErrors([
                        'email' => 'Too many login attempts. Please try again in 3 minute(s) and 0 second(s).',
                    ])->withInput();
                }

                $wcpuUser->save();

                $message = 'Invalid email or password.';
                if ($wcpuUser->failed_login_attempts >= 5 && $wcpuUser->failed_login_attempts < 8) {
                    $remaining = 8 - $wcpuUser->failed_login_attempts;
                    $message .= " Critical warning: {$remaining} attempt(s) remaining before permanent account lockout.";
                } elseif ($wcpuUser->failed_login_attempts < 5) {
                    $remaining = 5 - $wcpuUser->failed_login_attempts;
                    if ($remaining <= 2) {
                        $message .= " Warning: {$remaining} attempt(s) remaining before temporary lockout.";
                    }
                }

                return back()->withErrors(['email' => $message])->withInput();
            }

            // Password is correct! Rehash if needed
            if (Hash::needsRehash($wcpuUser->password)) {
                $wcpuUser->password = Hash::make($password);
                $wcpuUser->save();
            }

            // Reset failed login attempts on successful login
            $wcpuUser->failed_login_attempts = 0;
            $wcpuUser->locked_until = null;
            $wcpuUser->save();

            // Log the user into WCPU guard
            Auth::guard('wcpu')->login($wcpuUser, $request->filled('remember'));

            // Set session metadata
            $request->session()->regenerate();
            $request->session()->put('last_user_activity_at', now()->timestamp);
            $request->session()->put('auth_source', 'wcpu');

            // Update last login if method exists
            if (method_exists($wcpuUser, 'updateLastLogin')) {
                $wcpuUser->updateLastLogin();
            }

            // Log activity
            try {
                activity('auth')
                    ->causedBy($wcpuUser)
                    ->withProperties([
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ])
                    ->log('User logged in via centralized MSWDO login');
            } catch (\Throwable $e) {
                // ignore
            }

            return redirect()->to($this->resolveWcpuRedirect($wcpuUser));
        }

        // Neither system matched
        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
    }

    /**
     * Determine destination dashboard route for WCPU users based on their position and departments.
     */
    protected function resolveWcpuRedirect(WcpuUser $user): string
    {
        return DashboardRedirector::wcpuRouteFor($user);
    }

    /**
     * Record failed Desk Officer authentication attempts for User Logs.
     */
    protected function logWcpuFailedLogin(WcpuUser $user, Request $request, string $reason): void
    {
        if (strcasecmp(trim((string) $user->position), 'Desk Officer') !== 0) {
            return;
        }

        try {
            activity('auth')
                ->causedBy($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'failure_reason' => $reason,
                ])
                ->log('Failed login attempt');
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function logout(Request $request)
    {
        if (Auth::guard('wcpu')->check()) {
            $user = Auth::guard('wcpu')->user();
            if ($user) {
                try {
                    activity('auth')
                        ->causedBy($user)
                        ->withProperties([
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'timestamp' => now()->toDateTimeString(),
                        ])
                        ->log('User logged out via centralized MSWDO logout');
                } catch (\Throwable $e) {
                    // ignore
                }
            }
            Auth::guard('wcpu')->logout();
        }

        $request->session()->forget([
            'admin_user_id',
            'admin_user_name',
            'admin_user_role',
            'admin_just_logged_in',
            'auth_source',
            'last_user_activity_at',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

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
        if (Auth::guard('wcpu')->check()) {
            Auth::guard('wcpu')->logout();
        }

        $request->session()->forget([
            'admin_user_id',
            'admin_user_name',
            'admin_user_role',
            'admin_just_logged_in',
            'auth_source',
            'last_user_activity_at',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    // Forgot Password
    public function showForgotPassword()
    {
        return view('admin.mswdo-admin.forgot-password');
    }

    public function sendResetLink(SendResetLinkRequest $request)
    {
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
            'requested_from_url' => rtrim($request->getSchemeAndHttpHost(), '/'),
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

        return view('admin.mswdo-admin.reset-password', [
            'token' => $token,
            'email' => $resetRequest->email,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
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
