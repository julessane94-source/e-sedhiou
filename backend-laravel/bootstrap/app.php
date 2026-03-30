<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS géré automatiquement par HandleCors (config/cors.php)
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->alias([
            'mairie.token' => \App\Http\Middleware\VerifyMairieToken::class,
            'jwt.auth'     => \App\Http\Middleware\JwtAuthenticate::class,
            'role'         => \App\Http\Middleware\RequireRole::class,
            'portal.auth'  => \App\Http\Middleware\EnsurePortalAuthenticated::class,
            'portal.role'  => \App\Http\Middleware\EnsurePortalRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
