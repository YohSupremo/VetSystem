@extends('admin.dashboard')

@section('page-title', 'Grooming Details')
@section('page-description', 'View grooming appointment details')

@push('styles')
<style>
    .show-container {
        max-width: 800px;
        margin: 2rem auto;
    }
    .page-header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 0.3rem;
    }
    .page-header p {
        color: #6c757d;
        margin: 0;
    }
    .detail-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
    }
    .detail-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .detail-value {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }
    .btn {
        border-radius: 8px;
        padding: 0.6rem 1.4rem;
        border: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-primary { background: linear-gradient(135deg,#9c27b0,#6a1b9a); color: #fff; }
    .btn-secondary { background: #6c757d; color: #fff; }
    .btn-danger { background: #dc3545; color: #fff; }
</style>
@endpush

@section('content')
<div class="show-container">
    <div class="page-header">
        <h1><i class="fas fa-cut"></i> Grooming Details</h1>
        <p>View grooming appointment information.</p>
    </div>

    @php $appointment = $groomingAppointment->appointment; @endphp
    
    <div class="detail-card">
        <div class="detail-label">Pet</div>
        <div class="detail-value">
            {{ optional($appointment->pet)->name ?? 'Unknown Pet' }}
        </div>

        <div class="detail-label" style="margin-top:1rem;">Owner</div>
        <div class="detail-value">
            @php $ownerUser = optional(optional($appointment->pet)->owner)->user; @endphp
            @if($ownerUser)
                {{ $ownerUser->first_name }} {{ $ownerUser->last_name }}
            @else
                Unknown Owner
            @endif
        </div>

        <div class="detail-label" style="margin-top:1rem;">Grooming Service</div>
        <div class="detail-value">
            {{ $groomingAppointment->service->service_name ?? 'N/A' }}
            @if($groomingAppointment->service)
                @if($groomingAppointment->service->price)
                    - ₱{{ number_format($groomingAppointment->service->price, 2) }}
                @endif
                @if($groomingAppointment->service->duration_minutes)
                    ({{ $groomingAppointment->service->duration_minutes }} minutes)
                @endif
            @endif
        </div>

        <div class="detail-label" style="margin-top:1rem;">Assigned Groomer</div>
        <div class="detail-value">
            @if($groomingAppointment->groomer)
                {{ $groomingAppointment->groomer->first_name }} {{ $groomingAppointment->groomer->last_name }}
            @else
                Not assigned
            @endif
        </div>

        <div class="detail-label" style="margin-top:1rem;">Date</div>
        <div class="detail-value">
            {{ $appointment && $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : 'N/A' }}
        </div>

        <div class="detail-label" style="margin-top:1rem;">Status</div>
        <div class="detail-value">
            {{ ucfirst(str_replace('_',' ', $groomingAppointment->status ?? 'scheduled')) }}
        </div>

        @if($groomingAppointment->special_instructions)
            <div class="detail-label" style="margin-top:1rem;">Special Instructions</div>
            <div class="detail-value">
                {{ $groomingAppointment->special_instructions }}
            </div>
        @endif

        @if($appointment && $appointment->notes)
            <div class="detail-label" style="margin-top:1rem;">Notes</div>
            <div class="detail-value">
                {{ $appointment->notes }}
            </div>
        @endif

        <div class="actions">
            <a href="{{ route('admin.grooming.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.grooming.edit', $groomingAppointment->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.grooming.destroy', $groomingAppointment->id) }}" class="delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.delete-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var ok = confirm('Are you sure you want to delete this grooming appointment?');
            if (!ok) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush
@endsection

