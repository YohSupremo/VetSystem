@extends('admin.dashboard')

@section('page-title', 'Incident Details')
@section('page-description', 'Review incident details and assign responders')

@section('content')
<style>
    .detail-label {
        color: var(--light-text);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .detail-value {
        font-weight: 600;
        color: var(--dark-text);
    }
    
    .badge.bg-primary {
        background-color: #0d6efd !important;
    }
    
    .badge.bg-secondary {
        background-color: #6c757d !important;
    }
</style>

<div class="card">
    <div class="card-header">
        <h3>Incident {{ $incident->incident_number }}</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.incidents.edit', $incident->id) }}" class="btn btn-primary">Edit</a>
            <form action="{{ route('admin.incidents.destroy', $incident->id) }}" method="POST" onsubmit="return confirm('Delete this incident?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <a href="{{ route('admin.incidents.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="detail-label">Status</div>
                <div class="detail-value text-capitalize">{{ $incident->status }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Severity</div>
                <div class="detail-value text-capitalize">{{ $incident->severity }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Incident Date</div>
                <div class="detail-value">{{ optional($incident->incident_date)->format('M d, Y h:i A') }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Pet</div>
                <div class="detail-value">{{ $incident->pet->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Type</div>
                <div class="detail-value text-capitalize">{{ str_replace('_', ' ', $incident->incident_type) }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Reported By (Filed By)</div>
                <div class="detail-value">
                    {{ $incident->reportedBy->first_name ?? 'N/A' }} {{ $incident->reportedBy->last_name ?? '' }}
                    @if($incident->reportedBy)
                        <span class="badge bg-primary ms-2">{{ ucfirst($incident->reportedBy->role) }}</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Affected User</div>
                <div class="detail-value">
                    {{ $incident->affectedUser->first_name ?? 'N/A' }} {{ $incident->affectedUser->last_name ?? '' }}
                    @if($incident->affectedUser)
                        <span class="badge bg-secondary ms-2">{{ ucfirst($incident->affectedUser->role) }}</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Cage</div>
                <div class="detail-value">{{ $incident->cage->cage_code ?? 'N/A' }}</div>
            </div>
            <div class="col-12">
                <div class="detail-label">Location</div>
                <div class="detail-value">{{ $incident->location }}</div>
            </div>
            <div class="col-12">
                <div class="detail-label">Description</div>
                <div class="detail-value">{{ $incident->description }}</div>
            </div>
            @if($incident->immediate_action_taken)
                <div class="col-12">
                    <div class="detail-label">Immediate Action Taken</div>
                    <div class="detail-value">{{ $incident->immediate_action_taken }}</div>
                </div>
            @endif
            @if($incident->root_cause)
                <div class="col-12">
                    <div class="detail-label">Root Cause</div>
                    <div class="detail-value">{{ $incident->root_cause }}</div>
                </div>
            @endif
            @if($incident->corrective_action)
                <div class="col-12">
                    <div class="detail-label">Corrective Action</div>
                    <div class="detail-value">{{ $incident->corrective_action }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Assign Responders</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.incidents.status-update', $incident->id) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}" {{ $incident->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Responder</label>
                    <select name="responder" class="form-select">
                        <option value="">Select responder</option>
                        @foreach($responders as $responder)
                            <option value="{{ $responder }}">{{ $responder }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Admin Note</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Add any instructions or updates..."></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save me-2"></i>Save Update
                </button>
            </div>
        </form>
    </div>
</div>

@if($incident->incidentNotes->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h3>Incident Notes</h3>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($incident->incidentNotes as $note)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ optional($note->added_at)->format('M d, Y h:i A') }}</small>
                            @if($note->addedBy)
                                <span class="badge bg-info text-dark">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $note->addedBy->first_name }} {{ $note->addedBy->last_name }}
                                </span>
                            @endif
                        </div>
                        <div class="fw-semibold">{{ $note->note }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
@endsection
