@extends('admin.dashboard')

@section('page-title', 'Pet Allergies')
@section('page-description', 'List of recorded pet allergies')

@section('content')
<div class="container-fluid">
  
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-allergies"></i> Pet Allergies</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.pet-allergies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Allergy
                </a>
                <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary">
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
                                    <div>
                                        <h5 class="mb-1">{{ $pet->name }}</h5>
                                        <span class="pet-meta">{{ ucfirst($pet->species ?? 'Unknown') }}</span>
                                    </div>
                                    <i class="fas fa-allergies text-danger"></i>
                                </div>
                                <p class="text-muted mb-3">
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
                                    <a href="{{ route('admin.pet-allergies.pet', $pet) }}" class="btn btn-info btn-sm w-100">
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
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
}

.pet-meta {
    display: inline-block;
    font-size: 12px;
    padding: 3px 10px;
    border-radius: 999px;
    background: #FEE2E2;
    color: #991B1B;
    font-weight: 600;
}

.preview-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
}

.stat-item {
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    padding: 10px 8px;
    background: #FAFAFA;
    text-align: center;
}

.stat-label {
    display: block;
    font-size: 11px;
    color: #6B7280;
    margin-bottom: 2px;
}

.stat-value {
    font-size: 16px;
    font-weight: 700;
    color: #111827;
}
</style>
@endsection
