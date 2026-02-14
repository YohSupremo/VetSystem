@extends('admin.dashboard')

@section('page-title', 'Edit Prescription')
@section('page-description', 'Edit prescription details')

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-prescription-bottle"></i> Edit Prescription</h2>
            <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.prescriptions.update', $prescription->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Pet Information</h3>
                
                <!-- Pet (Read-only) -->
                <div class="form-group">
                    <label>Pet</label>
                    <div class="form-control-static">
                        <strong>{{ $prescription->medicalRecord?->pet?->name ?? 'Unknown' }}</strong>
                        <small class="text-muted d-block">
                            {{ $prescription->medicalRecord?->pet?->owner?->user?->first_name ?? '' }} {{ $prescription->medicalRecord?->pet?->owner?->user?->last_name ?? '' }} • {{ $prescription->medicalRecord?->pet?->species ?? '' }} @if($prescription->medicalRecord?->pet?->breed)• {{ $prescription->medicalRecord->pet->breed }}@endif
                        </small>
                    </div>
                    <input type="hidden" name="medical_record_id" value="{{ $prescription->medical_record_id }}">
                </div>

                <div class="form-group">
                    <label>Medical Record <span class="text-danger">*</span></label>
                    <select name="medical_record_id" class="form-control @error('medical_record_id') is-invalid @enderror">
                        <option value="">Select a medical record...</option>
                        @forelse($medicalRecords as $record)
                            <option value="{{ $record->id }}" {{ $prescription->medical_record_id == $record->id ? 'selected' : '' }}>
                                {{ $record->visit_date->format('M d, Y') }}
                            </option>
                        @empty
                            <option value="">No medical records for this pet</option>
                        @endforelse
                    </select>
                    @error('medical_record_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Medication Details</h3>
                
                <div class="form-group">
                    <label>Medication <span class="text-danger">*</span></label>
                    <select name="inventory_item_id" id="inventory_item_id" class="form-control">
                        <option value="">Select from inventory...</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" 
                                    data-name="{{ $medicine->name }}"
                                    {{ old('inventory_item_id') == $medicine->id ? 'selected' : '' }}>
                                {{ $medicine->name }}
                                @if($medicine->strength) - {{ $medicine->strength }}@endif
                                @if($medicine->dosage_form) - {{ $medicine->dosage_form }}@endif
                                ({{ $medicine->total_stock ?? 0 }} in stock)
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Select a medicine from inventory or keep current name</small>
                    @error('inventory_item_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Medication Name <span class="text-danger">*</span></label>
                    <input type="text" name="medication_name" id="medication_name" class="form-control @error('medication_name') is-invalid @enderror" 
                        value="{{ old('medication_name', $prescription->medication_name) }}">
                    <small class="form-text text-muted">Auto-filled when selecting from inventory</small>
                    @error('medication_name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Dosage <span class="text-danger">*</span></label>
                    <input type="text" name="dosage" class="form-control @error('dosage') is-invalid @enderror" 
                        placeholder="e.g., 500mg, 2 tablets" value="{{ old('dosage', $prescription->dosage) }}">
                    @error('dosage')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Frequency <span class="text-danger">*</span></label>
                        <input type="text" name="frequency" class="form-control @error('frequency') is-invalid @enderror" 
                            placeholder="e.g., Twice daily, Every 8 hours" value="{{ old('frequency', $prescription->frequency) }}">
                        @error('frequency')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Duration (Days) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" 
                            min="1" value="{{ old('duration_days', $prescription->duration_days) }}">
                        @error('duration_days')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                        min="1" value="{{ old('quantity', $prescription->quantity ?? '') }}">
                    <small class="form-text text-muted">Number of units to dispense</small>
                    @error('quantity')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Instructions</label>
                    <textarea name="instructions" class="form-control @error('instructions') is-invalid @enderror" 
                        rows="4" placeholder="e.g., Take with food, Avoid dairy products...">{{ old('instructions', $prescription->instructions) }}</textarea>
                    @error('instructions')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Prescription
                </button>
                <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-container {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.form-header h2 {
    margin: 0;
    color: var(--primary-orange, #ff6b35);
}

.form-section {
    margin-bottom: 30px;
}

.form-section h3 {
    color: var(--dark-text, #333);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--accent-pink, #ff91b9);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark-text, #333);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
}

.form-control-static {
    padding: 10px 15px;
    background-color: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-orange, #ff6b35);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.form-group input.is-invalid,
.form-group select.is-invalid,
.form-group textarea.is-invalid {
    border-color: #F44336;
}

.text-danger {
    color: #F44336;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.text-muted {
    color: #6c757d;
    font-size: 12px;
}

.d-block {
    display: block;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-primary {
    background: var(--primary-orange, #ff6b35);
    color: white;
}

.btn-primary:hover {
    background: #E85A2D;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn-secondary {
    background: #6C757D;
    color: white;
}

.btn-secondary:hover {
    background: #5A6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .form-container {
        padding: 20px;
    }
}
</style>

<script>
document.getElementById('inventory_item_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var medicationName = selectedOption.getAttribute('data-name');
    var medicationInput = document.getElementById('medication_name');
    
    if (medicationName) {
        medicationInput.value = medicationName;
    }
});
</script>
@endsection
