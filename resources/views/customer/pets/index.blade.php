@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'My Pets - PawCare')

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

.page-header p {
    color: #6B7280;
    font-size: 1.1rem;
}

.pets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.pet-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 1.5rem;
    overflow: hidden;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.pet-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.2);
}

.pet-photo {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: linear-gradient(135deg, var(--light-purple), rgba(236, 72, 153, 0.1));
}

.pet-info {
    padding: 1.5rem;
}

.pet-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.pet-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
}

.pet-detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6B7280;
    font-size: 0.95rem;
}

.pet-detail-item strong {
    color: var(--primary-purple);
}

.btn-add-pet {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border: none;
    border-radius: 1rem;
    padding: 0.85rem 2rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
    color: white;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-add-pet:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(147, 51, 234, 0.4);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    color: var(--primary-purple);
    margin-bottom: 1rem;
}

.empty-state p {
    color: #6B7280;
    margin-bottom: 2rem;
}

.alert {
    border-radius: 1rem;
    padding: 1rem 1.5rem;
    border: none;
    font-weight: 500;
    margin-bottom: 2rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

@media (max-width: 768px) {
    .pets-grid {
        grid-template-columns: 1fr;
    }
    
    .customer-main {
        padding: 1rem;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="customer-container">
    <!-- Main Content -->
    <main class="customer-main">
        <div class="page-header">
            <h2>My Pets</h2>
            <p>Manage your beloved companions</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('customer.pets.create') }}" class="btn-add-pet">
                <span>➕</span> Add New Pet
            </a>
        </div>

        @if($pets->count() > 0)
            <div class="pets-grid">
                @foreach($pets as $pet)
                    <div class="pet-card" onclick="window.location='{{ route('customer.pets.show', $pet->id) }}'">
                        @if($pet->photo_path)
                            <img src="{{ asset('storage/' . $pet->photo_path) }}" alt="{{ $pet->name }}" class="pet-photo">
                        @else
                            <div class="pet-photo d-flex align-items-center justify-content-center">
                                <span style="font-size: 4rem;">
                                    @if($pet->species == 'Dog') 🐕
                                    @elseif($pet->species == 'Cat') 🐈
                                    @elseif($pet->species == 'Bird') 🦜
                                    @elseif($pet->species == 'Rabbit') 🐇
                                    @elseif($pet->species == 'Hamster') 🐹
                                    @else 🐾
                                    @endif
                                </span>
                            </div>
                        @endif
                        
                        <div class="pet-info">
                            <h3 class="pet-name">{{ $pet->name }}</h3>
                            
                            <div class="pet-details">
                                <div class="pet-detail-item">
                                    <strong>Species:</strong> {{ $pet->species }}
                                </div>
                                
                                @if($pet->breed)
                                    <div class="pet-detail-item">
                                        <strong>Breed:</strong> {{ $pet->breed }}
                                    </div>
                                @endif
                                
                                <div class="pet-detail-item">
                                    <strong>Gender:</strong> {{ ucfirst($pet->gender) }}
                                </div>
                                
                                @if($pet->birth_date)
                                    <div class="pet-detail-item">
                                        <strong>Age:</strong> {{ \Carbon\Carbon::parse($pet->birth_date)->age }} years old
                                    </div>
                                @endif
                                
                                @if($pet->weight)
                                    <div class="pet-detail-item">
                                        <strong>Weight:</strong> {{ $pet->weight }} kg
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">🐾</div>
                <h3>No Pets Yet</h3>
                <p>Start by adding your first furry friend to our family!</p>
                <a href="{{ route('customer.pets.create') }}" class="btn-add-pet">
                    <span>➕</span> Add Your First Pet
                </a>
            </div>
        @endif
    </main>
</div>
@endsection
