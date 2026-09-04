<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Notification;
use Throwable;

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
        try {
            $notification->update(['is_read' => true]);

            return response()->json(['ok' => true]);
        } catch (Throwable $e) {
            LogHelper::logError('NOTIFICATION_READ_FAILED', "Gagal menandai notifikasi #{$notification->id} dibaca", $e);

            return response()->json(['ok' => false], 500);
        }
    }

    public function markAllRead()
    {
        try {
            Notification::unread()->update(['is_read' => true]);

            return response()->json(['ok' => true]);
        } catch (Throwable $e) {
            LogHelper::logError('NOTIFICATION_READ_ALL_FAILED', 'Gagal menandai semua notifikasi dibaca', $e);

            return response()->json(['ok' => false], 500);
        }
    }
}
