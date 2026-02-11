@extends('veterinarian.layout')

@section('title', 'Appointment Details - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Appointment Details</h2>
        <a href="#" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Appointments
        </a>
    </div>

    <!-- Patient Information -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="content-card" style="padding: 1.5rem;">
                <h5 class="mb-3">Patient Information</h5>
                <div class="row g-2">
                    <div class="col-6"><strong>Name:</strong></div>
                    <div class="col-6">{{ $appointment->pet->name }}</div>
                    
                    <div class="col-6"><strong>Species:</strong></div>
                    <div class="col-6">{{ $appointment->pet->species }}</div>
                    
                    <div class="col-6"><strong>Breed:</strong></div>
                    <div class="col-6">{{ $appointment->pet->breed }}</div>
                    
                    <div class="col-6"><strong>Age:</strong></div>
                    <div class="col-6">{{ $appointment->pet->age }} years</div>
                    
                    <div class="col-6"><strong>Weight:</strong></div>
                    <div class="col-6">{{ $appointment->pet->weight }} kg</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="content-card" style="padding: 1.5rem;">
                <h5 class="mb-3">Owner Information</h5>
                <div class="row g-2">
                    <div class="col-6"><strong>Name:</strong></div>
                    <div class="col-6">{{ $appointment->pet->owner->first_name }} {{ $appointment->pet->owner->last_name }}</div>
                    
                    <div class="col-6"><strong>Contact:</strong></div>
                    <div class="col-6">{{ $appointment->pet->owner->contact_number }}</div>
                    
                    <div class="col-6"><strong>Email:</strong></div>
                    <div class="col-6">{{ $appointment->pet->owner->email }}</div>
                    
                    <div class="col-12"><strong>Address:</strong></div>
                    <div class="col-12">{{ $appointment->pet->owner->address }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="content-card" style="padding: 1.5rem;">
        <h5 class="mb-3">Appointment Details</h5>
        <div class="row g-2">
            <div class="col-md-6"><strong>Date:</strong></div>
            <div class="col-md-6">{{ $appointment->appointment_date->format('F j, Y') }}</div>
            
            <div class="col-md-6"><strong>Time:</strong></div>
            <div class="col-md-6">{{ $appointment->start_time->format('g:i A') }} - {{ $appointment->end_time ? $appointment->end_time->format('g:i A') : 'TBD' }}</div>
            
            <div class="col-md-6"><strong>Type:</strong></div>
            <div class="col-md-6">{{ ucfirst($appointment->type) }}</div>
            
            <div class="col-md-6"><strong>Status:</strong></div>
            <div class="col-md-6">
                <span class="status-badge {{ $appointment->status }}">
                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                </span>
            </div>
            
            <div class="col-md-6"><strong>Queue Number:</strong></div>
            <div class="col-md-6">{{ $appointment->queue_number ?: 'N/A' }}</div>
            
            @if($appointment->check_in_time)
                <div class="col-md-6"><strong>Check-in Time:</strong></div>
                <div class="col-md-6">{{ $appointment->check_in_time->format('g:i A') }}</div>
            @endif
        </div>
        
        @if($appointment->reason)
            <div class="mt-3">
                <strong>Reason for Visit:</strong>
                <p class="mt-1">{{ $appointment->reason }}</p>
            </div>
        @endif

        @if($appointment->notes)
            <div class="mt-3">
                <strong>Notes:</strong>
                <p class="mt-1">{{ $appointment->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Medical Records -->
    <div class="content-card" style="padding: 1.5rem;">
        <h5 class="mb-3">Medical Records</h5>
        @if($appointment->pet->medicalRecords->count() > 0)
            <div class="row g-3">
                @foreach($appointment->pet->medicalRecords->take(3) as $record)
                    <div class="col-md-12">
                        <div class="appointment-item">
                            <div class="item-details flex-grow-1">
                                <h6>{{ $record->diagnosis }}</h6>
                                <p class="mb-1">{{ $record->chief_complaint }}</p>
                                <small class="text-muted">{{ $record->record_date->format('M j, Y g:i A') }}</small>
                            </div>
                            <a href="#" class="btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">No medical records found for this patient.</p>
        @endif
    </div>

    <!-- Actions -->
    <div class="content-card" style="padding: 1.5rem;">
        <h5 class="mb-3">Actions</h5>
        <div class="d-flex flex-wrap gap-2">
            @if($appointment->status === 'scheduled')
                <div class="d-flex gap-2 mb-3">
                    <a href="#" class="btn-action" onclick="updateAppointmentStatus({{ $appointment->id }}, 'in_progress')">
                        <i class="fas fa-play me-2"></i>Start Consultation
                    </a>
                    <a href="#" class="btn-action" onclick="updateAppointmentStatus({{ $appointment->id }}, 'completed')">
                        <i class="fas fa-check me-2"></i>Mark Complete
                    </a>
                    <a href="#" class="btn-secondary" onclick="cancelAppointment({{ $appointment->id }})">
                        <i class="fas fa-times me-2"></i>Cancel Appointment
                    </a>
                </div>

                <!-- Quick Actions -->
                <div class="mt-3">
                    <h5 class="mb-2">Quick Actions</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('veterinarian.medical-records.create', $appointment->pet->id) }}" class="btn-action">
                            <i class="fas fa-file-medical me-2"></i>Add Medical Record
                        </a>
                        <a href="{{ route('veterinarian.prescriptions.create', $appointment->pet->id) }}" class="btn-action">
                            <i class="fas fa-prescription-bottle-alt me-2"></i>Write Prescription
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden forms for status updates -->
<form id="statusForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="status" id="status">
    <input type="hidden" name="notes" id="notes">
</form>

<form id="cancelForm" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
function updateAppointmentStatus(appointmentId, status) {
    if (confirm('Are you sure you want to update this appointment status?')) {
        document.getElementById('status').value = status;
        document.getElementById('statusForm').action = '{{ route("veterinarian.appointments.update-status", ":id") }}'.replace(':id', appointmentId);
        document.getElementById('statusForm').submit();
    }
}

function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        document.getElementById('cancelForm').action = '{{ route("veterinarian.appointments.cancel", ":id") }}'.replace(':id', appointmentId);
        document.getElementById('cancelForm').submit();
    }
}
</script>
@endpush
@endsection
