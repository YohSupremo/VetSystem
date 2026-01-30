@extends('admin.dashboard')

@section('page-title', 'Create Prescription')
@section('page-description', 'Add new prescription for pet')

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-prescription-bottle"></i> New Prescription</h2>
            <a href="@if(request('pet_id')){{ route('admin.prescriptions.pet', request('pet_id')) }}@else{{ route('admin.prescriptions.index') }}@endif" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.prescriptions.store') }}" method="POST" class="form-main">
            @csrf

            <div class="form-section">
                <h3>Pet & Medical Record</h3>
                
                @if(request('pet_id'))
                    <!-- Pet Pre-selected (Read-only) -->
                    <div class="form-group">
                        <label>Pet <span class="text-danger">*</span></label>
                        <div class="form-control-static">
                            @php
                                $selectedPet = $pets->firstWhere('id', request('pet_id'));
                            @endphp
                            <strong>{{ $selectedPet->name ?? 'Unknown' }}</strong>
                            <small class="text-muted d-block">
                                {{ $selectedPet->owner->user->first_name ?? '' }} {{ $selectedPet->owner->user->last_name ?? '' }} • {{ $selectedPet->species }} @if($selectedPet->breed)• {{ $selectedPet->breed }}@endif
                            </small>
                        </div>
                        <input type="hidden" name="pet_id" value="{{ request('pet_id') }}" required>
                    </div>
                @else
                    <!-- Pet Selection (Editable) - selecting a pet reloads page to show only that pet's medical records -->
                    <div class="form-group">
                        <label>Select Pet <span class="text-danger">*</span></label>
                        <select name="pet_id" id="pet_id" class="form-control" required>
                            <option value="">Choose a pet...</option>
                            @forelse($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} - {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}
                                </option>
                            @empty
                                <option value="">No pets available</option>
                            @endforelse
                        </select>
                        @error('pet_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                @endif
                
                <div class="form-group">
                    <label>Medical Record (Optional)</label>
                    <select name="medical_record_id" class="form-control" @if(!request('pet_id')) disabled @endif>
                        @if(request('pet_id'))
                            <option value="">Select medical record...</option>
                            @forelse($medicalRecords as $record)
                                <option value="{{ $record->id }}" {{ old('medical_record_id') == $record->id ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}
                                </option>
                            @empty
                                <option value="">No medical records for this pet</option>
                            @endforelse
                        @else
                            <option value="">Select a pet first</option>
                        @endif
                    </select>
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
                    <small class="form-text text-muted">Select a medicine from inventory or enter custom name below</small>
                    @error('inventory_item_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Custom Medication Name <span class="text-danger">*</span></label>
                    <input type="text" name="medication" id="medication" class="form-control" 
                           value="{{ old('medication') }}" placeholder="e.g., Amoxicillin" required>
                    <small class="form-text text-muted">Auto-filled when selecting from inventory, or enter custom name</small>
                    @error('medication')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Dosage <span class="text-danger">*</span></label>
                    <input type="text" name="dosage" class="form-control" value="{{ old('dosage') }}" placeholder="e.g., 500mg" required>
                    @error('dosage')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Frequency <span class="text-danger">*</span></label>
                        <input type="text" name="frequency" class="form-control" value="{{ old('frequency') }}" placeholder="e.g., 3 times daily" required>
                        @error('frequency')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Duration (Days) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days') }}" placeholder="10" required>
                        @error('duration_days')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Instructions</label>
                    <textarea name="instructions" class="form-control" rows="3" placeholder="Special instructions, side effects, etc.">{{ old('instructions') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Prescription
                </button>
                <a href="@if(request('pet_id')){{ route('admin.prescriptions.pet', request('pet_id')) }}@else{{ route('admin.prescriptions.index') }}@endif" class="btn btn-secondary">Cancel</a>
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
    color: var(--primary-orange);
}

.form-section {
    margin-bottom: 30px;
}

.form-section h3 {
    color: var(--dark-text);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--accent-pink);
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
    color: var(--dark-text);
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
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.text-danger {
    color: #F44336;
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
    background: var(--primary-orange);
    color: white;
}

.btn-primary:hover {
    background: #E85A2D;
}

.btn-secondary {
    background: #6C757D;
    color: white;
}

.btn-secondary:hover {
    background: #5A6268;
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
}
</style>
@if(!request('pet_id'))
<script>
document.getElementById('pet_id').addEventListener('change', function() {
    var petId = this.value;
    if (petId) {
        window.location.href = '{{ route('admin.prescriptions.create') }}?pet_id=' + petId;
    }
});
</script>
@endif

<script>
document.getElementById('inventory_item_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var medicationName = selectedOption.getAttribute('data-name');
    var medicationInput = document.getElementById('medication');
    
    if (medicationName) {
        medicationInput.value = medicationName;
    } else {
        medicationInput.value = '';
    }
});
</script>
@endsection
