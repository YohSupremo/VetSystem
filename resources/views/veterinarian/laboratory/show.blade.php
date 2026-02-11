@extends('veterinarian.layout')

@section('title', 'Laboratory Test Details - PawCare')

@section('content')
<div class="row g-4">
    <!-- Lab Test Details -->
    <div class="col-lg-8">
        <div class="content-card">
            <div class="section-header">
                <h2 class="mb-3">Laboratory Test Details</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('veterinarian.patients.show', $labTest->pet_id) }}" class="btn-action">
                        <i class="fas fa-arrow-left me-2"></i>Back to Patient
                    </a>
                    <a href="{{ route('veterinarian.laboratory.edit', [$labTest->pet_id, $labTest->id]) }}" class="btn-action">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="mb-4">
                <h5 class="mb-3">Patient Information</h5>
                <div class="row g-2">
                    <div class="col-md-6"><strong>Name:</strong></div>
                    <div class="col-md-6">{{ $labTest->pet->name }}</div>
                    
                    <div class="col-md-6"><strong>Species:</strong></div>
                    <div class="col-md-6">{{ $labTest->pet->species }}</div>
                    
                    <div class="col-md-6"><strong>Breed:</strong></div>
                    <div class="col-md-6">{{ $labTest->pet->breed }}</div>
                    
                    <div class="col-md-6"><strong>Owner:</strong></div>
                    <div class="col-md-6">{{ $labTest->pet->owner->first_name }} {{ $labTest->pet->owner->last_name }}</div>
                </div>
            </div>

            <!-- Test Information -->
            <div class="mb-4">
                <h5 class="mb-3">Test Information</h5>
                <div class="row g-2">
                    <div class="col-md-6"><strong>Test Name:</strong></div>
                    <div class="col-md-6">{{ $labTest->test_name }}</div>
                    
                    <div class="col-md-6"><strong>Test Type:</strong></div>
                    <div class="col-md-6">{{ ucfirst($labTest->test_type) }}</div>
                    
                    <div class="col-md-6"><strong>Specimen Type:</strong></div>
                    <div class="col-md-6">{{ ucfirst($labTest->specimen_type) }}</div>
                    
                    <div class="col-md-6"><strong>Test Date:</strong></div>
                    <div class="col-md-6">{{ $labTest->test_date->format('F j, Y') }}</div>
                    
                    <div class="col-md-6"><strong>Status:</strong></div>
                    <div class="col-md-6">
                        <span class="status-badge {{ $labTest->status }}">
                            {{ ucfirst(str_replace('_', ' ', $labTest->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Results -->
            @if($labTest->results)
            <div class="mb-4">
                <h5 class="mb-3">Test Results</h5>
                <div class="results-content">
                    <p>{{ $labTest->results }}</p>
                </div>
            </div>
            @endif

            <!-- Interpretation -->
            @if($labTest->interpretation)
            <div class="mb-4">
                <h5 class="mb-3">Interpretation</h5>
                <div class="interpretation-content">
                    <p>{{ $labTest->interpretation }}</p>
                </div>
            </div>
            @endif

            @if($labTest->notes)
            <div class="mb-4">
                <h5 class="mb-3">Notes</h5>
                <p>{{ $labTest->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="content-card">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('veterinarian.medical-records.create', $labTest->pet_id) }}" class="btn-action">
                    <i class="fas fa-file-medical me-2"></i>Add Medical Record
                </a>
                <a href="{{ route('veterinarian.prescriptions.create', $labTest->pet_id) }}" class="btn-action">
                    <i class="fas fa-prescription-bottle-alt me-2"></i>Write Prescription
                </a>
                <a href="{{ route('veterinarian.vaccinations.create', $labTest->pet_id) }}" class="btn-action">
                    <i class="fas fa-syringe me-2"></i>Record Vaccination
                </a>
            </div>
        </div>

        <div class="content-card">
            <h5 class="mb-3">Test History</h5>
            @if($labTest->pet->laboratoryTests->count() > 1)
                <div class="test-history">
                    @foreach($labTest->pet->laboratoryTests->take(5) as $histTest)
                        @if($histTest->id !== $labTest->id)
                            <div class="history-item">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $histTest->test_name }}</strong>
                                    <small>{{ $histTest->test_date->format('M j, Y') }}</small>
                                </div>
                                <span class="status-badge {{ $histTest->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $histTest->status)) }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-muted">No previous laboratory tests recorded.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.results-content,
.interpretation-content {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.test-history {
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
