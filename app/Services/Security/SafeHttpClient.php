<?php

namespace App\Services\Security;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class SafeHttpClient
{
    /**
     * Private and loopback IPv4/IPv6 ranges to block for SSRF prevention.
     */
    private const BLOCKED_IP_RANGES = [
        '0.0.0.0/8',
        '10.0.0.0/8',
        '100.64.0.0/10',
        '127.0.0.0/8',
        '169.254.0.0/16',      // Cloud Metadata / Link-Local
        '172.16.0.0/12',
        '192.0.0.0/24',
        '192.0.2.0/24',
        '192.88.99.0/24',
        '192.168.0.0/16',
        '198.18.0.0/15',
        '198.51.100.0/24',
        '203.0.113.0/24',
        '224.0.0.0/4',
        '240.0.0.0/4',
        '255.255.255.255/32',
    ];

    /**
     * Validate an external URL to prevent SSRF attacks.
     *
     * @param string $url
     * @param bool $allowLocalhost Whether to allow localhost (e.g. for internal whatsapp gateway)
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function validateUrl(string $url, bool $allowLocalhost = false): bool
    {
        $parsed = parse_url($url);

        if (! $parsed || ! isset($parsed['scheme'], $parsed['host'])) {
            throw new InvalidArgumentException('URL tidak valid.');
        }

        // Only allow HTTP and HTTPS
        if (! in_array(strtolower($parsed['scheme']), ['http', 'https'], true)) {
            throw new InvalidArgumentException('Protokol harus HTTP atau HTTPS.');
        }

        $host = strtolower($parsed['host']);

        // Check for localhost or loopback name
        if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
            if ($allowLocalhost) {
                return true;
            }
            throw new InvalidArgumentException('Akses ke loopback / localhost diblokir untuk mencegah SSRF.');
        }

        // Resolve IP
        $ips = @dns_get_record($host, DNS_A + DNS_AAAA);
        $resolvedIps = [];

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $resolvedIps[] = $host;
        } elseif ($ips) {
            foreach ($ips as $record) {
                if (isset($record['ip'])) {
                    $resolvedIps[] = $record['ip'];
                }
                if (isset($record['ipv6'])) {
                    $resolvedIps[] = $record['ipv6'];
                }
            }
        }

        if (empty($resolvedIps)) {
            // Cannot resolve host
            throw new InvalidArgumentException("Host {$host} tidak dapat di-resolve.");
        }

        // Check against blocked IP ranges
        foreach ($resolvedIps as $ip) {
            if (self::isPrivateOrReservedIp($ip)) {
                if ($allowLocalhost && ($ip === '127.0.0.1' || $ip === '::1')) {
                    continue;
                }
                throw new InvalidArgumentException("Akses ke IP privat/internal ({$ip}) diblokir untuk mencegah SSRF.");
            }
        }

        return true;
    }

    /**
     * Check if an IP address is within private, loopback, or reserved ranges.
     */
    public static function isPrivateOrReservedIp(string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }

        // Cloud metadata check specifically (169.254.169.254)
        if ($ip === '169.254.169.254') {
            return true;
        }

        return false;
    }

    /**
     * Safely send a GET request after SSRF validation.
     */
    public static function get(string $url, array $query = [], int $timeout = 10, bool $allowLocalhost = false): Response
    {
        self::validateUrl($url, $allowLocalhost);

        return Http::timeout($timeout)->get($url, $query);
    }

    /**
     * Safely send a POST request after SSRF validation.
     */
    public static function post(string $url, array $data = [], int $timeout = 10, bool $allowLocalhost = false): Response
    {
        self::validateUrl($url, $allowLocalhost);

        return Http::timeout($timeout)->post($url, $data);
    }
}
