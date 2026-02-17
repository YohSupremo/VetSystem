@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Medical Records</h1>
            <p class="text-muted">View medical history for your pets</p>
        </div>
    </div>

    @if($pets->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-notes-medical fa-3x text-muted"></i>
            </div>
            <h5>No pets registered</h5>
            <p class="text-muted">Register your pets to view their medical history.</p>
            <a href="{{ route('customer.pets.create') }}" class="btn btn-outline-primary mt-2">
                Register a Pet
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($pets as $pet)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3 position-relative d-inline-block">
                                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="rounded-circle shadow-sm" width="120" height="120" style="object-fit: cover;">
                            </div>
                            
                            <h3 class="h4 fw-bold mb-1">{{ $pet->name }}</h3>
                            <p class="text-muted mb-3">{{ $pet->species }} • {{ $pet->breed }}</p>
                            
                            <div class="d-grid">
                                <a href="{{ route('customer.medical-records.pet', $pet->id) }}" class="btn btn-primary">
                                    <i class="fas fa-file-medical me-2"></i>View Records
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
