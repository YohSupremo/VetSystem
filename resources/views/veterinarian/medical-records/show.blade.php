@extends('veterinarian.layout')

@section('title', 'Medical Record - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Medical Record</h2>
        <div class="d-flex align-items-center gap-3">
            <div class="text-muted">
                <strong>Patient:</strong> {{ $medicalRecord->pet->name }} ({{ $medicalRecord->pet->species }})
            </div>
            <div class="text-muted">
                <strong>Date:</strong> {{ $medicalRecord->record_date->format('M d, Y') }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('veterinarian.medical-records.edit', [$medicalRecord->pet->id, $medicalRecord->id]) }}" class="btn-action">
                <i class="fas fa-edit me-2"></i>Edit Record
            </a>
            <a href="{{ route('veterinarian.patients.show', $medicalRecord->pet->id) }}" class="btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Patient
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Patient Information -->
        <div class="col-md-4">
            <div class="content-card" style="padding: 1.5rem;">
                <h5 class="mb-3">Patient Information</h5>
                <div class="mb-2">
                    <strong>Name:</strong> {{ $medicalRecord->pet->name }}
                </div>
                <div class="mb-2">
                    <strong>Species:</strong> {{ $medicalRecord->pet->species }}
                </div>
                <div class="mb-2">
                    <strong>Breed:</strong> {{ $medicalRecord->pet->breed }}
                </div>
                <div class="mb-2">
                    <strong>Age:</strong> {{ $medicalRecord->pet->age ?? 'N/A' }}
                </div>
                <div class="mb-2">
                    <strong>Owner:</strong><br>
                    {{ $medicalRecord->pet->owner->first_name }} {{ $medicalRecord->pet->owner->last_name }}
                </div>
            </div>
        </div>

        <!-- Medical Details -->
        <div class="col-md-8">
            <div class="content-card" style="padding: 1.5rem;">
                <h5 class="mb-3">Medical Details</h5>
                
                <div class="mb-3">
                    <h6 class="text-primary">Chief Complaint</h6>
                    <p>{{ $medicalRecord->chief_complaint }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-primary">Symptoms</h6>
                    <p>{{ nl2br($medicalRecord->symptoms) }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-primary">Physical Examination</h6>
                    <p>{{ nl2br($medicalRecord->physical_exam) }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-primary">Diagnosis</h6>
                    <p>{{ $medicalRecord->diagnosis }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-primary">Treatment Plan</h6>
                    <p>{{ nl2br($medicalRecord->treatment) }}</p>
                </div>

                @if($medicalRecord->lab_results)
                    <div class="mb-3">
                        <h6 class="text-primary">Laboratory Results</h6>
                        <p>{{ nl2br($medicalRecord->lab_results) }}</p>
                    </div>
                @endif

                @if($medicalRecord->follow_up_instructions)
                    <div class="mb-3">
                        <h6 class="text-primary">Follow-up Instructions</h6>
                        <p>{{ nl2br($medicalRecord->follow_up_instructions) }}</p>
                    </div>
                @endif

                @if($medicalRecord->notes)
                    <div class="mb-3">
                        <h6 class="text-primary">Additional Notes</h6>
                        <p>{{ nl2br($medicalRecord->notes) }}</p>
                    </div>
                @endif

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <strong>Recorded by:</strong> {{ $medicalRecord->veterinarian->first_name }} {{ $medicalRecord->veterinarian->last_name }}
                            </small>
                        </div>
                        <div>
                            <small class="text-muted">
                                <strong>Created:</strong> {{ $medicalRecord->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.text-primary {
    color: var(--primary-purple) !important;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.border-top {
    border-top: 1px solid #e5e7eb;
}

.text-muted {
    color: #6b7280;
    font-size: 0.9rem;
}

.text-muted strong {
    color: #374151;
}

p {
    margin-bottom: 1rem;
    line-height: 1.6;
}

h6 {
    font-size: 1rem;
    font-weight: 600;
}

.content-card h5 {
    color: var(--primary-purple);
    margin-bottom: 1.5rem;
}

.content-card h6 {
    color: var(--primary-purple);
}
</style>
@endpush
