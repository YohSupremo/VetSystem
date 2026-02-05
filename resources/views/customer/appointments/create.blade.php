@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Book Appointment - PawCare')

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
    max-width: 900px;
    margin: 0 auto;
}

.form-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 3rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.form-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.form-header h2 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-control, .form-select {
    border-radius: 1rem;
    border: 2px solid var(--light-purple);
    padding: 0.75rem 1.25rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
}

.form-control:focus, .form-select:focus {
    border-color: var(--secondary-purple);
    box-shadow: 0 0 0 4px rgba(167, 139, 250, 0.1);
    background: white;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border: none;
    border-radius: 1rem;
    padding: 0.85rem 2rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(147, 51, 234, 0.4);
    color: white;
}

.btn-secondary {
    background: white;
    border: 2px solid var(--light-purple);
    border-radius: 1rem;
    padding: 0.85rem 2rem;
    font-weight: 600;
    color: var(--primary-purple);
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: var(--light-purple);
    border-color: var(--secondary-purple);
    color: var(--primary-purple);
    transform: translateY(-2px);
}

.alert {
    border-radius: 1rem;
    padding: 1rem 1.5rem;
    border: none;
    font-weight: 500;
    margin-bottom: 2rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.alert-info {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
}
</style>
@endpush

@section('content')
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="customer-container">
    <!-- Header -->
    <header class="customer-header">
        <div class="header-content d-flex justify-content-between align-items-center">
            <div class="logo-section d-flex align-items-center gap-3">
                <div class="paw-icon">🐾</div>
                <h1 class="mb-0">PawCare</h1>
            </div>
            <div class="user-menu d-flex align-items-center gap-3">
                <div class="user-info d-flex align-items-center gap-2">
                    <span class="welcome-text">Welcome, {{ $user->first_name }}!</span>
                    <div class="user-avatar">
                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                    </div>
                </div>
                <a href="/logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="customer-main">
        <div class="form-card">
            <div class="form-header">
                <h2>Book an Appointment</h2>
                <p>Schedule a visit for your pet</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('customer.appointments.store') }}" method="POST">
                @csrf
                
                <div class="row g-3">
                    <!-- Select Pet -->
                    <div class="col-12">
                        <label class="form-label" for="pet_id">Select Pet *</label>
                        <select
                            id="pet_id"
                            name="pet_id"
                            class="form-select @error('pet_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Choose your pet</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} ({{ $pet->species }})
                                </option>
                            @endforeach
                        </select>
                        @error('pet_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Appointment Type -->
                    <div class="col-12">
                        <label class="form-label" for="type">Appointment Type *</label>
                        <select
                            id="type"
                            name="type"
                            class="form-select @error('type') is-invalid @enderror"
                            required
                        >
                            <option value="">Select appointment type</option>
                            <option value="checkup" {{ old('type') == 'checkup' ? 'selected' : '' }}>General Checkup</option>
                            <option value="vaccination" {{ old('type') == 'vaccination' ? 'selected' : '' }}>Vaccination</option>
                            <option value="surgery" {{ old('type') == 'surgery' ? 'selected' : '' }}>Surgery</option>
                            <option value="dental" {{ old('type') == 'dental' ? 'selected' : '' }}>Dental Care</option>
                            <option value="grooming" {{ old('type') == 'grooming' ? 'selected' : '' }}>Grooming</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Appointment Date -->
                    <div class="col-md-6">
                        <label class="form-label" for="appointment_date">Date *</label>
                        <input
                            id="appointment_date"
                            type="date"
                            name="appointment_date"
                            class="form-control @error('appointment_date') is-invalid @enderror"
                            value="{{ old('appointment_date') }}"
                            min="{{ now()->format('Y-m-d') }}"
                            required
                        >
                        @error('appointment_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Appointment Time -->
                    <div class="col-md-6">
                        <label class="form-label" for="start_time">Time *</label>
                        <input
                            id="start_time"
                            type="time"
                            name="start_time"
                            class="form-control @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time', '09:00') }}"
                            required
                        >
                        @error('start_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label" for="notes">Additional Notes</label>
                        <textarea
                            id="notes"
                            name="notes"
                            class="form-control @error('notes') is-invalid @enderror"
                            rows="4"
                            placeholder="Any specific concerns or information we should know?"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex gap-3 justify-content-end mt-4">
                    <a href="{{ route('customer.appointments.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
