@extends('admin.dashboard')

@section('page-title', $pet->name . ' - Prescriptions')
@section('page-description', 'View all prescriptions for ' . $pet->name)

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div class="header-info">
            <h3 style="margin:0;"><i class="fas fa-prescription"></i> Prescriptions for {{ $pet->name }}</h3>
            <p style="margin:0.5rem 0 0 0; color: var(--light-text); font-size: 14px;">
                <i class="fas fa-user"></i> Owner: {{ $pet->owner->user->first_name ?? '' }} {{ $pet->owner->user->last_name ?? 'No Owner' }}
            </p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to All
            </a>
            <a href="{{ route('admin.prescriptions.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Prescription
            </a>
        </div>
    </div>

    <div class="card-body">
        <!-- Grouped Prescriptions -->
        <div class="prescriptions-grouped">
            @forelse($groupedPrescriptions as $group)
                <div class="medical-record-group expanded">
                    <div class="group-header" onclick="toggleGroup(this)">
                        <div class="group-info">
                            <div class="medical-record-details">
                                <div class="record-icon">
                                    <i class="fas fa-file-medical"></i>
                                </div>
                                <div>
                                    <h4>Medical Record - {{ $group['medical_record']->visit_date->format('M d, Y') }}</h4>
                                    <p class="diagnosis">
                                        <i class="fas fa-stethoscope"></i>
                                        {{ $group['medical_record']->diagnosis }}
                                    </p>
                                    <p class="vet-info">
                                        <i class="fas fa-user-md"></i>
                                        Dr. {{ $group['medical_record']->veterinarian->first_name ?? '' }} 
                                        {{ $group['medical_record']->veterinarian->last_name ?? '' }}
                                    </p>
                                </div>
                            </div>
                            <div class="prescription-count">
                                <span class="badge">{{ $group['count'] }} Prescription{{ $group['count'] > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        <div class="toggle-icon">
                            <i class="fas fa-chevron-up"></i>
                        </div>
                    </div>
                    
                    <div class="group-content" style="max-height: 2000px;">
                        <div class="prescriptions-list">
                            @foreach($group['prescriptions'] as $prescription)
                                <div class="prescription-card">
                                    <div class="prescription-header">
                                        <div class="medication-name">
                                            <i class="fas fa-pills"></i>
                                            {{ $prescription->medication_name }}
                                        </div>
                                        <div class="prescription-status">
                                            @if($prescription->dispensed)
                                                <span class="status-badge dispensed">
                                                    <i class="fas fa-check-circle"></i> Dispensed
                                                </span>
                                            @else
                                                <span class="status-badge pending">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="prescription-details">
                                        <div class="detail-row">
                                            <span class="label">Dosage:</span>
                                            <span class="value">{{ $prescription->dosage }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Frequency:</span>
                                            <span class="value">{{ $prescription->frequency }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Duration:</span>
                                            <span class="value">{{ $prescription->duration_days }} days</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Quantity:</span>
                                            <span class="value">{{ $prescription->quantity }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Created:</span>
                                            <span class="value">{{ $prescription->created_at->format('M d, Y h:i A') }}</span>
                                        </div>
                                        @if($prescription->dispensed)
                                            <div class="detail-row">
                                                <span class="label">Dispensed:</span>
                                                <span class="value">{{ $prescription->dispensed_at ? $prescription->dispensed_at->format('M d, Y h:i A') : 'N/A' }}</span>
                                            </div>
                                        @endif
                                        @if($prescription->instructions)
                                            <div class="detail-row full-width">
                                                <span class="label">Instructions:</span>
                                                <span class="value">{{ $prescription->instructions }}</span>
                                            </div>
                                        @endif
                                        @if($prescription->assignedStaff)
                                            <div class="detail-row">
                                                <span class="label">Assigned Staff:</span>
                                                <span class="value">{{ $prescription->assignedStaff->first_name }} {{ $prescription->assignedStaff->last_name }}</span>
                                            </div>
                                        @endif
                                        @if($prescription->external_clinic_name)
                                            <div class="detail-row">
                                                <span class="label">External Clinic:</span>
                                                <span class="value">{{ $prescription->external_clinic_name }}</span>
                                            </div>
                                        @endif
                                        @if($prescription->external_veterinarian_name)
                                            <div class="detail-row">
                                                <span class="label">External Veterinarian:</span>
                                                <span class="value">{{ $prescription->external_veterinarian_name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="prescription-actions">
                                        @if($showTrash ?? false)
                                            <form action="{{ route('admin.prescriptions.restore', $prescription->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Restore this prescription?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-undo"></i> Restore
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.prescriptions.show', $prescription->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.prescriptions.edit', $prescription->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>No prescriptions found for {{ $pet->name }}.</p>
                    <a href="{{ route('admin.prescriptions.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Create First Prescription
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function toggleGroup(header) {
    const group = header.parentElement;
    const content = group.querySelector('.group-content');
    const icon = header.querySelector('.toggle-icon i');
    
    group.classList.toggle('expanded');
    
    if (group.classList.contains('expanded')) {
        content.style.maxHeight = content.scrollHeight + 'px';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        content.style.maxHeight = '0';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>

<style>
.header-info h3 {
    font-family: 'Fredoka', sans-serif;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

.prescriptions-grouped {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.medical-record-group {
    background: var(--white);
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.medical-record-group:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.group-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.group-header:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a4296 100%);
}

.group-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.medical-record-details {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.record-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.medical-record-details h4 {
    margin: 0 0 0.5rem 0;
    font-family: 'Fredoka', sans-serif;
    font-size: 18px;
}

.medical-record-details p {
    margin: 0.25rem 0;
    font-size: 13px;
    opacity: 0.9;
}

.medical-record-details p i {
    margin-right: 0.5rem;
}

.prescription-count .badge {
    background: rgba(255,255,255,0.25);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.toggle-icon {
    margin-left: 1rem;
    color: white;
    transition: transform 0.3s ease;
}

.group-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.prescriptions-list {
    padding: 1.5rem;
    display: grid;
    gap: 1rem;
}

.prescription-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid rgba(0,0,0,0.06);
    transition: all 0.2s ease;
}

.prescription-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.prescription-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.08);
}

.medication-name {
    font-family: 'Fredoka', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: var(--dark-text);
}

.medication-name i {
    margin-right: 0.5rem;
    color: var(--primary-blue);
}

.status-badge {
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.dispensed {
    background: #d4edda;
    color: #155724;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.prescription-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.detail-row {
    display: flex;
    gap: 0.5rem;
}

.detail-row.full-width {
    grid-column: 1 / -1;
    flex-direction: column;
}

.detail-row .label {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 13px;
}

.detail-row .value {
    color: var(--light-text);
    font-size: 13px;
}

.prescription-actions {
    display: flex;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0,0,0,0.06);
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--light-text);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 1rem;
    opacity: 0.5;
}
</style>
@endsection
