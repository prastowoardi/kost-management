<?php

namespace App\Http\Middleware;

use App\Helpers\LogHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AttackGuard
{
    /**
     * Patterns that indicate malicious Remote Code Execution (RCE) / PHP execution attempts.
     */
    private const RCE_PATTERNS = [
        '/(?:system|exec|passthru|shell_exec|proc_open|popen)\s*\(/i',
        '/(?:php:\/\/input|php:\/\/filter|data:\/\/text|phar:\/\/|expect:\/\/)/i',
        '/eval\s*\(\s*(?:base64_decode|gzinflate|eval|\$_)/i',
        '/\b(base64_decode|file_get_contents|readfile)\s*\(\s*["\'](?:php:\/\/|https?:\/\/)/i',
    ];

    /**
     * Patterns that indicate directory / path traversal attacks.
     */
    private const TRAVERSAL_PATTERNS = [
        '/\.\.\/|\.\.\\\\/',
        '/(?:\/|\\\\)etc(?:\/|\\\\)passwd/i',
        '/(?:\/|\\\\)etc(?:\/|\\\\)shadow/i',
        '/(?:\/|\\\\)windows(?:\/|\\\\)win\.ini/i',
        '/(?:\/|\\\\)boot\.ini/i',
    ];

    /**
     * Patterns that indicate blatant SQL Injection attempts in query parameters.
     */
    private const SQLI_PATTERNS = [
        '/\bunion\s+(?:all\s+)?select\b/i',
        '/\bselect\b.+\bfrom\b.+\binformation_schema\b/i',
        '/["\']\s*(?:or|and)\s+["\']?1["\']?\s*=\s*["\']?1/i',
        '/(?:benchmark|sleep)\s*\(\s*\d+\s*,/i',
        '/;\s*waitfor\s+delay\s+["\']/i',
        '/["\']\s*;\s*drop\s+table\b/i',
    ];

    /**
     * Patterns that indicate script / XSS injection attempts in query strings.
     */
    private const XSS_PATTERNS = [
        '/<script\b[^>]*>/i',
        '/javascript\s*:\s*[a-z0-9_]/i',
        '/vbscript\s*:/i',
        '/<(?:iframe|object|embed)\b[^>]*>/i',
        '/(?:onmouseover|onfocus|onload|onerror|onclick)\s*=\s*["\']?[^"\'>\s]+/i',
    ];

    /**
     * Handle incoming request and inspect for malicious attack vectors.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = rawurldecode($request->getRequestUri());

        // 1. Check for null-byte injection
        if (str_contains($uri, "\0") || str_contains($request->getQueryString() ?? '', "\0")) {
            return $this->blockRequest($request, 'NULL_BYTE_INJECTION', 'Null byte injection detected in URI');
        }

        // 2. Check URI & Query string against Path Traversal
        foreach (self::TRAVERSAL_PATTERNS as $pattern) {
            if (preg_match($pattern, $uri)) {
                return $this->blockRequest($request, 'PATH_TRAVERSAL', "Path traversal attempt matched: {$pattern}", 404);
            }
        }

        // 3. Check Query Parameters and Headers for RCE, SQLi, and XSS
        $allParams = $request->query();

        // Also inspect body inputs if not uploading raw files
        if (! $request->is('api/public/pay/*/upload') && ! $request->is('api/complaints')) {
            $inputs = $request->except([
                '_token',
                'password',
                'password_confirmation',
                'receipt_file',
                'image',
                'images',
                'photo',
                'proof_image',
                'contract',
            ]);
            $this->flattenArray($inputs, $allParams);
        }

        foreach ($allParams as $key => $value) {
            if (! is_string($value)) {
                continue;
            }

            // Test for RCE
            foreach (self::RCE_PATTERNS as $pattern) {
                if (preg_match($pattern, $value)) {
                    return $this->blockRequest($request, 'RCE_ATTEMPT', "RCE attempt in parameter '{$key}': {$pattern}");
                }
            }

            // Test for SQLi
            foreach (self::SQLI_PATTERNS as $pattern) {
                if (preg_match($pattern, $value)) {
                    return $this->blockRequest($request, 'SQLI_ATTEMPT', "SQL injection attempt in parameter '{$key}': {$pattern}");
                }
            }

            // Test for XSS in query parameters specifically
            if (isset($request->query()[$key])) {
                foreach (self::XSS_PATTERNS as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return $this->blockRequest($request, 'XSS_ATTEMPT', "XSS attempt in query parameter '{$key}': {$pattern}");
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Recursively flatten an array into key-value pairs for inspection.
     */
    private function flattenArray(array $array, array &$result, string $prefix = ''): void
    {
        foreach ($array as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : (string) $key;
            if (is_array($value)) {
                $this->flattenArray($value, $result, $fullKey);
            } elseif (is_string($value)) {
                $result[$fullKey] = $value;
            }
        }
    }

    /**
     * Reject attack and log event.
     */
    private function blockRequest(Request $request, string $threatType, string $detail, int $statusCode = 403): Response
    {
        $ip = $request->ip() ?: 'unknown';

        // Increment malicious attempt counter in cache
        $cacheKey = "threat_count:{$ip}";
        $threatCount = (int) Cache::get($cacheKey, 0) + 1;
        Cache::put($cacheKey, $threatCount, now()->addHours(6));

        if ($threatCount >= 5) {
            // Block IP for 2 hours if persistent
            Cache::put("scanner_blocked:{$ip}", true, now()->addHours(2));
        }

        LogHelper::logError('SECURITY_ATTACK_BLOCKED', "[AttackGuard] {$threatType} dari IP {$ip}: {$detail}", null, [
            'threat_type' => $threatType,
            'ip' => $ip,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'threat_count' => $threatCount,
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Permintaan ditolak oleh filter keamanan sistem.',
            ], $statusCode);
        }

        $viewName = view()->exists("errors.{$statusCode}") ? "errors.{$statusCode}" : 'errors.403';

        return response()->view($viewName, [
            'exception' => new \Exception('Permintaan tidak dapat diproses karena melanggar aturan keamanan sistem.'),
        ], $statusCode);
    }
}
