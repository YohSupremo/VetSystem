@extends('layout.base')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Pet Medical Records</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                <i class="fas fa-plus me-2"></i>New Record
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="medicalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="records-tab" data-bs-toggle="tab" data-bs-target="#records" type="button" role="tab" aria-controls="records" aria-selected="true">
                <i class="fas fa-notes-medical me-2"></i>Medical Records
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="vaccinations-tab" data-bs-toggle="tab" data-bs-target="#vaccinations" type="button" role="tab" aria-controls="vaccinations">
                <i class="fas fa-syringe me-2"></i>Vaccinations
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab" aria-controls="prescriptions">
                <i class="fas fa-prescription me-2"></i>Prescriptions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="surgeries-tab" data-bs-toggle="tab" data-bs-target="#surgeries" type="button" role="tab" aria-controls="surgeries">
                <i class="fas fa-scalpel me-2"></i>Surgeries
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="medicalTabsContent">
        <!-- Medical Records Tab -->
        <div class="tab-pane fade show active" id="records" role="tabpanel" aria-labelledby="records-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Pet Name</th>
                                    <th>Owner</th>
                                    <th>Diagnosis</th>
                                    <th>Veterinarian</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($medicalRecords as $record)
                                <tr>
                                    <td>{{ $record->visit_date->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $record->pet->photo_path ?? asset('images/default-pet.jpg') }}" 
                                                 class="rounded-circle me-2" width="32" height="32" 
                                                 alt="{{ $record->pet->name }}">
                                            {{ $record->pet->name }}
                                        </div>
                                    </td>
                                    <td>{{ $record->pet->owner->user->first_name }} {{ $record->pet->owner->user->last_name }}</td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            {{ Str::limit($record->diagnosis, 30) }}
                                        </span>
                                    </td>
                                    <td>Dr. {{ $record->veterinarian->first_name }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li><a class="dropdown-item" href="{{ route('admin.medical-records.show', $record->id) }}"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p class="mb-0">No medical records found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $medicalRecords->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Vaccinations Tab -->
        <div class="tab-pane fade" id="vaccinations" role="tabpanel" aria-labelledby="vaccinations-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Vaccine</th>
                                    <th>Pet</th>
                                    <th>Date Administered</th>
                                    <th>Next Due</th>
                                    <th>Administered By</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vaccinations as $vaccination)
                                <tr>
                                    <td>{{ $vaccination->vaccine->vaccine_name }}</td>
                                    <td>{{ $vaccination->pet->name }}</td>
                                    <td>{{ $vaccination->administered_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($vaccination->next_due_date->isPast())
                                            <span class="badge bg-danger">
                                                {{ $vaccination->next_due_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            {{ $vaccination->next_due_date->format('M d, Y') }}
                                        @endif
                                    </td>
                                    <td>{{ $vaccination->administeredBy->first_name }} {{ $vaccination->administeredBy->last_name }}</td>
                                    <td>
                                        @if($vaccination->next_due_date->isPast())
                                            <span class="badge bg-danger">Due</span>
                                        @else
                                            <span class="badge bg-success">Current</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-syringe fa-3x mb-3"></i>
                                            <p class="mb-0">No vaccination records found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescriptions Tab -->
        <div class="tab-pane fade" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Medication</th>
                                    <th>Pet</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prescriptions as $prescription)
                                <tr>
                                    <td>
                                        <strong>{{ $prescription->medication_name }}</strong>
                                        <div class="text-muted small">{{ $prescription->instructions }}</div>
                                    </td>
                                    <td>{{ $prescription->medicalRecord->pet->name }}</td>
                                    <td>{{ $prescription->dosage }}</td>
                                    <td>{{ $prescription->frequency }}</td>
                                    <td>{{ $prescription->duration_days }} days</td>
                                    <td>
                                        @if($prescription->dispensed)
                                            <span class="badge bg-success">Dispensed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-prescription fa-3x mb-3"></i>
                                            <p class="mb-0">No prescription records found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surgeries Tab -->
        <div class="tab-pane fade" id="surgeries" role="tabpanel" aria-labelledby="surgeries-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Procedure</th>
                                    <th>Pet</th>
                                    <th>Date</th>
                                    <th>Surgeon</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($surgeries as $surgery)
                                <tr>
                                    <td>
                                        <strong>{{ $surgery->procedure_name }}</strong>
                                        <div class="text-muted small">{{ $surgery->anesthesia_type }}</div>
                                    </td>
                                    <td>{{ $surgery->pet->name }}</td>
                                    <td>{{ $surgery->scheduled_date->format('M d, Y h:i A') }}</td>
                                    <td>Dr. {{ $surgery->surgeon->first_name }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'scheduled' => 'bg-info',
                                                'in_progress' => 'bg-primary',
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-danger'
                                            ][$surgery->status];
                                        @endphp
                                        <span class="badge {{ $statusClasses }}">
                                            {{ str_replace('_', ' ', ucfirst($surgery->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#surgeryDetailsModal{{ $surgery->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-scalpel fa-3x mb-3"></i>
                                            <p class="mb-0">No surgery records found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Record Modal -->
<div class="modal fade" id="addRecordModal" tabindex="-1" aria-labelledby="addRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addRecordModalLabel">New Medical Record</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.medical-records.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pet_id" class="form-label">Select Pet</label>
                            <select class="form-select" id="pet_id" name="pet_id" required>
                                <option value="" disabled selected>Select a pet</option>
                                @foreach($pets as $pet)
                                    <option value="{{ $pet->id }}">
                                        {{ $pet->name }} ({{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="visit_date" class="form-label">Visit Date</label>
                            <input type="datetime-local" class="form-control" id="visit_date" name="visit_date" required>
                        </div>
                        <div class="col-12">
                            <label for="complaint" class="form-label">Chief Complaint</label>
                            <textarea class="form-control" id="complaint" name="complaint" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="diagnosis" class="form-label">Diagnosis</label>
                            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="treatment_plan" class="form-label">Treatment Plan</label>
                            <textarea class="form-control" id="treatment_plan" name="treatment_plan" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Surgery Details Modal -->
@foreach($surgeries as $surgery)
<div class="modal fade" id="surgeryDetailsModal{{ $surgery->id }}" tabindex="-1" aria-labelledby="surgeryDetailsModalLabel{{ $surgery->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="surgeryDetailsModalLabel{{ $surgery->id }}">Surgery Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Procedure</h6>
                        <h4>{{ $surgery->procedure_name }}</h4>
                        <p class="mb-1"><strong>Anesthesia:</strong> {{ $surgery->anesthesia_type }}</p>
                        <p class="mb-1"><strong>Status:</strong> 
                            @php
                                $statusClasses = [
                                    'scheduled' => 'bg-info',
                                    'in_progress' => 'bg-primary',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger'
                                ][$surgery->status];
                            @endphp
                            <span class="badge {{ $statusClasses }}">
                                {{ str_replace('_', ' ', ucfirst($surgery->status)) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Patient Information</h6>
                                <p class="mb-1"><strong>Pet:</strong> {{ $surgery->pet->name }}</p>
                                <p class="mb-1"><strong>Owner:</strong> {{ $surgery->pet->owner->user->first_name }} {{ $surgery->pet->owner->user->last_name }}</p>
                                <p class="mb-0"><strong>Breed:</strong> {{ $surgery->pet->breed }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Surgery Notes</h6>
                        <div class="card">
                            <div class="card-body">
                                {!! nl2br(e($surgery->surgery_notes ?? 'No notes available.')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Post-Op Instructions</h6>
                        <div class="card">
                            <div class="card-body">
                                {!! nl2br(e($surgery->post_op_instructions ?? 'No post-op instructions available.')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.surgeries.edit', $surgery->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Surgery
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Set default date for visit date to now
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('visit_date').value = now.toISOString().slice(0, 16);
    });
</script>
@endpush

@endsection
