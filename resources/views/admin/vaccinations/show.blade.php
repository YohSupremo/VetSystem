@extends('admin.dashboard')

@section('page-title', 'Vaccination Record')
@section('page-description', $vaccination->pet->name ?? 'Pet Vaccination')

@section('content')
<div class="container-fluid">
    <div class="record-container">
        <div class="record-header">
            <h2><i class="fas fa-syringe"></i> Vaccination Record</h2>
            <div class="header-actions">
                <a href="{{ route('admin.vaccinations.edit', $vaccination->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.vaccinations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="record-content">
            <div class="record-section">
                <h3>Pet Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Pet Name</label>
                        <p>{{ $vaccination->pet->name ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Species</label>
                        <p>{{ $vaccination->pet->species ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Breed</label>
                        <p>{{ $vaccination->pet->breed ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Owner</label>
                        <p>{{ ($vaccination->pet && $vaccination->pet->owner && $vaccination->pet->owner->user) ? $vaccination->pet->owner->user->first_name . ' ' . $vaccination->pet->owner->user->last_name : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="record-section">
                <h3>Vaccination Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Vaccine Name</label>
                        <p>{{ $vaccination->vaccine_name ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Vaccination Date</label>
                        <p>{{ $vaccination->administered_date ? \Carbon\Carbon::parse($vaccination->administered_date)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Next Due Date</label>
                        <p>
                            {{ $vaccination->next_due_date ? \Carbon\Carbon::parse($vaccination->next_due_date)->format('M d, Y') : 'N/A' }}
                            @if($vaccination->next_due_date)
                                @php $daysUntil = \Carbon\Carbon::parse($vaccination->next_due_date)->diffInDays(\Carbon\Carbon::now(), false); @endphp
                                @if($daysUntil < 0)
                                    <span class="badge-overdue">OVERDUE</span>
                                @elseif($daysUntil < 30)
                                    <span class="badge-warning">Due Soon</span>
                                @else
                                    <span class="badge-ok">Up to Date</span>
                                @endif
                            @endif
                        </p>
                    </div>
                    <div class="info-item">
                        <label>Veterinarian</label>
                        <p>{{ ($vaccination->veterinarian) ? 'Dr. ' . $vaccination->veterinarian->first_name . ' ' . $vaccination->veterinarian->last_name : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="record-section">
                <h3>Administration Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Route of Administration</label>
                        <p>{{ $vaccination->route_of_administration ? ucfirst(str_replace('_', ' ', $vaccination->route_of_administration)) : 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Site of Injection</label>
                        <p>{{ $vaccination->site_of_injection ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Batch Number</label>
                        <p>{{ $vaccination->batch_number ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Recorded Date</label>
                        <p>{{ $vaccination->created_at ? \Carbon\Carbon::parse($vaccination->created_at)->format('M d, Y H:i A') : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            @if($vaccination->adverse_reactions)
            <div class="record-section alert-section">
                <h3>Adverse Reactions/Effects</h3>
                <div class="alert-box">
                    {{ $vaccination->adverse_reactions }}
                </div>
            </div>
            @endif

            @if($vaccination->notes)
            <div class="record-section">
                <h3>Notes</h3>
                <div class="text-content">
                    {{ $vaccination->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.record-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.record-header {
    background: linear-gradient(135deg, #FF6B9D 0%, #FF8BB5 100%);
    color: white;
    padding: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.record-header h2 {
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.record-content {
    padding: 30px;
}

.record-section {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid #f0f0f0;
}

.record-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.record-section h3 {
    color: var(--dark-text);
    margin-bottom: 15px;
    font-size: 18px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-item {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 6px;
}

.info-item label {
    display: block;
    font-weight: 600;
    color: #FF6B9D;
    margin-bottom: 5px;
    font-size: 12px;
    text-transform: uppercase;
}

.info-item p {
    margin: 0;
    color: var(--dark-text);
}

.text-content {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 6px;
    line-height: 1.6;
    color: var(--dark-text);
    white-space: pre-wrap;
    word-wrap: break-word;
}

.alert-section {
    background: #FFF3E0;
    border-left: 4px solid #FF9800;
    padding: 20px;
    border-radius: 6px;
}

.alert-box {
    background: white;
    padding: 15px;
    border-radius: 4px;
    color: var(--dark-text);
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.badge-ok, .badge-warning, .badge-overdue {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 10px;
    display: inline-block;
}

.badge-ok {
    background: #E8F5E9;
    color: #2E7D32;
}

.badge-warning {
    background: #FFF3E0;
    color: #F57C00;
}

.badge-overdue {
    background: #FFEBEE;
    color: #C62828;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
    font-size: 14px;
}

.btn-primary {
    background: white;
    color: #FF6B9D;
}

.btn-primary:hover {
    background: #f0f0f0;
}

.btn-secondary {
    background: #6C757D;
    color: white;
}

.btn-secondary:hover {
    background: #5A6268;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .record-header {
        flex-direction: column;
        gap: 15px;
    }
}
</style>
@endsection
