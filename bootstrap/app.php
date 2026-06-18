<?php

use App\Http\Middleware\CheckUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            CheckUserIsActive::class, // <-- Agrega esta línea
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Interceptamos el 403 (Acceso Denegado / Unauthorized)
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            return response()->view('pages.errors.error-403', [
                'title' => 'Acceso Denegado'
            ], 403);
        });

        // De paso, si quieres hacer lo mismo con el 404 (Not Found)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->view('pages.errors.error-404', [
                'title' => 'Página no encontrada'
            ], 404);
        });
    })->create();
