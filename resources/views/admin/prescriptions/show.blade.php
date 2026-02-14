@extends('admin.dashboard')

@section('page-title', 'Prescription Details')
@section('page-description', 'View prescription details')

@section('content')
<div class="prescription-show-container">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <div class="title-section">
                <div class="icon-wrapper">
                    <i class="fas fa-prescription"></i>
                </div>
                <div>
                    <h2>Prescription Details</h2>
                    <p class="prescription-id">ID: #{{ $prescription->id }}</p>
                </div>
            </div>
            <div class="status-badge-large">
                @if($prescription->dispensed)
                    <span class="badge-dispensed">
                        <i class="fas fa-check-circle"></i> Dispensed
                    </span>
                @else
                    <span class="badge-pending">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                @endif
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.prescriptions.pet', $prescription->pet->id) }}" class="btn btn-secondary">
                <i class="fas fa-paw"></i> Pet Prescriptions
            </a>
            <a href="{{ route('admin.prescriptions.edit', $prescription->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('admin.prescriptions.destroy', $prescription->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this prescription?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Medication Card -->
            <div class="info-card medication-card">
                <div class="card-header">
                    <i class="fas fa-pills"></i>
                    <h3>Medication Information</h3>
                </div>
                <div class="card-body">
                    <div class="medication-name-display">
                        <i class="fas fa-prescription-bottle"></i>
                        {{ $prescription->medication_name }}
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label"><i class="fas fa-syringe"></i> Dosage</span>
                            <span class="value">{{ $prescription->dosage }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-clock"></i> Frequency</span>
                            <span class="value">{{ $prescription->frequency }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-calendar-alt"></i> Duration</span>
                            <span class="value">{{ $prescription->duration_days }} days</span>
                        </div>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-boxes"></i> Quantity</span>
                            <span class="value">{{ $prescription->quantity }}</span>
                        </div>
                    </div>

                    @if($prescription->instructions)
                        <div class="instructions-section">
                            <div class="instructions-header">
                                <i class="fas fa-info-circle"></i> Instructions
                            </div>
                            <div class="instructions-content">
                                {{ $prescription->instructions }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="info-card timeline-card">
                <div class="card-header">
                    <i class="fas fa-history"></i>
                    <h3>Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon created">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Prescription Created</div>
                                <div class="timeline-date">{{ $prescription->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        
                        @if($prescription->dispensed)
                            <div class="timeline-item">
                                <div class="timeline-icon dispensed">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">Dispensed</div>
                                    <div class="timeline-date">
                                        {{ $prescription->dispensed_at ? $prescription->dispensed_at->format('M d, Y h:i A') : 'N/A' }}
                                    </div>
                                    @if($prescription->dispensedBy)
                                        <div class="timeline-meta">
                                            By: {{ $prescription->dispensedBy->first_name }} {{ $prescription->dispensedBy->last_name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Pet Information Card -->
            <div class="info-card pet-card">
                <div class="card-header">
                    <i class="fas fa-paw"></i>
                    <h3>Pet Information</h3>
                </div>
                <div class="card-body">
                    <div class="pet-profile">
                        <div class="pet-avatar">
                            <i class="fas fa-dog"></i>
                        </div>
                        <div class="pet-details">
                            <h4>
                                <a href="{{ route('admin.pets.show', $prescription->pet->id) }}">
                                    {{ $prescription->pet->name }}
                                </a>
                            </h4>
                            <p class="pet-meta">{{ $prescription->pet->species }} • {{ ucfirst($prescription->pet->gender) }}</p>
                        </div>
                    </div>
                    
                    <div class="info-list">
                        <div class="info-row">
                            <span class="label">Breed</span>
                            <span class="value">{{ $prescription->pet->breed ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Weight</span>
                            <span class="value">{{ $prescription->pet->weight ?? 'N/A' }} kg</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Age</span>
                            <span class="value">
                                @if($prescription->pet->date_of_birth)
                                    {{ \Carbon\Carbon::parse($prescription->pet->date_of_birth)->age }} years
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Owner Information Card -->
            <div class="info-card owner-card">
                <div class="card-header">
                    <i class="fas fa-user"></i>
                    <h3>Owner Information</h3>
                </div>
                <div class="card-body">
                    <div class="owner-profile">
                        <div class="owner-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="owner-details">
                            <h4>
                                <a href="{{ route('admin.pet-owners.show', $prescription->pet->owner->id) }}">
                                    {{ $prescription->pet->owner->user->first_name ?? '' }} 
                                    {{ $prescription->pet->owner->user->last_name ?? 'Unknown' }}
                                </a>
                            </h4>
                        </div>
                    </div>
                    
                    <div class="info-list">
                        <div class="info-row">
                            <span class="label"><i class="fas fa-phone"></i> Phone</span>
                            <span class="value">{{ $prescription->pet->owner->user->contact_number ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label"><i class="fas fa-envelope"></i> Email</span>
                            <span class="value">{{ $prescription->pet->owner->user->email ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label"><i class="fas fa-map-marker-alt"></i> Address</span>
                            <span class="value">{{ $prescription->pet->owner->user->address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Record Card -->
            @if($prescription->medicalRecord)
                <div class="info-card medical-record-card">
                    <div class="card-header">
                        <i class="fas fa-file-medical"></i>
                        <h3>Medical Record</h3>
                    </div>
                    <div class="card-body">
                        <div class="medical-record-link">
                            <a href="{{ route('admin.medical-records.show', $prescription->medicalRecord->id) }}">
                                <i class="fas fa-external-link-alt"></i>
                                View Full Medical Record
                            </a>
                        </div>
                        
                        <div class="info-list">
                            <div class="info-row">
                                <span class="label">Visit Date</span>
                                <span class="value">{{ $prescription->medicalRecord->visit_date->format('M d, Y') }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Veterinarian</span>
                                <span class="value">
                                    @if($prescription->medicalRecord->veterinarian)
                                        Dr. {{ $prescription->medicalRecord->veterinarian->first_name }} 
                                        {{ $prescription->medicalRecord->veterinarian->last_name }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            <div class="info-row full-width">
                                <span class="label">Complaint</span>
                                <span class="value">{{ $prescription->medicalRecord->complaint ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row full-width">
                                <span class="label">Diagnosis</span>
                                <span class="value diagnosis-text">{{ $prescription->medicalRecord->diagnosis ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.prescription-show-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.title-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.icon-wrapper {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}

.title-section h2 {
    margin: 0;
    font-family: 'Fredoka', sans-serif;
    font-size: 28px;
}

.prescription-id {
    margin: 0.25rem 0 0 0;
    opacity: 0.9;
    font-size: 14px;
}

.status-badge-large {
    font-size: 16px;
}

.badge-dispensed {
    background: rgba(76, 175, 80, 0.9);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.badge-pending {
    background: rgba(255, 193, 7, 0.9);
    color: #333;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.action-buttons .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.content-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 2rem;
}

@media (max-width: 992px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

.info-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    border: 1px solid rgba(0,0,0,0.06);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-header i {
    color: var(--primary-blue);
    font-size: 20px;
}

.card-header h3 {
    margin: 0;
    font-family: 'Fredoka', sans-serif;
    font-size: 18px;
    color: var(--dark-text);
}

.card-body {
    padding: 1.5rem;
}

/* Medication Card */
.medication-name-display {
    background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    font-size: 24px;
    font-weight: 600;
    font-family: 'Fredoka', sans-serif;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.medication-name-display i {
    font-size: 32px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-item {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,0.06);
}

.info-item .label {
    display: block;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 0.5rem;
    font-size: 13px;
}

.info-item .label i {
    margin-right: 0.5rem;
    color: var(--primary-blue);
}

.info-item .value {
    display: block;
    color: var(--light-text);
    font-size: 16px;
}

.instructions-section {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    border-radius: 8px;
}

.instructions-header {
    font-weight: 600;
    color: #856404;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.instructions-content {
    color: #856404;
    line-height: 1.6;
}

/* Timeline Card */
.timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 20px;
    top: 45px;
    width: 2px;
    height: calc(100% + 0.5rem);
    background: #e9ecef;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
    z-index: 1;
}

.timeline-icon.created {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.timeline-icon.dispensed {
    background: linear-gradient(135deg, #4caf50, #45a049);
    color: white;
}

.timeline-content {
    flex: 1;
}

.timeline-title {
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 0.25rem;
}

.timeline-date {
    color: var(--light-text);
    font-size: 14px;
}

.timeline-meta {
    color: var(--light-text);
    font-size: 13px;
    margin-top: 0.25rem;
}

/* Pet Card */
.pet-profile, .owner-profile {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.08);
}

.pet-avatar, .owner-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
    flex-shrink: 0;
}

.pet-details h4, .owner-details h4 {
    margin: 0 0 0.25rem 0;
    font-family: 'Fredoka', sans-serif;
    font-size: 20px;
}

.pet-details h4 a, .owner-details h4 a {
    color: var(--dark-text);
    text-decoration: none;
}

.pet-details h4 a:hover, .owner-details h4 a:hover {
    color: var(--primary-blue);
}

.pet-meta {
    margin: 0;
    color: var(--light-text);
    font-size: 14px;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.info-row.full-width {
    flex-direction: column;
    gap: 0.5rem;
}

.info-row .label {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 13px;
}

.info-row .label i {
    margin-right: 0.5rem;
    color: var(--primary-blue);
}

.info-row .value {
    color: var(--light-text);
    font-size: 14px;
    text-align: right;
}

.info-row.full-width .value {
    text-align: left;
}

.diagnosis-text {
    font-weight: 500;
    color: var(--dark-text);
}

/* Medical Record Card */
.medical-record-link {
    margin-bottom: 1.5rem;
}

.medical-record-link a {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-blue);
    text-decoration: none;
    font-weight: 600;
    padding: 0.75rem 1.25rem;
    background: #e3f2fd;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.medical-record-link a:hover {
    background: #bbdefb;
    transform: translateX(4px);
}
</style>
@endsection
