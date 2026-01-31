@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Customer Dashboard - PawCare')

@push('styles')
<style>
/* PawCare Customer Dashboard Styling */
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

.welcome-text {
    color: var(--primary-purple);
    font-weight: 500;
    font-size: 1.1rem;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.logout-btn {
    color: var(--primary-purple);
    text-decoration: none;
    font-weight: 500;
    padding: 0.6rem 1.2rem;
    border: 2px solid var(--light-purple);
    border-radius: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
}

.logout-btn:hover {
    background: var(--light-purple);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.2);
    color: var(--primary-purple);
}

.customer-main {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Stats Cards - PawCare Style */
.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(147, 51, 234, 0.2);
}

.stat-icon {
    font-size: 3rem;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1.5rem;
}

.stat-card.pets .stat-icon {
    background: linear-gradient(135deg, #10b981, #34d399);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
}

.stat-card.appointments .stat-icon {
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
}

.stat-card.health .stat-icon {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.stat-content h3 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.stat-content p {
    color: #6B7280;
    font-weight: 500;
    font-size: 1rem;
}

/* Quick Actions - PawCare Style */
.quick-actions h2 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 2rem;
    text-align: center;
}

.action-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 2.5rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: block;
    text-align: center;
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.action-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(147, 51, 234, 0.2);
    text-decoration: none;
    color: inherit;
}

.action-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1.5rem;
    background: linear-gradient(135deg, var(--light-purple), var(--secondary-purple));
    box-shadow: 0 8px 20px rgba(168, 85, 247, 0.3);
    margin: 0 auto 1.5rem;
}

.action-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.75rem;
}

.action-card p {
    color: #6B7280;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Section Headers */
.section-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.btn-add, .btn-view-all {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    text-decoration: none;
    padding: 0.75rem 1.5rem;
    border-radius: 1rem;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
}

.btn-add:hover, .btn-view-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
    text-decoration: none;
    color: white;
}

/* Pet and Appointment Cards */
.pet-card, .appointment-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 1.5rem;
    padding: 1.5rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
}

.pet-card:hover, .appointment-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(147, 51, 234, 0.15);
}

.pet-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--light-purple);
    box-shadow: 0 4px 15px rgba(167, 139, 250, 0.2);
}

.pet-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.default-avatar {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--light-purple);
    font-size: 1.5rem;
}

.pet-details h3, .appointment-details h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.pet-details p, .appointment-details p {
    color: #6B7280;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.appointment-date {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border-radius: 1rem;
    padding: 1rem;
    min-width: 70px;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
}

.date-day {
    font-size: 1.8rem;
    font-weight: 700;
    line-height: 1;
}

.date-month {
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    margin-top: 0.25rem;
}

.btn-view {
    background: transparent;
    color: var(--primary-purple);
    border: 2px solid var(--light-purple);
    padding: 0.6rem 1.2rem;
    border-radius: 1rem;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.btn-view:hover {
    background: var(--light-purple);
    text-decoration: none;
    color: var(--primary-purple);
    transform: translateY(-2px);
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 1rem;
}

.empty-state p {
    color: #6B7280;
    margin-bottom: 2rem;
    font-size: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    text-decoration: none;
    padding: 1rem 2rem;
    border-radius: 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
    text-decoration: none;
    color: white;
}

/* Recent Activity */
.recent-activity {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 2.5rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.recent-activity h2 {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 2rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(167, 139, 250, 0.1);
    border-radius: 1.5rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(167, 139, 250, 0.1);
}

.activity-item:hover {
    background: rgba(167, 139, 250, 0.15);
    transform: translateY(-2px);
}

.activity-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.activity-content h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.activity-content p {
    color: #6B7280;
    font-size: 0.9rem;
}

/* Status Badges */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-badge.pending {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.status-badge.confirmed {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.status-badge.in_progress {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
}

/* Responsive */
@media (max-width: 768px) {
    .customer-header {
        padding: 1rem;
    }
    
    .customer-main {
        padding: 1rem;
    }
    
    .stat-card, .action-card {
        padding: 1.5rem;
    }
    
    .logo-section h1 {
        font-size: 1.5rem;
    }
    
    .welcome-text {
        font-size: 0.9rem;
    }
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
        <!-- Stats Cards -->
        <section class="stats-section mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="stat-card pets">
                        <div class="stat-icon">🐕</div>
                        <div class="stat-content">
                            <h3>{{ $petCount }}</h3>
                            <p>My Pets</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card appointments">
                        <div class="stat-icon">📅</div>
                        <div class="stat-content">
                            <h3>{{ $upcomingCount }}</h3>
                            <p>Upcoming Appointments</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card health">
                        <div class="stat-icon">💊</div>
                        <div class="stat-content">
                            <h3>Active</h3>
                            <p>Treatments</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="quick-actions mb-4">
            <h2 class="mb-3">Quick Actions</h2>
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <a href="#" class="action-card text-decoration-none">
                        <div class="action-icon">📅</div>
                        <h3>Book Appointment</h3>
                        <p>Schedule a visit for your pet</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="#" class="action-card text-decoration-none">
                        <div class="action-icon">🐾</div>
                        <h3>Add Pet</h3>
                        <p>Register a new pet</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="#" class="action-card text-decoration-none">
                        <div class="action-icon">📋</div>
                        <h3>Medical Records</h3>
                        <p>View pet health history</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="#" class="action-card text-decoration-none">
                        <div class="action-icon">💳</div>
                        <h3>Billing</h3>
                        <p>View invoices and payments</p>
                    </a>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <!-- My Pets Section -->
            <section class="col-lg-6">
                <div class="pets-section">
                    <div class="section-header d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">My Pets</h2>
                        <a href="#" class="btn-add">+ Add New Pet</a>
                    </div>
                <div class="pets-list">
                    @if($pets->count() > 0)
                        @foreach($pets as $pet)
                            <div class="pet-card">
                                <div class="pet-info">
                                    <div class="pet-avatar">
                                        @if($pet->photo_path)
                                            <img src="{{ asset($pet->photo_path) }}" alt="{{ $pet->name }}">
                                        @else
                                            <div class="default-avatar">🐾</div>
                                        @endif
                                    </div>
                                    <div class="pet-details">
                                        <h3>{{ $pet->name }}</h3>
                                        <p>{{ $pet->species }} • {{ $pet->breed ?? 'Mixed' }}</p>
                                        @if($pet->birth_date)
                                            <p class="age">{{ \Carbon\Carbon::parse($pet->birth_date)->age }} years old</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="pet-actions">
                                    <a href="#" class="btn-view">View Details</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">🐾</div>
                            <h3>No pets registered yet</h3>
                            <p>Add your first pet to get started</p>
                            <a href="#" class="btn-primary">Add Your First Pet</a>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Upcoming Appointments -->
            <section class="col-lg-6">
                <div class="appointments-section">
                    <div class="section-header d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Upcoming Appointments</h2>
                        <a href="#" class="btn-view-all">View All</a>
                    </div>
                <div class="appointments-list">
                    @if($upcomingAppointments->count() > 0)
                        @foreach($upcomingAppointments as $appointment)
                            <div class="appointment-card">
                                <div class="appointment-date">
                                    <div class="date-day">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</div>
                                    <div class="date-month">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</div>
                                </div>
                                <div class="appointment-details">
                                    <h4>{{ $appointment->type }}</h4>
                                    <p>{{ $appointment->pet->name }}</p>
                                    <p class="time">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('h:i A') }}</p>
                                </div>
                                <div class="appointment-status">
                                    <span class="status-badge {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">📅</div>
                            <h3>No upcoming appointments</h3>
                            <p>Schedule your next visit</p>
                            <a href="#" class="btn-primary">Book Appointment</a>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Recent Activity -->
        @if($recentAppointments->count() > 0)
        <section class="recent-activity mb-4">
            <h2 class="mb-3">Recent Visits</h2>
            <div class="activity-list">
                @foreach($recentAppointments as $appointment)
                    <div class="activity-item">
                        <div class="activity-icon">✓</div>
                        <div class="activity-content">
                            <h4>{{ $appointment->type }} - {{ $appointment->pet->name }}</h4>
                            <p>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                        </div>
                        <a href="#" class="btn-view">View Details</a>
                    </div>
                @endforeach
            </div>
        </section>
        @endif
    </main>
</div>
@endsection
