@extends('admin.dashboard')

@section('page-title', 'Pets Registry')
@section('page-description', 'Manage all pets in the clinic')

@section('content')
<style>
    .pets-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .pet-card {
        background: var(--white);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
    }

    .pet-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .pet-image {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, var(--primary-orange), var(--accent-pink));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 60px;
        overflow: hidden;
    }

    .pet-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pet-info {
        padding: 20px;
    }

    .pet-name {
        font-family: 'Fredoka', sans-serif;
        font-size: 20px;
        font-weight: 600;
        color: var(--dark-text);
        margin: 0 0 10px 0;
    }

    .pet-details {
        font-size: 13px;
        color: var(--light-text);
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .pet-owner {
        background: var(--soft-gray);
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 12px;
    }

    .pet-owner strong {
        display: block;
        color: var(--dark-text);
        margin-bottom: 3px;
    }

    .card-actions {
        display: flex;
        gap: 10px;
    }

    .card-actions .btn {
        flex: 1;
        padding: 8px 12px;
        font-size: 12px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--light-text);
    }

    .empty-state i {
        font-size: 64px;
        color: var(--soft-gray);
        margin-bottom: 20px;
        display: block;
    }

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .species-badge {
        display: inline-block;
        background: rgba(255, 140, 66, 0.1);
        color: var(--primary-orange);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 8px;
    }
</style>

<div class="top-bar">
    <div>
        <h3 style="margin: 0; font-family: 'Fredoka', sans-serif; font-size: 24px; color: var(--dark-text);">
            All Pets
        </h3>
        <p style="margin: 5px 0 0 0; color: var(--light-text); font-size: 14px;">
            Total: {{ count($pets) }} pets registered
        </p>
    </div>
    <a href="{{ route('admin.pets.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Pet
    </a>
</div>



@if($pets->isEmpty())
    <div class="empty-state">
        <i class="fas fa-paw"></i>
        <h3>No Pets Yet</h3>
        <p>Start by registering your first pet.</p>
        <a href="{{ route('admin.pets.create') }}" class="btn btn-primary" style="margin-top: 20px;">
            <i class="fas fa-plus"></i> Add First Pet
        </a>
    </div>
@else
    <div class="pets-container">
        @foreach($pets as $pet)
            <div class="pet-card">
                <div class="pet-image">
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span style="display:none; font-size: 2rem;">🐾</span>
                </div>

                <div class="pet-info">
                    <h4 class="pet-name">{{ $pet->name }}</h4>
                    
                    <span class="species-badge">{{ ucfirst($pet->species) }}</span>

                    <div class="pet-details">
                        <div><strong>Breed:</strong> {{ $pet->breed }}</div>
                        @if($pet->birth_date)
                            <div><strong>Age:</strong> {{ $pet->birth_date->age }} years</div>
                        @endif
                        <div><strong>Gender:</strong> {{ ucfirst($pet->gender) }}</div>
                        @if($pet->weight)
                            <div><strong>Weight:</strong> {{ $pet->weight }} kg</div>
                        @endif
                        @if($pet->microchip_number)
                            <div><strong>Microchip:</strong> {{ $pet->microchip_number }}</div>
                        @endif
                    </div>

                    <div class="pet-owner">
                        <strong><i class="fas fa-user"></i> Owner</strong>
                        {{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}
                    </div>

                    <div class="card-actions">
                        <a href="{{ route('admin.pets.show', $pet) }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('admin.pets.edit', $pet) }}" class="btn btn-secondary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.pets.destroy', $pet) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary" style="width: 100%; background: #ff6b6b; color: white;" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
