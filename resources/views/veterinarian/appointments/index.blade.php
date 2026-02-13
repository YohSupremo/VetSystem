@extends('veterinarian.layout')

@section('title', 'Appointments - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Appointments</h2>
        <a href="#" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    @if($appointments->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Patient</th>
                        <th>Owner</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $appointment->appointment_date->format('M j, Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $appointment->appointment_date->format('g:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pet-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">🐾</div>
                                    <div>
                                        <strong>{{ $appointment->pet->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $appointment->pet->species }} • {{ $appointment->pet->breed }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $appointment->pet->owner->first_name }} {{ $appointment->pet->owner->last_name }}
                                <br>
                                <small class="text-muted">{{ $appointment->pet->owner->user->contact_number }}</small>
                            </td>
                            <td>
                                <span class="status-badge {{ $appointment->status }}">
                                    {{ ucfirst($appointment->type) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $appointment->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(is_null($appointment->veterinarian_id))
                                        <form action="{{ route('veterinarian.appointments.claim', $appointment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-hand-paper"></i> Claim
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('veterinarian.appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                            <form action="#" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $appointment->status === 'confirmed' ? 'in_progress' : 'completed' }}">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-check"></i> {{ $appointment->status === 'confirmed' ? 'Start' : 'Complete' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $appointments->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <h3>No appointments found</h3>
            <p>You don't have any appointments scheduled.</p>
        </div>
    @endif
</div>
@endsection
