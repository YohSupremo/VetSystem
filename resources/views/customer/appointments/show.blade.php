@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Appointment Details - PawCare')

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

.appointment-details {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--soft-gray);
}

.appointment-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--dark-text);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: rgba(255, 193, 7, 0.1);
    color: #f59e0b;
}

.status-confirmed {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}

.status-in-progress {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.status-completed {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.detail-section {
    margin-bottom: 2rem;
}

.detail-section h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-section h3 i {
    color: var(--primary-purple);
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.detail-item {
    padding: 1rem;
    background: var(--soft-gray);
    border-radius: 0.5rem;
    border-left: 4px solid var(--primary-purple);
}

.detail-label {
    font-size: 0.875rem;
    color: var(--light-text);
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--dark-text);
}

.notes-section {
    background: var(--soft-gray);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
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
    .appointment-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
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
        <div class="appointment-details">
            <div class="appointment-header">
                <h2 class="appointment-title">Appointment Details</h2>
                <span class="status-badge status-{{ $appointment->status }}">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            <!-- Pet Information -->
            <div class="detail-section">
                <h3><i class="fas fa-paw"></i> Pet Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Pet Name</div>
                        <div class="detail-value">{{ $appointment->pet->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Species</div>
                        <div class="detail-value">{{ ucfirst($appointment->pet->species) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Breed</div>
                        <div class="detail-value">{{ $appointment->pet->breed ?? 'Not specified' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Registration Number</div>
                        <div class="detail-value">{{ $appointment->pet->registration_number ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Appointment Information -->
            <div class="detail-section">
                <h3><i class="fas fa-calendar"></i> Appointment Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Date & Time</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Type</div>
                        <div class="detail-value">{{ ucfirst($appointment->type) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">{{ ucfirst($appointment->status) }}</div>
                    </div>
                    @if($appointment->veterinarian)
                    <div class="detail-item">
                        <div class="detail-label">Veterinarian</div>
                        <div class="detail-value">Dr. {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if($appointment->notes)
            <div class="detail-section">
                <h3><i class="fas fa-notes-medical"></i> Notes</h3>
                <div class="notes-section">
                    {{ $appointment->notes }}
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('customer.appointments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Appointments
                </a>
                
                @if(in_array($appointment->status, ['pending', 'confirmed']))
                <a href="{{ route('customer.appointments.edit', $appointment->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Appointment
                </a>
                @endif
                
                @if(in_array($appointment->status, ['pending', 'confirmed']))
                <form action="{{ route('customer.appointments.cancel', $appointment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel Appointment
                    </button>
                </form>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
