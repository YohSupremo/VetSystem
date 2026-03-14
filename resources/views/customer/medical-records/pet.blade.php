@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', "{{ $pet->name }}'s Medical History - PawCare")

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

/* Medical Records Container */
.medical-records-container {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.medical-records-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

/* Navigation Tabs */
.nav-tabs {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.nav-tabs .nav-link {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px 12px 0 0;
    padding: 0.75rem 1.25rem;
    color: #000;
    font-weight: 600;
    transition: var(--transition-smooth);
    text-decoration: none;
    border-bottom: none;
}

.nav-tabs .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border: none;
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
    margin-bottom: 2rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -2.5rem;
    top: 0.5rem;
    width: 16px;
    height: 16px;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(147, 51, 234, 0.4);
}

.record-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.5rem;
    transition: var(--transition-smooth);
}

.record-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.record-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
}

.record-meta {
    color: #555;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.record-description {
    color: #333;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.btn-record {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    color: #000;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--transition-smooth);
}

.btn-record:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

/* Tables */
.glass-table {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 12px;
    overflow: hidden;
}

.glass-table th {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
    padding: 1rem;
}

.glass-table td {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: #000;
    padding: 1rem;
    vertical-align: middle;
}

.glass-table tbody tr:hover td {
    background: rgba(255, 255, 255, 0.1);
}

/* Status Badges */
.status-badge {
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

.status-success {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
    color: rgba(16, 185, 129, 0.9);
}

.status-danger {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgba(239, 68, 68, 0.9);
}

.status-warning {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.3);
    color: rgba(245, 158, 11, 0.9);
}

.status-info {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.3);
    color: rgba(59, 130, 246, 0.9);
}

/* Prescription Cards */
.prescription-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.5rem;
    transition: var(--transition-smooth);
    height: 100%;
}

.prescription-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.prescription-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
}

.prescription-meta {
    color: #555;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.prescription-input {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 0.5rem;
    color: #000;
    font-weight: 600;
}

/* Condition/Allergy Cards */
.condition-card, .allergy-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 1.5rem;
    transition: var(--transition-smooth);
    margin-bottom: 1rem;
}

.condition-card:hover, .allergy-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.condition-title, .allergy-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
}

.btn-small {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    color: #000;
    font-weight: 600;
    font-size: 0.75rem;
    transition: var(--transition-smooth);
}

.btn-small:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
}

.btn-small.danger {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgba(239, 68, 68, 0.9);
}

.btn-small.danger:hover {
    background: rgba(239, 68, 68, 0.3);
    border-color: rgba(239, 68, 68, 0.4);
    color: rgba(239, 68, 68, 0.9);
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: #333;
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

/* Pet Avatar */
.pet-avatar {
    border: 3px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.2);
}

@media (max-width: 768px) {
    .medical-records-container {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .nav-tabs .nav-link {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .record-card, .prescription-card, .condition-card, .allergy-card {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .btn-record, .btn-small {
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
        <div class="page-header mb-5 d-flex align-items-center">
            <a href="{{ route('customer.medical-records.index') }}" class="btn-back me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex-grow-1">
                <h1 class="page-title">{{ $pet->name }}'s Medical History</h1>
                <p class="page-subtitle">Complete timeline of visits and treatments</p>
            </div>
            <div class="pet-avatar">
                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="rounded-circle" width="60" height="60" style="object-fit: cover;">
            </div>
        </div>

        <div class="medical-records-container">
            <div class="p-4">
                <ul class="nav nav-tabs" id="recordTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="records-tab" data-bs-toggle="tab" data-bs-target="#records" type="button" role="tab" aria-controls="records" aria-selected="true">
                            Medical Visits
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="vaccinations-tab" data-bs-toggle="tab" data-bs-target="#vaccinations" type="button" role="tab" aria-controls="vaccinations" aria-selected="false">
                            Vaccinations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab" aria-controls="prescriptions" aria-selected="false">
                            Prescriptions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="chronic-allergies-tab" data-bs-toggle="tab" data-bs-target="#chronic-allergies" type="button" role="tab" aria-controls="chronic-allergies" aria-selected="false">
                            Chronic & Allergies
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="surgeries-tab" data-bs-toggle="tab" data-bs-target="#surgeries" type="button" role="tab" aria-controls="surgeries" aria-selected="false">
                            Surgeries
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="labs-tab" data-bs-toggle="tab" data-bs-target="#labs" type="button" role="tab" aria-controls="labs" aria-selected="false">
                            Laboratory Tests
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="recordTabsContent">
                    <!-- Medical Records Tab -->
                    <div class="tab-pane fade show active" id="records" role="tabpanel" aria-labelledby="records-tab">
                        @if($medicalRecords->isEmpty())
                            <div class="empty-state">
                                <p>No medical records found for this pet.</p>
                            </div>
                        @else
                            <div class="timeline">
                                @foreach($medicalRecords as $record)
                                    <div class="timeline-item">
                                        <div class="record-card">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="record-title">{{ $record->diagnosis ?: 'Checkup / Consultation' }}</h5>
                                                    <div class="record-meta">
                                                        <i class="far fa-calendar-alt me-1"></i> {{ date('F d, Y', strtotime($record->visit_date)) }}
                                                        @if($record->weight)
                                                            <span class="mx-2">•</span>
                                                            <i class="fas fa-weight me-1"></i> {{ $record->weight }} kg
                                                        @endif
                                                    </div>
                                                </div>
                                                <a href="{{ route('customer.medical-records.show', ['petId' => $pet->id, 'recordId' => $record->id]) }}" class="btn-record">
                                                    Details
                                                </a>
                                            </div>
                                            
                                            <p class="record-description">{{ Str::limit($record->treatment, 150) }}</p>
                                            
                                            @if($record->veterinarian)
                                                <div class="d-flex align-items-center mt-3 pt-3 border-top border-white">
                                                    <div class="small text-muted">Attending Vet:</div>
                                                    <div class="ms-2 fw-bold small">Dr. {{ $record->veterinarian->first_name }} {{ $record->veterinarian->last_name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                        <!-- Vaccinations Tab -->
                        <div class="tab-pane fade" id="vaccinations" role="tabpanel" aria-labelledby="vaccinations-tab">
                            @if($vaccinations->isEmpty())
                                <div class="empty-state">
                                    <p>No vaccination history found.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="glass-table table">
                                        <thead>
                                            <tr>
                                                <th>Vaccine</th>
                                                <th>Date Administered</th>
                                                <th>Next Due</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vaccinations as $vac)
                                                <tr>
                                                    <td class="fw-bold">{{ $vac->inventoryItem->name ?? 'Unknown Vaccine' }}</td>
                                                    <td>{{ date('M d, Y', strtotime($vac->administered_date)) }}</td>
                                                    <td>
                                                        @if($vac->next_due_date)
                                                            <span class="status-badge {{ $vac->next_due_date < now() ? 'status-danger' : 'status-success' }}">
                                                                {{ date('M d, Y', strtotime($vac->next_due_date)) }}
                                                            </span>
                                                        @else
                                                            <span style="color: #666;">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $vac->remarks ?: '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Prescriptions Tab -->
                        <div class="tab-pane fade" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab">
                            @if($prescriptions->isEmpty())
                                <div class="empty-state">
                                    <p>No details found.</p>
                                </div>
                            @else
                                <div class="row g-3">
                                    @foreach($prescriptions as $presc)
                                        <div class="col-md-6">
                                            <div class="prescription-card">
                                                <h6 class="prescription-title">{{ $presc->medication_name }}</h6>
                                                <p class="prescription-meta">{{ date('M d, Y', strtotime($presc->created_at)) }}</p>
                                                <hr style="border-color: rgba(255, 255, 255, 0.2); margin: 1rem 0;">
                                                <div class="input-group input-group-sm mb-2">
                                                    <span class="input-group-text prescription-input">Dosage</span>
                                                    <input type="text" class="form-control prescription-input" value="{{ $presc->dosage }}" readonly>
                                                </div>
                                                <div class="input-group input-group-sm mb-2">
                                                    <span class="input-group-text prescription-input">Frequency</span>
                                                    <input type="text" class="form-control prescription-input" value="{{ $presc->frequency }}" readonly>
                                                </div>
                                                <div class="small">
                                                    <strong>Duration:</strong> {{ $presc->duration_days ? $presc->duration_days . ' days' : 'As needed' }}
                                                </div>
                                                <div class="mt-3">
                                                    <a href="{{ route('customer.prescriptions.print', ['petId' => $pet->id, 'prescriptionId' => $presc->id]) }}" class="btn-record">
                                                        View / Print Prescription
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        <!-- Chronic Conditions & Allergies Tab -->
                        <div class="tab-pane fade" id="chronic-allergies" role="tabpanel" aria-labelledby="chronic-allergies-tab">
                            <div class="row">
                                <!-- Chronic Conditions -->
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Chronic Conditions</h5>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addChronicConditionModal">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                    @if($chronicConditions->isEmpty())
                                        <div class="text-center py-4">
                                            <p class="text-muted">No chronic conditions recorded.</p>
                                        </div>
                                    @else
                                        @foreach($chronicConditions as $condition)
                                            <div class="card border mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0">{{ $condition->condition_name }}</h6>
                                                        <div>
                                                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editChronicCondition({{ $condition->id }}, '{{ $condition->condition_name }}', '{{ $condition->diagnosed_date }}', '{{ addslashes($condition->ongoing_treatment ?? '') }}', '{{ addslashes($condition->notes ?? '') }}')" data-bs-toggle="modal" data-bs-target="#editChronicConditionModal">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form method="POST" action="{{ route('customer.medical-records.chronic-conditions.destroy', ['petId' => $pet->id, 'conditionId' => $condition->id]) }}" class="d-inline" onsubmit="return confirm('Delete this chronic condition?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="small text-muted mb-2">
                                                        <i class="far fa-calendar-alt"></i> Diagnosed: {{ $condition->diagnosed_date->format('M d, Y') }}
                                                    </div>
                                                    @if($condition->ongoing_treatment)
                                                        <div class="small mb-2">
                                                            <strong>Treatment:</strong> {{ $condition->ongoing_treatment }}
                                                        </div>
                                                    @endif
                                                    @if($condition->notes)
                                                        <div class="small">
                                                            <strong>Notes:</strong> {{ $condition->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <!-- Pet Allergies -->
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Allergies</h5>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAllergyModal">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                    @if($allergies->isEmpty())
                                        <div class="text-center py-4">
                                            <p class="text-muted">No allergies recorded.</p>
                                        </div>
                                    @else
                                        @foreach($allergies as $allergy)
                                            <div class="card border mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0">{{ $allergy->allergen }}</h6>
                                                        <div>
                                                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editAllergy({{ $allergy->id }}, '{{ $allergy->allergen }}', '{{ $allergy->reaction_type ?? '' }}', '{{ $allergy->severity ?? 'mild' }}', '{{ $allergy->diagnosed_date }}', '{{ addslashes($allergy->notes ?? '') }}')" data-bs-toggle="modal" data-bs-target="#editAllergyModal">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form method="POST" action="{{ route('customer.medical-records.allergies.destroy', ['petId' => $pet->id, 'allergyId' => $allergy->id]) }}" class="d-inline" onsubmit="return confirm('Delete this allergy?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="small text-muted mb-2">
                                                        <i class="far fa-calendar-alt"></i> Diagnosed: {{ $allergy->diagnosed_date->format('M d, Y') }}
                                                        @if($allergy->severity)
                                                            <span class="badge bg-{{ $allergy->severity === 'severe' ? 'danger' : ($allergy->severity === 'moderate' ? 'warning' : 'info') }} ms-2">
                                                                {{ ucfirst($allergy->severity) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($allergy->reaction_type)
                                                        <div class="small mb-2">
                                                            <strong>Reaction:</strong> {{ $allergy->reaction_type }}
                                                        </div>
                                                    @endif
                                                    @if($allergy->notes)
                                                        <div class="small">
                                                            <strong>Notes:</strong> {{ $allergy->notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Surgeries Tab -->
                        <div class="tab-pane fade" id="surgeries" role="tabpanel" aria-labelledby="surgeries-tab">
                            @if($surgeries->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No surgery records found.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Procedure</th>
                                                <th>Date</th>
                                                <th>Surgeon</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($surgeries as $surgery)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold">{{ $surgery->surgeryType->name ?? 'N/A' }}</div>
                                                        @if($surgery->anesthesia_type)
                                                            <div class="small text-muted">{{ $surgery->anesthesia_type }}</div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $surgery->scheduled_date ? $surgery->scheduled_date->format('M d, Y H:i A') : '-' }}</td>
                                                    <td>{{ $surgery->surgeon ? 'Dr. ' . $surgery->surgeon->first_name . ' ' . $surgery->surgeon->last_name : '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $surgery->status === 'completed' ? 'success' : ($surgery->status === 'cancelled' ? 'danger' : 'info') }}">
                                                            {{ ucfirst($surgery->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ Str::limit($surgery->notes ?? '-', 50) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Laboratory Tests Tab -->
                        <div class="tab-pane fade" id="labs" role="tabpanel" aria-labelledby="labs-tab">
                            @if($labTests->isEmpty())
                                <div class="text-center py-5">
                                    <p class="text-muted">No laboratory test records found.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Test Name</th>
                                                <th>Test Date</th>
                                                <th>Results</th>
                                                <th>Ordered By</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($labTests as $test)
                                                <tr>
                                                    <td class="fw-bold">{{ $test->test->test_name ?? 'N/A' }}</td>
                                                    <td>{{ $test->requested_date ? $test->requested_date->format('M d, Y') : '-' }}</td>
                                                    <td>{{ Str::limit($test->results ?? 'Pending', 50) }}</td>
                                                    <td>{{ $test->requestedBy ? 'Dr. ' . $test->requestedBy->first_name . ' ' . $test->requestedBy->last_name : '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $test->status === 'completed' ? 'success' : ($test->status === 'pending' ? 'warning' : 'info') }}">
                                                            {{ ucfirst($test->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Chronic Condition Modal -->
<div class="modal fade" id="addChronicConditionModal" tabindex="-1" aria-labelledby="addChronicConditionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.medical-records.chronic-conditions.store', $pet->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addChronicConditionModalLabel">Add Chronic Condition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="condition_name" class="form-label">Condition Name*</label>
                        <input type="text" class="form-control" id="condition_name" name="condition_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="diagnosed_date" class="form-label">Diagnosed Date*</label>
                        <input type="date" class="form-control" id="diagnosed_date" name="diagnosed_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="ongoing_treatment" class="form-label">Ongoing Treatment</label>
                        <textarea class="form-control" id="ongoing_treatment" name="ongoing_treatment" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Chronic Condition Modal -->
<div class="modal fade" id="editChronicConditionModal" tabindex="-1" aria-labelledby="editChronicConditionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editChronicConditionForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editChronicConditionModalLabel">Edit Chronic Condition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_condition_name" class="form-label">Condition Name*</label>
                        <input type="text" class="form-control" id="edit_condition_name" name="condition_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_diagnosed_date" class="form-label">Diagnosed Date*</label>
                        <input type="date" class="form-control" id="edit_diagnosed_date" name="diagnosed_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_ongoing_treatment" class="form-label">Ongoing Treatment</label>
                        <textarea class="form-control" id="edit_ongoing_treatment" name="ongoing_treatment" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Allergy Modal -->
<div class="modal fade" id="addAllergyModal" tabindex="-1" aria-labelledby="addAllergyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('customer.medical-records.allergies.store', $pet->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addAllergyModalLabel">Add Allergy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="allergen" class="form-label">Allergen*</label>
                        <input type="text" class="form-control" id="allergen" name="allergen" required>
                    </div>
                    <div class="mb-3">
                        <label for="reaction_type" class="form-label">Reaction Type</label>
                        <input type="text" class="form-control" id="reaction_type" name="reaction_type" placeholder="e.g., Skin rash, Vomiting, etc.">
                    </div>
                    <div class="mb-3">
                        <label for="severity" class="form-label">Severity</label>
                        <select class="form-select" id="severity" name="severity">
                            <option value="mild">Mild</option>
                            <option value="moderate">Moderate</option>
                            <option value="severe">Severe</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="allergy_diagnosed_date" class="form-label">Diagnosed Date*</label>
                        <input type="date" class="form-control" id="allergy_diagnosed_date" name="diagnosed_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="allergy_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="allergy_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Allergy Modal -->
<div class="modal fade" id="editAllergyModal" tabindex="-1" aria-labelledby="editAllergyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editAllergyForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editAllergyModalLabel">Edit Allergy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_allergen" class="form-label">Allergen*</label>
                        <input type="text" class="form-control" id="edit_allergen" name="allergen" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_reaction_type" class="form-label">Reaction Type</label>
                        <input type="text" class="form-control" id="edit_reaction_type" name="reaction_type">
                    </div>
                    <div class="mb-3">
                        <label for="edit_severity" class="form-label">Severity</label>
                        <select class="form-select" id="edit_severity" name="severity">
                            <option value="mild">Mild</option>
                            <option value="moderate">Moderate</option>
                            <option value="severe">Severe</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_allergy_diagnosed_date" class="form-label">Diagnosed Date*</label>
                        <input type="date" class="form-control" id="edit_allergy_diagnosed_date" name="diagnosed_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_allergy_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_allergy_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editChronicCondition(id, name, diagnosedDate, treatment, notes) {
    document.getElementById('edit_condition_name').value = name;
    document.getElementById('edit_diagnosed_date').value = diagnosedDate;
    document.getElementById('edit_ongoing_treatment').value = treatment;
    document.getElementById('edit_notes').value = notes;
    document.getElementById('editChronicConditionForm').action = '/customer/medical-records/pets/{{ $pet->id }}/chronic-conditions/' + id;
}

function editAllergy(id, allergen, reactionType, severity, diagnosedDate, notes) {
    document.getElementById('edit_allergen').value = allergen;
    document.getElementById('edit_reaction_type').value = reactionType;
    document.getElementById('edit_severity').value = severity;
    document.getElementById('edit_allergy_diagnosed_date').value = diagnosedDate;
    document.getElementById('edit_allergy_notes').value = notes;
    document.getElementById('editAllergyForm').action = '/customer/medical-records/pets/{{ $pet->id }}/allergies/' + id;
}
</script>
@endsection
