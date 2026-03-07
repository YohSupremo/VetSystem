<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function markAsRead(int $id): JsonResponse
    {
        $customer = $this->resolveCustomerUser();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $updated = Notification::query()
            ->forUser($customer->id)
            ->visibleToRole($customer->role)
            ->where('id', $id)
            ->where('status', '!=', Notification::STATUS_READ)
            ->update([
                'status' => Notification::STATUS_READ,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'updated' => (bool) $updated,
            'unread_count' => $this->getUnreadCountValue($customer),
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $customer = $this->resolveCustomerUser();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $updated = Notification::query()
            ->forUser($customer->id)
            ->visibleToRole($customer->role)
            ->unread()
            ->update([
                'status' => Notification::STATUS_READ,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'updated' => (int) $updated,
            'unread_count' => 0,
        ]);
    }

    public function getUnreadCount(): JsonResponse
    {
        $customer = $this->resolveCustomerUser();

        if (!$customer) {
            return response()->json(['count' => 0], 401);
        }

        return response()->json([
            'count' => $this->getUnreadCountValue($customer),
        ]);
    }

    private function resolveCustomerUser(): ?User
    {
        $username = session('username');

        if (!$username) {
            return null;
        }

        $user = User::where('username', $username)->first();

        if (!$user || !in_array($user->role, ['pet_owner', 'registered_user'], true)) {
            return null;
        }

        return $user;
    }

    private function getUnreadCountValue(User $customer): int
    {
        return Notification::query()
            ->forUser($customer->id)
            ->visibleToRole($customer->role)
            ->unread()
            ->count();
    }
}
