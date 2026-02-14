@extends('admin.dashboard')

@section('page-title', 'Create Medical Record')
@section('page-description', 'Add new medical record for pet')

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-stethoscope"></i> New Medical Record</h2>
            <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.medical-records.store') }}" method="POST" class="form-main">
            @csrf
            @if(isset($selectedPetId) && $selectedPetId)
                <input type="hidden" name="pet_id_prefill" value="{{ $selectedPetId }}">
            @endif

            <div class="form-section">
                <h3>Pet Information</h3>
                
                <div class="form-group">
                    <label>Select Pet <span class="text-danger">*</span></label>
                    <select name="pet_id" class="form-control">
                        <option value="">Choose a pet...</option>
                        @forelse($pets as $pet)
                            <option value="{{ $pet->id }}" {{ (old('pet_id', $selectedPetId ?? null) == $pet->id) ? 'selected' : '' }}>
                                {{ $pet->name }} - {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}
                            </option>
                        @empty
                            <option value="">No pets available</option>
                        @endforelse
                    </select>
                    @error('pet_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Veterinarian <span class="text-danger">*</span></label>
                    <select name="veterinarian_id" class="form-control">
                        <option value="">Choose veterinarian...</option>
                        @forelse($veterinarians as $vet)
                            <option value="{{ $vet->id }}" {{ old('veterinarian_id') == $vet->id ? 'selected' : '' }}>
                                Dr. {{ $vet->first_name }} {{ $vet->last_name }}
                            </option>
                        @empty
                            <option value="">No veterinarians available</option>
                        @endforelse
                    </select>
                    @error('veterinarian_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Linked Appointment (Optional)</label>
                    <select name="appointment_id" class="form-control">
                        <option value="">Select an appointment...</option>
                        @forelse($appointments as $app)
                            <option value="{{ $app->id }}" data-pet-id="{{ $app->pet_id }}" {{ old('appointment_id') == $app->id ? 'selected' : '' }}>
                                {{ $app->appointment_date->format('M d, Y h:i A') }} ({{ ucfirst($app->type) }})
                            </option>
                        @empty
                            <option value="">No active appointments</option>
                        @endforelse
                    </select>
                    @error('appointment_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const petSelect = document.querySelector('select[name="pet_id"]');
                        const appSelect = document.querySelector('select[name="appointment_id"]');
                        
                        if (!petSelect || !appSelect) return;

                        // Store all original appointment options (skipping the first placeholder)
                        const allAppOptions = Array.from(appSelect.options).slice(1);
                        const placeholder = appSelect.options[0];

                        function updateAppointments() {
                            const selectedPetId = petSelect.value;
                            const currentAppId = appSelect.value;
                            
                            // Clear dropdown
                            appSelect.innerHTML = '';
                            appSelect.appendChild(placeholder);
                            
                            if (selectedPetId) {
                                // Add only matching appointments
                                allAppOptions.forEach(option => {
                                    if (option.dataset.petId == selectedPetId) {
                                        appSelect.appendChild(option);
                                    }
                                });
                            }
                            
                            // Try to restore selection if it's still in the list
                            if (currentAppId && appSelect.querySelector(`option[value="${currentAppId}"]`)) {
                                appSelect.value = currentAppId;
                            } else {
                                appSelect.value = "";
                            }
                        }

                        petSelect.addEventListener('change', updateAppointments);
                        
                        // Initial run if pet is already selected (e.g. valid edit or pre-fill)
                        if (petSelect.value) {
                            updateAppointments();
                        } else {
                            // If no pet selected, clear appointments initially
                            appSelect.innerHTML = '';
                            appSelect.appendChild(placeholder);
                        }
                    });
                </script>

                <div class="form-group">
                    <label>Visit Date <span class="text-danger">*</span></label>
                    <input type="date" name="visit_date" class="form-control" value="{{ old('visit_date') }}">
                    @error('visit_date')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Examination Details</h3>
                
                <div class="form-group">
                    <label>Chief Complaint <span class="text-danger">*</span></label>
                    <textarea name="complaint" class="form-control" rows="3">{{ old('complaint') }}</textarea>
                    @error('complaint')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-section">
                    <h4>Vital Signs</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Temperature (°C)</label>
                            <input type="number" name="temperature" class="form-control" step="0.1" value="{{ old('temperature') }}" placeholder="38.5">
                            @error('temperature')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Heart Rate (bpm)</label>
                            <input type="number" name="heart_rate" class="form-control" value="{{ old('heart_rate') }}" placeholder="80">
                            @error('heart_rate')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Respiratory Rate (rpm)</label>
                            <input type="number" name="respiratory_rate" class="form-control" value="{{ old('respiratory_rate') }}" placeholder="20">
                            @error('respiratory_rate')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Blood Pressure (Systolic)</label>
                            <input type="number" name="blood_pressure_systolic" class="form-control" value="{{ old('blood_pressure_systolic') }}" placeholder="120">
                            @error('blood_pressure_systolic')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Blood Pressure (Diastolic)</label>
                            <input type="number" name="blood_pressure_diastolic" class="form-control" value="{{ old('blood_pressure_diastolic') }}" placeholder="80">
                            @error('blood_pressure_diastolic')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    

                </div>

                <div class="form-group">
                    <label>Examination Notes</label>
                    <textarea name="examination_notes" class="form-control" rows="4" placeholder="Physical examination findings, observations, etc...">{{ old('examination_notes') }}</textarea>
                    @error('examination_notes')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Diagnosis & Treatment</h3>
                
                <div class="form-group">
                    <label>Diagnosis</label>
                    <textarea name="diagnosis" class="form-control" rows="3">{{ old('diagnosis') }}</textarea>
                    @error('diagnosis')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Treatment Plan</label>
                    <textarea name="treatment_plan" class="form-control" rows="4">{{ old('treatment_plan') }}</textarea>
                    @error('treatment_plan')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Follow-up Date</label>
                    <input type="date" name="follow_up_date" class="form-control" value="{{ old('follow_up_date') }}">
                    @error('follow_up_date')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Record
                </button>
                <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary">Cancel</a>
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
    grid-template-columns: 1fr 1fr 1fr;
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
@endsection
