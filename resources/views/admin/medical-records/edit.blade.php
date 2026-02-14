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
                </div>

                <div class="form-group">
                    <label>Treatment Plan</label>
                    <textarea name="treatment_plan" class="form-control" rows="4">{{ $record->treatment_plan }}</textarea>
                </div>

                <div class="form-group">
                    <label>Follow-up Date</label>
                    <input type="date" name="follow_up_date" class="form-control" value="{{ $record->follow_up_date ? date('Y-m-d', strtotime($record->follow_up_date)) : '' }}">
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
