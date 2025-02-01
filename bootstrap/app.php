<?php

use App\Http\Middleware\ForceJsonOnApi;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSingletons([
        Illuminate\Contracts\Http\Kernel::class =>
            App\Http\Kernel::class,
        Illuminate\Contracts\Debug\ExceptionHandler::class =>
            App\Exceptions\Handler::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(ForceJsonOnApi::class);

        $middleware->group(
            'api',
            [
                EnsureFrontendRequestsAreStateful::class,
                ThrottleRequests::with(100, 1),
                SubstituteBindings::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
