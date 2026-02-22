@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Medical Records - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
}

.pet-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
    transition: var(--transition-smooth);
}

.pet-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    pointer-events: none;
}

.pet-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 60px rgba(31, 38, 135, 0.4);
    border-color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
}

.pet-avatar img {
    border: 3px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.2);
}

.pet-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
}

.pet-breed {
    color: #333;
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

.btn-view-records {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    padding: 0.75rem 1rem;
    color: #000;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: var(--transition-smooth);
    text-align: center;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-view-records::before {
    content: '';
    position: absolute;
    inset: -1px;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(236, 72, 153, 0.2));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.btn-view-records:hover::before {
    opacity: 1;
}

.btn-view-records:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
}

.empty-state::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 4px 10px rgba(147, 51, 234, 0.3));
}

.empty-title {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
}

.empty-description {
    color: #333;
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@media (max-width: 768px) {
    .pet-card {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .btn-view-records {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .empty-state {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="customer-container">
    <main class="customer-main">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <h1 class="page-title">Medical Records</h1>
            <p class="page-subtitle">View medical history for your pets</p>
        </div>

        @if($pets->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-notes-medical"></i>
                </div>
                <h2 class="empty-title">No pets registered</h2>
                <p class="empty-description">Register your pets to view their medical history.</p>
                <a href="{{ route('customer.pets.create') }}" class="btn-view-records">
                    <i class="fas fa-plus me-2"></i>Register a Pet
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach($pets as $pet)
                    <div class="col-md-6 col-lg-4">
                        <div class="pet-card h-100">
                            <div class="card-body p-4 text-center">
                                <div class="mb-3 position-relative d-inline-block">
                                    <div class="pet-avatar">
                                        <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                                    </div>
                                </div>
                                
                                <h3 class="pet-name">{{ $pet->name }}</h3>
                                <p class="pet-breed">{{ $pet->species }} • {{ $pet->breed }}</p>
                                
                                <div class="d-grid">
                                    <a href="{{ route('customer.medical-records.pet', $pet->id) }}" class="btn-view-records">
                                        <i class="fas fa-file-medical me-2"></i>View Records
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</div>
@endsection
