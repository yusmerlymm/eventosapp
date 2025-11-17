<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CustomCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
            CustomCors::class,
        ]);

            // Si usas Laravel Sanctum para autenticación, descomenta esta línea:
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

        // Puedes añadir otros middlewares aquí si los necesitas para el grupo 'web', por ejemplo:
        // $middleware->web(append: [
        //      \App\Http\Middleware\HandleInertiaRequests::class,
        // ]);
        
        // Registrar middleware de roles
        $middleware->alias([
            'role' => \Modules\Auth\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create(); 