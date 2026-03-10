@extends('admin.dashboard')

@section('page-title', 'Prescription Management')
@section('page-description', 'View and manage pet prescriptions')

@section('content')
<div class="card prescriptions-card">
    <div class="card-header prescriptions-header">
        <h3><i class="fas fa-prescription"></i> Prescriptions</h3>
        <div class="d-flex gap-2">
            @if($showTrash ?? false)
                <a href="{{ route('admin.prescriptions.index', request()->except('trash', 'page')) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back To Active
                </a>
            @else
                <a href="{{ route('admin.prescriptions.index', array_merge(request()->query(), ['trash' => 1])) }}" class="btn btn-secondary">
                    <i class="fas fa-trash-restore"></i> View Trash
                </a>
            @endif
            <a href="{{ route('admin.prescriptions.create') }}" class="btn btn-primary prescriptions-create-btn">
                <i class="fas fa-plus"></i> Create Prescription
            </a>
        </div>
    </div>

    <div class="card-body">
        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="{{ route('admin.prescriptions.index') }}" class="filter-form">
                <div class="filter-grid">
                    <div class="filter-item">
                        <label><i class="fas fa-paw"></i> Pet</label>
                        <select name="pet_id" class="form-control">
                            <option value="">All Pets</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ request('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} - {{ $pet->owner->user->first_name ?? '' }} {{ $pet->owner->user->last_name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <label><i class="fas fa-calendar"></i> From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="filter-item">
                        <label><i class="fas fa-calendar"></i> To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="filter-item">
                        <label><i class="fas fa-check-circle"></i> Status</label>
                        <select name="dispensed" class="form-control">
                            <option value="">All Status</option>
                            <option value="0" {{ request('dispensed') === '0' ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ request('dispensed') === '1' ? 'selected' : '' }}>Dispensed</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Prescriptions List -->
        <div class="prescriptions-list">
            @forelse($groupedPrescriptions as $group)
                <div class="prescription-group-card">
                    <div class="group-summary">
                        <div class="pet-info">
                            <div class="pet-avatar">
                                <img src="{{ $group['medical_record']->pet->photo_url }}" alt="{{ $group['medical_record']->pet->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <i class="fas fa-paw" style="display:none;"></i>
                            </div>
                            <div class="pet-details">
                                <h4>{{ $group['medical_record']->pet->name }}</h4>
                                <p class="owner-name">
                                    <i class="fas fa-user"></i>
                                    {{ $group['medical_record']->pet->owner->user->first_name ?? '' }} 
                                    {{ $group['medical_record']->pet->owner->user->last_name ?? 'No Owner' }}
                                </p>
                                <p class="medical-info">
                                    <i class="fas fa-pills"></i>
                                    Latest: {{ $group['latest_prescription']->medication_name }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="prescription-summary">
                            <div class="prescription-count">
                                <span class="count-badge">{{ $group['count'] }}</span>
                                <span class="count-label">Prescription{{ $group['count'] > 1 ? 's' : '' }}</span>
                            </div>
                            <div class="latest-prescription">
                                <span class="latest-label">Latest Date:</span>
                                <span class="latest-date">{{ $group['latest_prescription']->created_at->format('M d, Y') }}</span>
                                <span class="status-indicator">
                                    @if($group['latest_prescription']->dispensed)
                                        <span class="badge-mini dispensed">Dispensed</span>
                                    @else
                                        <span class="badge-mini pending">Pending</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ route('admin.prescriptions.pet', ['pet' => $group['medical_record']->pet->id, 'trash' => (int)($showTrash ?? false)]) }}" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View All
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>No prescriptions found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.prescriptions-card {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(251, 146, 60, 0.18);
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
}

.prescriptions-header {
    background: linear-gradient(135deg, rgba(255, 247, 237, 0.95), rgba(253, 242, 248, 0.95));
    border-bottom: 1px solid rgba(251, 146, 60, 0.2);
    padding: 1.05rem 1.2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.8rem;
}

.prescriptions-header h3 {
    margin: 0;
    font-weight: 800;
    background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.prescriptions-create-btn {
    border: none;
    border-radius: 12px;
    padding: 0.58rem 1rem;
    font-weight: 700;
    background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
    box-shadow: 0 10px 20px rgba(236, 72, 153, 0.24);
}

.prescriptions-create-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 24px rgba(236, 72, 153, 0.3);
}

.filters-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.filter-item label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--dark-text);
    font-size: 14px;
}

.filter-item label i {
    margin-right: 0.5rem;
    color: var(--primary-blue);
}

.filter-actions {
    display: flex;
    gap: 0.75rem;
}

.prescriptions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.prescription-group-card {
    background: var(--white);
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.prescription-group-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.group-summary {
    padding: 1.5rem;
    display: grid;
    grid-template-columns: 2fr 1.5fr auto;
    gap: 2rem;
    align-items: center;
}

@media (max-width: 992px) {
    .group-summary {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}

.pet-info {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.pet-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.7);
}

.pet-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.pet-avatar i {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pet-details h4 {
    margin: 0 0 0.5rem 0;
    font-family: 'Fredoka', sans-serif;
    font-size: 20px;
    color: var(--dark-text);
}

.pet-details p {
    margin: 0.25rem 0;
    font-size: 13px;
    color: var(--light-text);
}

.pet-details p i {
    margin-right: 0.5rem;
    color: var(--primary-blue);
}

.owner-name {
    font-weight: 600;
    color: var(--dark-text) !important;
}

.prescription-summary {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.prescription-count {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.count-badge {
    background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    font-family: 'Fredoka', sans-serif;
}

.count-label {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 14px;
}

.latest-prescription {
    background: #f8f9fa;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    border-left: 4px solid var(--primary-blue);
}

.latest-label {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 12px;
    display: block;
    margin-bottom: 0.25rem;
}

.latest-med {
    display: block;
    font-weight: 600;
    color: var(--primary-blue);
    font-size: 15px;
    margin-bottom: 0.25rem;
}

.latest-date {
    display: block;
    color: var(--light-text);
    font-size: 12px;
    margin-bottom: 0.5rem;
}

.status-indicator {
    display: block;
}

.badge-mini {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.badge-mini.dispensed {
    background: #d4edda;
    color: #155724;
}

.badge-mini.pending {
    background: #fff3cd;
    color: #856404;
}

.action-buttons {
    display: flex;
    align-items: center;
}

.action-buttons .btn {
    white-space: nowrap;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
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
