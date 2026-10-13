<?php

namespace App\Http\Middleware\Wcpu;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceInactivityTimeout
{
    public const MESSAGE = 'You have been inactive for more than 30 minutes. Due to security purposes, the system automatically logs out your account.';

    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('wcpu.logout') || $request->routeIs('logout')) {
            return $next($request);
        }

        $guard = Auth::guard('wcpu')->check() ? 'wcpu' : (Auth::check() ? 'web' : null);

        if ($guard) {
            $last = $request->session()->get('last_user_activity_at', now()->timestamp);
            if (now()->timestamp - $last >= 1800) {
                Auth::guard($guard)->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $request->session()->flash('inactivity_message', self::MESSAGE);
                $loginRoute = 'admin.login.form';
                return $request->expectsJson()
                    ? response()->json(['message' => self::MESSAGE, 'login_url' => route($loginRoute, ['inactive' => 1])], 401)
                    : redirect()->route($loginRoute, ['inactive' => 1]);
            }
            // AJAX polling is not evidence that a person is still using the page.
            if (!$request->session()->has('last_user_activity_at') || $request->routeIs('session.activity')
                || (!$request->ajax() && !$request->expectsJson() && !$request->routeIs('session.status'))) {
                $request->session()->put('last_user_activity_at', now()->timestamp);
            }
        }
        return $next($request);
    }
}
