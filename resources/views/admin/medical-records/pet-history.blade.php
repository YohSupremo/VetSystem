@extends('admin.dashboard')

@section('page-title', 'Medical History')
@section('page-description', 'Complete medical history for ' . $pet->name)

@section('content')
<div class="container-fluid">
    <div class="history-container">
        <div class="history-header">
            <div>
                <h2><i class="fas fa-history"></i> Complete Medical History</h2>
                <p class="pet-info">
                    <strong>{{ $pet->name }}</strong> - {{ $pet->species }} ({{ $pet->breed }})<br>
                    Owner: {{ $pet->owner && $pet->owner->user ? $pet->owner->user->first_name . ' ' . $pet->owner->user->last_name : 'N/A' }}
                </p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Records
                </a>
            </div>
        </div>

        <div class="history-content">
            @if($medicalRecords->count() > 0)
                <div class="timeline">
                    @foreach($medicalRecords as $record)
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="record-card">
                                    <div class="record-card-header">
                                        <div>
                                            <h4>{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</h4>
                                            <p class="vet-info">
                                                <i class="fas fa-user-md"></i> 
                                                Dr. {{ $record->veterinarian ? $record->veterinarian->first_name . ' ' . $record->veterinarian->last_name : 'N/A' }}
                                            </p>
                                        </div>
                                        <a href="{{ route('admin.medical-records.show', $record->id) }}" class="btn btn-sm btn-outline">
                                            View Details
                                        </a>
                                    </div>

                                    <div class="record-card-body">
                                        <div class="record-detail">
                                            <strong>Chief Complaint:</strong>
                                            <p>{{ $record->complaint }}</p>
                                        </div>

                                        @if($record->vital_signs && is_array($record->vital_signs))
                                            <div class="record-detail">
                                                <strong>Vital Signs:</strong>
                                                <div class="vitals-inline">
                                                    @if(isset($record->vital_signs['temperature']) && $record->vital_signs['temperature'])
                                                        <span><i class="fas fa-thermometer-half"></i> {{ $record->vital_signs['temperature'] }}°C</span>
                                                    @endif
                                                    @if(isset($record->vital_signs['heart_rate']) && $record->vital_signs['heart_rate'])
                                                        <span><i class="fas fa-heartbeat"></i> {{ $record->vital_signs['heart_rate'] }} bpm</span>
                                                    @endif
                                                    @if(isset($record->vital_signs['respiratory_rate']) && $record->vital_signs['respiratory_rate'])
                                                        <span><i class="fas fa-lungs"></i> {{ $record->vital_signs['respiratory_rate'] }} rpm</span>
                                                    @endif
                                                    @if(isset($record->vital_signs['blood_pressure']) && $record->vital_signs['blood_pressure'])
                                                        <span><i class="fas fa-tint"></i> {{ $record->vital_signs['blood_pressure'] }} mmHg</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if($record->diagnosis)
                                            <div class="record-detail">
                                                <strong>Diagnosis:</strong>
                                                <p>{{ Str::limit($record->diagnosis, 150) }}</p>
                                            </div>
                                        @endif

                                        @if($record->treatment_plan)
                                            <div class="record-detail">
                                                <strong>Treatment:</strong>
                                                <p>{{ Str::limit($record->treatment_plan, 150) }}</p>
                                            </div>
                                        @endif

                                        @if($record->follow_up_date)
                                            <div class="record-detail">
                                                <strong>Follow-up:</strong>
                                                <p><i class="fas fa-calendar-check"></i> {{ \Carbon\Carbon::parse($record->follow_up_date)->format('M d, Y') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-records">
                    <i class="fas fa-file-medical fa-3x"></i>
                    <h3>No Medical Records Found</h3>
                    <p>This pet has no medical history yet. Go to the Medical Records page to create one.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.history-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.history-header {
    background: linear-gradient(135deg, var(--primary-orange) 0%, #FF8C42 100%);
    color: white;
    padding: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.history-header h2 {
    margin: 0 0 10px 0;
}

.pet-info {
    margin: 0;
    opacity: 0.95;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.history-content {
    padding: 30px;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--accent-pink);
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 8px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--primary-orange);
    border: 3px solid white;
    box-shadow: 0 0 0 2px var(--primary-orange);
}

.timeline-content {
    margin-left: 20px;
}

.record-card {
    background: #f9f9f9;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s;
}

.record-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.record-card-header {
    background: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #f0f0f0;
}

.record-card-header h4 {
    margin: 0 0 5px 0;
    color: var(--primary-orange);
}

.vet-info {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.record-card-body {
    padding: 20px;
}

.record-detail {
    margin-bottom: 15px;
}

.record-detail:last-child {
    margin-bottom: 0;
}

.record-detail strong {
    display: block;
    color: var(--dark-text);
    margin-bottom: 5px;
}

.record-detail p {
    margin: 0;
    color: #666;
    line-height: 1.5;
}

.vitals-inline {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 5px;
}

.vitals-inline span {
    background: white;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    color: #666;
}

.vitals-inline i {
    color: var(--primary-orange);
    margin-right: 5px;
}

.no-records {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.no-records i {
    color: var(--accent-pink);
    margin-bottom: 20px;
}

.no-records h3 {
    color: var(--dark-text);
    margin-bottom: 10px;
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

.btn-sm {
    padding: 8px 16px;
    font-size: 13px;
}

.btn-outline {
    background: white;
    color: var(--primary-orange);
    border: 2px solid var(--primary-orange);
}

.btn-outline:hover {
    background: var(--primary-orange);
    color: white;
}

@media (max-width: 768px) {
    .history-header {
        flex-direction: column;
        gap: 20px;
        align-items: flex-start;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .header-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .record-card-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .vitals-inline {
        flex-direction: column;
        gap: 8px;
    }
}
</style>
@endsection
