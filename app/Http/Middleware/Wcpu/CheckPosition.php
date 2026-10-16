<?php

namespace App\Http\Middleware\Wcpu;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPosition
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$positions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$positions)
    {
        $user = Auth::guard('wcpu')->user() ?: Auth::user();

        if (!$user) {
            return redirect()->route('admin.login.form');
        }

        // Administrators retain access to all routes through either account type.
        if (isset($user->position) && ($user->position === 'Administrator' || (method_exists($user, 'hasRole') && $user->hasRole('Administrator')))) {
            return $next($request);
        }

        // Honor both the account's primary position and its assigned roles.
        $hasPosition = isset($user->position) && in_array($user->position, $positions);
        $hasRole = method_exists($user, 'hasAnyRole') && $user->hasAnyRole($positions);

        if (!$hasPosition && !$hasRole) {
            if (isset($user->position) && $user->position === 'Desk Officer') {
                return redirect()->route('desk-officer.dashboard')
                    ->with('error', 'You do not have permission to access this page.');
            }

            return redirect()->route('admin-dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
