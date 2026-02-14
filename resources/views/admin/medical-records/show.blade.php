@extends('admin.dashboard')

@section('page-title', 'Medical Record Details')
@section('page-description', $record->pet->name ?? 'Pet Medical Record')

@section('content')
<div class="container-fluid">
    <div class="record-container">
        <div class="record-header">
            <h2><i class="fas fa-stethoscope"></i> Medical Record</h2>
            <div class="header-actions">
                <a href="{{ route('admin.medical-records.edit', $record->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.medical-records.destroy', $record->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this medical record? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary" style="background:#ff6b6b; color:white;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="record-content">
            <div class="record-section">
                <h3>Pet Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Pet Name</label>
                        <p>{{ $record->pet->name ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Species</label>
                        <p>{{ $record->pet->species ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Breed</label>
                        <p>{{ $record->pet->breed ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Owner</label>
                        <p>{{ ($record->pet && $record->pet->owner && $record->pet->owner->user) ? $record->pet->owner->user->first_name . ' ' . $record->pet->owner->user->last_name : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="record-section">
                <h3>Visit Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Visit Date</label>
                        <p>{{ $record->visit_date ? \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Veterinarian</label>
                        <p>{{ ($record->veterinarian) ? 'Dr. ' . $record->veterinarian->first_name . ' ' . $record->veterinarian->last_name : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Linked Appointment</label>
                        @if($record->appointment)
                            <p>{{ $record->appointment->appointment_date->format('M d, Y h:i A') }} ({{ ucfirst($record->appointment->type) }})</p>
                        @else
                            <p class="text-muted">None</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="record-section">
                <h3>Chief Complaint</h3>
                <div class="text-content">
                    {{ $record->complaint ?: 'N/A' }}
                </div>
            </div>

            <div class="record-section">
                <h3>Vital Signs</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label><i class="fas fa-thermometer-half"></i> Temperature</label>
                        <p>{{ $record->temperature ? $record->temperature . '°C' : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-heartbeat"></i> Heart Rate</label>
                        <p>{{ $record->heart_rate ? $record->heart_rate . ' bpm' : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-lungs"></i> Respiratory Rate</label>
                        <p>{{ $record->respiratory_rate ? $record->respiratory_rate . ' rpm' : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label><i class="fas fa-tint"></i> Blood Pressure</label>
                        <p>{{ $record->blood_pressure ? $record->blood_pressure . ' mmHg' : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="record-section">
                <h3>Examination Notes</h3>
                <div class="text-content">
                    {{ $record->examination_notes ?: 'N/A' }}
                </div>
            </div>

            <div class="record-section">
                <h3>Diagnosis</h3>
                <div class="text-content">
                    {{ $record->diagnosis ?: 'N/A' }}
                </div>
            </div>

            <div class="record-section">
                <h3>Treatment Plan</h3>
                <div class="text-content">
                    {{ $record->treatment_plan ?: 'N/A' }}
                </div>
            </div>

            <div class="record-section">
                <h3>Follow-up Date</h3>
                <p>{{ $record->follow_up_date ? \Carbon\Carbon::parse($record->follow_up_date)->format('M d, Y') : 'Not scheduled' }}</p>
            </div>

            <div class="record-section">
                <h3>Associated Prescriptions</h3>
                @if($record->prescriptions && count($record->prescriptions) > 0)
                    <div class="prescriptions-list">
                        @foreach($record->prescriptions as $prescription)
                            <div class="prescription-item">
                                <strong>{{ $prescription->medication }}</strong><br>
                                {{ $prescription->dosage }} - {{ $prescription->frequency }}<br>
                                <small class="text-muted">Duration: {{ $prescription->duration_days }} days</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No prescriptions for this record</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.record-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.record-header {
    background: linear-gradient(135deg, var(--primary-orange) 0%, #FF8C42 100%);
    color: white;
    padding: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.record-header h2 {
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.record-content {
    padding: 30px;
}

.record-section {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid #f0f0f0;
}

.record-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.text-content {
    white-space: pre-wrap;
}

.record-section h3 {
    color: var(--dark-text);
    margin-bottom: 15px;
    font-size: 18px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-item {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 6px;
}

.info-item label {
    display: block;
    font-weight: 600;
    color: var(--primary-orange);
    margin-bottom: 5px;
    font-size: 12px;
    text-transform: uppercase;
}

.info-item p {
    margin: 0;
    color: var(--dark-text);
}

.text-content {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 6px;
    line-height: 1.6;
    color: var(--dark-text);
    white-space: pre-wrap;
    word-wrap: break-word;
}

.text-muted {
    color: #999;
}

.prescriptions-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.prescription-item {
    background: #f9f9f9;
    padding: 15px;
    border-left: 4px solid var(--primary-orange);
    border-radius: 4px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
    font-size: 14px;
}

.btn-primary {
    background: white;
    color: var(--primary-orange);
}

.btn-primary:hover {
    background: #f0f0f0;
}

.btn-secondary {
    background: #6C757D;
    color: white;
}

.btn-secondary:hover {
    background: #5A6268;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .record-header {
        flex-direction: column;
        gap: 15px;
    }
}
</style>
@endsection
