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
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.page-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.appointment-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 1.5rem;
    padding: 1.5rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
}

.appointment-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(147, 51, 234, 0.15);
}

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
}

.appointment-info h4 {
    color: var(--primary-purple);
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.appointment-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6B7280;
}

.detail-item strong {
    color: var(--primary-purple);
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 0.85rem;
}

.badge-pending {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.badge-scheduled {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
}

.badge-in_progress {
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    color: #3730a3;
}

.badge-completed {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.badge-cancelled {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.btn-book {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border: none;
    border-radius: 1rem;
    padding: 0.85rem 2rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
    color: white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-book:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(147, 51, 234, 0.4);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 2rem;
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
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

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-purple);
    margin: 2rem 0 1rem 0;
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
        <div class="page-header">
            <h2>My Appointments</h2>
            <p>View and manage your pet appointments</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('customer.appointments.create') }}" class="btn-book">
                <span>📅</span> Book New Appointment
            </a>
        </div>

        @if($upcomingAppointments->count() > 0 || $pastAppointments->count() > 0)
            @if($upcomingAppointments->count() > 0)
                <h3 class="section-title">Upcoming Appointments</h3>
                @foreach($upcomingAppointments as $appointment)
                    <div class="appointment-card">
                        <div class="appointment-header">
                            <div class="appointment-info">
                                <h4>{{ $appointment->pet->name }}</h4>
                                <div class="appointment-details">
                                    <div class="detail-item">
                                        <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
                                    </div>
                                    <div class="detail-item">
                                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    </div>
                                    <div class="detail-item">
                                        <strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}
                                    </div>
                                    @if($appointment->veterinarian)
                                        <div class="detail-item">
                                            <strong>Veterinarian:</strong> {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}
                                        </div>
                                    @endif
                                    @if($appointment->notes)
                                        <div class="detail-item" style="flex-direction: column; align-items: flex-start; margin-top: 0.5rem; padding: 0.75rem; background: {{ $appointment->status === 'cancelled' ? 'rgba(254, 202, 202, 0.3)' : 'rgba(167, 139, 250, 0.1)' }}; border-radius: 0.75rem;">
                                            <strong style="margin-bottom: 0.25rem;">{{ $appointment->status === 'cancelled' ? '⚠️ Cancellation Reason' : '📝 Notes' }}:</strong>
                                            <span style="white-space: pre-wrap;">{{ $appointment->notes }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <span class="badge badge-{{ $appointment->status }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @endif

            @if($pastAppointments->count() > 0)
                <h3 class="section-title">Past Appointments</h3>
                @foreach($pastAppointments as $appointment)
                    <div class="appointment-card">
                        <div class="appointment-header">
                            <div class="appointment-info">
                                <h4>{{ $appointment->pet->name }}</h4>
                                <div class="appointment-details">
                                    <div class="detail-item">
                                        <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
                                    </div>
                                    <div class="detail-item">
                                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    </div>
                                    <div class="detail-item">
                                        <strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('g:i A') }}
                                    </div>
                                    @if($appointment->veterinarian)
                                        <div class="detail-item">
                                            <strong>Veterinarian:</strong> {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}
                                        </div>
                                    @endif
                                    @if($appointment->notes)
                                        <div class="detail-item" style="flex-direction: column; align-items: flex-start; margin-top: 0.5rem; padding: 0.75rem; background: {{ $appointment->status === 'cancelled' ? 'rgba(254, 202, 202, 0.3)' : 'rgba(167, 139, 250, 0.1)' }}; border-radius: 0.75rem;">
                                            <strong style="margin-bottom: 0.25rem;">{{ $appointment->status === 'cancelled' ? '⚠️ Cancellation Reason' : '📝 Notes' }}:</strong>
                                            <span style="white-space: pre-wrap;">{{ $appointment->notes }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <span class="badge badge-{{ $appointment->status }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📅</div>
                <h3>No Appointments Yet</h3>
                <p>Book your first appointment for your pet!</p>
                <a href="{{ route('customer.appointments.create') }}" class="btn-book mt-3">
                    <span>📅</span> Book Your First Appointment
                </a>
            </div>
        @endif
    </main>
</div>
@endsection
