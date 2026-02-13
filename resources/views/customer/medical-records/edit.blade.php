@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Edit Medical Record - PawCare')

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
    max-width: 800px;
    margin: 0 auto;
}

.medical-form {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--dark-text);
    margin-bottom: 0.5rem;
}

.form-subtitle {
    color: var(--light-text);
}

.form-section {
    margin-bottom: 2rem;
}

.form-section h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section h3 i {
    color: var(--primary-purple);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--soft-gray);
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-purple);
    box-shadow: 0 0 10px rgba(167, 139, 250, 0.1);
}

.vital-signs {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.vital-input {
    text-align: center;
}

.vital-input input {
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

.btn-danger {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .form-grid {
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
        <form action="{{ route('customer.medical-records.update', [$record->pet_id, $record->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="medical-form">
                <div class="form-header">
                    <h2 class="form-title">Edit Medical Record</h2>
                    <p class="form-subtitle">Update health information for {{ $record->pet->name }}</p>
                </div>

                <!-- Pet Information -->
                <div class="form-section">
                    <h3><i class="fas fa-paw"></i> Pet Information</h3>
                    <div class="form-group">
                        <label>Current Pet</label>
                        <input type="text" value="{{ $record->pet->name }} ({{ ucfirst($record->pet->species) }})" readonly style="background: var(--soft-gray);">
                    </div>
                </div>

                <!-- Visit Information -->
                <div class="form-section">
                    <h3><i class="fas fa-calendar"></i> Visit Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="visit_date">Visit Date *</label>
                            <input type="date" name="visit_date" id="visit_date" value="{{ $record->visit_date->format('Y-m-d') }}" required max="{{ now()->toDateString() }}">
                        </div>
                        <div class="form-group">
                            <label for="veterinarian_id">Veterinarian</label>
                            <select name="veterinarian_id" id="veterinarian_id">
                                <option value="">-- Select veterinarian --</option>
                                @foreach($veterinarians as $vet)
                                <option value="{{ $vet->id }}" {{ $record->veterinarian_id == $vet->id ? 'selected' : '' }}>
                                    Dr. {{ $vet->first_name }} {{ $vet->last_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Examination Details -->
                <div class="form-section">
                    <h3><i class="fas fa-stethoscope"></i> Examination Details</h3>
                    <div class="form-group">
                        <label for="complaint">Chief Complaint</label>
                        <textarea name="complaint" id="complaint" placeholder="Describe the main reason for visit...">{{ old('complaint', $record->complaint) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="examination_notes">Examination Notes</label>
                        <textarea name="examination_notes" id="examination_notes" placeholder="Detailed examination findings...">{{ old('examination_notes', $record->examination_notes) }}</textarea>
                    </div>
                </div>

                <!-- Vital Signs -->
                <div class="form-section">
                    <h3><i class="fas fa-heartbeat"></i> Vital Signs</h3>
                    <div class="vital-signs">
                        <div class="form-group vital-input">
                            <label for="temperature">Temperature (°F)</label>
                            <input type="number" name="temperature" id="temperature" step="0.1" value="{{ old('temperature', $record->temperature) }}" placeholder="101.5">
                        </div>
                        <div class="form-group vital-input">
                            <label for="heart_rate">Heart Rate (bpm)</label>
                            <input type="number" name="heart_rate" id="heart_rate" value="{{ old('heart_rate', $record->heart_rate) }}" placeholder="80">
                        </div>
                        <div class="form-group vital-input">
                            <label for="respiratory_rate">Respiratory Rate (bpm)</label>
                            <input type="number" name="respiratory_rate" id="respiratory_rate" value="{{ old('respiratory_rate', $record->respiratory_rate) }}" placeholder="20">
                        </div>
                        <div class="form-group vital-input">
                            <label for="blood_pressure">Blood Pressure</label>
                            <input type="text" name="blood_pressure" id="blood_pressure" value="{{ old('blood_pressure', $record->blood_pressure) }}" placeholder="120/80">
                        </div>
                    </div>
                </div>

                <!-- Diagnosis & Treatment -->
                <div class="form-section">
                    <h3><i class="fas fa-notes-medical"></i> Diagnosis & Treatment</h3>
                    <div class="form-group">
                        <label for="diagnosis">Diagnosis</label>
                        <textarea name="diagnosis" id="diagnosis" placeholder="Final diagnosis...">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="treatment_plan">Treatment Plan</label>
                        <textarea name="treatment_plan" id="treatment_plan" placeholder="Recommended treatment...">{{ old('treatment_plan', $record->treatment_plan) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="follow_up_date">Follow-up Date</label>
                        <input type="date" name="follow_up_date" id="follow_up_date" value="{{ $record->follow_up_date ? $record->follow_up_date->format('Y-m-d') : '' }}" min="{{ now()->toDateString() }}">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('customer.medical-records.pet', $record->pet_id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Pet Records
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Medical Record
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>
@endsection
