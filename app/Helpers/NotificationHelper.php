<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function create(string $type, string $title, ?string $message = null, ?string $link = null): Notification
    {
        return Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }
}
