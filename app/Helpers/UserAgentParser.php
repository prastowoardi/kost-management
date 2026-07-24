<?php

namespace App\Helpers;

class UserAgentParser
{
    public static function parse(?string $ua): array
    {
        if (! $ua) {
            return ['browser' => null, 'os' => null, 'device' => null, 'raw' => null];
        }

        return [
            'browser' => self::detectBrowser($ua),
            'os' => self::detectOs($ua),
            'device' => self::detectDevice($ua),
            'raw' => $ua,
        ];
    }

    private static function detectBrowser(string $ua): string
    {
        if (preg_match('/Edg\/([\d.]+)/i', $ua, $m)) {
            return 'Edge '.$m[1];
        }
        if (preg_match('/OPR\/([\d.]+)/i', $ua, $m)) {
            return 'Opera '.$m[1];
        }
        if (preg_match('/Firefox\/([\d.]+)/i', $ua, $m)) {
            return 'Firefox '.$m[1];
        }
        if (preg_match('/Chrome\/([\d.]+)/i', $ua, $m)) {
            return 'Chrome '.$m[1];
        }
        if (preg_match('/Safari\/([\d.]+)/i', $ua, $m)) {
            return 'Safari '.$m[1];
        }
        if (stripos($ua, 'okhttp') !== false) {
            return 'OkHttp';
        }
        if (preg_match('/Dalvik\/([\d.]+)/i', $ua, $m)) {
            return 'Android App';
        }

        return 'Unknown';
    }

    private static function detectOs(string $ua): string
    {
        if (preg_match('/Windows NT ([\d.]+)/i', $ua, $m)) {
            $v = $m[1];
            $names = ['10.0' => 'Windows 10', '6.3' => 'Windows 8.1', '6.2' => 'Windows 8', '6.1' => 'Windows 7'];

            return $names[$v] ?? 'Windows '.$v;
        }
        if (preg_match('/Android ([\d.]+)/i', $ua, $m)) {
            return 'Android '.$m[1];
        }
        if (preg_match('/iPhone OS ([\d_]+)/i', $ua, $m)) {
            return 'iOS '.str_replace('_', '.', $m[1]);
        }
        if (preg_match('/CPU OS ([\d_]+)/i', $ua, $m)) {
            return 'iPadOS '.str_replace('_', '.', $m[1]);
        }
        if (preg_match('/Mac OS X ([\d_]+)/i', $ua, $m)) {
            return 'macOS '.str_replace('_', '.', $m[1]);
        }
        if (preg_match('/Linux/i', $ua)) {
            return 'Linux';
        }
        if (stripos($ua, 'okhttp') !== false) {
            return 'Android';
        }
        if (preg_match('/KFAPWI|Kindle|Silk/i', $ua)) {
            return 'Fire OS';
        }

        return 'Unknown';
    }

    private static function detectDevice(string $ua): string
    {
        if (stripos($ua, 'tablet') !== false || stripos($ua, 'ipad') !== false) {
            return 'tablet';
        }
        if (stripos($ua, 'mobile') !== false || stripos($ua, 'iphone') !== false || stripos($ua, 'android') !== false) {
            return 'mobile';
        }
        if (stripos($ua, 'okhttp') !== false || stripos($ua, 'dalvik') !== false) {
            return 'mobile';
        }

        return 'desktop';
    }
}
