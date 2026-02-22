@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'My Pets - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.pets-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.page-header {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 2rem;
    padding: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
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
    transition: var(--transition-smooth);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
    height: 100%;
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

.pet-avatar-container {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
}

.pet-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    transition: var(--transition-smooth);
}

.pet-card:hover .pet-avatar {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(31, 38, 135, 0.4);
    border-color: rgba(255, 255, 255, 0.4);
}

.pet-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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

.pet-stats {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.pet-stat {
    text-align: center;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: var(--transition-quick);
    position: relative;
}

.pet-stat::before {
    content: '';
    position: absolute;
    inset: -1px;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.3), rgba(236, 72, 153, 0.3));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.pet-stat:hover::before {
    opacity: 1;
}

.pet-stat:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    border-color: rgba(147, 51, 234, 0.4);
    box-shadow: 0 4px 12px rgba(147, 51, 234, 0.2);
}

.stat-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pet-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn-pet-action {
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

.btn-pet-action::before {
    content: '';
    position: absolute;
    inset: -1px;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(236, 72, 153, 0.2));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.btn-pet-action:hover::before {
    opacity: 1;
}

.btn-pet-action:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.btn-pet-action.danger {
    border-color: rgba(239, 68, 68, 0.3);
}

.btn-pet-action.danger::before {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
}

.btn-pet-action.danger:hover {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.4);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 2rem;
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
    margin-bottom: 2rem;
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

.alert {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.alert-success {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
}

.alert-success .btn-close {
    filter: brightness(0) invert(1);
}

@media (max-width: 768px) {
    .customer-main {
        padding: 2rem 1rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .pets-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .pet-card {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .pet-stat {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .btn-pet-action {
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
    
    .pet-stats {
        gap: 1rem;
    }
    
    .pet-stat {
        padding: 0.5rem 0.75rem;
    }
    
    .stat-value {
        font-size: 1rem;
    }
    
    .stat-label {
        font-size: 0.7rem;
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

<div class="pets-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">My Pets</h1>
                <p class="page-subtitle">Manage your beloved pet profiles</p>
            </div>
            <a href="{{ route('customer.pets.create') }}" class="btn-gradient">
                <i class="fas fa-plus me-2"></i>Add New Pet
            </a>
        </div>
    </div>

    @if($pets->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🐕</div>
            <h2 class="empty-title">No pets registered yet</h2>
            <p class="empty-description">
                Start managing your pet's health by adding your first furry friend to the system.
            </p>
            <a href="{{ route('customer.pets.create') }}" class="btn-gradient">
                <i class="fas fa-plus me-2"></i>Register Your First Pet
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($pets as $pet)
                <div class="col-md-6 col-lg-4">
                    <div class="pet-card">
                        <div class="card-body p-4 text-center">
                            <div class="pet-avatar-container">
                                <div class="pet-avatar">
                                    @if($pet->photo_url)
                                        <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-gradient">
                                            <i class="fas fa-paw fa-3x text-white"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <h3 class="pet-name">{{ $pet->name }}</h3>
                            <p class="pet-breed">{{ $pet->species }} • {{ $pet->breed }}</p>
                            
                            <div class="pet-stats">
                                <div class="pet-stat">
                                    <span class="stat-value">{{ $pet->age }}</span>
                                    <span class="stat-label">Age</span>
                                </div>
                                <div class="pet-stat">
                                    <span class="stat-value">{{ $pet->gender }}</span>
                                    <span class="stat-label">Gender</span>
                                </div>
                                <div class="pet-stat">
                                    <span class="stat-value">{{ $pet->weight }}kg</span>
                                    <span class="stat-label">Weight</span>
                                </div>
                            </div>
                            
                            <div class="pet-actions">
                                <a href="{{ route('customer.pets.show', $pet->id) }}" class="btn-pet-action">
                                    <i class="fas fa-paw me-2"></i>View Profile
                                </a>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('customer.pets.edit', $pet->id) }}" class="btn-pet-action">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <form action="{{ route('customer.pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove {{ $pet->name }}? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-pet-action danger w-100">
                                                <i class="fas fa-trash-alt me-1"></i> Remove
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
