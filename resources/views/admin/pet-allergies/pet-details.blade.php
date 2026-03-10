@extends('admin.dashboard')

@section('page-title', 'Pet Allergy Details')
@section('page-description', 'Allergies for ' . ($pet->name ?? 'pet'))

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-allergies"></i> {{ $pet->name }} - Pet Allergies</h3>
            <a href="{{ route('admin.pet-allergies.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <div><strong>Owner:</strong>
                @if($pet->owner && $pet->owner->user)
                    {{ trim(($pet->owner->user->first_name ?? '') . ' ' . ($pet->owner->user->last_name ?? '')) }}
                @else
                    N/A
                @endif
            </div>
        </div>
    </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Allergen</th>
                            <th>Medical Record Source</th>
                            <th>Reaction</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allergies as $allergy)
                            <tr>
                                <td>{{ $allergy->id }}</td>
                                <td>{{ $allergy->allergen }}</td>
                                <td>
                                    @if($allergy->medicalRecord)
                                        <a href="{{ route('admin.medical-records.show', $allergy->medicalRecord) }}" class="btn btn-sm btn-outline-primary">
                                            MR #{{ $allergy->medical_record_id }}
                                        </a>
                                        <div class="text-muted small mt-1">
                                            {{ optional($allergy->medicalRecord->visit_date)->format('M d, Y') ?: 'No visit date' }}
                                        </div>
                                    @else
                                        <span class="text-muted">Manual entry</span>
                                    @endif
                                </td>
                                <td>{{ $allergy->reaction_type ?: 'N/A' }}</td>
                                <td>
                                    <span class="severity-badge severity-{{ $allergy->severity ?? 'mild' }}">{{ ucfirst($allergy->severity ?? 'mild') }}</span>
                                </td>
                                <td>
                                    @if($allergy->is_active)
                                        <span class="status-badge status-active">Active</span>
                                    @else
                                        <span class="status-badge status-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if($showTrash ?? false)
                                            <form action="{{ route('admin.pet-allergies.restore', $allergy->id) }}" method="POST" onsubmit="return confirm('Restore this allergy record?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.pet-allergies.show', $allergy) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.pet-allergies.edit', $allergy) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.pet-allergies.destroy', $allergy) }}" method="POST" onsubmit="return confirm('Delete this allergy record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No allergies found for this pet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.status-badge,
.severity-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
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
