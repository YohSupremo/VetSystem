@extends('layouts.admin')

@section('title', 'Queue Management')

@push('styles')
<style>
    .queue-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }
    .queue-column {
        flex: 1;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .queue-column h3 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
        color: #333;
    }
    .queue-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 12px 15px;
        margin-bottom: 10px;
        cursor: move;
        transition: all 0.3s ease;
    }
    .queue-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .queue-item.in-progress {
        border-left: 4px solid #007bff;
    }
    .queue-item.completed {
        opacity: 0.7;
        background-color: #f8f9fa;
    }
    .queue-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .queue-number {
        background: #007bff;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 12px;
        font-weight: bold;
    }
    .pet-name {
        font-weight: 600;
        color: #333;
    }
    .pet-type {
        font-size: 12px;
        color: #6c757d;
    }
    .wait-time {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
        margin-top: 10px;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .stats-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
    }
    .stat-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Queue Management</h1>
        <div class="d-flex
        ">
            <div class="mr-3">
                <select id="veterinarianFilter" class="form-control form-control-sm">
                    <option value="">All Veterinarians</option>
                    @foreach($veterinarians as $vet)
                        <option value="{{ $vet->id }}">{{ $vet->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-sm btn-primary" id="callNextBtn">
                <i class="fas fa-bell"></i> Call Next
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-value" id="waitingCount">0</div>
                <div class="stat-label">Waiting</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-value" id="inProgressCount">0</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-value" id="completedCount">0</div>
                <div class="stat-label">Completed Today</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-value" id="avgWaitTime">0 min</div>
                <div class="stat-label">Avg. Wait Time</div>
            </div>
        </div>
    </div>

    <div class="queue-container">
        <!-- Waiting Column -->
        <div class="queue-column">
            <h3>Waiting <span class="badge badge-primary" id="waitingBadge">0</span></h3>
            <div id="waiting-queue" class="sortable-queue" data-status="scheduled">
                @foreach($appointments['scheduled'] as $appointment)
                    @include('admin.queue.partials.appointment-card', ['appointment' => $appointment])
                @endforeach
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="queue-column">
            <h3>In Progress <span class="badge badge-info" id="inProgressBadge">0</span></h3>
            <div id="in-progress-queue" class="sortable-queue" data-status="in_progress">
                @foreach($appointments['in_progress'] as $appointment)
                    @include('admin.queue.partials.appointment-card', ['appointment' => $appointment])
                @endforeach
            </div>
        </div>

        <!-- Completed Column -->
        <div class="queue-column">
            <h3>Completed <span class="badge badge-secondary" id="completedBadge">0</span></h3>
            <div id="completed-queue" class="sortable-queue" data-status="completed">
                @foreach($appointments['completed'] as $appointment)
                    @include('admin.queue.partials.appointment-card', ['appointment' => $appointment])
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Appointment Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="statusForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" id="appointment_id">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no_show">No Show</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Sortable for all queue columns
    const sortableOptions = {
        group: 'queue',
        animation: 150,
        onEnd: function(evt) {
            const appointmentId = evt.item.dataset.id;
            const newStatus = evt.to.dataset.status;
            
            // Update the appointment status via AJAX
            updateAppointmentStatus(appointmentId, newStatus);
        },
        onAdd: function(evt) {
            // Update the UI to reflect the new status
            const item = evt.item;
            const newStatus = evt.to.dataset.status;
            item.className = item.className.replace(/queue-item-\w+/, `queue-item-${newStatus}`);
            
            // Update the status badge
            updateQueueCounts();
        }
    };

    // Initialize Sortable on all queue columns
    document.querySelectorAll('.sortable-queue').forEach(function(el) {
        new Sortable(el, sortableOptions);
    });

    // Update queue counts on page load
    updateQueueCounts();
    
    // Refresh queue data every 30 seconds
    setInterval(fetchQueueData, 30000);

    // Call next patient
    $('#callNextBtn').click(function() {
        const veterinarianId = $('#veterinarianFilter').val() || 'all';
        
        $.ajax({
            url: `/admin/queue/call-next/${veterinarianId}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Refresh the queue
                    fetchQueueData();
                    // Show success message
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to call next patient');
                console.error(xhr);
            }
        });
    });

    // Filter by veterinarian
    $('#veterinarianFilter').change(function() {
        fetchQueueData();
    });

    // Update appointment status
    function updateAppointmentStatus(appointmentId, status) {
        $.ajax({
            url: `/admin/queue/${appointmentId}/status`,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Update the UI
                    fetchQueueData();
                    toastr.success('Appointment status updated');
                } else {
                    toastr.error(response.message || 'Failed to update status');
                }
            },
            error: function(xhr) {
                toastr.error('Failed to update appointment status');
                console.error(xhr);
            }
        });
    }

    // Fetch queue data
    function fetchQueueData() {
        const veterinarianId = $('#veterinarianFilter').val() || 'all';
        
        $.ajax({
            url: `/admin/queue/data`,
            method: 'GET',
            data: { veterinarian_id: veterinarianId },
            success: function(response) {
                if (response.success) {
                    // Update the queue columns
                    updateQueueUI(response.data);
                    // Update the stats
                    updateStatsUI(response.stats);
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch queue data', xhr);
            }
        });
    }

    // Update the queue UI with new data
    function updateQueueUI(data) {
        // Clear existing queue items
        $('.sortable-queue').empty();
        
        // Add appointments to their respective columns
        data.forEach(function(appointment) {
            const itemHtml = `
                <div class="queue-item queue-item-${appointment.status}" data-id="${appointment.id}">
                    <div class="queue-item-header">
                        <span class="queue-number">#${appointment.queue_number}</span>
                        <span class="badge badge-${getStatusBadgeClass(appointment.status)}">
                            ${formatStatus(appointment.status)}
                        </span>
                    </div>
                    <div class="pet-name">${appointment.pet_name}</div>
                    <div class="pet-type">${appointment.type}</div>
                    ${appointment.check_in_time ? `<div class="wait-time">Waited: ${appointment.wait_time} min</div>` : ''}
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-outline-primary edit-status" 
                                data-id="${appointment.id}" 
                                data-status="${appointment.status}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info view-details" 
                                data-id="${appointment.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            `;
            
            $(`#${appointment.status.replace('_', '-')}-queue`).append(itemHtml);
        });
        
        // Update the queue counts
        updateQueueCounts();
    }
    
    // Update the stats UI
    function updateStatsUI(stats) {
        $('#waitingCount').text(stats.waiting);
        $('#inProgressCount').text(stats.in_progress);
        $('#completedCount').text(stats.completed);
        $('#avgWaitTime').text(stats.average_wait_time ? stats.average_wait_time + ' min' : 'N/A');
    }
    
    // Update the queue count badges
    function updateQueueCounts() {
        $('.queue-column').each(function() {
            const status = $(this).find('.sortable-queue').data('status');
            const count = $(`#${status.replace('_', '-')}-queue .queue-item`).length;
            $(`#${status.replace('_', '')}Badge`).text(count);
        });
    }
    
    // Format status for display
    function formatStatus(status) {
        return status.split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }
    
    // Get badge class based on status
    function getStatusBadgeClass(status) {
        const statusClasses = {
            'scheduled': 'primary',
            'in_progress': 'info',
            'completed': 'success',
            'cancelled': 'danger',
            'no_show': 'warning'
        };
        return statusClasses[status] || 'secondary';
    }
    
    // Handle edit status button click
    $(document).on('click', '.edit-status', function() {
        const appointmentId = $(this).data('id');
        const currentStatus = $(this).data('status');
        
        $('#appointment_id').val(appointmentId);
        $('#status').val(currentStatus);
        $('#statusModal').modal('show');
    });
    
    // Handle status form submission
    $('#statusForm').submit(function(e) {
        e.preventDefault();
        
        const appointmentId = $('#appointment_id').val();
        const status = $('#status').val();
        const notes = $('#notes').val();
        
        $.ajax({
            url: `/admin/queue/${appointmentId}/status`,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status,
                notes: notes
            },
            success: function(response) {
                if (response.success) {
                    $('#statusModal').modal('hide');
                    fetchQueueData();
                    toastr.success('Appointment status updated');
                } else {
                    toastr.error(response.message || 'Failed to update status');
                }
            },
            error: function(xhr) {
                toastr.error('Failed to update appointment status');
                console.error(xhr);
            }
        });
    });
    
    // Reset form when modal is hidden
    $('#statusModal').on('hidden.bs.modal', function() {
        $('#statusForm')[0].reset();
    });
});
</script>
@endpush
