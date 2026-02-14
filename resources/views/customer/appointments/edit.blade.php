@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('customer.appointments.show', $appointment->id) }}" class="text-decoration-none text-muted me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="h3 mb-0">Edit Appointment</h1>
                            <p class="text-muted mb-0">Reschedule or update your request</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.appointments.update', $appointment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Pet Selection (Disabled/Read-only usually, but let's allow change if user wants) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pet</label>
                            <select name="pet_id" class="form-select">
                                @foreach($pets as $pet)
                                    <option value="{{ $pet->id }}" {{ old('pet_id', $appointment->pet_id) == $pet->id ? 'selected' : '' }}>
                                        {{ $pet->name }} ({{ $pet->species }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Selection -->
                        <div class="mb-4">
                            <label for="appointment_date" class="form-label fw-bold">New Date</label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" 
                                   min="{{ echo date('Y-m-d', strtotime('+1 day')); }}" 
                                   value="{{ old('appointment_date', date('Y-m-d', strtotime($appointment->appointment_date))) }}" required>
                        </div>

                        <!-- Time Selection -->
                        <div class="mb-4">
                            <label for="appointment_time" class="form-label fw-bold">New Time</label>
                            <select name="appointment_time" id="appointment_time" class="form-select" required>
                                @php
                                    $times = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'];
                                    $current_time = date('H:i', strtotime($appointment->appointment_date));
                                @endphp
                                @foreach($times as $time)
                                    <option value="{{ $time }}" {{ old('appointment_time', $current_time) == $time ? 'selected' : '' }}>
                                        {{ date('h:i A', strtotime("2000-01-01 $time")) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Appointment Type -->
                        <div class="mb-4">
                            <label for="type" class="form-label fw-bold">Type</label>
                            <select name="type" id="type" class="form-select" required>
                                @foreach($appointmentTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $appointment->type) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $appointment->notes) }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Update Appointment</button>
                            <a href="{{ route('customer.appointments.show', $appointment->id) }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
