@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('content')
@include('layout.customer-navbar')
<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('customer.appointments.index') }}" class="text-decoration-none text-muted me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h5 class="mb-0 fw-bold text-primary">Book Appointment</h5>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.appointments.store') }}" method="POST">
                        @csrf
                        
                        <!-- Pet Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted mb-2">Select Pet</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($pets as $pet)
                                    <input type="radio" class="btn-check" name="pet_id" id="pet_{{ $pet->id }}" value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-secondary rounded-pill px-3" for="pet_{{ $pet->id }}">
                                        {{ $pet->name }}
                                    </label>
                                @endforeach
                            </div>
                            @error('pet_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Appointment Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold text-muted">Reason for Visit</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Select reason...</option>
                                @foreach($appointmentTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <!-- Date Selection -->
                            <div class="col-6">
                                <label for="appointment_date" class="form-label fw-bold text-muted">Date</label>
                                <input type="date" name="appointment_date" id="appointment_date" class="form-control" 
                                       min="{{ date('Y-m-d') }}" value="{{ old('appointment_date') }}" required>
                            </div>
                            
                            <!-- Time Selection -->
                            <div class="col-6">
                                <label for="start_time" class="form-label fw-bold text-muted">Time</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" 
                                       value="{{ old('start_time') }}" required>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold text-muted">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Describe symptoms or specific concerns...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">
                                Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
