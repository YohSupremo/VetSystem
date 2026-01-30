@extends('admin.dashboard')

@section('page-title', 'Create Prescription')
@section('page-description', 'Create a new prescription for a patient')

@push('styles')
<style>
    .medication-suggestions {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: white;
    }

    .suggestion-item {
        padding: 0.5rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
    }

    .suggestion-item:hover {
        background: #f8f9fa;
    }

    .suggestion-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i class="fas fa-prescription-bottle"></i> Create Prescription</h1>
                    <p class="text-muted">Create a new prescription for a patient</p>
                </div>
                <a href="{{ route('admin.pharmacy.prescriptions') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Prescriptions
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Prescription Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.pharmacy.prescriptions.store') }}">
                        @csrf

                        <!-- Medical Record Selection -->
                        <div class="mb-3">
                            <label for="medical_record_id" class="form-label">Medical Record <span class="text-danger">*</span></label>
                            <select name="medical_record_id" id="medical_record_id" class="form-select @error('medical_record_id') is-invalid @enderror" required>
                                <option value="">Select a medical record...</option>
                                @foreach($medicalRecords as $record)
                                    <option value="{{ $record->id }}"
                                            {{ (isset($selectedMedicalRecord) && $selectedMedicalRecord->id == $record->id) ? 'selected' : '' }}>
                                        {{ $record->visit_date->format('M j, Y') }} - {{ $record->pet->name }}
                                        ({{ $record->pet->owner->user->first_name }} {{ $record->pet->owner->user->last_name }})
                                        @if($record->complaint)
                                            - {{ Str::limit($record->complaint, 50) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('medical_record_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Medication with Suggestions -->
                        <div class="mb-3">
                            <label for="medication" class="form-label">Medication <span class="text-danger">*</span></label>
                            <input type="text" name="medication" id="medication" class="form-control @error('medication') is-invalid @enderror"
                                   value="{{ old('medication') }}" required autocomplete="off">
                            @error('medication')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Medication Suggestions -->
                            <div id="medication-suggestions" class="medication-suggestions mt-1" style="display: none;">
                                <div class="p-2 text-muted small">Available medications in inventory:</div>
                                @foreach($suggestedMedications as $med)
                                    <div class="suggestion-item" data-medication="{{ $med->name }}">
                                        <strong>{{ $med->name }}</strong>
                                        @if($med->strength)
                                            <span class="text-muted"> - {{ $med->strength }}</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">Stock: {{ $med->total_stock }} units</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Dosage -->
                                <div class="mb-3">
                                    <label for="dosage" class="form-label">Dosage <span class="text-danger">*</span></label>
                                    <input type="text" name="dosage" id="dosage" class="form-control @error('dosage') is-invalid @enderror"
                                           value="{{ old('dosage') }}" placeholder="e.g., 5mg, 10ml" required>
                                    @error('dosage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Frequency -->
                                <div class="mb-3">
                                    <label for="frequency" class="form-label">Frequency <span class="text-danger">*</span></label>
                                    <input type="text" name="frequency" id="frequency" class="form-control @error('frequency') is-invalid @enderror"
                                           value="{{ old('frequency') }}" placeholder="e.g., Twice daily, Every 8 hours" required>
                                    @error('frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="mb-3">
                            <label for="duration_days" class="form-label">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" id="duration_days" class="form-control @error('duration_days') is-invalid @enderror"
                                   value="{{ old('duration_days') }}" min="1" required>
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instructions</label>
                            <textarea name="instructions" id="instructions" class="form-control @error('instructions') is-invalid @enderror"
                                      rows="3" placeholder="Special instructions for the prescription...">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.pharmacy.prescriptions') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Select a medical record first to associate the prescription</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Type in the medication field to see available inventory suggestions</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Use clear dosage instructions (e.g., "5mg twice daily")</li>
                        <li><i class="fas fa-check text-success me-2"></i> Specify duration in days for treatment planning</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const medicationInput = document.getElementById('medication');
    const suggestionsDiv = document.getElementById('medication-suggestions');

    // Show suggestions when typing
    medicationInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        if (query.length > 0) {
            suggestionsDiv.style.display = 'block';
        } else {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!medicationInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Handle suggestion selection
    document.querySelectorAll('.suggestion-item').forEach(item => {
        item.addEventListener('click', function() {
            medicationInput.value = this.dataset.medication;
            suggestionsDiv.style.display = 'none';
        });
    });
});
</script>
@endpush