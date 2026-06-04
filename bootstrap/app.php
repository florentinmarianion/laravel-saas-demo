<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'active.company'     => \App\Http\Middleware\EnsureActiveCompany::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 405 Method Not Allowed → returnam 404 fara sa revelam ca ruta exista
        // IMPORTANT: folosim response() direct, nu abort() — abort() ar arunca
        // o noua exceptie si ar crea un loop fatal
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not found.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

    })->create();
