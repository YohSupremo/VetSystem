@extends('layout.base')

@push('styles')
<style>
    .badge.bg-info {
        background-color: #0dcaf0 !important;
        color: #000;
        padding: 0.35em 0.65em;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .list-group-item {
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.375rem;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Incident {{ $incident->incident_number }}</h1>
            <p class="text-muted">Status: <span class="text-capitalize">{{ $incident->status }}</span></p>
        </div>
        <a href="{{ route('customer.incidents.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="bg-white p-4 rounded-3 shadow-sm">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-muted">Pet</label>
                <div class="fw-semibold">{{ $incident->pet->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted">Incident Date</label>
                <div class="fw-semibold">{{ optional($incident->incident_date)->format('M d, Y h:i A') }}</div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted">Filed By</label>
                <div class="fw-semibold">
                    {{ $incident->reportedBy->first_name ?? 'N/A' }} {{ $incident->reportedBy->last_name ?? '' }}
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted">Type</label>
                <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $incident->incident_type) }}</div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted">Severity</label>
                <div class="fw-semibold text-capitalize">{{ $incident->severity }}</div>
            </div>
            <div class="col-12">
                <label class="form-label text-muted">Location</label>
                <div class="fw-semibold">{{ $incident->location }}</div>
            </div>
            <div class="col-12">
                <label class="form-label text-muted">Description</label>
                <div class="fw-semibold">{{ $incident->description }}</div>
            </div>
            @if($incident->immediate_action_taken)
                <div class="col-12">
                    <label class="form-label text-muted">Immediate Action Taken</label>
                    <div class="fw-semibold">{{ $incident->immediate_action_taken }}</div>
                </div>
            @endif
        </div>

        <div class="mt-4 pt-4 border-top">
            <h5 class="mb-3"><i class="fas fa-headset me-2"></i>Responders & Updates</h5>
            @if($incident->incidentNotes->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach($incident->incidentNotes as $note)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="small text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ optional($note->added_at)->format('M d, Y h:i A') }}
                                </div>
                                @if($note->addedBy)
                                    <div class="small badge bg-info text-dark">
                                        <i class="fas fa-badge-check me-1"></i>
                                        <strong>{{ $note->addedBy->first_name }} {{ $note->addedBy->last_name }}</strong>
                                        @if($note->addedBy->role)
                                            <span class="badge bg-secondary text-capitalize ms-1">{{ $note->addedBy->role }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="fw-semibold">{{ $note->note }}</div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>No responders have provided updates yet. Our team will review your report and respond shortly.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
