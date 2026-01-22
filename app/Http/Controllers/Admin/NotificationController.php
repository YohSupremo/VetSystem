<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            if (!$userId) {
                return response()->json([]);
            }

            $notifications = Notification::forUser($userId)
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($notif) {
                    return [
                        'id' => $notif->id,
                        'title' => $notif->title,
                        'message' => $notif->message,
                        'type' => $notif->type,
                        'icon' => $notif->icon,
                        'time' => $notif->created_at->diffForHumans(),
                        'unread' => !$notif->is_read,
                    ];
                });

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getMessages(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            if (!$userId) {
                return response()->json([]);
            }

            $messages = Message::forRecipient($userId)
                ->with('sender')
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'sender' => $msg->sender->first_name . ' ' . $msg->sender->last_name,
                        'avatar' => strtoupper(substr($msg->sender->first_name, 0, 1) . substr($msg->sender->last_name, 0, 1)),
                        'preview' => substr($msg->body, 0, 60) . (strlen($msg->body) > 60 ? '...' : ''),
                        'time' => $msg->created_at->diffForHumans(),
                        'unread' => !$msg->is_read,
                    ];
                });

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function markNotificationAsRead($id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markMessageAsRead($id): JsonResponse
    {
        $message = Message::findOrFail($id);
        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsAsRead(): JsonResponse
    {
        Notification::forUser(Auth::id())
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCounts(): JsonResponse
    {
        try {
            $userId = Auth::id();
            
            if (!$userId) {
                return response()->json([
                    'notifications' => 0,
                    'messages' => 0,
                ]);
            }

            return response()->json([
                'notifications' => Notification::forUser($userId)->unread()->count(),
                'messages' => Message::forRecipient($userId)->unread()->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'notifications' => 0,
                'messages' => 0,
            ]);
        }
    }
}
