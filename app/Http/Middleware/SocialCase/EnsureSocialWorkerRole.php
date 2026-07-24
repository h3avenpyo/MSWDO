<?php

namespace App\Http\Middleware\SocialCase;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSocialWorkerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = strtolower((string) $request->session()->get('admin_user_role'));

        $isSocialCaseWorker = str_contains($role, 'social worker') || str_contains($role, 'social case');
        $isSystemAdmin = $role === 'admin';

        if (! $isSocialCaseWorker && ! $isSystemAdmin) {
            abort(403, 'Only a social worker may access the case assessment step.');
        }

        return $next($request);
    }
}
