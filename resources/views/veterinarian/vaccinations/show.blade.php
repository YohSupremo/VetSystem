@extends('veterinarian.layout')

@section('title', 'Vaccination Details - PawCare')

@section('content')
<div class="row g-4">
    <!-- Vaccination Details -->
    <div class="col-lg-8">
        <div class="content-card">
            <div class="section-header">
                <h2 class="mb-3">Vaccination Details</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('veterinarian.patients.show', $vaccination->pet_id) }}" class="btn-action">
                        <i class="fas fa-arrow-left me-2"></i>Back to Patient
                    </a>
                    <a href="{{ route('veterinarian.vaccinations.edit', [$vaccination->pet_id, $vaccination->id]) }}" class="btn-action">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="mb-4">
                <h5 class="mb-3">Patient Information</h5>
                <div class="row g-2">
                    <div class="col-md-6"><strong>Name:</strong></div>
                    <div class="col-md-6">{{ $vaccination->pet->name }}</div>
                    
                    <div class="col-md-6"><strong>Species:</strong></div>
                    <div class="col-md-6">{{ $vaccination->pet->species }}</div>
                    
                    <div class="col-md-6"><strong>Breed:</strong></div>
                    <div class="col-md-6">{{ $vaccination->pet->breed }}</div>
                    
                    <div class="col-md-6"><strong>Owner:</strong></div>
                    <div class="col-md-6">{{ $vaccination->pet->owner->first_name }} {{ $vaccination->pet->owner->last_name }}</div>
                </div>
            </div>

            <!-- Vaccination Information -->
            <div class="mb-4">
                <h5 class="mb-3">Vaccination Information</h5>
                <div class="row g-2">
                    <div class="col-md-6"><strong>Vaccine Name:</strong></div>
                    <div class="col-md-6">{{ $vaccination->vaccine_name }}</div>
                    
                    <div class="col-md-6"><strong>Vaccine Type:</strong></div>
                    <div class="col-md-6">{{ ucfirst($vaccination->vaccine_type) }}</div>
                    
                    <div class="col-md-6"><strong>Manufacturer:</strong></div>
                    <div class="col-md-6">{{ $vaccination->manufacturer ?: 'N/A' }}</div>
                    
                    <div class="col-md-6"><strong>Batch Number:</strong></div>
                    <div class="col-md-6">{{ $vaccination->batch_number ?: 'N/A' }}</div>
                    
                    <div class="col-md-6"><strong>Administration Date:</strong></div>
                    <div class="col-md-6">{{ $vaccination->vaccination_date->format('F j, Y') }}</div>
                    
                    <div class="col-md-6"><strong>Next Due Date:</strong></div>
                    <div class="col-md-6">{{ $vaccination->next_due_date ? $vaccination->next_due_date->format('F j, Y') : 'N/A' }}</div>
                    
                    <div class="col-md-6"><strong>Status:</strong></div>
                    <div class="col-md-6">
                        <span class="status-badge {{ $vaccination->status }}">
                            {{ ucfirst(str_replace('_', ' ', $vaccination->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            @if($vaccination->notes)
            <div class="mb-4">
                <h5 class="mb-3">Notes</h5>
                <p>{{ $vaccination->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="content-card">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('veterinarian.medical-records.create', $vaccination->pet_id) }}" class="btn-action">
                    <i class="fas fa-file-medical me-2"></i>Add Medical Record
                </a>
                <a href="{{ route('veterinarian.prescriptions.create', $vaccination->pet_id) }}" class="btn-action">
                    <i class="fas fa-prescription-bottle-alt me-2"></i>Write Prescription
                </a>
                <a href="{{ route('veterinarian.laboratory.create', $vaccination->pet_id) }}" class="btn-action">
                    <i class="fas fa-microscope me-2"></i>Order Lab Test
                </a>
            </div>
        </div>

        <div class="content-card">
            <h5 class="mb-3">Vaccination History</h5>
            @if($vaccination->pet->vaccinations->count() > 1)
                <div class="vaccination-history">
                    @foreach($vaccination->pet->vaccinations->take(5) as $histVaccination)
                        @if($histVaccination->id !== $vaccination->id)
                            <div class="history-item">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $histVaccination->vaccine_name }}</strong>
                                    <small>{{ $histVaccination->vaccination_date->format('M j, Y') }}</small>
                                </div>
                                <span class="status-badge {{ $histVaccination->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $histVaccination->status)) }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-muted">No previous vaccinations recorded.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.vaccination-history {
    max-height: 300px;
    overflow-y: auto;
}

.history-item {
    padding: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 0.5rem;
}

.history-item:last-child {
    border-bottom: none;
}
</style>
@endpush
