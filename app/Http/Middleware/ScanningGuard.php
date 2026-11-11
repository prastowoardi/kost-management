<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ScanningGuard
{
    /**
     * Check if client IP is flagged as a malicious scanner.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip() ?: 'unknown';

        // Check if IP is currently blacklisted by honeypots or attack guards
        if (Cache::has("scanner_blocked:{$ip}")) {
            // Whitelist localhost only when in local dev unless running automated tests specifically testing this
            if (app()->environment('local') && ($ip === '127.0.0.1' || $ip === '::1') && ! config('security.test_blocking', false)) {
                return $next($request);
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'forbidden',
                    'message' => 'Akses dari alamat IP ini diblokir sementara karena aktivitas pemindaian mencurigakan.',
                ], 403);
            }

            return response()->view('errors.403', [
                'exception' => new \Exception('Akses Anda dibatasi sementara karena aktivitas tidak wajar terdeteksi dari IP ini.'),
            ], 403);
        }

        return $next($request);
    }
}
