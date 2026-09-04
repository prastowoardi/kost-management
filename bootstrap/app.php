<?php

use App\Helpers\LogHelper;
use App\Http\Middleware\CheckActive;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
            'active' => CheckActive::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        $middleware->appendToGroup('web', [
            CheckActive::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            $action = 'UNHANDLED_EXCEPTION';

            if ($e instanceof NotFoundHttpException) {
                $action = 'ROUTE_NOT_FOUND';
            } elseif ($e instanceof AccessDeniedHttpException) {
                $action = 'ACCESS_DENIED';
            } elseif ($e instanceof HttpException) {
                $action = 'HTTP_'.$e->getStatusCode().'_ERROR';
            }

            $description = str_replace(['\\', "\n", "\r"], ' ', $e->getMessage());
            $description = substr($description, 0, 200);

            LogHelper::logError($action, $description, $e);

            return null;
        });
    })->create();
