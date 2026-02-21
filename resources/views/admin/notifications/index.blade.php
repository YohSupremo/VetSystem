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
                                            <button class="btn btn-xs btn-ghost" onclick="markAsRead({{ $notification->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-xs btn-ghost text-error" onclick="deleteNotification({{ $notification->id }})">
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
function markAsRead(notificationId) {
    fetch(`{{ route('admin.notifications.mark-read', '') }}/${notificationId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-id="${notificationId}"]`).remove();
            updateUnreadCount();
        }
    });
}

function markAllAsRead() {
    fetch(`{{ route('admin.notifications.mark-all-read') }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`{{ route('admin.notifications.delete', '') }}/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-id="${notificationId}"]`).remove();
                updateUnreadCount();
            }
        });
    }
}

function updateUnreadCount() {
    fetch(`{{ route('admin.notifications.unread-count') }}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('unread-count').textContent = data.count;
        });
}

// Update unread count on page load
document.addEventListener('DOMContentLoaded', updateUnreadCount);
</script>
@endsection
