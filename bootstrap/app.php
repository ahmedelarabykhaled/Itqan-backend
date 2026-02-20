<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request): ?string {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    message: $exception->getMessage() ?: 'Unauthenticated.',
                    status: 401
                );
            }

            return null;
        });

        // Validation errors
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Validation error',
                    errors: $e->errors(),
                    status: 422
                );
            }

            return null;
        });

        // handle 500 errors
        $exceptions->render(function (Exception $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Server error',
                    status: 500
                );
            }

            return null;
        });
    })->create();
