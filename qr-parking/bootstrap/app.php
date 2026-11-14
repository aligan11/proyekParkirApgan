<?php

use App\Http\Middleware\CheckAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'staff' => \App\Http\Middleware\CheckStaff::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class, // kalau kamu juga pakai admin
            'check.role' => \App\Http\Middleware\MiddlewareWeb\CheckAdminRole::class,

        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
