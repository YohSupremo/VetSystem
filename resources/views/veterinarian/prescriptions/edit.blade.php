@extends('veterinarian.layout')

@section('title', 'Edit Prescription - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Edit Prescription</h2>
        <div class="d-flex align-items-center gap-3">
            <div class="text-muted">
                <strong>Patient:</strong> {{ $prescription->pet->name }} ({{ $prescription->pet->species }})
            </div>
            <div class="text-muted">
                <strong>Current Status:</strong> 
                <span class="badge badge-{{ $prescription->status }}">
                    {{ ucfirst($prescription->status) }}
                </span>
            </div>
        </div>
        <a href="{{ route('veterinarian.prescriptions.show', [$prescription->pet->id, $prescription->id]) }}" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Prescription
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('veterinarian.prescriptions.update', [$prescription->pet->id, $prescription->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="medication_id" class="form-label">Medication *</label>
                    <select name="medication_id" id="medication_id" class="form-control" required>
                        <option value="">Select medication...</option>
                        @foreach($medications as $medication)
                            <option value="{{ $medication->id }}" 
                                    {{ old('medication_id', $prescription->medication_id) == $medication->id ? 'selected' : '' }}>
                                {{ $medication->name }} ({{ $medication->strength }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis *</label>
                    <input type="text" name="diagnosis" id="diagnosis" class="form-control" required
                           placeholder="Primary diagnosis..." value="{{ old('diagnosis', $prescription->diagnosis) }}">
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="dosage" class="form-label">Dosage *</label>
                    <input type="text" name="dosage" id="dosage" class="form-control" required
                           placeholder="e.g., 10mg, 5ml" value="{{ old('dosage', $prescription->dosage) }}">
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="frequency" class="form-label">Frequency *</label>
                    <input type="text" name="frequency" id="frequency" class="form-control" required
                           placeholder="e.g., Twice daily, Every 8 hours" value="{{ old('frequency', $prescription->frequency) }}">
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="duration" class="form-label">Duration *</label>
                    <input type="text" name="duration" id="duration" class="form-control" required
                           placeholder="e.g., 7 days, 2 weeks" value="{{ old('duration', $prescription->duration) }}">
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="instructions" class="form-label">Administration Instructions</label>
                    <textarea name="instructions" id="instructions" class="form-control" rows="4"
                              placeholder="How to administer the medication...">{{ old('instructions', $prescription->instructions) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"
                              placeholder="Any additional notes about the prescription...">{{ old('notes', $prescription->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-action">
                <i class="fas fa-save me-2"></i>Update Prescription
            </button>
            <a href="{{ route('veterinarian.prescriptions.show', [$prescription->pet->id, $prescription->id]) }}" class="btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--primary-purple);
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--light-purple);
    border-radius: 0.5rem;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
    resize: vertical;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-purple);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.alert li {
    margin-bottom: 0.5rem;
}

.text-muted {
    color: #6b7280;
    font-size: 0.9rem;
}

.text-muted strong {
    color: #374151;
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
</style>
@endpush
