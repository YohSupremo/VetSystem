@extends('veterinarian.layout')

@section('title', 'Patient Details - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Patient Profile</h2>
        <a href="#" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Patients
        </a>
    </div>

    <!-- Patient Information -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="content-card" style="padding: 1.5rem;">
                <h5 class="mb-3">Basic Information</h5>
                <div class="row g-2">
                    <div class="col-6"><strong>Name:</strong></div>
                    <div class="col-6">{{ $pet->name }}</div>
                    
                    <div class="col-6"><strong>Species:</strong></div>
                    <div class="col-6">{{ $pet->species }}</div>
                    
                    <div class="col-6"><strong>Breed:</strong></div>
                    <div class="col-6">{{ $pet->breed }}</div>
                    
                    <div class="col-6"><strong>Gender:</strong></div>
                    <div class="col-6">{{ ucfirst($pet->gender) }}</div>
                    
                    <div class="col-6"><strong>Age:</strong></div>
                    <div class="col-6">{{ $pet->age }} years</div>
                    
                    <div class="col-6"><strong>Weight:</strong></div>
                    <div class="col-6">{{ $pet->weight }} kg</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="content-card" style="padding: 1.5rem;">
                <h5 class="mb-3">Owner Information</h5>
                <div class="row g-2">
                    <div class="col-6"><strong>Name:</strong></div>
                    <div class="col-6">{{ $pet->owner->first_name }} {{ $pet->owner->last_name }}</div>
                    
                    <div class="col-6"><strong>Contact:</strong></div>
                    <div class="col-6">{{ $pet->owner->contact_number }}</div>
                    
                    <div class="col-12"><strong>Email:</strong></div>
                    <div class="col-12 small">{{ $pet->owner->email }}</div>
                    
                    <div class="col-12"><strong>Address:</strong></div>
                    <div class="col-12 small">{{ $pet->owner->address }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="content-card" style="padding: 1.5rem;">
                <h5 class="mb-3">Visit Summary</h5>
                <div class="row g-2">
                    <div class="col-6"><strong>Total Visits:</strong></div>
                    <div class="col-6">{{ $pet->appointments->count() }}</div>
                    
                    <div class="col-6"><strong>Medical Records:</strong></div>
                    <div class="col-6">{{ $pet->medicalRecords->count() }}</div>
                    
                    <div class="col-6"><strong>Prescriptions:</strong></div>
                    <div class="col-6">{{ $pet->prescriptions->count() }}</div>
                    
                    <div class="col-6"><strong>Vaccinations:</strong></div>
                    <div class="col-6">{{ $pet->vaccinations->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-card" style="padding: 1.5rem;">
        <h5 class="mb-3">Quick Actions</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="#" class="btn btn-primary">
                <i class="fas fa-file-medical me-2"></i>Add Medical Record
            </a>
            <a href="#" class="btn btn-warning">
                <i class="fas fa-prescription-bottle-alt me-2"></i>Write Prescription
            </a>
            <a href="#" class="btn btn-success">
                <i class="fas fa-syringe me-2"></i>Add Vaccination
            </a>
            <a href="#" class="btn btn-info">
                <i class="fas fa-microscope me-2"></i>Order Lab Test
            </a>
        </div>
    </div>

    <!-- Tabbed Content -->
    <div class="content-card">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="patientTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="medical-records-tab" data-bs-toggle="tab" data-bs-target="#medical-records" type="button" role="tab">
                    Medical Records
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab">
                    Prescriptions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab">
                    Appointments
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vaccinations-tab" data-bs-toggle="tab" data-bs-target="#vaccinations" type="button" role="tab">
                    Vaccinations
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-3" id="patientTabsContent">
            <!-- Medical Records Tab -->
            <div class="tab-pane fade show active" id="medical-records" role="tabpanel">
                @if($pet->medicalRecords->count() > 0)
                    <div class="row g-3">
                        @foreach($pet->medicalRecords as $record)
                            <div class="col-12">
                                <div class="appointment-item">
                                    <div class="item-details flex-grow-1">
                                        <h6>{{ $record->diagnosis }}</h6>
                                        <p class="mb-1"><strong>Chief Complaint:</strong> {{ $record->chief_complaint }}</p>
                                        <p class="mb-1"><strong>Treatment:</strong> {{ $record->treatment }}</p>
                                        @if($record->follow_up_instructions)
                                            <p class="mb-1"><strong>Follow-up:</strong> {{ $record->follow_up_instructions }}</p>
                                        @endif
                                        <small class="text-muted">{{ $record->record_date->format('M j, Y g:i A') }}</small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <h3>No medical records found</h3>
                        <p>No medical records have been created for this patient.</p>
                    </div>
                @endif
            </div>

            <!-- Prescriptions Tab -->
            <div class="tab-pane fade" id="prescriptions" role="tabpanel">
                @if($pet->prescriptions->count() > 0)
                    <div class="row g-3">
                        @foreach($pet->prescriptions as $prescription)
                            <div class="col-12">
                                <div class="appointment-item">
                                    <div class="item-details flex-grow-1">
                                        <h6>{{ $prescription->medication->name }}</h6>
                                        <p class="mb-1"><strong>Dosage:</strong> {{ $prescription->dosage }}</p>
                                        <p class="mb-1"><strong>Frequency:</strong> {{ $prescription->frequency }}</p>
                                        <p class="mb-1"><strong>Duration:</strong> {{ $prescription->duration }}</p>
                                        <p class="mb-1"><strong>Instructions:</strong> {{ $prescription->instructions }}</p>
                                        <span class="status-badge {{ $prescription->status }}">
                                            {{ ucfirst($prescription->status) }}
                                        </span>
                                    </div>
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">💊</div>
                        <h3>No prescriptions found</h3>
                        <p>No prescriptions have been written for this patient.</p>
                    </div>
                @endif
            </div>

            <!-- Appointments Tab -->
            <div class="tab-pane fade" id="appointments" role="tabpanel">
                @if($pet->appointments->count() > 0)
                    <div class="row g-3">
                        @foreach($pet->appointments as $appointment)
                            <div class="col-12">
                                <div class="appointment-item">
                                    <div class="item-details flex-grow-1">
                                        <h6>{{ ucfirst($appointment->type) }}</h6>
                                        <p class="mb-1"><strong>Date:</strong> {{ $appointment->appointment_date->format('M j, Y') }}</p>
                                        <p class="mb-1"><strong>Time:</strong> {{ $appointment->start_time->format('g:i A') }} - {{ $appointment->end_time->format('g:i A') }}</p>
                                        @if($appointment->reason)
                                            <p class="mb-1"><strong>Reason:</strong> {{ $appointment->reason }}</p>
                                        @endif
                                        <span class="status-badge {{ $appointment->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </span>
                                    </div>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📅</div>
                        <h3>No appointments found</h3>
                        <p>No appointments have been scheduled for this patient.</p>
                    </div>
                @endif
            </div>

            <!-- Vaccinations Tab -->
            <div class="tab-pane fade" id="vaccinations" role="tabpanel">
                @if($pet->vaccinations->count() > 0)
                    <div class="row g-3">
                        @foreach($pet->vaccinations as $vaccination)
                            <div class="col-12">
                                <div class="appointment-item">
                                    <div class="item-details flex-grow-1">
                                        <h6>{{ $vaccination->vaccine_name }}</h6>
                                        <p class="mb-1"><strong>Batch Number:</strong> {{ $vaccination->batch_number }}</p>
                                        <p class="mb-1"><strong>Administered:</strong> {{ $vaccination->administration_date->format('M j, Y') }}</p>
                                        <p class="mb-1"><strong>Next Due:</strong> {{ $vaccination->next_due_date ? $vaccination->next_due_date->format('M j, Y') : 'N/A' }}</p>
                                        @if($vaccination->notes)
                                            <p class="mb-1"><strong>Notes:</strong> {{ $vaccination->notes }}</p>
                                        @endif
                                    </div>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">💉</div>
                        <h3>No vaccinations found</h3>
                        <p>No vaccinations have been recorded for this patient.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
