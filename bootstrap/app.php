<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions) {

        /*
        |--------------------------------------------------------------------------
        | HANDLE SESSION EXPIRED (419)
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function ($e, $request) {

            if (
                $e instanceof \Illuminate\Session\TokenMismatchException ||
                (
                    method_exists($e, 'getStatusCode') &&
                    $e->getStatusCode() === 419
                )
            ) {

                return redirect()
                    ->route('login')
                    ->with(
                        'error',
                        'Your session has expired. Please login again.'
                    );
            }

        });

        /*
        |--------------------------------------------------------------------------
        | GLOBAL EXCEPTION LOGGING
        |--------------------------------------------------------------------------
        */
        $exceptions->report(function (\Throwable $e) {

            \Log::error('Application Exception', [

                'message' => $e->getMessage(),

                'file' => $e->getFile(),

                'line' => $e->getLine(),

                'url' => request()->fullUrl(),

                'method' => request()->method(),

                'ip' => request()->ip(),

                'user' => auth()->check()
                    ? auth()->user()->userid
                    : 'guest',

            ]);

        });

    })

    ->create();