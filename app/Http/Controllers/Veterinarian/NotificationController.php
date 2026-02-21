<?php

namespace App\Http\Controllers\Veterinarian;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role ?? null;
        $notifications = Notification::forUser(Auth::id())
            ->visibleToRole($role)
            ->latest()
            ->paginate(15);

        return view('veterinarian.notifications.index', compact('notifications'));
    }

    public function getNotifications(): JsonResponse
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([]);
            }

            $notifications = Notification::forUser($userId)
                ->visibleToRole(Auth::user()->role ?? null)
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
                        'priority' => $notif->priority,
                        'time' => $notif->created_at->diffForHumans(),
                        'unread' => !$notif->is_read,
                        'action_url' => $notif->action_url,
                    ];
                });

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function markAsRead($id): JsonResponse
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->visibleToRole(Auth::user()->role ?? null)
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Notification not found'], 404);
        }
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::forUser(Auth::id())
            ->visibleToRole(Auth::user()->role ?? null)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'status' => Notification::STATUS_READ,
            ]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount(): JsonResponse
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['count' => 0]);
            }

            return response()->json([
                'count' => Notification::forUser($userId)
                    ->visibleToRole(Auth::user()->role ?? null)
                    ->unread()
                    ->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    public function delete($id): JsonResponse
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->visibleToRole(Auth::user()->role ?? null)
                ->firstOrFail();

            $notification->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Notification not found'], 404);
        }
    }

    public function settings()
    {
        $settings = NotificationSetting::where('user_id', Auth::id())
            ->first() ?? new NotificationSetting(
                NotificationSetting::getDefaultSettings(Auth::id())
            );

        return view('veterinarian.notifications.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'notifications_enabled' => 'boolean',
            'in_app_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'appointment_reminder_enabled' => 'boolean',
            'medical_record_enabled' => 'boolean',
            'prescription_enabled' => 'boolean',
            'vaccination_enabled' => 'boolean',
            'lab_result_enabled' => 'boolean',
            'surgery_status_enabled' => 'boolean',
            'quiet_hours_enabled' => 'boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        NotificationSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return redirect()->back()->with('success', 'Notification settings updated successfully');
    }
}
