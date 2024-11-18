<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RedirectIfAuthenticatedWithToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware with an alias
        // $middleware->alias(['auth_central' => RedirectIfAuthenticatedWithToken::class]);
        // $middleware->append(RedirectIfAuthenticatedWithToken::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();