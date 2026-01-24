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
    $petName = $appointment->pet->name ?? 'Unknown Pet';
    $petType = $appointment->pet->species ?? 'N/A';
    $ownerName = $appointment->pet->owner->user->first_name . ' ' . $appointment->pet->owner->user->last_name ?? 'Unknown Owner';
    $veterinarianName = $appointment->veterinarian->first_name . ' ' . $appointment->veterinarian->last_name ?? 'Unassigned';
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
    
    <div class="d-flex justify-content-between align-items-center mb-2">
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
    </div>
    
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
                <button class="btn btn-sm btn-outline-success start-appointment" data-id="{{ $appointment->id }}">
                    <i class="fas fa-play"></i> Start
                </button>
            @elseif($appointment->status === 'in_progress')
                <button class="btn btn-sm btn-outline-primary complete-appointment" data-id="{{ $appointment->id }}">
                    <i class="fas fa-check"></i> Complete
                </button>
            @endif
            
            <button class="btn btn-sm btn-outline-secondary edit-notes" data-id="{{ $appointment->id }}" data-notes="{{ $appointment->notes ?? '' }}">
                <i class="fas fa-edit"></i> Notes
            </button>
        </div>
        
        <div class="dropdown d-inline-block">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="moreActions{{ $appointment->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moreActions{{ $appointment->id }}">
                <a class="dropdown-item view-details" href="#" data-id="{{ $appointment->id }}">
                    <i class="fas fa-eye mr-2"></i>View Details
                </a>
                @if($appointment->status !== 'cancelled' && $appointment->status !== 'no_show')
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Change Status</h6>
                    <a class="dropdown-item change-status" href="#" data-id="{{ $appointment->id }}" data-status="scheduled" data-current-status="{{ $appointment->status }}">
                        <span class="badge badge-primary mr-2">&nbsp;</span> Scheduled
                    </a>
                    <a class="dropdown-item change-status" href="#" data-id="{{ $appointment->id }}" data-status="in_progress" data-current-status="{{ $appointment->status }}">
                        <span class="badge badge-info mr-2">&nbsp;</span> In Progress
                    </a>
                    <a class="dropdown-item change-status" href="#" data-id="{{ $appointment->id }}" data-status="completed" data-current-status="{{ $appointment->status }}">
                        <span class="badge badge-success mr-2">&nbsp;</span> Completed
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item change-status text-danger" href="#" data-id="{{ $appointment->id }}" data-status="cancelled" data-current-status="{{ $appointment->status }}">
                        <i class="fas fa-times-circle mr-2"></i> Cancel
                    </a>
                    <a class="dropdown-item change-status text-warning" href="#" data-id="{{ $appointment->id }}" data-status="no_show" data-current-status="{{ $appointment->status }}">
                        <i class="fas fa-user-slash mr-2"></i> Mark as No Show
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
