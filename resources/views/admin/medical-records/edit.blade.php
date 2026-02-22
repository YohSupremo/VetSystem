@extends('admin.dashboard')

@section('page-title', 'Edit Medical Record')
@section('page-description', 'Update medical record for ' . ($record->pet->name ?? 'pet'))

@section('content')
<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-stethoscope"></i> Edit Medical Record</h2>
            <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.medical-records.update', $record->id) }}" method="POST" class="form-main">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Pet Information</h3>
                
                <div class="form-group">
                    <label>Pet</label>
                    <input type="text" class="form-control" value="{{ $record->pet->name ?? 'N/A' }}" disabled>
                    <input type="hidden" name="pet_id" value="{{ $record->pet_id }}">
                </div>

                @if(!empty($activeAllergies) && $activeAllergies->count() > 0)
                    <div class="allergy-alert-box">
                        <div class="allergy-alert-title"><i class="fas fa-exclamation-triangle"></i> Active Allergy Alert</div>
                        <ul class="allergy-alert-list">
                            @foreach($activeAllergies as $allergy)
                                <li>
                                    <strong>{{ $allergy->allergen }}</strong>
                                    @if(!empty($allergy->reaction_type))
                                        ({{ $allergy->reaction_type }})
                                    @endif
                                    - <span class="severity-inline">{{ strtoupper($allergy->severity ?? 'unknown') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label>Veterinarian <span class="text-danger">*</span></label>
                    <select name="veterinarian_id" class="form-control">
                        <option value="">Choose veterinarian...</option>
                        @forelse($veterinarians as $vet)
                            <option value="{{ $vet->id }}" {{ $record->veterinarian_id == $vet->id ? 'selected' : '' }}>
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
                            @if($app->pet_id == $record->pet_id)
                                <option value="{{ $app->id }}" {{ $record->appointment_id == $app->id ? 'selected' : '' }}>
                                    {{ $app->appointment_date->format('M d, Y h:i A') }} ({{ ucfirst($app->type) }})
                                </option>
                            @endif
                        @empty
                            <option value="">No recent appointments</option>
                        @endforelse
                    </select>
                    @error('appointment_id')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Visit Date <span class="text-danger">*</span></label>
                    <input type="date" name="visit_date" class="form-control" value="{{ $record->visit_date ? date('Y-m-d', strtotime($record->visit_date)) : '' }}">
                    @error('visit_date')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Examination Details</h3>
                
                <div class="form-group">
                    <label>Chief Complaint <span class="text-danger">*</span></label>
                    <textarea name="complaint" class="form-control" rows="3">{{ $record->complaint }}</textarea>
                    @error('complaint')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-section">
                    <h4>Vital Signs</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Temperature (°C)</label>
                            <input type="number" name="temperature" class="form-control" step="0.1" value="{{ old('temperature', isset($vitalSigns['temperature']) ? $vitalSigns['temperature'] : '') }}" placeholder="38.5">
                        </div>
                        <div class="form-group">
                            <label>Heart Rate (bpm)</label>
                            <input type="number" name="heart_rate" class="form-control" value="{{ old('heart_rate', isset($vitalSigns['heart_rate']) ? $vitalSigns['heart_rate'] : '') }}" placeholder="80">
                        </div>
                        <div class="form-group">
                            <label>Respiratory Rate (rpm)</label>
                            <input type="number" name="respiratory_rate" class="form-control" value="{{ old('respiratory_rate', isset($vitalSigns['respiratory_rate']) ? $vitalSigns['respiratory_rate'] : '') }}" placeholder="20">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Blood Pressure (Systolic)</label>
                            @php
                                $bp = isset($vitalSigns['blood_pressure']) ? explode('/', $vitalSigns['blood_pressure']) : ['', ''];
                            @endphp
                            <input type="number" name="blood_pressure_systolic" class="form-control" value="{{ old('blood_pressure_systolic', $bp[0] ?? '') }}" placeholder="120">
                        </div>
                        <div class="form-group">
                            <label>Blood Pressure (Diastolic)</label>
                            <input type="number" name="blood_pressure_diastolic" class="form-control" value="{{ old('blood_pressure_diastolic', $bp[1] ?? '') }}" placeholder="80">
                    </div>
                </div>

                <div class="form-group">
                    <label>Examination Notes</label>
                    <textarea name="examination_notes" class="form-control" rows="4" placeholder="Physical examination findings, observations, etc...">{{ $record->examination_notes }}</textarea>
                    @error('examination_notes')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Diagnosis & Treatment</h3>
                
                <div class="form-group">
                    <label>Diagnosis</label>
                    <textarea name="diagnosis" class="form-control" rows="3">{{ $record->diagnosis }}</textarea>
                    @error('diagnosis')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="chronic-toggle">
                        <input type="checkbox" name="mark_as_chronic" value="1" {{ old('mark_as_chronic', !empty($linkedChronic) || !empty($existingChronicForDiagnosis) ? 1 : 0) ? 'checked' : '' }}>
                        <span>Mark diagnosis as chronic condition</span>
                    </label>
                    @error('mark_as_chronic')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div id="chronic-fields" class="chronic-fields" style="display: none;">
                    <div class="form-group">
                        <label>Condition Name <span class="text-danger">*</span></label>
                        <input type="text" name="chronic_condition_name" class="form-control" value="{{ old('chronic_condition_name', $linkedChronic->condition_name ?? $record->diagnosis) }}" placeholder="e.g. Diabetes Mellitus">
                        @error('chronic_condition_name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Ongoing Treatment</label>
                        <textarea name="chronic_ongoing_treatment" class="form-control" rows="3" placeholder="Current long-term treatment plan...">{{ old('chronic_ongoing_treatment', $linkedChronic->ongoing_treatment ?? $record->treatment_plan) }}</textarea>
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
                        <input type="text" name="allergy_allergen" class="form-control" value="{{ old('allergy_allergen', $linkedAllergy->allergen ?? '') }}" placeholder="e.g. Chicken, Penicillin, Pollen">
                        @error('allergy_allergen')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Reaction Type</label>
                        <input type="text" name="allergy_reaction_type" class="form-control" value="{{ old('allergy_reaction_type', $linkedAllergy->reaction_type ?? '') }}" placeholder="e.g. Skin rash, Vomiting">
                        @error('allergy_reaction_type')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Severity <span class="text-danger">*</span></label>
                        <select name="allergy_severity" class="form-control">
                            <option value="">Choose severity...</option>
                            <option value="mild" {{ old('allergy_severity', $linkedAllergy->severity ?? '') === 'mild' ? 'selected' : '' }}>Mild</option>
                            <option value="moderate" {{ old('allergy_severity', $linkedAllergy->severity ?? '') === 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="severe" {{ old('allergy_severity', $linkedAllergy->severity ?? '') === 'severe' ? 'selected' : '' }}>Severe</option>
                        </select>
                        @error('allergy_severity')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Treatment Plan</label>
                    <textarea name="treatment_plan" class="form-control" rows="4">{{ $record->treatment_plan }}</textarea>
                    @error('treatment_plan')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Follow-up Date</label>
                    <input type="date" name="follow_up_date" class="form-control" value="{{ $record->follow_up_date ? date('Y-m-d', strtotime($record->follow_up_date)) : '' }}">
                    @error('follow_up_date')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Record
                </button>
                <a href="{{ route('admin.medical-records.show', $record->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const markAsChronic = document.querySelector('input[name="mark_as_chronic"]');
    const chronicFields = document.getElementById('chronic-fields');
    const markAsAllergy = document.querySelector('input[name="mark_as_allergy"]');
    const allergyFields = document.getElementById('allergy-fields');

    function toggleChronicFields() {
        if (!markAsChronic || !chronicFields) {
            return;
        }

        chronicFields.style.display = markAsChronic.checked ? 'block' : 'none';
    }

    function toggleAllergyFields() {
        if (!markAsAllergy || !allergyFields) {
            return;
        }

        allergyFields.style.display = markAsAllergy.checked ? 'block' : 'none';
    }

    if (markAsChronic) {
        markAsChronic.addEventListener('change', toggleChronicFields);
    }

    if (markAsAllergy) {
        markAsAllergy.addEventListener('change', toggleAllergyFields);
    }

    toggleChronicFields();
    toggleAllergyFields();
});
</script>

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

.form-group input:disabled {
    background: #f5f5f5;
    cursor: not-allowed;
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
