@extends('veterinarian.layout')

@section('title', 'Write Prescription - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Write Prescription</h2>
        <a href="#" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Patient
        </a>
    </div>

    <!-- Patient Info -->
    <div class="appointment-item mb-4">
        <div class="pet-avatar me-3">🐾</div>
        <div class="item-details">
            <h5>{{ $pet->name }}</h5>
            <p class="mb-1">{{ $pet->species }} • {{ $pet->breed }} • {{ $pet->age }} years old</p>
            <p class="text-muted">Owner: {{ $pet->owner->first_name }} {{ $pet->owner->last_name }}</p>
        </div>
    </div>

    <form action="#" method="POST">
        @csrf
        
        <div class="row g-3">
            <!-- Medication Selection -->
            <div class="col-md-6">
                <label for="medication_id" class="form-label">
                    Medication <span class="text-danger">*</span>
                </label>
                <select id="medication_id" name="medication_id" class="form-select" required>
                    <option value="">Select a medication...</option>
                    @foreach($medications as $medication)
                        <option value="{{ $medication->id }}" {{ old('medication_id') == $medication->id ? 'selected' : '' }}>
                            {{ $medication->name }} ({{ $medication->strength }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Diagnosis -->
            <div class="col-md-6">
                <label for="diagnosis" class="form-label">Diagnosis</label>
                <input type="text" id="diagnosis" name="diagnosis" class="form-control"
                       placeholder="Enter diagnosis..." value="{{ old('diagnosis') }}">
            </div>

            <!-- Dosage -->
            <div class="col-md-6">
                <label for="dosage" class="form-label">
                    Dosage <span class="text-danger">*</span>
                </label>
                <input type="text" id="dosage" name="dosage" class="form-control" required
                       placeholder="e.g., 10mg, 5ml" value="{{ old('dosage') }}">
            </div>

            <!-- Frequency -->
            <div class="col-md-6">
                <label for="frequency" class="form-label">
                    Frequency <span class="text-danger">*</span>
                </label>
                <input type="text" id="frequency" name="frequency" class="form-control" required
                       placeholder="e.g., Twice daily, Every 8 hours" value="{{ old('frequency') }}">
            </div>

            <!-- Duration -->
            <div class="col-md-6">
                <label for="duration" class="form-label">
                    Duration <span class="text-danger">*</span>
                </label>
                <input type="text" id="duration" name="duration" class="form-control" required
                       placeholder="e.g., 7 days, 2 weeks" value="{{ old('duration') }}">
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="discontinued" {{ old('status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
            </div>

            <!-- Instructions -->
            <div class="col-12">
                <label for="instructions" class="form-label">
                    Instructions <span class="text-danger">*</span>
                </label>
                <textarea id="instructions" name="instructions" rows="3" class="form-control" required
                          placeholder="Provide detailed instructions for medication administration...">{{ old('instructions') }}</textarea>
            </div>

            <!-- Notes -->
            <div class="col-12">
                <label for="notes" class="form-label">Additional Notes</label>
                <textarea id="notes" name="notes" rows="3" class="form-control"
                          placeholder="Any additional notes about prescription...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="#" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-prescription-bottle-alt me-2"></i>Write Prescription
            </button>
        </div>
    </form>
</div>
@endsection
