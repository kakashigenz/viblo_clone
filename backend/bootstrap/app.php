<?php

use App\Exceptions\IncorrectPasswordException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnverifiedEmailException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
    })
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['api', 'auth:sanctum']],
    )
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions
            ->render(function (ResourceNotFoundException $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'message' => $e->getMessage()
                    ], 404);
                }
            })
            ->render(function (IncorrectPasswordException $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'message' => $e->getMessage()
                    ], 400);
                }
            })->render(function (UnverifiedEmailException $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'message' => $e->getMessage()
                    ], 403);
                }
            });
    })->create();
