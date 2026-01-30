@extends('admin.dashboard')

@section('page-title', 'Prescription Management')
@section('page-description', 'View and manage pet prescriptions')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;"><i class="fas fa-prescription"></i> Prescriptions</h3>
        <a href="{{ route('admin.prescriptions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Prescription
        </a>
    </div>

    <div class="card-body">
        <div class="prescriptions-container">
            @forelse($prescriptions as $prescription)
                <div class="pet-card">
                    <div class="pet-image">
                        <i class="fas fa-prescription-bottle"></i>
                    </div>
                    <div class="pet-info">
                        <div class="pet-name">{{ $prescription->medication }}</div>
                        <div class="pet-details">
                            <div><strong>Pet:</strong> {{ $prescription->pet->name }}</div>
                            <div><strong>Owner:</strong> {{ $prescription->pet->owner->user->first_name ?? '' }} {{ $prescription->pet->owner->user->last_name ?? 'No Owner' }}</div>
                            <div><strong>Dosage:</strong> {{ $prescription->dosage }}</div>
                            <div><strong>Frequency:</strong> {{ $prescription->frequency }}</div>
                            <div><strong>Duration:</strong> {{ $prescription->duration_days }} days</div>
                            <div><strong>Created:</strong> {{ $prescription->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="pet-actions">
                            <a href="{{ route('admin.prescriptions.show', $prescription->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.prescriptions.edit', $prescription->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>No prescriptions found.</p>
                </div>
            @endforelse
        </div>

        @if($prescriptions->hasPages())
            <div style="margin-top:16px;">
                {{ $prescriptions->links() }}
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

.empty-state {
    text-align: center;
    padding: 24px;
    color: var(--light-text);
    grid-column: 1 / -1;
}
</style>
@endsection
