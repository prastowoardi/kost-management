<?php

use App\Http\Controllers\HoneypotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Security Honeypot Routes
|--------------------------------------------------------------------------
| Trap automated bot scanners looking for vulnerable files and endpoints.
| Triggers automatic IP throttling and blocks malicious bots.
*/

Route::middleware('throttle:30,1')->group(function () {
    // 1. WordPress Probes
    Route::any('/wp-login.php', [HoneypotController::class, 'trap']);
    Route::any('/xmlrpc.php', [HoneypotController::class, 'trap']);
    Route::any('/wp-config.php', [HoneypotController::class, 'trap']);
    Route::any('/wp-cron.php', [HoneypotController::class, 'trap']);
    Route::any('/wp-admin/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/wp-content/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/wp-includes/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/wp-json/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');

    // 2. Sensitive Environment & Configuration Probes
    Route::any('/.env{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/.git/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/.svn/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/.aws/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/.vscode/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/config.json', [HoneypotController::class, 'trap']);
    Route::any('/web.config', [HoneypotController::class, 'trap']);
    Route::any('/settings.json', [HoneypotController::class, 'trap']);
    Route::any('/runtime-config.js', [HoneypotController::class, 'trap']);
    Route::any('/server-info', [HoneypotController::class, 'trap']);
    Route::any('/sentry.properties', [HoneypotController::class, 'trap']);
    Route::any('/docker-compose.yml', [HoneypotController::class, 'trap']);
    Route::any('/Dockerfile', [HoneypotController::class, 'trap']);

    // 3. Database Management Probes
    Route::any('/phpmyadmin{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/pma{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/adminer{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/mysql{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/dbadmin{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/myadmin{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/sql{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');

    // 4. Common Web Shells & Backdoors
    Route::any('/shell.php', [HoneypotController::class, 'trap']);
    Route::any('/c99.php', [HoneypotController::class, 'trap']);
    Route::any('/r57.php', [HoneypotController::class, 'trap']);
    Route::any('/cmd.php', [HoneypotController::class, 'trap']);
    Route::any('/alfa.php', [HoneypotController::class, 'trap']);
    Route::any('/wso.php', [HoneypotController::class, 'trap']);
    Route::any('/eval.php', [HoneypotController::class, 'trap']);
    Route::any('/b374k.php', [HoneypotController::class, 'trap']);
    Route::any('/uploader.php', [HoneypotController::class, 'trap']);

    // 5. Framework & Dependency Leak Probes
    Route::any('/vendor/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/composer.json', [HoneypotController::class, 'trap']);
    Route::any('/composer.lock', [HoneypotController::class, 'trap']);
    Route::any('/package.json', [HoneypotController::class, 'trap']);
    Route::any('/actuator/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/solr/{any?}', [HoneypotController::class, 'trap'])->where('any', '.*');
    Route::any('/manager/html', [HoneypotController::class, 'trap']);
    Route::any('/administrator', [HoneypotController::class, 'trap']);
});
