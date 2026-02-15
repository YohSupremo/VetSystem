@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'My Appointments - PawCare')

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
    color: #64748B;
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
    color: #475569;
}

.filter-select {
    appearance: none;
    border: 1px solid rgba(15, 23, 42, 0.1);
    border-radius: 999px;
    padding: 0.6rem 2.5rem 0.6rem 1rem;
    background: white;
    font-weight: 600;
    color: #0F172A;
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
    background: #0F172A;
    color: white;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.filter-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px -10px rgba(15, 23, 42, 0.6);
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

/* Card Design */
.appointment-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.04);
}

.appointment-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
    color: #0F172A;
    margin: 0;
}

/* Status */
.status {
    padding: 0.4rem 0.875rem;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-pending {
    background: #FEF3C7;
    color: #78350F;
}

.status-scheduled {
    background: #DBEAFE;
    color: #1E3A8A;
}

.status-in_progress {
    background: #E0E7FF;
    color: #3730A3;
}

.status-completed {
    background: #D1FAE5;
    color: #064E3B;
}

.status-cancelled {
    background: #FEE2E2;
    color: #7F1D1D;
}

/* Type Badge */
.type-badge {
    display: inline-block;
    padding: 0.5rem 1.125rem;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.08), rgba(236, 72, 153, 0.08));
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--primary-purple);
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
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(236, 72, 153, 0.1));
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
    color: #94A3B8;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #0F172A;
}

/* Notes */
.notes {
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.05), rgba(236, 72, 153, 0.05));
    border-left: 3px solid var(--primary-purple);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.notes.cancelled {
    background: #FEF2F2;
    border-left-color: #DC2626;
}

.notes-title {
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    color: var(--primary-purple);
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.notes.cancelled .notes-title {
    color: #DC2626;
}

.notes-body {
    font-size: 0.875rem;
    color: #475569;
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
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-view {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
}

.btn-view:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    color: white;
}

.btn-edit {
    background: linear-gradient(135deg, #3B82F6, #06B6D4);
    color: white;
}

.btn-edit:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    color: white;
}

.btn-cancel {
    background: white;
    color: #DC2626;
    border: 2px solid #DC2626;
}

.btn-cancel:hover {
    background: #DC2626;
    color: white;
}

/* Alert */
.alert {
    background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
    border-left: 4px solid #10B981;
    color: #064E3B;
    padding: 1.125rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    font-weight: 600;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);
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

/* Empty State */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.empty-icon {
    font-size: 5rem;
    opacity: 0.2;
    margin-bottom: 1.5rem;
}

.empty-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0F172A;
    margin-bottom: 0.625rem;
}

.empty-text {
    font-size: 1.0625rem;
    color: #64748B;
    margin-bottom: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .appointments-grid {
        grid-template-columns: 1fr;
    }
    
    .hero-title {
        font-size: 2.25rem;
    }
    
    .pet-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .card-actions {
        grid-template-columns: 1fr;
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
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
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