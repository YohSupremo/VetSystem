@extends('admin.dashboard')

@section('page-title', 'Pet Allergies')
@section('page-description', 'List of recorded pet allergies')

@section('content')
<div class="container-fluid">
  
    <div class="card allergy-card-shell">
        <div class="card-header d-flex justify-content-between align-items-center allergy-header">
            <h3 class="card-title"><i class="fas fa-allergies"></i> Pet Allergies</h3>
            <div class="d-flex gap-2 allergy-actions">
                @if($showTrash ?? false)
                    <a href="{{ route('admin.pet-allergies.index', request()->except('trash', 'page')) }}" class="btn btn-secondary allergy-secondary-btn">
                        <i class="fas fa-arrow-left"></i> Back To Active
                    </a>
                @else
                    <a href="{{ route('admin.pet-allergies.index', array_merge(request()->query(), ['trash' => 1])) }}" class="btn btn-secondary allergy-secondary-btn">
                        <i class="fas fa-trash-restore"></i> View Trash
                    </a>
                @endif
                <a href="{{ route('admin.pet-allergies.create') }}" class="btn btn-primary allergy-primary-btn">
                    <i class="fas fa-plus"></i> Add Allergy
                </a>
                <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary allergy-secondary-btn">
                    <i class="fas fa-arrow-left"></i> Back to Medical Records
                </a>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.pet-allergies.index') }}" class="row g-2 mb-3">
                <div class="col-md-9">
                    <input type="text" name="q" class="form-control" placeholder="Search allergen, pet, or owner" value="{{ $filters['q'] ?? '' }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.pet-allergies.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>

            <div class="row g-3">
                @forelse($groupedPets as $pet)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 pet-preview-card">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="pet-header-info">
                                        <div class="pet-avatar">
                                            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}">
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $pet->name }}</h5>
                                            <span class="pet-meta">{{ ucfirst($pet->species ?? 'Unknown') }}</span>
                                        </div>
                                    </div>
                                    <div class="module-icon module-icon-allergy">
                                        <i class="fas fa-allergies"></i>
                                    </div>
                                </div>
                                <p class="owner-line mb-3">
                                    <strong>Owner:</strong>
                                    @if($pet->owner && $pet->owner->user)
                                        {{ trim(($pet->owner->user->first_name ?? '') . ' ' . ($pet->owner->user->last_name ?? '')) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                                <div class="preview-stats mb-3">
                                    <div class="stat-item">
                                        <span class="stat-label">Records</span>
                                        <span class="stat-value">{{ $pet->allergy_total_count ?? 0 }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Active</span>
                                        <span class="stat-value text-success">{{ $pet->allergy_active_count ?? 0 }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-label">Inactive</span>
                                        <span class="stat-value text-muted">{{ ($pet->allergy_total_count ?? 0) - ($pet->allergy_active_count ?? 0) }}</span>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('admin.pet-allergies.pet', ['pet' => $pet, 'trash' => (int)($showTrash ?? false)]) }}" class="btn-preview-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-4">No allergy records found.</div>
                    </div>
                @endforelse
            </div>

            @if($groupedPets->hasPages())
                <div class="mt-3">
                    {{ $groupedPets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.allergy-card-shell {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(251, 146, 60, 0.18);
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
}

.allergy-header {
    background: linear-gradient(135deg, rgba(255, 247, 237, 0.95), rgba(253, 242, 248, 0.95));
    border-bottom: 1px solid rgba(251, 146, 60, 0.2);
    padding: 1.05rem 1.2rem;
}

.allergy-header .card-title {
    margin: 0;
    font-weight: 800;
    background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.allergy-actions {
    flex-wrap: wrap;
    justify-content: flex-end;
}

.allergy-primary-btn,
.allergy-secondary-btn {
    border-radius: 12px;
    font-weight: 700;
    padding: 0.58rem 1rem;
}

.allergy-primary-btn {
    border: none;
    background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
    box-shadow: 0 10px 20px rgba(236, 72, 153, 0.24);
}

.allergy-primary-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 24px rgba(236, 72, 153, 0.3);
}

.allergy-secondary-btn {
    border: 1px solid #FED7AA;
    background: #FFF7ED;
    color: #C2410C;
}

.allergy-secondary-btn:hover {
    background: #FFEDD5;
    border-color: #FDBA74;
    color: #9A3412;
}

.status-badge,
.severity-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 14px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.2px;
}

.status-active {
    background: #DCFCE7;
    color: #166534;
    border: 1px solid #86EFAC;
}

.status-inactive {
    background: #F3F4F6;
    color: #374151;
    border: 1px solid #D1D5DB;
}

.severity-mild {
    background: #DBEAFE;
    color: #1E40AF;
    border: 1px solid #93C5FD;
}

.severity-moderate {
    background: #FEF3C7;
    color: #92400E;
    border: 1px solid #FCD34D;
}

.severity-severe {
    background: #FEE2E2;
    color: #991B1B;
    border: 1px solid #FCA5A5;
}

.pet-preview-card {
    border: 1px solid rgba(236, 72, 153, 0.24);
    border-radius: 16px;
    background: linear-gradient(160deg, rgba(253, 242, 248, 0.96) 0%, rgba(252, 231, 243, 0.92) 100%);
    box-shadow: 0 10px 28px rgba(236, 72, 153, 0.16);
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.pet-preview-card:hover {
    transform: translateY(-5px);
    border-color: rgba(236, 72, 153, 0.4);
    box-shadow: 0 18px 36px rgba(236, 72, 153, 0.26);
}

.pet-meta {
    display: inline-block;
    font-size: 12px;
    padding: 3px 10px;
    border-radius: 999px;
    background: rgba(236, 72, 153, 0.14);
    color: #BE185D;
    font-weight: 600;
}

.pet-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.pet-avatar {
    width: 52px;
    height: 52px;
    border-radius: 999px;
    overflow: hidden;
    border: 2px solid rgba(236, 72, 153, 0.28);
    background: #F3F4F6;
    flex-shrink: 0;
    box-shadow: 0 6px 14px rgba(236, 72, 153, 0.22);
}

.pet-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.module-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.module-icon-allergy {
    background: rgba(236, 72, 153, 0.15);
    color: #BE185D;
}

.owner-line {
    color: #6A3A59;
    font-size: 15px;
}

.preview-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.stat-item {
    border: 1px solid rgba(236, 72, 153, 0.16);
    border-radius: 10px;
    padding: 11px 8px;
    background: rgba(255, 255, 255, 0.84);
    text-align: center;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45);
}

.stat-label {
    display: block;
    font-size: 11px;
    color: #BE185D;
    margin-bottom: 2px;
}

.stat-value {
    font-size: 16px;
    font-weight: 700;
    color: #111827;
}

.btn-preview-view {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 14px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    color: #FFFFFF;
    background: linear-gradient(135deg, #EC4899 0%, #DB2777 55%, #BE185D 100%);
    border: 1px solid rgba(236, 72, 153, 0.36);
    box-shadow: 0 10px 18px rgba(190, 24, 93, 0.3);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-preview-view:hover {
    color: #FFFFFF;
    transform: translateY(-1px);
    box-shadow: 0 14px 24px rgba(190, 24, 93, 0.36);
}

@media (max-width: 768px) {
    .allergy-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }
}
</style>
@endsection
