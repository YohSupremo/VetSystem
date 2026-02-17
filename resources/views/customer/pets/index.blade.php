@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <style>
                .hover-card {
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }
                .hover-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
                }
            </style>
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
                                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="rounded-circle shadow-sm" width="120" height="120" style="object-fit: cover;">
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
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.pets.show', $pet->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-paw me-1"></i> Profile
                                </a>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('customer.pets.edit', $pet->id) }}" class="btn btn-outline-secondary w-100" title="Edit Pet">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <form action="{{ route('customer.pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove {{ $pet->name }}? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100" title="Remove Pet">
                                                <i class="fas fa-trash-alt"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
