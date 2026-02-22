@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'My Appointments - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.customer-header {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
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
    padding: 3rem 2rem;
    max-width: 1300px;
    margin: 0 auto;
}

/* Hero Section */
.hero-section {
    text-align: center;
    margin-bottom: 3.5rem;
}

.hero-title {
    font-size: 3rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--pink) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.75rem;
    letter-spacing: -0.03em;
}

.hero-subtitle {
    font-size: 1.15rem;
    color: #333;
    font-weight: 500;
}

/* Action Bar */
.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-form {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 700;
    color: #333;
}

.filter-select {
    appearance: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 999px;
    padding: 0.6rem 2.5rem 0.6rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    font-weight: 600;
    color: #000;
    position: relative;
}

.filter-select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
    border-color: rgba(147, 51, 234, 0.5);
}

.filter-btn {
    border: none;
    padding: 0.65rem 1.25rem;
    border-radius: 999px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
    cursor: pointer;
    transition: var(--transition-smooth);
}

.filter-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px -10px rgba(31, 38, 135, 0.4);
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    box-shadow: 0 10px 25px -5px rgba(147, 51, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 35px -5px rgba(147, 51, 234, 0.4);
    color: white;
}

/* Grid Layout */
.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

/* Card Design with Glassmorphism */
.appointment-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    overflow: hidden;
    transition: var(--transition-smooth);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
}

.appointment-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    pointer-events: none;
}

.appointment-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 60px rgba(31, 38, 135, 0.4);
    border-color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
}

/* Card Top Bar */
.card-topbar {
    height: 6px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

/* Card Content */
.card-content {
    padding: 1.75rem;
}

/* Pet Header */
.pet-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.pet-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.pet-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.pet-name {
    font-size: 1.375rem;
    font-weight: 800;
    color: #000;
    margin: 0;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
}

/* Status with Glassmorphism */
.status {
    padding: 0.4rem 0.875rem;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
}

.status-pending {
    background: rgba(254, 243, 199, 0.3);
    border-color: rgba(254, 243, 199, 0.5);
    color: rgba(120, 53, 15, 0.9);
}

.status-scheduled {
    background: rgba(219, 234, 254, 0.3);
    border-color: rgba(219, 234, 254, 0.5);
    color: rgba(30, 58, 138, 0.9);
}

.status-in_progress {
    background: rgba(224, 231, 255, 0.3);
    border-color: rgba(224, 231, 255, 0.5);
    color: rgba(55, 48, 163, 0.9);
}

.status-completed {
    background: rgba(209, 250, 229, 0.3);
    border-color: rgba(209, 250, 229, 0.5);
    color: rgba(6, 78, 59, 0.9);
}

.status-cancelled {
    background: rgba(254, 226, 226, 0.3);
    border-color: rgba(254, 226, 226, 0.5);
    color: rgba(127, 29, 29, 0.9);
}

/* Type Badge with Glassmorphism */
.type-badge {
    display: inline-block;
    padding: 0.5rem 1.125rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 1.5rem;
}

/* Info Grid */
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
}

.info-icon {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.info-text {
    flex: 1;
    padding-top: 0.25rem;
}

.info-label {
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #555;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #000;
}

/* Notes with Glassmorphism */
.notes {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-left: 3px solid var(--primary-purple);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.notes.cancelled {
    background: rgba(254, 226, 226, 0.2);
    border-left-color: rgba(220, 38, 38, 0.5);
    border-color: rgba(254, 226, 226, 0.3);
}

.notes-title {
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    color: #000;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.notes.cancelled .notes-title {
    color: rgba(220, 38, 38, 0.9);
}

.notes-body {
    font-size: 0.875rem;
    color: #333;
    line-height: 1.6;
    white-space: pre-wrap;
}

/* Actions */
.card-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.card-actions.single {
    grid-template-columns: 1fr;
}

.btn-action {
    padding: 0.75rem;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    transition: var(--transition-smooth);
    border: none;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
}

.btn-view {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
}

.btn-view:hover {
    opacity: 0.9;
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
    color: white;
}

.btn-edit {
    background: linear-gradient(135deg, #3B82F6, #06B6D4);
    color: white;
}

.btn-edit:hover {
    opacity: 0.9;
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    color: white;
}

.btn-cancel {
    background: rgba(220, 38, 38, 0.2);
    color: rgba(220, 38, 38, 0.9);
    border: 1px solid rgba(220, 38, 38, 0.3);
}

.btn-cancel:hover {
    background: rgba(220, 38, 38, 0.3);
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3);
    color: #000;
}

/* Alert with Glassmorphism */
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

/* Section Header */
.section-header {
    margin: 3.5rem 0 2rem 0;
    position: relative;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: inline-block;
    margin: 0;
}

.section-line {
    height: 4px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink), transparent);
    border-radius: 2px;
    margin-top: 0.75rem;
}

/* Empty State with Glassmorphism */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
}

.empty-state::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 4px 10px rgba(147, 51, 234, 0.3));
}

.empty-title {
    font-size: 1.75rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.625rem;
}

.empty-text {
    font-size: 1.0625rem;
    color: #333;
    margin-bottom: 2rem;
}

/* Responsive */
@media (max-width: 1024px) {
    .customer-container {
        padding: 1.5rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .page-subtitle {
        font-size: 1rem;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .appointments-grid {
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
    }
    
    .appointment-card {
        padding: 1.5rem;
        min-height: 200px;
    }
    
    .appointment-header {
        margin-bottom: 1rem;
    }
    
    .appointment-title {
        font-size: 1.25rem;
    }
    
    .appointment-meta {
        font-size: 0.875rem;
    }
    
    .appointment-description {
        font-size: 0.875rem;
    }
    
    .card-actions {
        gap: 0.75rem;
    }
    
    .btn-action {
        padding: 0.75rem 1.25rem;
        font-size: 0.875rem;
    }
    
    .empty-state {
        padding: 3rem;
    }
    
    .empty-text {
        font-size: 1rem;
    }
}

@media (max-width: 768px) {
    .customer-container {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1rem;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.75rem;
    }
    
    .page-subtitle {
        font-size: 0.9rem;
    }
    
    .hero-title {
        font-size: 2.25rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .appointments-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .appointment-card {
        padding: 1.25rem;
        min-height: 180px;
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .appointment-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .appointment-title {
        font-size: 1.125rem;
    }
    
    .appointment-meta {
        font-size: 0.75rem;
    }
    
    .appointment-description {
        font-size: 0.75rem;
    }
    
    .card-actions {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    .filter-btn {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-purple), var(--pink)) !important;
        color: white !important;
    }
    
    .btn-action {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .empty-state {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
}

@media (max-width: 480px) {
    .customer-main {
        padding: 2rem 1rem;
    }
    
    .hero-title {
        font-size: 2rem;
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
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
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">My Appointments</h1>
            <p class="hero-subtitle">Manage your pet care schedule</p>
        </div>

        @if(session('success'))
            <div class="alert">
                <span style="font-size: 1.25rem;">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Action Bar -->
        <div class="action-bar">
            <form class="filter-form" method="GET" action="{{ route('customer.appointments.index') }}">
                <label class="filter-label" for="status">Status</label>
                <select class="filter-select" name="status" id="status">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <button class="filter-btn" type="submit">Apply</button>
            </form>
            <a href="{{ route('customer.appointments.create') }}" class="btn-primary">
                <span>+</span>
                <span>New Appointment</span>
            </a>
        </div>

        @if($upcomingAppointments->count() > 0 || $pastAppointments->count() > 0)
            
            <!-- Upcoming Appointments -->
            @if($upcomingAppointments->count() > 0)
                <div class="section-header">
                    <h2 class="section-title">Upcoming</h2>
                    <div class="section-line"></div>
                </div>
                
                <div class="appointments-grid">
                    @foreach($upcomingAppointments as $appointment)
                        <div class="appointment-card">
                            <div class="card-topbar"></div>
                            
                            <div class="card-content">
                                <div class="pet-header">
                                    <div class="pet-title">
                                        <div class="pet-icon">🐾</div>
                                        <h3 class="pet-name">{{ $appointment->pet->name }}</h3>
                                    </div>
                                    <span class="status status-{{ $appointment->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                    </span>
                                </div>

                                <div class="type-badge">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
                                </div>

                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-icon">📅</div>
                                        <div class="info-text">
                                            <div class="info-label">Date</div>
                                            <div class="info-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-icon">🕐</div>
                                        <div class="info-text">
                                            <div class="info-label">Time</div>
                                            <div class="info-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</div>
                                        </div>
                                    </div>

                                    @if($appointment->veterinarian)
                                        <div class="info-item">
                                            <div class="info-icon">👨‍⚕️</div>
                                            <div class="info-text">
                                                <div class="info-label">Veterinarian</div>
                                                <div class="info-value">Dr. {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($appointment->notes)
                                    <div class="notes {{ $appointment->status === 'cancelled' ? 'cancelled' : '' }}">
                                        <div class="notes-title">
                                            {{ $appointment->status === 'cancelled' ? '⚠ Cancellation Reason' : '📝 Notes' }}
                                        </div>
                                        <div class="notes-body">{{ $appointment->notes }}</div>
                                    </div>
                                @endif

                                <div class="card-actions {{ $appointment->status === 'pending' || $appointment->status === 'scheduled' ? '' : 'single' }}">
                                    <a href="{{ route('customer.appointments.show', $appointment->id) }}" class="btn-action btn-view">
                                        View
                                    </a>

                                    @if($appointment->status === 'pending')
                                        <a href="{{ route('customer.appointments.edit', $appointment->id) }}" class="btn-action btn-edit">
                                            Edit
                                        </a>
                                    @elseif($appointment->status === 'scheduled')
                                        <form action="{{ route('customer.appointments.cancel', $appointment->id) }}" method="POST" style="display: contents;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn-action btn-cancel" onclick="return confirm('Cancel this appointment?');">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Past Appointments -->
            @if($pastAppointments->count() > 0)
                <div class="section-header">
                    <h2 class="section-title">Past</h2>
                    <div class="section-line"></div>
                </div>
                
                <div class="appointments-grid">
                    @foreach($pastAppointments as $appointment)
                        <div class="appointment-card">
                            <div class="card-topbar"></div>
                            
                            <div class="card-content">
                                <div class="pet-header">
                                    <div class="pet-title">
                                        <div class="pet-icon">🐾</div>
                                        <h3 class="pet-name">{{ $appointment->pet->name }}</h3>
                                    </div>
                                    <span class="status status-{{ $appointment->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                    </span>
                                </div>

                                <div class="type-badge">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
                                </div>

                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-icon">📅</div>
                                        <div class="info-text">
                                            <div class="info-label">Date</div>
                                            <div class="info-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</div>
                                        </div>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-icon">🕐</div>
                                        <div class="info-text">
                                            <div class="info-label">Time</div>
                                            <div class="info-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</div>
                                        </div>
                                    </div>

                                    @if($appointment->veterinarian)
                                        <div class="info-item">
                                            <div class="info-icon">👨‍⚕️</div>
                                            <div class="info-text">
                                                <div class="info-label">Veterinarian</div>
                                                <div class="info-value">Dr. {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($appointment->notes)
                                    <div class="notes {{ $appointment->status === 'cancelled' ? 'cancelled' : '' }}">
                                        <div class="notes-title">
                                            {{ $appointment->status === 'cancelled' ? '⚠ Cancellation Reason' : '📝 Notes' }}
                                        </div>
                                        <div class="notes-body">{{ $appointment->notes }}</div>
                                    </div>
                                @endif

                                <div class="card-actions single">
                                    <a href="{{ route('customer.appointments.show', $appointment->id) }}" class="btn-action btn-view">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <h3 class="empty-title">No Appointments Yet</h3>
                <p class="empty-text">Start by booking your first appointment</p>
                <a href="{{ route('customer.appointments.create') }}" class="btn-primary">
                    <span>+</span>
                    <span>Book Appointment</span>
                </a>
            </div>
        @endif
    </main>
</div>
@endsection