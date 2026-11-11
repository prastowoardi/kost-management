<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HoneypotController extends Controller
{
    /**
     * Trap automated scanners and vulnerability probing bots.
     */
    public function trap(Request $request): Response
    {
        $ip = $request->ip() ?: 'unknown';
        $uri = $request->getRequestUri();
        $method = $request->method();
        $ua = $request->userAgent() ?: 'Unknown Bot';

        // Increment hit count for this IP
        $counterKey = "honeypot_hits:{$ip}";
        $hits = (int) Cache::get($counterKey, 0) + 1;
        Cache::put($counterKey, $hits, now()->addHours(24));

        // Mark IP as blocked scanner for 12 hours
        Cache::put("scanner_blocked:{$ip}", true, now()->addHours(12));

        // Log the honeypot detection
        Log::warning("HONEYPOT_TRIGGERED: Bot scanner detected from IP {$ip} requesting '{$uri}' using {$method} (Total hits: {$hits})");

        LogHelper::logError('HONEYPOT_TRIGGERED', "Bot scanner dari IP {$ip} terperangkap di honeypot '{$uri}'", null, [
            'ip' => $ip,
            'url' => $request->fullUrl(),
            'method' => $method,
            'user_agent' => $ua,
            'hits' => $hits,
            'status' => 'IP_AUTOMATICALLY_BLOCKED',
        ]);

        // Return a realistic 404 response
        return response()->view('errors.404', [
            'exception' => new \Exception('Not Found'),
        ], 404);
    }
}
