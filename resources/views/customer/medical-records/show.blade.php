@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Medical Record Details - PawCare')

@push('styles')
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.customer-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
}

.logo-section .paw-icon {
    font-size: 2.5rem;
    animation: bounce 2s infinite;
}

.logo-section h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.customer-main {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.record-details {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.record-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--soft-gray);
}

.record-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--dark-text);
}

.record-date {
    font-size: 1rem;
    color: var(--light-text);
    background: var(--soft-gray);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
}

.record-sections {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.record-section {
    background: var(--soft-gray);
    padding: 1.5rem;
    border-radius: 0.5rem;
}

.record-section h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.record-section h3 i {
    color: var(--primary-purple);
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 0.875rem;
    color: var(--light-text);
}

.detail-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--dark-text);
}

.notes-section {
    background: var(--soft-gray);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
}

.vital-signs {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.vital-card {
    background: white;
    padding: 1rem;
    border-radius: 0.5rem;
    text-align: center;
    border-left: 4px solid var(--primary-purple);
}

.vital-label {
    font-size: 0.75rem;
    color: var(--light-text);
    margin-bottom: 0.5rem;
}

.vital-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-purple);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
}

.btn-secondary {
    background: var(--soft-gray);
    color: var(--dark-text);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .record-sections {
        grid-template-columns: 1fr;
    }
    
    .vital-signs {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<div class="customer-container">
    <header class="customer-header">
        <div class="logo-section">
            <a href="{{ route('customer.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                <i class="fas fa-paw paw-icon text-primary"></i>
                <h1 class="ms-3 mb-0">PawCare</h1>
            </a>
        </div>
        <div class="user-section">
            <span class="text-muted">Welcome, {{ $user->first_name }}</span>
        </div>
    </header>

    <main class="customer-main">
        <div class="record-details">
            <div class="record-header">
                <div>
                    <h2 class="record-title">Medical Record Details</h2>
                    <p>Record ID: #{{ $record->id }}</p>
                </div>
                <div class="record-date">
                    {{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}
                </div>
            </div>

            <div class="record-sections">
                <!-- Pet Information -->
                <div class="record-section">
                    <h3><i class="fas fa-paw"></i> Pet Information</h3>
                    <div class="detail-item">
                        <span class="detail-label">Pet Name</span>
                        <span class="detail-value">{{ $record->pet->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Species</span>
                        <span class="detail-value">{{ ucfirst($record->pet->species) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Breed</span>
                        <span class="detail-value">{{ $record->pet->breed ?? 'Not specified' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Registration Number</span>
                        <span class="detail-value">{{ $record->pet->registration_number ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Visit Information -->
                <div class="record-section">
                    <h3><i class="fas fa-calendar"></i> Visit Information</h3>
                    <div class="detail-item">
                        <span class="detail-label">Visit Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</span>
                    </div>
                    @if($record->veterinarian)
                    <div class="detail-item">
                        <span class="detail-label">Veterinarian</span>
                        <span class="detail-value">Dr. {{ $record->veterinarian->first_name }} {{ $record->veterinarian->last_name }}</span>
                    </div>
                    @endif
                    @if($record->appointment)
                    <div class="detail-item">
                        <span class="detail-label">Appointment Type</span>
                        <span class="detail-value">{{ ucfirst($record->appointment->type) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Examination Details -->
            <div class="record-section">
                <h3><i class="fas fa-stethoscope"></i> Examination Details</h3>
                @if($record->complaint)
                <div class="notes-section">
                    <h4>Chief Complaint</h4>
                    <p>{{ $record->complaint }}</p>
                </div>
                @endif
                @if($record->examination_notes)
                <div class="notes-section">
                    <h4>Examination Notes</h4>
                    <p>{{ $record->examination_notes }}</p>
                </div>
                @endif
            </div>

            <!-- Vital Signs -->
            <div class="record-section">
                <h3><i class="fas fa-heartbeat"></i> Vital Signs</h3>
                <div class="vital-signs">
                    @if($record->temperature)
                    <div class="vital-card">
                        <div class="vital-label">Temperature</div>
                        <div class="vital-value">{{ $record->temperature }}°F</div>
                    </div>
                    @endif
                    @if($record->heart_rate)
                    <div class="vital-card">
                        <div class="vital-label">Heart Rate</div>
                        <div class="vital-value">{{ $record->heart_rate }} bpm</div>
                    </div>
                    @endif
                    @if($record->respiratory_rate)
                    <div class="vital-card">
                        <div class="vital-label">Respiratory Rate</div>
                        <div class="vital-value">{{ $record->respiratory_rate }} bpm</div>
                    </div>
                    @endif
                    @if($record->blood_pressure)
                    <div class="vital-card">
                        <div class="vital-label">Blood Pressure</div>
                        <div class="vital-value">{{ $record->blood_pressure }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Diagnosis & Treatment -->
            <div class="record-sections">
                <div class="record-section">
                    <h3><i class="fas fa-notes-medical"></i> Diagnosis</h3>
                    @if($record->diagnosis)
                    <div class="notes-section">
                        <p>{{ $record->diagnosis }}</p>
                    </div>
                    @else
                    <p style="color: var(--light-text); font-style: italic;">No diagnosis recorded</p>
                    @endif
                </div>

                <div class="record-section">
                    <h3><i class="fas fa-clipboard-list"></i> Treatment Plan</h3>
                    @if($record->treatment_plan)
                    <div class="notes-section">
                        <p>{{ $record->treatment_plan }}</p>
                    </div>
                    @else
                    <p style="color: var(--light-text); font-style: italic;">No treatment plan recorded</p>
                    @endif
                    @if($record->follow_up_date)
                    <div class="detail-item" style="margin-top: 1rem;">
                        <span class="detail-label">Follow-up Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($record->follow_up_date)->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('customer.medical-records.pet', $record->pet_id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Pet Records
                </a>
                <a href="{{ route('customer.medical-records.edit', [$record->pet_id, $record->id]) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Record
                </a>
            </div>
        </div>
    </main>
</div>
@endsection
