<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Arahkan pengunjung yang belum login ke beranda sambil membuka modal login
        $middleware->redirectGuestsTo(fn() => route('beranda', ['modal' => 'login']));
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureUserNotBlocked::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
