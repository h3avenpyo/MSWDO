<?php

namespace App\Http\Middleware\Financial;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFinancialStep2Access
{
    /**
     * Handle an incoming request.
     *
     * Ensure that only users with direct Step 2 / Admin role OR who have successfully
     * entered Step 2 credentials in the current session can access Financial Step 2.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = strtolower((string) $request->session()->get('admin_user_role'));
        $isDirectAuthorized = in_array($role, ['financialstep2', 'admin'], true);
        $isSessionUnlocked = (bool) $request->session()->get('financial_step2_authorized');

        if ($isDirectAuthorized || $isSessionUnlocked) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'error' => 'step2_unauthorized',
                'message' => 'Step 2 access is restricted. Authentication required.',
            ], 403);
        }

        return redirect()->route('admin.financial.financialstep1')
            ->with('step2_auth_required', true)
            ->with('error', 'Step 2 access is restricted. Please enter authorized credentials to proceed.');
    }
}
