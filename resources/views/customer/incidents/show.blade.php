@extends('layout.base')

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

        @if($incident->incidentNotes->isNotEmpty())
            <hr>
            <h5 class="mb-3">Updates</h5>
            <ul class="list-group list-group-flush">
                @foreach($incident->incidentNotes as $note)
                    <li class="list-group-item">
                        <div class="small text-muted">{{ optional($note->added_at)->format('M d, Y h:i A') }}</div>
                        <div class="fw-semibold">{{ $note->note }}</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
