@extends('veterinarian.layout')

@section('title', 'Prescription - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Prescription Details</h2>
        <div class="d-flex align-items-center gap-3">
            <div class="text-muted">
                <strong>Patient:</strong> {{ $prescription->pet->name }} ({{ $prescription->pet->species }})
            </div>
            <div class="text-muted">
                <strong>Date:</strong> {{ $prescription->prescription_date->format('M d, Y') }}
            </div>
            <div class="text-muted">
                <strong>Status:</strong> 
                <span class="badge badge-{{ $prescription->status }}">
                    {{ ucfirst($prescription->status) }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('veterinarian.prescriptions.edit', [$prescription->pet->id, $prescription->id]) }}" class="btn-action">
                <i class="fas fa-edit me-2"></i>Edit Prescription
            </a>
            <a href="{{ route('veterinarian.patients.show', $prescription->pet->id) }}" class="btn-secondary">
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
                    <strong>Name:</strong> {{ $prescription->pet->name }}
                </div>
                <div class="mb-2">
                    <strong>Species:</strong> {{ $prescription->pet->species }}
                </div>
                <div class="mb-2">
                    <strong>Breed:</strong> {{ $prescription->pet->breed }}
                </div>
                <div class="mb-2">
                    <strong>Age:</strong> {{ $prescription->pet->age ?? 'N/A' }}
                </div>
                <div class="mb-2">
                    <strong>Owner:</strong><br>
                    {{ $prescription->pet->owner->first_name }} {{ $prescription->pet->owner->last_name }}
                </div>
            </div>
        </div>

        <!-- Prescription Details -->
        <div class="col-md-8">
            <div class="content-card" style="padding: 1.5rem;">
                <h5 class="mb-3">Prescription Details</h5>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-primary">Medication</h6>
                        <p><strong>{{ $prescription->medication->name }}</strong> ({{ $prescription->medication->strength }})</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Diagnosis</h6>
                        <p>{{ $prescription->diagnosis }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <h6 class="text-primary">Dosage</h6>
                        <p>{{ $prescription->dosage }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-primary">Frequency</h6>
                        <p>{{ $prescription->frequency }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-primary">Duration</h6>
                        <p>{{ $prescription->duration }}</p>
                    </div>
                </div>

                @if($prescription->instructions)
                    <div class="mb-3">
                        <h6 class="text-primary">Administration Instructions</h6>
                        <p>{{ nl2br($prescription->instructions) }}</p>
                    </div>
                @endif

                @if($prescription->notes)
                    <div class="mb-3">
                        <h6 class="text-primary">Additional Notes</h6>
                        <p>{{ nl2br($prescription->notes) }}</p>
                    </div>
                @endif

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <strong>Prescribed by:</strong> {{ $prescription->veterinarian->first_name }} {{ $prescription->veterinarian->last_name }}
                            </small>
                        </div>
                        <div>
                            <small class="text-muted">
                                <strong>Created:</strong> {{ $prescription->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Status Update Form -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-primary mb-3">Update Status</h6>
                    <form action="{{ route('veterinarian.prescriptions.update-status', [$prescription->pet->id, $prescription->id]) }}" method="POST" class="d-flex gap-2 align-items-center">
                        @csrf
                        <select name="status" class="form-control" style="width: auto;">
                            <option value="active" {{ $prescription->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $prescription->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="discontinued" {{ $prescription->status == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                        <button type="submit" class="btn-action">
                            <i class="fas fa-sync me-2"></i>Update Status
                        </button>
                    </form>
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

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-active {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.badge-completed {
    background: linear-gradient(135deg, #dbeafe, #93c5fd);
    color: #1e40af;
}

.badge-discontinued {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.form-control {
    padding: 0.5rem;
    border: 2px solid var(--light-purple);
    border-radius: 0.25rem;
    font-size: 0.9rem;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-purple);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
}
</style>
@endpush
