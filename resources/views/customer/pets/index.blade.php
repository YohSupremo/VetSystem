@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">My Pets</h1>
            <p class="text-muted">Manage your pet profiles</p>
        </div>
        <a href="{{ route('customer.pets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Pet
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($pets->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-paw fa-3x text-muted"></i>
            </div>
            <h5>No pets registered yet</h5>
            <p class="text-muted">Add your beloved pets to manage their health records and appointments.</p>
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
                                @if($pet->photo_path)
                                    <img src="{{ asset($pet->photo_path) }}" alt="{{ $pet->name }}" class="rounded-circle shadow-sm" width="120" height="120" style="object-fit: cover;">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto" style="width: 120px; height: 120px;">
                                        <i class="fas fa-paw fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <h3 class="h4 fw-bold mb-1">{{ $pet->name }}</h3>
                            <p class="text-muted mb-3">{{ $pet->species }} • {{ $pet->breed }}</p>
                            
                            <div class="d-flex justify-content-center gap-3 mb-4">
                                <div class="text-center px-2">
                                    <div class="fw-bold">{{ $pet->age }}</div>
                                    <div class="small text-muted">Age</div>
                                </div>
                                <div class="vr"></div>
                                <div class="text-center px-2">
                                    <div class="fw-bold">{{ $pet->gender }}</div>
                                    <div class="small text-muted">Gender</div>
                                </div>
                                <div class="vr"></div>
                                <div class="text-center px-2">
                                    <div class="fw-bold">{{ $pet->weight }} kg</div>
                                    <div class="small text-muted">Weight</div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('customer.pets.show', $pet->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="fas fa-notes-medical me-1"></i> Profile
                                </a>
                                <a href="{{ route('customer.pets.edit', $pet->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-edit"></i>
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
