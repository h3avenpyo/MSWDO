<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Allow requests only when the authenticated session role is among $roles.
     *
     * Usage: middleware('role:social_worker,eligibility_checker')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = strtolower((string) $request->session()->get('admin_user_role'));

        $allowed = array_map('strtolower', $roles);

        if (in_array('admin', $allowed, true) && $role === 'admin') {
            return $next($request);
        }

        if (! in_array($role, $allowed, true)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
