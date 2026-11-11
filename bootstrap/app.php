<?php

use App\Helpers\LogHelper;
use App\Http\Middleware\AttackGuard;
use App\Http\Middleware\CheckActive;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\ScanningGuard;
use App\Http\Middleware\SecurityHeaders;
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
            'attack.guard' => AttackGuard::class,
            'scanning.guard' => ScanningGuard::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Global security layers
        $middleware->append(SecurityHeaders::class);
        $middleware->append(ScanningGuard::class);
        $middleware->append(AttackGuard::class);

        $middleware->appendToGroup('web', [
            CheckActive::class,
        ]);

        $middleware->appendToGroup('api', [
            CheckActive::class,
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            // Abaikan pencatatan 404 (Route not found / Bot scanning) ke Activity Log
            // agar tidak memenuhi database activity log admin
            if ($e instanceof NotFoundHttpException) {
                return null;
            }

            $action = 'UNHANDLED_EXCEPTION';

            if ($e instanceof AccessDeniedHttpException) {
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
