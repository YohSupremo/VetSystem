@extends('admin.dashboard')

@section('page-title', 'Edit Grooming Appointment')
@section('page-description', 'Update grooming appointment details')

@push('styles')
<style>
    .form-container {
        max-width: 700px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.75rem;
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
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.35rem;
    }
    .form-control {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d1d3e2;
        padding: 0.6rem 0.8rem;
    }
    .form-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    .btn {
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.4rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-primary {
        background: linear-gradient(135deg,#9c27b0,#6a1b9a);
        color: #fff;
    }
    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }
    .btn-danger {
        background: #dc3545;
        color: #fff;
        margin-left: auto;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1><i class="fas fa-cut"></i> Edit Grooming Appointment</h1>
        <p>Update the details for this grooming visit.</p>
    </div>

    @php $appointment = $groomingAppointment->appointment; @endphp

    <form method="POST" action="{{ route('admin.grooming.update', $groomingAppointment->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Pet</label>
            <input type="text" class="form-control" value="{{ optional($appointment->pet)->name ?? 'Unknown Pet' }}" disabled>
        </div>

        <div class="form-group">
            <label for="service_id">Grooming Service</label>
            <select id="service_id" name="service_id" class="form-control" required>
                <option value="">Select a service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" @selected(old('service_id', $groomingAppointment->service_id) == $service->id)>
                        {{ $service->service_name }} 
                        @if($service->price) - ₱{{ number_format($service->price, 2) }} @endif
                        @if($service->duration_minutes) ({{ $service->duration_minutes }} min) @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="groomer_id">Assign Groomer (optional)</label>
            <select id="groomer_id" name="groomer_id" class="form-control">
                <option value="">No groomer assigned</option>
                @foreach($groomers as $groomer)
                    <option value="{{ $groomer->id }}" @selected(old('groomer_id', $groomingAppointment->groomer_id) == $groomer->id)>
                        {{ $groomer->first_name }} {{ $groomer->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="appointment_date">Date</label>
            <input type="date" id="appointment_date" name="appointment_date" class="form-control"
                   value="{{ old('appointment_date', optional($appointment->appointment_date)->format('Y-m-d')) }}" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                @php $currentStatus = $groomingAppointment->status ?? 'scheduled'; @endphp
                <option value="scheduled" @selected($currentStatus === 'scheduled')>Scheduled</option>
                <option value="in_progress" @selected($currentStatus === 'in_progress')>In Progress</option>
                <option value="completed" @selected($currentStatus === 'completed')>Completed</option>
                <option value="cancelled" @selected($currentStatus === 'cancelled')>Cancelled</option>
            </select>
        </div>

        <div class="form-group">
            <label for="special_instructions">Special Instructions (optional)</label>
            <textarea id="special_instructions" name="special_instructions" rows="3" class="form-control">{{ old('special_instructions', $groomingAppointment->special_instructions) }}</textarea>
        </div>

        <div class="form-group">
            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes', $appointment->notes) }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.grooming.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <form method="POST" action="{{ route('admin.grooming.destroy', $groomingAppointment->id) }}" class="delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </form>
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

