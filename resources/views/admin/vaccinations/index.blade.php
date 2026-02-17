@extends('admin.dashboard')

@section('page-title', 'Vaccination Records')
@section('page-description', 'Manage pet vaccination records')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;"><i class="fas fa-syringe"></i> Vaccination Records</h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-success">
                <i class="fas fa-vial"></i> Manage Vaccines (Inventory)
            </a>
            <a href="{{ route('admin.vaccinations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Vaccination
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="pets-container">
            @forelse($pets as $pet)
                <div class="pet-card">
                    <div class="pet-image">
                        <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="fas fa-paw" style="display:none;"></i>
                    </div>
                    <div class="pet-info">
                        <div class="pet-name">{{ $pet->name }}</div>
                        <div class="pet-details">
                            <div><strong>Owner:</strong> {{ $pet->owner->user->first_name ?? '' }} {{ $pet->owner->user->last_name ?? 'No Owner' }}</div>
                            <div><strong>Contact:</strong> {{ $pet->owner->user->contact_number ?? 'N/A' }}</div>
                            <div><strong>Species:</strong> {{ ucfirst($pet->species ?? 'N/A') }} @if($pet->breed) • {{ $pet->breed }} @endif</div>
                            <div><strong>Vaccinations:</strong> {{ $pet->vaccination_total_count ?? $pet->vaccinations->count() }}</div>
                        </div>
                        <div class="pet-actions">
                            <a href="{{ route('admin.vaccinations.pet', $pet->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.vaccinations.create', ['pet_id' => $pet->id]) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-plus"></i> Add
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>No pets with vaccination records yet.</p>
                </div>
            @endforelse
        </div>

        @if($pets->hasPages())
            <div style="margin-top:16px;">
                {{ $pets->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.pets-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.pet-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    border: 1px solid rgba(0,0,0,0.06);
}

.pet-image {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-blue), var(--accent-purple));
    color: rgba(255,255,255,0.9);
    font-size: 54px;
    overflow: hidden;
}

.pet-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pet-info {
    padding: 1.25rem;
}

.pet-name {
    font-family: 'Fredoka', sans-serif;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--dark-text);
}

.pet-details {
    color: var(--light-text);
    font-size: 13px;
    display: grid;
    gap: 6px;
}

.pet-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 12px;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.empty-state {
    text-align: center;
    padding: 24px;
    color: var(--light-text);
    grid-column: 1 / -1;
}
</style>
@endsection
