<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Request;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'stripe/*'
        ]);

        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo('/posts');

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'billing' => \App\Http\Middleware\Billing::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 500 Internal Server Error, 503 Service Unavailable, etc.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (in_array($e->getStatusCode(), [500, 503, 403, 404])) {
                return Inertia::render('Error', ['status' => $e->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($e->getStatusCode());
            }
        });
    })->create();
