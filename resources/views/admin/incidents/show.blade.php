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
</style>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3>Incident {{ $incident->incident_number }}</h3>
        <a href="{{ route('admin.incidents.index') }}" class="btn btn-secondary">Back to List</a>
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
                <div class="detail-label">Reported By</div>
                <div class="detail-value">{{ $incident->reportedBy->first_name ?? 'N/A' }} {{ $incident->reportedBy->last_name ?? '' }}</div>
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
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Assign Responders</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.incidents.update', $incident->id) }}">
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
                        <div class="small text-muted">{{ optional($note->added_at)->format('M d, Y h:i A') }}</div>
                        <div class="fw-semibold">{{ $note->note }}</div>
                        <div class="small text-muted">Added by: {{ $note->addedBy->first_name ?? 'System' }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
@endsection
