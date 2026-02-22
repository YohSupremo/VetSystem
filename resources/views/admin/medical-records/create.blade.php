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

                <div id="allergy-alert-box" class="allergy-alert-box" style="display: none;"></div>

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
                        const markAsChronic = document.querySelector('input[name="mark_as_chronic"]');
                        const chronicFields = document.getElementById('chronic-fields');
                        const markAsAllergy = document.querySelector('input[name="mark_as_allergy"]');
                        const allergyFields = document.getElementById('allergy-fields');
                        const allergyAlertBox = document.getElementById('allergy-alert-box');
                        const allergyMap = @json($allergyMap ?? []);
                        
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

                            renderAllergyAlert(selectedPetId);
                        }

                        function renderAllergyAlert(petId) {
                            if (!allergyAlertBox) {
                                return;
                            }

                            const allergies = (allergyMap[String(petId)] || []).filter(function (allergy) {
                                return allergy
                                    && allergy.allergen
                                    && String(allergy.allergen).trim() !== ''
                                    && allergy.reaction_type
                                    && String(allergy.reaction_type).trim() !== ''
                                    && allergy.severity
                                    && String(allergy.severity).trim() !== '';
                            });

                            if (!petId || allergies.length === 0) {
                                allergyAlertBox.style.display = 'none';
                                allergyAlertBox.innerHTML = '';
                                return;
                            }

                            const items = allergies.map(function (allergy) {
                                const severity = allergy.severity ? allergy.severity.toUpperCase() : 'UNKNOWN';
                                const reaction = allergy.reaction_type ? ` (${allergy.reaction_type})` : '';
                                return `<li><strong>${allergy.allergen}</strong>${reaction} - <span class="severity-inline">${severity}</span></li>`;
                            }).join('');

                            allergyAlertBox.innerHTML = `
                                <div class="allergy-alert-title"><i class="fas fa-exclamation-triangle"></i> Active Allergy Alert</div>
                                <ul class="allergy-alert-list">${items}</ul>
                            `;
                            allergyAlertBox.style.display = 'block';
                        }

                        function toggleAllergyFields() {
                            if (!markAsAllergy || !allergyFields) {
                                return;
                            }

                            allergyFields.style.display = markAsAllergy.checked ? 'block' : 'none';
                        }

                        function toggleChronicFields() {
                            if (!markAsChronic || !chronicFields) {
                                return;
                            }

                            chronicFields.style.display = markAsChronic.checked ? 'block' : 'none';
                        }

                        petSelect.addEventListener('change', updateAppointments);
                        if (markAsChronic) {
                            markAsChronic.addEventListener('change', toggleChronicFields);
                        }
                        if (markAsAllergy) {
                            markAsAllergy.addEventListener('change', toggleAllergyFields);
                        }
                        
                        // Initial run if pet is already selected (e.g. valid edit or pre-fill)
                        if (petSelect.value) {
                            updateAppointments();
                        } else {
                            // If no pet selected, clear appointments initially
                            appSelect.innerHTML = '';
                            appSelect.appendChild(placeholder);
                            renderAllergyAlert('');
                        }

                        toggleChronicFields();
                        toggleAllergyFields();
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
                    <label class="chronic-toggle">
                        <input type="checkbox" name="mark_as_chronic" value="1" {{ old('mark_as_chronic') ? 'checked' : '' }}>
                        <span>Mark diagnosis as chronic condition</span>
                    </label>
                    @error('mark_as_chronic')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div id="chronic-fields" class="chronic-fields" style="display: none;">
                    <div class="form-group">
                        <label>Condition Name <span class="text-danger">*</span></label>
                        <input type="text" name="chronic_condition_name" class="form-control" value="{{ old('chronic_condition_name') }}" placeholder="e.g. Diabetes Mellitus">
                        @error('chronic_condition_name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Ongoing Treatment</label>
                        <textarea name="chronic_ongoing_treatment" class="form-control" rows="3" placeholder="Current long-term treatment plan...">{{ old('chronic_ongoing_treatment') }}</textarea>
                        @error('chronic_ongoing_treatment')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="chronic-toggle">
                        <input type="checkbox" name="mark_as_allergy" value="1" {{ old('mark_as_allergy') ? 'checked' : '' }}>
                        <span>Mark diagnosis as pet allergy</span>
                    </label>
                    @error('mark_as_allergy')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div id="allergy-fields" class="allergy-fields" style="display: none;">
                    <div class="form-group">
                        <label>Allergen <span class="text-danger">*</span></label>
                        <input type="text" name="allergy_allergen" class="form-control" value="{{ old('allergy_allergen') }}" placeholder="e.g. Chicken, Penicillin, Pollen">
                        @error('allergy_allergen')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Reaction Type</label>
                        <input type="text" name="allergy_reaction_type" class="form-control" value="{{ old('allergy_reaction_type') }}" placeholder="e.g. Skin rash, Vomiting">
                        @error('allergy_reaction_type')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Severity <span class="text-danger">*</span></label>
                        <select name="allergy_severity" class="form-control">
                            <option value="">Choose severity...</option>
                            <option value="mild" {{ old('allergy_severity') === 'mild' ? 'selected' : '' }}>Mild</option>
                            <option value="moderate" {{ old('allergy_severity') === 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="severe" {{ old('allergy_severity') === 'severe' ? 'selected' : '' }}>Severe</option>
                        </select>
                        @error('allergy_severity')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
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

.allergy-alert-box {
    margin-bottom: 18px;
    background: #FFF4E5;
    border: 1px solid #FFD8A8;
    border-left: 4px solid #F59E0B;
    border-radius: 8px;
    padding: 12px 14px;
}

.allergy-alert-title {
    font-weight: 700;
    color: #92400E;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.allergy-alert-list {
    margin: 0;
    padding-left: 20px;
    color: #7C2D12;
}

.severity-inline {
    font-weight: 700;
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

.chronic-toggle {
    display: inline-flex !important;
    align-items: center;
    gap: 10px;
    margin-bottom: 0 !important;
    font-weight: 600;
    cursor: pointer;
}

.form-group input[type="checkbox"] {
    width: auto;
    padding: 0;
    margin: 0;
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
