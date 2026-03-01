<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create and send a notification to a specific user
     */
    public function send(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): ?Notification {
        // Check if user has notifications enabled
        $settings = NotificationSetting::where('user_id', $user->id)->first();

        if (!$settings) {
            $settings = NotificationSetting::create(
                NotificationSetting::getDefaultSettings($user->id)
            );
        }

        if (!$settings->notifications_enabled) {
            return null;
        }

        $notificationData = [
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'method' => $data['method'] ?? Notification::METHOD_IN_APP,
            'status' => Notification::STATUS_PENDING,
            'priority' => $data['priority'] ?? Notification::PRIORITY_NORMAL,
            'icon' => $data['icon'] ?? $this->getIconForType($type),
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'scheduled_for' => $data['scheduled_for'] ?? now(),
        ];

        return Notification::create($notificationData);
    }

    /**
     * Create and send notification to multiple users by role
     */
    public function sendToRole(
        string $role,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): Collection {
        $users = User::where('role', $role)->where('is_active', true)->get();

        return $users->map(fn($user) => $this->send($user, $type, $title, $message, $data))
            ->filter(fn($notif) => $notif !== null);
    }

    /**
     * Create and send notification to multiple specific users
     */
    public function sendToUsers(
        array $userIds,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): Collection {
        $users = User::whereIn('id', $userIds)->where('is_active', true)->get();

        return $users->map(fn($user) => $this->send($user, $type, $title, $message, $data))
            ->filter(fn($notif) => $notif !== null);
    }

    /**
     * Create and send notification to all staff (non-customer roles)
     */
    public function sendToAllStaff(
        string $type,
        string $title,
        string $message,
        array $data = []
    ): Collection {
        $staffRoles = ['admin', 'veterinarian', 'pharmacy', 'reception', 'boarding', 'groomer', 'staff'];
        $users = User::whereIn('role', $staffRoles)->where('is_active', true)->get();

        return $users->map(fn($user) => $this->send($user, $type, $title, $message, $data))
            ->filter(fn($notif) => $notif !== null);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): Notification
    {
        $notification->markAsRead();
        return $notification;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications()
            ->where('status', '!=', Notification::STATUS_READ)
            ->update(['read_at' => now(), 'status' => Notification::STATUS_READ]);
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()
            ->where('status', '!=', Notification::STATUS_READ)
            ->count();
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecent(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get notifications by type for a user
     */
    public function getByType(User $user, string $type, int $limit = 10): Collection
    {
        return $user->notifications()
            ->where('type', $type)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Delete old notifications (older than specified days)
     */
    public function deleteOld(int $days = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($days))
            ->where('status', Notification::STATUS_READ)
            ->delete();
    }

    /**
     * Get default icon based on notification type
     */
    private function getIconForType(string $type): string
    {
        return match ($type) {
            Notification::TYPE_APPOINTMENT => 'calendar-alt',
            Notification::TYPE_VACCINATION => 'syringe',
            Notification::TYPE_PAYMENT, Notification::TYPE_PAYMENT_OVERDUE => 'credit-card',
            Notification::TYPE_LAB_TEST => 'flask',
            Notification::TYPE_PRESCRIPTION => 'pills',
            Notification::TYPE_BOARDING => 'building',
            Notification::TYPE_INVENTORY => 'boxes',
            Notification::TYPE_EXPIRY => 'exclamation-triangle',
            default => 'bell',
        };
    }

    /**
     * Batch send notifications with retry logic
     */
    public function batchSend(array $notifications): void
    {
        foreach ($notifications as $notif) {
            try {
                $this->send(
                    $notif['user'],
                    $notif['type'],
                    $notif['title'],
                    $notif['message'],
                    $notif['data'] ?? []
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send notification', [
                    'user_id' => $notif['user']->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Update notification delivery status
     */
    public function updateDeliveryStatus(
        Notification $notification,
        string $status,
        ?string $errorMessage = null
    ): Notification {
        $notification->update([
            'status' => $status,
            'sent_at' => $status === Notification::STATUS_SENT ? now() : $notification->sent_at,
            'error_message' => $errorMessage,
        ]);

        return $notification;
    }

    /**
     * Retry failed notifications
     */
    public function retryFailed(int $maxRetries = 3): int
    {
        $failedNotifications = Notification::where('status', Notification::STATUS_FAILED)
            ->where('retry_count', '<', $maxRetries)
            ->get();

        foreach ($failedNotifications as $notif) {
            $notif->increment('retry_count');
            $notif->update(['status' => Notification::STATUS_PENDING]);
        }

        return $failedNotifications->count();
    }
}
