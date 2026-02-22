@extends('admin.dashboard')

@section('page-title', 'Chronic Condition Details')
@section('page-description', 'View chronic condition information')

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-notes-medical"></i> Chronic Condition #{{ $condition->id }}</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.chronic-conditions.edit', $condition) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('admin.chronic-conditions.destroy', $condition) }}" method="POST" onsubmit="return confirm('Delete this chronic condition?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                <a href="{{ route('admin.chronic-conditions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Pet</label>
                        <div>{{ $condition->pet->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Owner</label>
                        <div>
                            @if($condition->pet && $condition->pet->owner && $condition->pet->owner->user)
                                {{ trim(($condition->pet->owner->user->first_name ?? '') . ' ' . ($condition->pet->owner->user->last_name ?? '')) }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Condition Name</label>
                        <div>{{ $condition->condition_name }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Diagnosed Date</label>
                        <div>{{ $condition->diagnosed_date ? $condition->diagnosed_date->format('M d, Y') : 'N/A' }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="detail-item">
                        <label>Status</label>
                        <div>
                            @if($condition->is_active)
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="detail-item">
                        <label>Ongoing Treatment</label>
                        <div>{{ $condition->ongoing_treatment ?: 'N/A' }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="detail-item">
                        <label>Notes</label>
                        <div>{{ $condition->notes ?: 'N/A' }}</div>
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

.status-badge {
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
</style>
@endsection
