<?php

namespace App\Http\Controllers\Wcpu\Auth;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('admin-tailwind.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check if account is locked before attempting login
        $login = $request->input('login');
        $user = User::where('email', $login)->orWhere('username', $login)->first();
        
        // Check for permanent lockout
        if ($user && $user->account_locked) {
            $this->logFailedDeskOfficerLogin($user, $request, 'Account is permanently locked');
            return $this->sendPermanentlyLockedResponse();
        }
        
        // Check if currently in temporary lockout period
        if ($user && $user->locked_until && now()->lessThan($user->locked_until)) {
            $this->logFailedDeskOfficerLogin($user, $request, 'Account is temporarily locked');
            return $this->sendLockedAccountResponse($user);
        }
        
        // Clear temporary lockout if it has expired (but keep attempt count)
        if ($user && $user->locked_until && now()->greaterThanOrEqualTo($user->locked_until)) {
            $user->locked_until = null;
            $user->save();
        }

        // Attempt to log the user in
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        // Accept either email or username via the "login" field
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * The maximum number of attempts to allow.
     *
     * @return int
     */
    protected $maxAttempts = 5;

    /**
     * The number of minutes to throttle for.
     *
     * @return int
     */
    protected $decayMinutes = 15;

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app('Illuminate\Cache\RateLimiter');
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return strtolower($request->input($this->username())).'|'.$request->ip();
    }

    /**
     * Attempt to log the user into the application.
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $login = $credentials['login'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$login || !$password) {
            return false;
        }

        // Find user by email or username
        $user = User::where('email', $login)->orWhere('username', $login)->first();

        if (!$user) {
            return false;
        }

        if (!$user->is_active) {
            $this->logFailedDeskOfficerLogin($user, $request, 'Account is inactive');
            return false;
        }

        // Verify password first
        if (!Hash::check($password, $user->password)) {
            $this->logFailedDeskOfficerLogin($user, $request, 'Invalid password');

            // Increment failed login attempts
            $currentAttempts = $user->failed_login_attempts ?? 0;
            $user->failed_login_attempts = $currentAttempts + 1;
            
            // Permanent lock at 8 failed attempts
            if ($user->failed_login_attempts >= 8) {
                $user->account_locked = true;
                $user->locked_until = null;
                $user->save();
                
                session(['show_forgot_password' => true]);
                throw ValidationException::withMessages([
                    $this->username() => ["Account permanently locked due to too many failed login attempts. Please reset your password to unlock your account."],
                ])->status(423);
            }
            // Temporary 3-minute lock at 5 failed attempts
            elseif ($user->failed_login_attempts == 5) {
                $user->locked_until = now()->addMinutes(3);
                $user->save();
                
                $lockedUntil = \Carbon\Carbon::parse($user->locked_until);
                session(['locked_until' => $lockedUntil->timestamp]);
                
                throw ValidationException::withMessages([
                    $this->username() => ["Too many login attempts. Please try again in 3 minute(s) and 0 second(s)."],
                ])->status(429);
            }
            
            $user->save();
            return false;
        }

        // If hashing algorithm changed, rehash the password
        if (Hash::needsRehash($user->password)) {
            $user->password = Hash::make($password);
            $user->save();
        }

        // Reset failed login attempts on successful login
        $this->resetLoginAttempts($user);

        // Log the user in
        Auth::login($user, $request->filled('remember'));

        // Update last login info if method exists
        if (method_exists($user, 'updateLastLogin')) {
            $user->updateLastLogin();
        }

        // Check for completed password reset requests within 24 hours
        $recentPasswordReset = \App\Models\Wcpu\PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('resolved_at', '>=', now()->subHours(24))
            ->whereNotNull('new_password')
            ->orderBy('resolved_at', 'desc')
            ->first();

        if ($recentPasswordReset && !session('password_reset_notice')) {
            $request->session()->put('password_reset_notice', [
                'id' => 'password_reset_' . $recentPasswordReset->id,
                'user_id' => $user->id,
                'email' => $user->email,
                'new_password' => $recentPasswordReset->new_password,
                'message' => 'Your password has been reset and is ready for viewing.',
                'expires_at' => now()->addHours(24)->toDateTimeString(),
                'request_id' => $recentPasswordReset->id,
            ]);
            $request->session()->put('password_reset_notice_popup_displayed', false);
        }

        return true;
    }

    /**
     * Record failed Desk Officer authentication attempts for User Logs.
     */
    protected function logFailedDeskOfficerLogin(User $user, Request $request, string $reason): void
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
        } catch (Throwable $exception) {
            Log::error('Unable to record failed Desk Officer login attempt.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
    
    /**
     * Handle a failed login attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $login = $request->input('login');
        $user = User::where('email', $login)->orWhere('username', $login)->first();
        
        $message = trans('auth.failed');
        
        // Add warning if user exists and has remaining attempts
        if ($user && $user->failed_login_attempts > 0) {
            if ($user->failed_login_attempts >= 5 && $user->failed_login_attempts < 8) {
                // Between 5-7 attempts (after temporary lockout, before permanent)
                $remainingAttempts = 8 - $user->failed_login_attempts;
                $message .= " Critical warning: {$remainingAttempts} attempt(s) remaining before permanent account lockout.";
            } elseif ($user->failed_login_attempts < 5) {
                // Between 1-4 attempts (before temporary lockout)
                $remainingAttempts = 5 - $user->failed_login_attempts;
                if ($remainingAttempts <= 2) {
                    $message .= " Warning: {$remainingAttempts} attempt(s) remaining before temporary lockout.";
                }
            }
        }
        
        throw ValidationException::withMessages([
            $this->username() => [$message],
        ]);
    }
    
    /**
     * Reset failed login attempts for the user.
     *
     * @param  User  $user
     * @return void
     */
    protected function resetLoginAttempts($user)
    {
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->save();
    }
    
    /**
     * Send response when account is locked.
     *
     * @param  User  $user
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockedAccountResponse($user)
    {
        // If locked_until is not set but attempts >= 5, set it now
        if (!$user->locked_until && $user->failed_login_attempts >= 5) {
            $user->locked_until = now()->addMinutes(3);
            $user->save();
        }
        
        // Store locked_until timestamp in session for client-side countdown
        $lockedUntil = \Carbon\Carbon::parse($user->locked_until);
        session(['locked_until' => $lockedUntil->timestamp]);
        
        $remainingTime = now()->diffInSeconds($lockedUntil);
        $minutes = floor($remainingTime / 60);
        $seconds = $remainingTime % 60;
        
        $timeMessage = $minutes > 0 
            ? "{$minutes} minute(s) and {$seconds} second(s)"
            : "{$seconds} second(s)";
        
        throw ValidationException::withMessages([
            $this->username() => ["Too many login attempts. Please try again in {$timeMessage}."],
        ])->status(429);
    }
    
    /**
     * Send response when account is permanently locked.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendPermanentlyLockedResponse()
    {
        session(['show_forgot_password' => true]);
        
        throw ValidationException::withMessages([
            $this->username() => ["Account permanently locked due to too many failed login attempts. Please reset your password to unlock your account."],
        ])->status(423);
    }
    
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        // Change to 'login' so rate limiting and validation messages use the same key
        return 'login';
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request)
    {
        // Return login + password (login may be email or username)
        return $request->only('login', 'password');
    }

    /**
     * Send the response after the user was authenticated.
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $request->session()->put('last_user_activity_at', now()->timestamp);

        $this->clearLoginAttempts($request);

        $user = $this->guard()->user();
        
        // Log the successful login
        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('User logged in');

        if ($response = $this->authenticated($request, $user)) {
            return $response;
        }

        // Redirect based on user role
        $redirectRoute = 'admin.dashboard'; // Default to admin dashboard
        
        // Check if user is a desk officer
        if ($user->position === 'Desk Officer') {
            // Check if user has BCPC as default case department
            $defaultCaseDepartment = $user->defaultCaseDepartment();
            $redirectRoute = $defaultCaseDepartment === 'BCPC'
                ? 'desk-officer.bcpc.dashboard'
                : 'desk-officer.dashboard';
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect()->intended(route($redirectRoute));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        \Log::info('Logout method called', [
            'user_id' => $this->guard()->id(),
            'session_id' => $request->session()->getId()
        ]);
        
        $user = $this->guard()->user();
        
        // Log the logout action before actually logging out
        if ($user) {
            try {
                \Log::info('Attempting to log logout activity', [
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
                
                $activity = activity('auth')
                    ->causedBy($user)
                    ->withProperties([
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'timestamp' => now()->toDateTimeString(),
                        'url' => $request->fullUrl(),
                        'method' => $request->method()
                    ])
                    ->log('User logged out');
                    
                \Log::info('Logout activity logged', [
                    'activity_id' => $activity->id ?? 'no_id',
                    'log_name' => $activity->log_name ?? 'no_log_name'
                ]);
                
            } catch (\Exception $e) {
                // Log any errors that occur during activity logging
                \Log::error('Failed to log user logout activity', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        // Perform the actual logout
        $this->guard()->logout();

        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Perform the actual logout
        $this->guard()->logout();

        // Clear any MSWDO session keys as well
        $request->session()->forget([
            'admin_user_id',
            'admin_user_name',
            'admin_user_role',
            'admin_just_logged_in',
            'auth_source',
            'last_user_activity_at',
        ]);

        if ($request->boolean('inactive')) {
            return redirect()->route('admin.login.form', ['inactive' => 1])
                ->with('inactivity_message', \App\Http\Middleware\Wcpu\EnforceInactivityTimeout::MESSAGE);
        }
        // Check if there's a custom logged out response
        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        // Return appropriate response based on request type
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->route('admin.login.form')->with('status', 'You have been successfully logged out.');
    }

    /**
     * Get the guard to be used during authentication.
     */
    protected function guard()
    {
        return Auth::guard('wcpu');
    }

    /**
     * Clear the list of login attempts from the user's session.
     */
    protected function clearLoginAttempts(Request $request)
    {
        if ($this->maxAttempts > 0) {
            $this->limiter()->clear($this->throttleKey($request));
        }

        $request->session()->forget($this->throttleKey($request));
    }

    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
{
    if (strtolower($user->position) === 'administrator') {
        return redirect()->route('admin-dashboard'); // ✅ this exists
    } elseif (strtolower($user->position) === 'desk officer') {
        if ($user->isMultiCaseDepartment()) {
            return redirect()->route('desk-officer.bcpc.dashboard');
        }

        return redirect()->route('desk-officer.dashboard'); // ✅ this exists
    }

    return redirect()->intended('/');
}


    /**
     * The user has logged out of the application.
     */
    protected function loggedOut(Request $request)
    {
        return null;
    }

    /**
     * Show the password reset access verification form.
     */
    public function showPasswordResetAccessForm(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This access link is invalid or has expired.');
        }

        $resetRequest = \App\Models\Wcpu\PasswordResetRequest::with('user')->findOrFail($id);

        if ($resetRequest->status !== 'completed') {
            abort(403, 'This password reset access link is no longer valid.');
        }

        if ($request->session()->get('password_reset_access_used_' . $resetRequest->id)) {
            return redirect()->route('login')->withErrors([
                'access' => 'This password reset access link has already been used.'
            ]);
        }

        return view('auth.passwords.access', compact('resetRequest'));
    }

    /**
     * Verify the identity details for the password reset access link.
     */
    public function verifyPasswordResetAccess(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'department' => 'required|string',
        ]);

        $resetRequest = \App\Models\Wcpu\PasswordResetRequest::with('user')->findOrFail($id);
        $user = $resetRequest->user;

        if (! $user) {
            abort(404);
        }

        $expectedName = trim($user->first_name . ' ' . $user->last_name);
        $expectedDepartment = trim((string) $user->department);

        if (
            mb_strtolower(trim($request->input('full_name'))) !== mb_strtolower($expectedName) ||
            mb_strtolower(trim($request->input('email'))) !== mb_strtolower($user->email) ||
            mb_strtolower(trim($request->input('department'))) !== mb_strtolower($expectedDepartment)
        ) {
            return back()->withErrors([
                'access' => 'The details you entered do not match the account information.'
            ])->withInput();
        }

        $request->session()->put('password_reset_access_used_' . $resetRequest->id, true);
        $request->session()->put('password_reset_notice', [
            'id' => 'password_reset_' . $resetRequest->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'new_password' => $resetRequest->new_password,
            'message' => 'Your password has been reset and is ready for viewing.',
            'expires_at' => now()->addHours(24)->toDateTimeString(),
            'request_id' => $resetRequest->id,
        ]);
        $request->session()->put('password_reset_notice_popup_displayed', false);

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($user->position === 'Administrator') {
            return redirect()->route('admin-dashboard');
        }

        return redirect()->route(
            strtolower((string) $user->position) === 'desk officer' && $user->isMultiCaseDepartment()
                ? 'desk-officer.bcpc.dashboard'
                : 'desk-officer.dashboard'
        );
    }

    /**
     * Mark the password reset notice as displayed so it does not re-pop on every page.
     */
    public function markPasswordResetNoticeSeen(Request $request)
    {
        return response()->json(['success' => true]);
    }

    /**
     * Handle password reset request from login page
     */
    public function requestPasswordReset(Request $request)
    {
        $request->validate([
            'reset-identifier' => 'required|string',
        ]);

        $identifier = $request->input('reset-identifier');
        
        // Find the account by the submitted identifier (email or username).
        $user = User::where('email', $identifier)->orWhere('username', $identifier)->first();

        if ($user) {
            // Check if user already has a pending password reset request
            $existingRequest = \App\Models\Wcpu\PasswordResetRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'success' => true,
                    'message' => 'You already have a pending password reset request. Please wait for the administrator to process it.'
                ]);
            }

            // Create the in-app admin review request only.
            $resetRequest = \App\Models\Wcpu\PasswordResetRequest::create([
                'user_id' => $user->id,
                'username' => $identifier,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            Log::info('Password reset request created', [
                'request_id' => $resetRequest->id,
                'user_id' => $user->id,
                'username' => $identifier,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your password reset request has been submitted. An administrator will assist you shortly.'
            ]);
        }
        
        // User not found
        return response()->json([
            'success' => false,
            'message' => 'We could not find a user with that email or username.'
        ], 404);
    }
}
