@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', "{{ $pet->name }} - PawCare")

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

/* Pet Profile Card */
.pet-profile-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.pet-profile-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.pet-profile-body {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 2rem;
    text-align: center;
}

.pet-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    margin: 0 auto 1.5rem;
    object-fit: cover;
    transition: var(--transition-smooth);
}

.pet-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(31, 38, 135, 0.4);
}

.pet-name {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.pet-breed {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
}

.pet-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.pet-stat {
    text-align: center;
    padding: 0.75rem 1.25rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    transition: var(--transition-smooth);
    min-width: 100px;
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.pet-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}

.divider {
    width: 1px;
    background: rgba(255, 255, 255, 0.2);
}

/* Details Card */
.details-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.details-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.details-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1rem 1.5rem;
    font-weight: 700;
    color: #000;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.details-body {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
}

.detail-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    color: #555;
    font-weight: 500;
}

.detail-value {
    color: #000;
    font-weight: 700;
}

/* Action Buttons */
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
    width: 100%;
    justify-content: center;
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

/* History Card */
.history-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.history-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.history-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: between;
    align-items: center;
}

.history-title {
    font-weight: 700;
    color: #000;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0;
}

.history-body {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, var(--primary-purple), var(--pink));
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -2.5rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(147, 51, 234, 0.4);
}

.timeline-date {
    color: #555;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.timeline-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
}

.timeline-description {
    color: #333;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.timeline-vet {
    color: var(--primary-purple);
    font-size: 0.875rem;
    font-weight: 600;
}

/* Vaccination & Prescription Cards */
.vaccination-card, .prescription-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
    height: 100%;
}

.vaccination-card::before, .prescription-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.card-header-custom {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1rem 1.5rem;
    font-weight: 700;
    color: #000;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.card-body-custom {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
}

.vaccination-item, .prescription-item {
    padding: 1rem;
    margin-bottom: 1rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    transition: var(--transition-smooth);
}

.vaccination-item:hover, .prescription-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.vaccination-name, .prescription-name {
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
}

.vaccination-date, .prescription-details {
    color: #555;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
}

.status-active {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
    color: rgba(16, 185, 129, 0.9);
}

.status-completed {
    background: rgba(107, 114, 128, 0.2);
    border-color: rgba(107, 114, 128, 0.3);
    color: rgba(107, 114, 128, 0.9);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem;
    color: #333;
}

/* Link */
.btn-link {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 0.5rem 1rem;
    color: #000;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    transition: var(--transition-smooth);
}

.btn-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.3);
    color: #000;
    text-decoration: none;
}

@media (max-width: 768px) {
    .pet-profile-card, .details-card, .history-card, .vaccination-card, .prescription-card {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .pet-profile-body, .details-body, .history-body, .card-body-custom {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }
    
    .btn-action {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .pet-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .divider {
        width: 100%;
        height: 1px;
        margin: 0.5rem 0;
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
        <div class="row">
            <!-- Sidebar: Pet Profile -->
            <div class="col-lg-4 mb-4">
                <div class="pet-profile-card">
                    <div class="pet-profile-body">
                        <div class="mb-3 position-relative d-inline-block">
                            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="pet-avatar">
                        </div>
                        
                        <h2 class="pet-name">{{ $pet->name }}</h2>
                        <p class="pet-breed">{{ $pet->species }} • {{ $pet->breed }}</p>
                        
                        <div class="pet-stats">
                            <div class="pet-stat">
                                <div class="stat-value">{{ $pet->age }}</div>
                                <div class="stat-label">Age</div>
                            </div>
                            <div class="divider"></div>
                            <div class="pet-stat">
                                <div class="stat-value">{{ $pet->gender }}</div>
                                <div class="stat-label">Gender</div>
                            </div>
                            <div class="divider"></div>
                            <div class="pet-stat">
                                <div class="stat-value">{{ $pet->weight }} kg</div>
                                <div class="stat-label">Weight</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.pets.edit', $pet->id) }}" class="btn-action">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </a>
                            <a href="{{ route('pets.qr.public', $pet->id) }}" class="btn-action" target="_blank">
                                <i class="fas fa-qrcode me-2"></i>Medical QR Code
                            </a>
                            <a href="{{ route('customer.appointments.create', ['pet_id' => $pet->id]) }}" class="btn-action primary">
                                <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="details-card">
                    <div class="details-header">Details</div>
                    <div class="details-body">
                        <div class="detail-item">
                            <span class="detail-label">Birth Date</span>
                            <span class="detail-value">{{ date('M d, Y', strtotime($pet->dob)) }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Color</span>
                            <span class="detail-value">{{ $pet->color ?: 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Microchip/Reg #</span>
                            <span class="detail-value">{{ $pet->registration_number ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content: History -->
            <div class="col-lg-8">
                <div class="history-card">
                    <div class="history-header">
                        <h5 class="history-title">Recent Medical History</h5>
                        <a href="{{ route('customer.medical-records.pet', $pet->id) }}" class="btn-link">View All</a>
                    </div>
                    <div class="history-body">
                        @if($pet->medicalRecords->isEmpty())
                            <div class="empty-state">
                                <p>No medical records found.</p>
                            </div>
                        @else
                            <div class="timeline">
                                @foreach($pet->medicalRecords->take(3) as $record)
                                    <div class="timeline-item">
                                        <div class="timeline-date">{{ date('M d, Y', strtotime($record->visit_date)) }}</div>
                                        <h6 class="timeline-title">{{ $record->diagnosis ?: 'Checkup' }}</h6>
                                        <p class="timeline-description">{{ Str::limit($record->treatment, 100) }}</p>
                                        @if($record->veterinarian)
                                            <div class="timeline-vet">Dr. {{ $record->veterinarian->last_name }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="vaccination-card">
                            <div class="card-header-custom">Recent Vaccinations</div>
                            <div class="card-body-custom">
                                @if($pet->vaccinations->isEmpty())
                                    <div class="empty-state">
                                        <p>No vaccinations recorded.</p>
                                    </div>
                                @else
                                    @foreach($pet->vaccinations->take(3) as $vac)
                                        <div class="vaccination-item">
                                            <div class="vaccination-name">{{ $vac->vaccine_name }}</div>
                                            <div class="vaccination-date">{{ date('M d, Y', strtotime($vac->administered_date)) }}</div>
                                            <div class="vaccination-date">Next due: {{ $vac->next_due_date ? date('M d, Y', strtotime($vac->next_due_date)) : 'N/A' }}</div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="prescription-card">
                            <div class="card-header-custom">Active Prescriptions</div>
                            <div class="card-body-custom">
                                @if($pet->prescriptions->isEmpty())
                                    <div class="empty-state">
                                        <p>No prescriptions found.</p>
                                    </div>
                                @else
                                    @foreach($pet->prescriptions->take(3) as $presc)
                                        <div class="prescription-item">
                                            <div class="prescription-name">{{ $presc->medication_name }}</div>
                                            <div class="prescription-details">{{ $presc->dosage }} - {{ $presc->frequency }}</div>
                                            @if($presc->assignedStaff)
                                                <div class="prescription-details">Assigned staff: {{ $presc->assignedStaff->first_name }} {{ $presc->assignedStaff->last_name }}</div>
                                            @endif
                                            @if($presc->external_clinic_name || $presc->external_veterinarian_name)
                                                <div class="prescription-details">
                                                    External source:
                                                    {{ $presc->external_clinic_name ?: 'N/A' }}
                                                    @if($presc->external_veterinarian_name)
                                                        - {{ $presc->external_veterinarian_name }}
                                                    @endif
                                                </div>
                                            @endif
                                            @if($presc->end_date && $presc->end_date >= now())
                                                <div class="status-badge status-active">Active</div>
                                            @else
                                                <div class="status-badge status-completed">Completed</div>
                                            @endif
                                            <div class="mt-2">
                                                <a href="{{ route('customer.prescriptions.print', ['petId' => $pet->id, 'prescriptionId' => $presc->id]) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   target="_blank">
                                                    <i class="fas fa-print me-1"></i>Print Prescription
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
