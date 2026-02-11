@extends('veterinarian.layout')

@section('title', 'Create Appointment - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Create New Appointment</h2>
        <a href="{{ route('veterinarian.appointments.index') }}" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Appointments
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('veterinarian.appointments.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="pet_id" class="form-label">Select Pet</label>
                    <select name="pet_id" id="pet_id" class="form-control" required>
                        <option value="">Choose a pet...</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}">
                                {{ $pet->name }} ({{ $pet->owner->first_name }} {{ $pet->owner->last_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="appointment_date" class="form-label">Appointment Date</label>
                    <input type="date" name="appointment_date" id="appointment_date" 
                           class="form-control" required min="{{ date('Y-m-d') }}">
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="time" name="start_time" id="start_time" 
                           class="form-control" required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="time" name="end_time" id="end_time" 
                           class="form-control" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Reason for Visit</label>
            <textarea name="reason" id="reason" class="form-control" rows="3" required
                      placeholder="e.g., Annual checkup, Vaccination, Surgery consultation..."></textarea>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Additional Notes</label>
            <textarea name="notes" id="notes" class="form-control" rows="4"
                      placeholder="Any additional information about the appointment..."></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-action">
                <i class="fas fa-plus me-2"></i>Create Appointment
            </button>
            <a href="{{ route('veterinarian.appointments.index') }}" class="btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--primary-purple);
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--light-purple);
    border-radius: 0.5rem;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-purple);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.alert li {
    margin-bottom: 0.5rem;
}
</style>
@endpush
