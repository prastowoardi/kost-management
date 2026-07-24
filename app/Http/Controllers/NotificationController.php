<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->take(10)->get();

        return response()->json([
            'unread_count' => Notification::unread()->count(),
            'items' => $notifications->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'link' => $n->link,
                'is_read' => $n->is_read,
                'time' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    public function markRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        Notification::unread()->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }
}
