@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('customer.appointments.index') }}" class="text-decoration-none text-muted me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="h3 mb-0">Appointment Details</h1>
                    </div>
                    <div>
                        @if($appointment->status === 'confirmed')
                            <span class="badge bg-success fs-6">Confirmed</span>
                        @elseif($appointment->status === 'cancelled')
                            <span class="badge bg-danger fs-6">Cancelled</span>
                        @elseif($appointment->status === 'completed')
                            <span class="badge bg-primary fs-6">Completed</span>
                        @else
                            <span class="badge bg-warning text-dark fs-6">Pending Confirmation</span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h5 class="text-muted small text-uppercase mb-2">Date & Time</h5>
                            <p class="fs-5 fw-bold mb-0">
                                {{ date('l, F d, Y', strtotime($appointment->appointment_date)) }}
                            </p>
                            <p class="fs-5 mb-0 text-primary">
                                <i class="far fa-clock me-2"></i>{{ date('h:i A', strtotime($appointment->appointment_date)) }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted small text-uppercase mb-2">Appointment Type</h5>
                            <p class="fs-5 fw-bold mb-0">
                                {{ ucfirst(str_replace('_', ' ', $appointment->type)) }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-muted small text-uppercase mb-3">Pet Information</h5>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                <img src="{{ $appointment->pet->photo_url }}" alt="{{ $appointment->pet->name }}" class="rounded-circle me-3" width="60" height="60" style="object-fit: cover;">
                                <div>
                                    <h5 class="mb-1">{{ $appointment->pet->name }}</h5>
                                    <p class="mb-0 text-muted small">
                                        {{ $appointment->pet->species }} • {{ $appointment->pet->breed }} • {{ $appointment->pet->age }} old
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($appointment->veterinarian)
                        <div class="mb-4">
                            <h5 class="text-muted small text-uppercase mb-2">Assigned Veterinarian</h5>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    Dr
                                </div>
                                <div>
                                    <p class="fs-5 fw-bold mb-0">Dr. {{ $appointment->veterinarian->first_name }} {{ $appointment->veterinarian->last_name }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($appointment->notes)
                        <div class="mb-4">
                            <h5 class="text-muted small text-uppercase mb-2">Your Notes</h5>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-info">
                                {{ $appointment->notes }}
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        @if($appointment->status === 'pending')
                            <a href="{{ route('customer.appointments.edit', $appointment->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Request
                            </a>
                            <form action="{{ route('customer.appointments.cancel', $appointment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times me-2"></i>Cancel Appointment
                                </button>
                            </form>
                        @endif
                        
                        @if($appointment->status === 'completed' || $appointment->status === 'cancelled')
                            <a href="{{ route('customer.appointments.create') }}" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i>Book Again
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
