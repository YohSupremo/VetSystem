@php
    $statusClasses = [
        'scheduled' => 'primary',
        'in_progress' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
        'no_show' => 'warning'
    ];
    
    $statusLabel = ucfirst(str_replace('_', ' ', $appointment->status));
    $badgeClass = $statusClasses[$appointment->status] ?? 'secondary';
    
    // Calculate wait time if checked in
    $waitTime = null;
    if ($appointment->check_in_time) {
        $checkIn = \Carbon\Carbon::parse($appointment->check_in_time);
        $now = now();
        $waitTime = $checkIn->diffInMinutes($now);
    }
    
    // Format appointment time
    $appointmentTime = $appointment->appointment_date->format('h:i A');
    
    // Get pet and owner details
    $petName = optional($appointment->pet)->name ?? 'Unknown Pet';
    $petType = optional($appointment->pet)->species ?? 'N/A';
    $ownerUser = optional(optional($appointment->pet)->owner)->user;
    $ownerName = $ownerUser ? ($ownerUser->first_name . ' ' . $ownerUser->last_name) : 'Unknown Owner';
    $veterinarian = optional($appointment->veterinarian);
    $veterinarianName = $veterinarian->first_name && $veterinarian->last_name
        ? ($veterinarian->first_name . ' ' . $veterinarian->last_name)
        : 'Unassigned';
@endphp

<div class="queue-item queue-item-{{ $appointment->status }}" data-id="{{ $appointment->id }}">
    <div class="queue-item-header">
        @if($appointment->queue_number)
            <span class="queue-number">#{{ $appointment->queue_number }}</span>
        @endif
        <span class="badge badge-{{ $badgeClass }}">
            {{ $statusLabel }}
        </span>
    </div>
    
    <a href="{{ route('admin.queue.show', $appointment) }}" class="d-flex justify-content-between align-items-center mb-2" style="text-decoration: none; color: inherit;">
        <div>
            <div class="pet-name">{{ $petName }}</div>
            <div class="pet-type">{{ $petType }}</div>
        </div>
        <div class="text-right">
            <div class="text-muted small">{{ $appointmentTime }}</div>
            @if($appointment->type)
                <span class="badge badge-light">{{ ucfirst($appointment->type) }}</span>
            @endif
        </div>
    </a>
    
    <div class="small text-muted mb-2">
        <div><i class="fas fa-user-md mr-1"></i> {{ $veterinarianName }}</div>
        @if($appointment->check_in_time)
            <div><i class="far fa-clock mr-1"></i> Checked in: {{ $appointment->check_in_time->format('h:i A') }}</div>
        @endif
        @if($waitTime !== null)
            <div><i class="fas fa-hourglass-half mr-1"></i> Waited: {{ $waitTime }} minutes</div>
        @endif
    </div>
    
    @if($appointment->notes)
        <div class="small text-muted mb-2">
            <strong>Notes:</strong> {{ Str::limit($appointment->notes, 50) }}
        </div>
    @endif
    
    <div class="action-buttons d-flex justify-content-between">
        <div>
            @if($appointment->status === 'scheduled')
                <form method="POST" action="{{ route('admin.queue.status.update', ['appointment' => $appointment->id]) }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-play"></i> Start
                    </button>
                </form>
            @elseif($appointment->status === 'in_progress')
                <form method="POST" action="{{ route('admin.queue.status.update', ['appointment' => $appointment->id]) }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-check"></i> Complete
                    </button>
                </form>
            @endif
        </div>
        
        <div>
            @if($appointment->status !== 'cancelled' && $appointment->status !== 'no_show')
                <form method="POST" action="{{ route('admin.queue.status.update', ['appointment' => $appointment->id]) }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-times-circle"></i> Cancel
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.queue.status.update', ['appointment' => $appointment->id]) }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="no_show">
                    <button type="submit" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-user-slash"></i> No Show
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
