<?php

namespace App\Http\Controllers\Admin;

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
        $notifications = $this->baseQuery()
            ->latest()
            ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function getNotifications(): JsonResponse
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([]);
            }

            $notifications = $this->baseQuery()
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
                        'unread' => $notif->status !== Notification::STATUS_READ,
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
            $updated = $this->baseQuery()
                ->where('id', $id)
                ->update([
                    'status' => Notification::STATUS_READ,
                    'read_at' => now(),
                ]);

            if (!$updated) {
                return response()->json(['error' => 'Notification not found'], 404);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Notification not found'], 404);
        }
    }

    public function markAllAsRead(): JsonResponse
    {
        $updated = $this->baseQuery()
            ->unread()
            ->update([
                'read_at' => now(),
                'status' => Notification::STATUS_READ,
            ]);

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    public function getUnreadCount(): JsonResponse
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['count' => 0]);
            }

            return response()->json([
                'count' => $this->baseQuery()->unread()->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    public function getUnreadCounts(): JsonResponse
    {
        return $this->getUnreadCount();
    }

    public function delete($id): JsonResponse
    {
        try {
            $notification = $this->baseQuery()
                ->where('id', $id)
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

        return view('admin.notifications.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'notifications_enabled' => 'boolean',
            'in_app_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'appointment_reminder_enabled' => 'boolean',
            'payment_due_enabled' => 'boolean',
            'low_stock_enabled' => 'boolean',
            'incident_report_enabled' => 'boolean',
            'system_alert_enabled' => 'boolean',
            'user_management_enabled' => 'boolean',
            'quiet_hours_enabled' => 'boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $notificationFields = [
            'notifications_enabled',
            'in_app_enabled',
            'email_enabled',
            'sms_enabled',
            'appointment_reminder_enabled',
            'payment_due_enabled',
            'low_stock_enabled',
            'incident_report_enabled',
            'quiet_hours_enabled',
        ];

        $notificationData = [
            'quiet_hours_start' => $validated['quiet_hours_start'] ?? null,
            'quiet_hours_end' => $validated['quiet_hours_end'] ?? null,
        ];

        foreach ($notificationFields as $field) {
            $notificationData[$field] = $request->boolean($field);
        }

        NotificationSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            $notificationData
        );

        return redirect()->back()->with('success', 'Notification settings updated successfully');
    }

    public function deleteOld(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $count = Notification::where('created_at', '<', now()->subDays($validated['days']))
            ->where('status', Notification::STATUS_READ)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "$count notifications deleted",
        ]);
    }

    private function baseQuery()
    {
        $role = Auth::user()->role ?? null;

        if ($role === 'admin') {
            return Notification::query()
                ->adminOverview()
                ->where('title', '!=', 'New Appointment Assigned')
                ->where(function ($query) {
                    $query->where('title', 'not like', 'Order%')
                        ->where('message', 'not like', 'Your order #%');
                });
        }

        return Notification::forUser(Auth::id())
            ->visibleToRole($role);
    }
}
