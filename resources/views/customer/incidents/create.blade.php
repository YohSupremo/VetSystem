@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Report an Incident - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
}

/* Form Container */
.incident-form {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.incident-form::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.form-label {
    font-size: 0.875rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-input, .form-select, .form-textarea {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 0.75rem;
    color: #000;
    font-weight: 600;
    transition: var(--transition-smooth);
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: rgba(147, 51, 234, 0.4);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
}

.form-input::placeholder, .form-select::placeholder, .form-textarea::placeholder {
    color: #666;
}

.btn-submit {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: var(--transition-smooth);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
    color: white;
}

.btn-cancel {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 0.75rem 2rem;
    color: #000;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition-smooth);
    display: inline-flex;
    align-items: center;
}

.btn-cancel:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.3);
    color: #000;
    text-decoration: none;
}

/* Alert */
.alert {
    background: rgba(239, 68, 68, 0.2);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-left: 4px solid rgba(239, 68, 68, 0.5);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #000;
    padding: 1.125rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    font-weight: 600;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
}

.alert ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.alert li {
    margin-bottom: 0.5rem;
}

.alert li:last-child {
    margin-bottom: 0;
}

/* Form Sections */
.form-section {
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

/* Priority Indicators */
.severity-high {
    border-color: rgba(239, 68, 68, 0.4) !important;
}

.severity-high:focus {
    border-color: rgba(239, 68, 68, 0.6) !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
}

.severity-medium {
    border-color: rgba(245, 158, 11, 0.4) !important;
}

.severity-medium:focus {
    border-color: rgba(245, 158, 11, 0.6) !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
}

.severity-low {
    border-color: rgba(16, 185, 129, 0.4) !important;
}

.severity-low:focus {
    border-color: rgba(16, 185, 129, 0.6) !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
}

@media (max-width: 768px) {
    .incident-form {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        padding: 1.5rem;
    }
    
    .form-input, .form-select, .form-textarea {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .btn-cancel {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="customer-container">
    <main class="customer-main">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <h1 class="page-title">Report an Incident</h1>
            <p class="page-subtitle">Provide details so our team can respond quickly.</p>
        </div>

        @if($errors->any())
            <div class="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.incidents.store') }}" class="incident-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Pet</label>
                    <select name="pet_id" class="form-select" required>
                        <option value="">Select a pet</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Incident Date & Time</label>
                    <input type="datetime-local" name="incident_date" class="form-input" value="{{ old('incident_date') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Incident Type</label>
                    <select name="incident_type" class="form-select" required>
                        <option value="">Select type</option>
                        @foreach($incidentTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('incident_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Severity</label>
                    <select name="severity" class="form-select severity-{{ old('severity') ?? '' }}" required>
                        <option value="">Select severity</option>
                        @foreach($severityOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('severity') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-section">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-input" value="{{ old('location') }}" placeholder="Where did this occur?" required>
            </div>

            <div class="form-section">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea form-input" rows="4" required>{{ old('description') }}</textarea>
            </div>

            <div class="form-section">
                <label class="form-label">Immediate Action Taken (optional)</label>
                <textarea name="immediate_action_taken" class="form-textarea form-input" rows="3">{{ old('immediate_action_taken') }}</textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('customer.incidents.index') }}" class="btn-cancel">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane me-2"></i>Submit Report
                </button>
            </div>
        </form>
    </main>
</div>
@endsection
