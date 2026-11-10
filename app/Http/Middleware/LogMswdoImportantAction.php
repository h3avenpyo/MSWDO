<?php

namespace App\Http\Middleware;

use App\Services\MswdoActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogMswdoImportantAction
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $route = $request->route();
        $name = $route?->getName() ?? '';
        // Model writes and authentication already have authoritative logs.
        // Browser recent-activity feeds are not trusted audit sources.
        if ($response->getStatusCode() >= 400 || $request->attributes->get('mswdo_activity_recorded')
            || ! str_starts_with($name, 'admin.') || str_contains($route?->getActionName() ?? '', '\\Wcpu\\')
            || str_contains($name, 'wcpu') || str_contains($name, 'activity')
            || ! preg_match('/(?:^|[.\-_])(print|reprint|export|pdf|download|send|approve|reject|release|generate)(?:[.\-_]|$)/i', $name, $match)) {
            return $response;
        }
        // Validation redirects must never appear as successful actions.
        if ($request->hasSession() && $request->session()->has('errors')) {
            return $response;
        }

        $module = match (true) {
            str_contains($name, 'financial') => 'Financial Assistance',
            str_contains($name, 'senior') => 'Senior Citizens',
            str_contains($name, 'social-case') => 'Social Case Study',
            default => 'Administration',
        };
        app(MswdoActivityLogger::class)->record(strtolower($match[1]), $module, 'Completed '.$name);

        return $response;
    }
}
