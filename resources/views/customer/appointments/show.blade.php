@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', "Appointment Details - PawCare")

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

/* Appointment Container */
.appointment-container {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.appointment-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

/* Header */
.appointment-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem;
    display: flex;
    justify-content: between;
    align-items: center;
}

/* Content Sections */
.content-section {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #000;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
}

/* DateTime Display */
.datetime-display {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 1.25rem;
    transition: var(--transition-smooth);
}

.datetime-display:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.datetime-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
}

.datetime-time {
    font-size: 1.25rem;
    font-weight: 600;
    color: #000;
    margin-bottom: 0;
}

/* Type Display */
.type-display {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 1.25rem;
    transition: var(--transition-smooth);
}

.type-display:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.type-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0;
}

/* Pet Info Card */
.pet-info-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    transition: var(--transition-smooth);
}

.pet-info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.pet-avatar {
    border: 3px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.2);
    margin-right: 1rem;
}

.pet-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.25rem;
}

.pet-details {
    color: #555;
    font-size: 0.875rem;
    margin-bottom: 0;
}

/* Veterinarian Card */
.vet-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    transition: var(--transition-smooth);
}

.vet-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.vet-avatar {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    margin-right: 1rem;
}

.vet-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0;
}

/* Notes Card */
.notes-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.5rem;
    border-left: 4px solid rgba(59, 130, 246, 0.5);
    transition: var(--transition-smooth);
}

.notes-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.notes-content {
    color: #000;
    line-height: 1.6;
    margin: 0;
}

/* Status Badge */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
}

.status-confirmed {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
    color: rgba(16, 185, 129, 0.9);
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgba(239, 68, 68, 0.9);
}

.status-completed {
    background: rgba(147, 51, 234, 0.2);
    border-color: rgba(147, 51, 234, 0.3);
    color: rgba(147, 51, 234, 0.9);
}

.status-pending {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.3);
    color: rgba(245, 158, 11, 0.9);
}

/* Buttons */
.btn-action {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 0.75rem 1.5rem;
    color: #000;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition-smooth);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-action:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.3);
    color: #000;
    text-decoration: none;
}

.btn-action.primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border: none;
}

.btn-action.primary:hover {
    color: white;
}

.btn-action.danger {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgba(239, 68, 68, 0.9);
}

.btn-action.danger:hover {
    background: rgba(239, 68, 68, 0.3);
    border-color: rgba(239, 68, 68, 0.4);
    color: rgba(239, 68, 68, 0.9);
}

/* Alert */
.alert {
    background: rgba(16, 185, 129, 0.2);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-left: 4px solid rgba(16, 185, 129, 0.5);
    border: 1px solid rgba(16, 185, 129, 0.3);
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

/* Back Button */
.btn-back {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 0.75rem 1.5rem;
    color: #000;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition-smooth);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-back:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.3);
    color: #000;
    text-decoration: none;
}

/* Divider */
.divider {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .appointment-container {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .appointment-header {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .content-section, .datetime-display, .type-display, .pet-info-card, .vet-card, .notes-card {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .btn-action {
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
            <a href="{{ route('customer.appointments.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title">Appointment Details</h1>
        </div>

        <div class="appointment-container">
            <div class="appointment-header">
                <div class="d-flex align-items-center">
                    <h1 class="page-title mb-0">Appointment Details</h1>
                </div>
                <div>
                    @if($appointment->status === 'confirmed')
                        <span class="status-badge status-confirmed">Confirmed</span>
                    @elseif($appointment->status === 'cancelled')
                        <span class="status-badge status-cancelled">Cancelled</span>
                    @elseif($appointment->status === 'completed')
                        <span class="status-badge status-completed">Completed</span>
                    @else
                        <span class="status-badge status-pending">Pending Confirmation</span>
                    @endif
                </div>
            </div>
            
            <div class="p-4">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="datetime-display">
                            <h5 class="section-title">Date & Time</h5>
                            <p class="datetime-title">
                                {{ date('l, F d, Y', strtotime($appointment->appointment_date)) }}
                            </p>
                            <p class="datetime-time">
                                <i class="far fa-clock me-2"></i>{{ date('h:i A', strtotime($appointment->appointment_date)) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="type-display">
                            <h5 class="section-title">Appointment Type</h5>
                            <p class="type-title">
                                {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h5 class="section-title">Pet Information</h5>
                    <div class="pet-info-card">
                        <img src="{{ $appointment->pet->photo_url }}" alt="{{ $appointment->pet->name }}" class="pet-avatar rounded-circle" width="60" height="60" style="object-fit: cover;">
                        <div>
                            <h5 class="pet-name">{{ $appointment->pet->name }}</h5>
                            <p class="pet-details">
                                {{ $appointment->pet->species }} • {{ $appointment->pet->breed }} • {{ $appointment->pet->age }} old
                            </p>
                        </div>
                    </div>
                </div>

                @if($appointment->veterinarian)
                    <div class="content-section">
                        <h5 class="section-title">Assigned Veterinarian</h5>
                        <div class="vet-card">
                            <div class="vet-avatar">Dr</div>
                            <div>
                                <p class="vet-name">Dr. {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($appointment->notes)
                    <div class="content-section">
                        <h5 class="section-title">Your Notes</h5>
                        <div class="notes-card">
                            <p class="notes-content">{{ $appointment->notes }}</p>
                        </div>
                    </div>
                @endif

                <div class="divider"></div>

                <div class="d-flex justify-content-end gap-2">
                    @if($appointment->status === 'pending')
                        <a href="{{ route('customer.appointments.edit', $appointment->id) }}" class="btn-action">
                            <i class="fas fa-edit me-2"></i>Edit Request
                        </a>
                        <form action="{{ route('customer.appointments.cancel', $appointment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                            @csrf
                            <button type="submit" class="btn-action danger">
                                <i class="fas fa-times me-2"></i>Cancel Appointment
                            </button>
                        </form>
                    @endif
                    
                    @if($appointment->status === 'completed' || $appointment->status === 'cancelled')
                        <a href="{{ route('customer.appointments.create') }}" class="btn-action primary">
                            <i class="fas fa-redo me-2"></i>Book Again
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
