<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogHelper
{
    public static function log($action, $description, $model = null, $payload = null)
    {
        $currentUserId = Auth::id();
        $ua = Request::userAgent();

        $device = UserAgentParser::parse($ua);

        $mergedPayload = $payload;
        $deviceInfo = [
            'browser' => $device['browser'],
            'os' => $device['os'],
            'device' => $device['device'],
            'ip' => Request::ip(),
        ];
        $mergedPayload = $payload
            ? (is_array($payload) ? array_merge($payload, $deviceInfo) : array_merge((array) $payload, $deviceInfo))
            : $deviceInfo;

        ActivityLog::create([
            'user_id'    => $currentUserId,
            'action'     => $action,
            'description'=> $description,
            'model_type' => $model ? get_class($model) : 'App\Models\User',
            'model_id'   => $model ? ($model->id ?? null) : $currentUserId,
            'payload'    => $mergedPayload,
            'ip_address' => Request::ip(),
            'user_agent' => $ua,
        ]);
    }
}