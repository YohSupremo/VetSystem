@extends('admin.dashboard')

@section('page-title', 'Vaccination Records')
@section('page-description', 'Manage pet vaccination records')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Vaccination Records</h2>
        <a href="{{ route('admin.vaccinations.create') }}" class="btn btn-primary">
            <i class="fas fa-syringe me-2"></i>Add New Vaccination
        </a>
    </div>

    <div class="pets-container">
        @forelse($pets as $pet)
            <div class="pet-card">
                <div class="pet-image" style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);">
                    @if($pet->image_path)
                        <img src="{{ asset('storage/' . $pet->image_path) }}" alt="{{ $pet->name }}">
                    @else
                        <i class="fas fa-syringe"></i>
                    @endif
                </div>
                <div class="pet-info">
                    <h3 class="pet-name">{{ $pet->name }}</h3>
                    <div class="pet-details">
                        <p><i class="fas fa-user-tie me-2"></i> {{ $pet->owner->user->first_name ?? '' }} {{ $pet->owner->user->last_name ?? 'No Owner' }}</p>
                        <p><i class="fas fa-phone me-2"></i> {{ $pet->owner->user->contact_number ?? 'N/A' }}</p>
                        <p><i class="fas fa-paw me-2"></i> {{ $pet->species }} @if($pet->breed) • {{ $pet->breed }} @endif</p>
                        <p><i class="fas fa-syringe me-2"></i> 
                            @if($pet->vaccinations->count() > 0)
                                {{ $pet->vaccinations->count() }} Vaccination(s)
                            @else
                                No Vaccinations
                            @endif
                        </p>
                    </div>
                    <div class="pet-actions mt-3">
                        <a href="{{ route('admin.vaccinations.pet', $pet->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> View All
                        </a>
                        <a href="{{ route('admin.vaccinations.create', ['pet_id' => $pet->id]) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-plus-circle me-1"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No pets found. Please add a pet first.
                </div>
            </div>
        @endforelse
    </div>

    @if($pets->hasPages())
        <div class="mt-4">
            {{ $pets->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
/* Reusing styles from medical-records with some adjustments */
.pets-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.pet-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.pet-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.pet-image {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.2);
    font-size: 4rem;
    overflow: hidden;
}

.pet-image i {
    opacity: 0.5;
}

.pet-info {
    padding: 1.25rem;
}

.pet-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #2c3e50;
}

.pet-details p {
    margin-bottom: 0.4rem;
    color: #6c757d;
    font-size: 0.9rem;
}

.pet-details i {
    width: 20px;
    text-align: center;
    color: #6c757d;
}

.pet-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.alert {
    border-radius: 8px;
}
</style>
@endpush
@endsection
