<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('admin_user_id')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'unauthenticated',
                    'message' => 'Your session has expired. Please log in again.',
                ], 401);
            }

            return redirect()->route('admin.login.form');
        }

        return $next($request);
    }
}