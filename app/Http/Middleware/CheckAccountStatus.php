<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Verify the authenticated admin session still belongs to an active account.
     *
     * The admin panel authenticates via session keys (admin_user_id, ...),
     * not the default Auth guard. If the session user's account is inactive
     * (or no longer exists), the session is invalidated and the request is
     * rejected — a JSON 401 for API/AJAX calls, or a redirect to the login
     * page for normal page loads.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('admin_user_id')) {
            return $next($request);
        }

        $user = User::find(session('admin_user_id'));

        if (! $user) {
            $this->invalidateSession($request);

            return $this->deactivatedResponse($request);
        }

        $status = is_object($user->status) ? $user->status->value : $user->status;

        if ($status === 'inactive') {
            $this->invalidateSession($request);

            return $this->deactivatedResponse($request);
        }

        return $next($request);
    }

    /**
     * Clear the admin session keys, regenerate the session id and CSRF token.
     */
    protected function invalidateSession(Request $request): void
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

    /**
     * Respond appropriately for JSON/API requests vs. full page navigations.
     */
    protected function deactivatedResponse(Request $request): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'error' => 'account_deactivated',
                'message' => 'Your account has been deactivated. Please contact the administrator.',
            ], 401);
        }

        return redirect()->route('admin.login.form')
            ->with('account_deactivated', true)
            ->with('message', 'Your account has been deactivated. Please contact the administrator.');
    }
}