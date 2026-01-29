@extends('admin.dashboard')

@section('page-title', 'Queue Entry Details')
@section('page-description', 'View details for this queued appointment')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Appointment #{{ $appointment->id }}</h3>
        <span class="badge badge-{{ $appointment->status === 'scheduled' ? 'primary' : ($appointment->status === 'in_progress' ? 'info' : ($appointment->status === 'completed' ? 'success' : 'secondary')) }}">
            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
        </span>
    </div>
    <div class="card-body">
        @include('admin.queue.partials.appointment-card', ['appointment' => $appointment])

        <hr>

        <div class="row">
            <div class="col-md-6">
                <h5>Pet & Owner</h5>
                <p class="mb-1"><strong>Pet:</strong> {{ optional($appointment->pet)->name ?? 'Unknown' }}</p>
                <p class="mb-1"><strong>Species:</strong> {{ optional($appointment->pet)->species ?? 'N/A' }}</p>
                <p class="mb-1">
                    <strong>Owner:</strong>
                    @if(optional(optional($appointment->pet)->owner)->user)
                        {{ $appointment->pet->owner->user->first_name }} {{ $appointment->pet->owner->user->last_name }}
                    @else
                        Unknown
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <h5>Appointment Info</h5>
                <p class="mb-1"><strong>Date:</strong> {{ $appointment->appointment_date->format('Y-m-d') }}</p>
                <p class="mb-1"><strong>Time:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                <p class="mb-1">
                    <strong>Veterinarian:</strong>
                    @if($appointment->veterinarian)
                        {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}
                    @else
                        Unassigned
                    @endif
                </p>
                <p class="mb-1"><strong>Type:</strong> {{ ucfirst($appointment->type) }}</p>
                <p class="mb-1"><strong>Queue Number:</strong> {{ $appointment->queue_number ?? 'Not assigned' }}</p>
            </div>
        </div>

        @if($appointment->reason)
            <hr>
            <h5>Reason</h5>
            <p>{{ $appointment->reason }}</p>
        @endif

        @if($appointment->notes)
            <hr>
            <h5>Internal Notes</h5>
            <p>{{ $appointment->notes }}</p>
        @endif

        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('admin.queue.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Queue
            </a>
            <a href="{{ route('admin.queue.edit', $appointment) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Queue Entry
            </a>
        </div>
    </div>
</div>
@endsection

