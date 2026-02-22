@extends('admin.dashboard')

@section('page-title', 'Pet Allergy Details')
@section('page-description', 'View pet allergy information')

@section('content')
<div class="container-fluid">
  
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-allergies"></i> Pet Allergy #{{ $allergy->id }}</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.pet-allergies.edit', $allergy) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.pet-allergies.destroy', $allergy) }}" method="POST" onsubmit="return confirm('Delete this allergy record?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                <a href="{{ route('admin.pet-allergies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Pet</label>
                        <div>{{ $allergy->pet->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Owner</label>
                        <div>
                            @if($allergy->pet && $allergy->pet->owner && $allergy->pet->owner->user)
                                {{ trim(($allergy->pet->owner->user->first_name ?? '') . ' ' . ($allergy->pet->owner->user->last_name ?? '')) }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Allergen</label>
                        <div>{{ $allergy->allergen }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Reaction Type</label>
                        <div>{{ $allergy->reaction_type ?: 'N/A' }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Severity</label>
                        <div>
                            <span class="severity-badge severity-{{ $allergy->severity ?? 'mild' }}">{{ ucfirst($allergy->severity ?? 'mild') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Status</label>
                        <div>
                            @if($allergy->is_active)
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Diagnosed Date</label>
                        <div>{{ $allergy->diagnosed_date ? $allergy->diagnosed_date->format('M d, Y') : 'N/A' }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="detail-item">
                        <label>Notes</label>
                        <div>{{ $allergy->notes ?: 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-item label {
    display: block;
    margin-bottom: 6px;
    font-weight: 700;
    color: var(--dark-text);
}

.detail-item div {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px 12px;
    color: #374151;
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
</style>
@endsection
