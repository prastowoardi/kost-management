<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Illuminate\Support\Facades\Request;

class LogHelper
{
    public static function log($action, $description, $model = null, $payload = null)
    {
        $currentUserId = Auth::id(); 

        ActivityLog::create([
            'user_id'    => $currentUserId,
            'action'     => $action,
            'description'=> $description,
            'model_type' => $model ? get_class($model) : 'App\Models\User', 
            'model_id'   => $model ? ($model->id ?? null) : $currentUserId,
            'payload'    => $payload,
            'ip_address' => Request::ip(),
        ]);
    }
}