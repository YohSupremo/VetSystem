@extends('layout.base')
@php($bodyClass = 'customer-body')

@section('title', 'Scan Your Pets')

@section('content')
@include('layout.customer-navbar')

<style>
    .pet-qr-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .pet-qr-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
    
    .qr-code-box {
        background: white;
        padding: 15px;
        border-radius: 12px;
        display: inline-block;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .qr-code-box img {
        display: block;
        width: 150px;
        height: 150px;
    }
    
    .btn-simulate {
        background: linear-gradient(135deg, #FF7E7E 0%, #FF6B6B 100%);
        color: white;
        border: none;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .btn-simulate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 126, 126, 0.4);
        color: white;
    }
</style>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-qrcode"></i> Scan Your Caged Pets
            </h2>
        </div>
    </div>

    @if($pets->isEmpty())
        <div class="alert alert-info text-center">
            <h4><i class="fas fa-inbox"></i> No Caged Pets</h4>
            <p>You don't have any pets currently in boarding or cages.</p>
            <div class="mt-3">
                <a href="{{ route('customer.pets.index') }}" class="btn btn-primary">
                    <i class="fas fa-paw"></i> View All Pets
                </a>
                <a href="{{ route('customer.pets.create') }}" class="btn btn-secondary">
                    <i class="fas fa-plus"></i> Add New Pet
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($pets as $pet)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm pet-qr-card">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px; overflow: hidden;">
                            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold">{{ $pet->name }}</h5>
                            <p class="card-text text-muted small">
                                <strong>Species:</strong> {{ $pet->species ?? 'N/A' }}<br>
                                <strong>Breed:</strong> {{ $pet->breed ?? 'N/A' }}<br>
                                <strong>Weight:</strong> {{ $pet->weight ? $pet->weight . ' kg' : 'N/A' }}
                            </p>

                            @if($pet->cageAssignments && $pet->cageAssignments->isNotEmpty())
                                @php($assignment = $pet->cageAssignments->first())
                                <div class="alert alert-warning small mb-3">
                                    <strong>Current Cage:</strong> {{ $assignment->cage->cage_code ?? $assignment->cage->name ?? 'N/A' }}<br>
                                    <strong>Checked In:</strong> {{ $assignment->check_in_time ? $assignment->check_in_time->format('M d, Y H:i') : 'N/A' }}
                                </div>
                            @endif

                            <div class="qr-code-box mb-3">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&ecc=H&data={{ urlencode($pet->scanUrl) }}" alt="QR Code">
                            </div>

                            <div class="small text-muted mb-2" style="font-family: 'Courier New', monospace; word-break: break-all;">
                                {{ $pet->scanUrl }}
                            </div>

                            <a href="{{ $pet->scanUrl }}" target="_blank" class="btn btn-simulate w-100">
                                <i class="fas fa-qrcode"></i> Simulate Scan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
