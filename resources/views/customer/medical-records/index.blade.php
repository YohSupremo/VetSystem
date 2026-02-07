@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Medical Records - PawCare')

@push('styles')
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.customer-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
}

.logo-section .paw-icon {
    font-size: 2.5rem;
    animation: bounce 2s infinite;
}

.logo-section h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.customer-main {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.page-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.pet-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 1.5rem;
    padding: 1.5rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
    display: flex;
    gap: 1.5rem;
    align-items: start;
}

.pet-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(147, 51, 234, 0.15);
}

.pet-photo {
    width: 100px;
    height: 100px;
    border-radius: 1rem;
    object-fit: cover;
    border: 3px solid rgba(167, 139, 250, 0.3);
}

.pet-info {
    flex: 1;
}

.pet-info h3 {
    color: var(--primary-purple);
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.pet-details {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, rgba(167, 139, 250, 0.1), rgba(236, 72, 153, 0.1));
    border-radius: 0.75rem;
    font-size: 0.9rem;
}

.records-count {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border-radius: 0.75rem;
    color: #1e40af;
    font-weight: 600;
}

.btn-view {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border: none;
    border-radius: 1rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(147, 51, 234, 0.4);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 2rem;
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
}
</style>
@endpush

@section('content')
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="customer-container">
    <!-- Header -->
    <header class="customer-header">
        <div class="header-content d-flex justify-content-between align-items-center">
            <div class="logo-section d-flex align-items-center gap-3">
                <div class="paw-icon">🐾</div>
                <h1 class="mb-0">PawCare</h1>
            </div>
            <div class="user-menu d-flex align-items-center gap-3">
                <div class="user-info d-flex align-items-center gap-2">
                    <span class="welcome-text">Welcome, {{ $user->first_name }}!</span>
                    <div class="user-avatar">
                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                    </div>
                </div>
                <a href="/logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="customer-main">
        <div class="page-header">
            <h2>📋 Medical Records</h2>
            <p>View your pets' complete medical history</p>
        </div>

        @if($pets->count() > 0)
            @foreach($pets as $pet)
                <div class="pet-card">
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="pet-photo">
                    
                    <div class="pet-info">
                        <h3>{{ $pet->name }}</h3>
                        
                        <div class="pet-details">
                            <span class="detail-badge">
                                <strong>Species:</strong> {{ ucfirst($pet->species) }}
                            </span>
                            <span class="detail-badge">
                                <strong>Breed:</strong> {{ $pet->breed ?? 'Mixed' }}
                            </span>
                            <span class="detail-badge">
                                <strong>Age:</strong> {{ $pet->age }}
                            </span>
                        </div>
                        
                        <div class="d-flex gap-2 align-items-center">
                            <span class="records-count">
                                📄 {{ $pet->medicalRecords->count() }} Medical Records
                            </span>
                            <a href="{{ route('customer.medical-records.pet', $pet->id) }}" class="btn-view">
                                <span>👁️</span> View Records
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-state-icon">🐾</div>
                <h3>No Pets Registered</h3>
                <p>Please register a pet first to view medical records</p>
                <a href="{{ route('customer.pets.create') }}" class="btn-view mt-3">
                    <span>➕</span> Add Your First Pet
                </a>
            </div>
        @endif
    </main>
</div>
@endsection
