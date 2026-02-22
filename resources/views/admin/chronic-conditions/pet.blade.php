@extends('admin.dashboard')

@section('page-title', 'Pet Chronic Conditions')
@section('page-description', 'Chronic conditions for ' . ($pet->name ?? 'pet'))

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-notes-medical"></i> {{ $pet->name }} - Chronic Conditions</h3>
            <a href="{{ route('admin.chronic-conditions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <p class="mb-3"><strong>Owner:</strong>
                @if($pet->owner && $pet->owner->user)
                    {{ trim(($pet->owner->user->first_name ?? '') . ' ' . ($pet->owner->user->last_name ?? '')) }}
                @else
                    N/A
                @endif
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Condition</th>
                            <th>Medical Record Source</th>
                            <th>Diagnosed</th>
                            <th>Treatment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conditions as $condition)
                            <tr>
                                <td>{{ $condition->id }}</td>
                                <td>{{ $condition->condition_name }}</td>
                                <td>
                                    @if($condition->medicalRecord)
                                        <a href="{{ route('admin.medical-records.show', $condition->medicalRecord) }}" class="btn btn-sm btn-outline-primary">
                                            MR #{{ $condition->medical_record_id }}
                                        </a>
                                        <div class="text-muted small mt-1">
                                            {{ optional($condition->medicalRecord->visit_date)->format('M d, Y') ?: 'No visit date' }}
                                        </div>
                                    @else
                                        <span class="text-muted">Manual entry</span>
                                    @endif
                                </td>
                                <td>{{ $condition->diagnosed_date ? $condition->diagnosed_date->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($condition->ongoing_treatment ?? 'N/A', 60) }}</td>
                                <td>
                                    @if($condition->is_active)
                                        <span class="status-badge status-active">Active</span>
                                    @else
                                        <span class="status-badge status-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('admin.chronic-conditions.show', $condition) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.chronic-conditions.edit', $condition) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.chronic-conditions.destroy', $condition) }}" method="POST" onsubmit="return confirm('Delete this chronic condition record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No chronic conditions found for this pet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 14px;
    font-size: 12px;
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
</style>
@endsection
