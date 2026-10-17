<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Route;

$render = function (int $userId, string $uri, string $name, string $routeName) use ($app) {
    $user = \App\Models\Wcpu\User::find($userId);
    Auth::shouldUse('wcpu');
    Auth::guard('wcpu')->setUser($user);

    $request = Request::create($uri, 'GET');
    $app->instance('request', $request);

    $route = new Route(['GET'], $uri, function () {});
    $route->name($routeName);
    $route->bind($request);
    $request->setRouteResolver(function () use ($route) {
        return $route;
    });

    try {
        $view = null;
        if ($uri === '/admin-dashboard') {
            $view = $app->call([$app->make(\App\Http\Controllers\Wcpu\AdminController::class), 'dashboard']);
        } elseif ($uri === '/desk-officer/dashboard') {
            $view = $app->call([$app->make(\App\Http\Controllers\Wcpu\DeskOfficerController::class), 'dashboard']);
        } else {
            $view = $app->call([$app->make(\App\Http\Controllers\Wcpu\CaseProfileController::class), 'index']);
        }
        $html = $view instanceof \Illuminate\View\View ? $view->render() : (string) $view;
        file_put_contents(__DIR__ . "/_render_$name.html", $html);
        echo "$uri (user $userId) -> $name (" . strlen($html) . " bytes)" . PHP_EOL;
    } catch (\Throwable $e) {
        echo "$uri (user $userId) -> ERROR: " . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    }
};

$render(1, '/admin-dashboard', 'dash1', 'admin-dashboard');
$render(1, '/case-profiles', 'cp1', 'case-profiles.index');
$render(2, '/desk-officer/dashboard', 'dash2', 'desk-officer.dashboard');
$render(2, '/case-profiles', 'cp2', 'case-profiles.index');
