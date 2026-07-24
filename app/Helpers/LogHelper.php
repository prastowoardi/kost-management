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
        $userName = Auth::user()?->name ?? 'System';
        $ua = Request::userAgent();

        $device = UserAgentParser::parse($ua);

        $source = Request::is('api/*') ? 'api' : 'web';

        $context = [
            'source' => $source,
            'browser' => $device['browser'],
            'os' => $device['os'],
            'device' => $device['device'],
            'ip' => Request::ip(),
            'request_method' => Request::method(),
            'request_url' => Request::fullUrl(),
        ];

        $mergedPayload = $payload
            ? (is_array($payload) ? array_merge($payload, $context) : array_merge((array) $payload, $context))
            : $context;

        ActivityLog::create([
<<<<<<< HEAD
            'user_id' => $currentUserId,
            'action' => $action,
            'description' => $description,
=======
            'user_id'    => $currentUserId,
            'action'     => $action,
            'description'=> "[$userName] $description",
>>>>>>> 4c91f8b711ef89c9bf482d8f4a41035ab2c33a8b
            'model_type' => $model ? get_class($model) : 'App\Models\User',
            'model_id' => $model ? ($model->id ?? null) : $currentUserId,
            'payload' => $mergedPayload,
            'ip_address' => Request::ip(),
            'user_agent' => $ua,
        ]);
    }

    public static function logError($action, $description, $exception = null, $payload = null): void
    {
        $data = $payload ?: [];

        if ($exception) {
            $data['error_message'] = $exception->getMessage();
            $data['error_file'] = $exception->getFile() . ':' . $exception->getLine();
            $data['error_trace'] = $exception->getTraceAsString();
        } else {
            $data['error_message'] = $description;
        }

        self::log($action, $description, null, $data);
    }
}
