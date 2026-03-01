@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-3 gap-6">
        <!-- Notifications List -->
        <div class="col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Notifications</h2>
                    @if($notifications->count() > 0)
                    <button class="btn btn-sm btn-outline" onclick="markAllAsRead()">
                        Mark all as read
                    </button>
                    @endif
                </div>

                @if($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="notification-item border-l-4 border-blue-500 p-4 rounded hover:bg-gray-50 transition" 
                                 data-id="{{ $notification->id }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-{{ $notification->icon ?? 'bell' }} text-blue-500"></i>
                                            <h3 class="font-semibold text-gray-900">{{ $notification->title }}</h3>
                                            @if(!$notification->is_read)
                                                <span class="badge badge-primary badge-sm">New</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 text-sm mt-2">{{ $notification->message }}</p>
                                        <div class="flex justify-between items-center mt-3">
                                            <span class="text-gray-400 text-xs">
                                                <i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" class="text-blue-500 text-sm hover:underline">
                                                    View Details →
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-2 ml-4">
                                        @if(!$notification->is_read)
                                            <button class="btn btn-xs btn-ghost" onclick="markAsRead('{{ route(str_replace('.notifications.index', '.notifications.mark-read', Route::currentRouteName()), ['id' => $notification->id]) }}', {{ $notification->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-xs btn-ghost text-error" onclick="deleteNotification('{{ route(str_replace('.notifications.index', '.notifications.delete', Route::currentRouteName()), ['id' => $notification->id]) }}', {{ $notification->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-bell text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No notifications yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Notifications:</span>
                        <span class="badge badge-lg">{{ $notifications->total() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Unread:</span>
                        <span class="badge badge-primary badge-lg" id="unread-count">0</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cog"></i> Settings
                </h3>
                <p class="text-gray-600 text-sm mb-4">Manage your notification preferences</p>
                <a href="{{ route(str_replace('notifications', 'notifications.settings', Route::currentRouteName())) }}" 
                   class="btn btn-primary btn-block btn-sm">
                    Notification Settings
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());
const unreadCountUrl = @json(
    route(
        Route::has(str_replace('.notifications.index', '.notifications.unread-count', Route::currentRouteName()))
            ? str_replace('.notifications.index', '.notifications.unread-count', Route::currentRouteName())
            : str_replace('.notifications.index', '.unread-count', Route::currentRouteName())
    )
);

function showFlashMessage(message, type = 'success') {
    const existing = document.getElementById('notification-flash-message');
    if (existing) {
        existing.remove();
    }

    const flash = document.createElement('div');
    flash.id = 'notification-flash-message';
    flash.textContent = message;
    flash.style.position = 'fixed';
    flash.style.top = '20px';
    flash.style.right = '20px';
    flash.style.zIndex = '9999';
    flash.style.padding = '12px 16px';
    flash.style.borderRadius = '8px';
    flash.style.color = '#fff';
    flash.style.fontWeight = '600';
    flash.style.boxShadow = '0 8px 20px rgba(0,0,0,0.2)';
    flash.style.backgroundColor = type === 'success' ? '#16a34a' : '#dc2626';

    document.body.appendChild(flash);

    setTimeout(() => {
        flash.remove();
    }, 2500);
}

function markAsRead(url, notificationId) {
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.ok ? response.json() : Promise.reject(response))
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-id="${notificationId}"]`).remove();
            updateUnreadCount();
            showFlashMessage('Notification marked as read.');
        } else {
            alert('Unable to mark notification as read.');
        }
    })
    .catch(() => alert('Unable to mark notification as read.'));
}

function markAllAsRead() {
    const url = '{{ route(str_replace('.notifications.index', '.notifications.mark-all-read', Route::currentRouteName())) }}';
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.ok ? response.json() : Promise.reject(response))
    .then(data => {
        if (data.success) {
            const unreadBadges = document.querySelectorAll('.notification-item .badge.badge-primary.badge-sm');
            unreadBadges.forEach((badge) => badge.remove());

            const markReadButtons = document.querySelectorAll('.notification-item button[onclick*="markAsRead("]');
            markReadButtons.forEach((button) => button.remove());

            updateUnreadCount();
            showFlashMessage('All notifications marked as read.');
        } else {
            alert('Unable to mark all notifications as read.');
        }
    })
    .catch(() => alert('Unable to mark all notifications as read.'));
}

function deleteNotification(url, notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject(response))
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-id="${notificationId}"]`).remove();
                updateUnreadCount();
            } else {
                alert('Unable to delete notification.');
            }
        })
        .catch(() => alert('Unable to delete notification.'));
    }
}

function updateUnreadCount() {
    fetch(unreadCountUrl)
        .then(response => response.json())
        .then(data => {
            document.getElementById('unread-count').textContent = data.count;
        });
}

// Update unread count on page load
document.addEventListener('DOMContentLoaded', updateUnreadCount);
</script>
@endsection
