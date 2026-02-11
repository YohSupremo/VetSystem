@extends('veterinarian.layout')

@section('title', 'Veterinarian Dashboard - PawCare')

@section('content')
<!-- Stats Cards -->
<section class="stats-section mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="stat-card waiting">
                <div class="stat-icon">⏰</div>
                <div class="stat-content">
                    <h3>{{ $queueStats['waiting'] }}</h3>
                    <p>Waiting</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card progress">
                <div class="stat-icon">👨‍⚕️</div>
                <div class="stat-content">
                    <h3>{{ $queueStats['in_progress'] }}</h3>
                    <p>In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card completed">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <h3>{{ $queueStats['completed'] }}</h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card total">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <h3>{{ $todayAppointments->count() }}</h3>
                    <p>Total Today</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Medical Stats Cards -->
<section class="stats-section mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="stat-card vaccination">
                <div class="stat-icon">💉</div>
                <div class="stat-content">
                    <h3>{{ $vaccinationStats['total'] }}</h3>
                    <p>Total Vaccinations</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card vaccination-today">
                <div class="stat-icon">🏥</div>
                <div class="stat-content">
                    <h3>{{ $vaccinationStats['today'] }}</h3>
                    <p>Today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card lab-total">
                <div class="stat-icon">🔬</div>
                <div class="stat-content">
                    <h3>{{ $labStats['total'] }}</h3>
                    <p>Lab Tests</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card lab-pending">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <h3>{{ $labStats['pending'] }}</h3>
                    <p>Pending</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="row g-4">
    <!-- Today's Appointments -->
    <section class="col-lg-7">
        <div class="content-card">
            <div class="section-header">
                <h2 class="mb-3">Today's Appointments</h2>
                <a href="#" class="btn-action">View All</a>
            </div>
            
            @if($todayAppointments->count() > 0)
                <div class="appointments-list">
                    @foreach($todayAppointments as $appointment)
                        <div class="appointment-item">
                            <div class="pet-avatar">🐕</div>
                            <div class="item-details flex-grow-1">
                                <h4>{{ $appointment->pet->name }}</h4>
                                <p>{{ $appointment->pet->owner->first_name }} {{ $appointment->pet->owner->last_name }}</p>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <span class="text-sm">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $appointment->start_time->format('g:i A') }}
                                    </span>
                                    <span class="status-badge {{ $appointment->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                    </span>
                                </div>
                                @if($appointment->reason)
                                    <p class="mt-2 mb-0">{{ $appointment->reason }}</p>
                                @endif
                            </div>
                            <a href="#" class="btn-secondary">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">📅</div>
                    <h3>No appointments scheduled for today</h3>
                    <p>Enjoy your free time!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Recent Patients -->
    <section class="col-lg-5">
        <div class="content-card">
            <div class="section-header">
                <h2 class="mb-3">Recent Patients</h2>
                <a href="#" class="btn-action">View All</a>
            </div>
            
            @if($recentPatients->count() > 0)
                <div class="patients-list">
                    @foreach($recentPatients as $patient)
                        <div class="patient-item">
                            <div class="pet-avatar">🐾</div>
                            <div class="item-details flex-grow-1">
                                <h4>{{ $patient->name }}</h4>
                                <p>{{ $patient->species }} • {{ $patient->breed }}</p>
                                <p class="text-sm">{{ $patient->owner->first_name }} {{ $patient->owner->last_name }}</p>
                            </div>
                            <a href="#" class="btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">🐾</div>
                    <h3>No recent patients</h3>
                    <p>Your recent appointments will appear here</p>
                </div>
            @endif
        </div>
    </section>
</div>

<!-- Upcoming Appointments -->
@if($upcomingAppointments->count() > 0)
<section class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Upcoming Appointments</h2>
    </div>
    <div class="row g-3">
        @foreach($upcomingAppointments as $appointment)
            <div class="col-md-6 col-lg-4">
                <div class="content-card" style="padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-0">{{ $appointment->pet->name }}</h5>
                        <span class="badge bg-light text-dark">{{ $appointment->appointment_date->format('M j') }}</span>
                    </div>
                    <p class="mb-2"><strong>{{ $appointment->start_time->format('g:i A') }}</strong></p>
                    <p class="text-muted mb-2">{{ $appointment->pet->owner->first_name }} {{ $appointment->pet->owner->last_name }}</p>
                    @if($appointment->reason)
                        <p class="small">{{ $appointment->reason }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
.stat-card.vaccination {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card.vaccination-today {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card.lab-total {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card.lab-pending {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
</style>
@endpush
