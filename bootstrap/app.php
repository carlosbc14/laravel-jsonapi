<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            \App\Http\Middleware\EnsureJsonApiHeader::class,
            \App\Http\Middleware\EnsureJsonApiDocument::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => (string) $e->getStatusCode(),
                            'title' => Response::$statusTexts[$e->getStatusCode()],
                            'detail' => $e->getMessage(),
                        ],
                    ],
                ], $e->getStatusCode());
            }
        });
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => '401',
                            'title' => 'Unauthenticated',
                            'detail' => 'This action requires authentication.',
                        ],
                    ],
                ], 401);
            }
        });
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                $errors = [];
                foreach ($e->errors() as $field => $messages) {
                    $errors[] = [
                        'status' => '422',
                        'title' => 'Validation Error',
                        'detail' => str_replace(['data.attributes.', 'data.'], '', $messages[0]),
                        'source' => [
                            'pointer' => '/' . str_replace('.', '/', $field),
                        ],
                    ];
                }

                return response()->json([
                    'errors' => $errors,
                ], 422);
            }
        });
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => '500',
                            'title' => 'Server Error',
                            'detail' => app()->environment('production') || !config('app.debug')
                                ? 'An unexpected error occurred on the server.'
                                : $e->getMessage(),
                        ],
                    ],
                ], '500');
            }
        });
    })->create();
