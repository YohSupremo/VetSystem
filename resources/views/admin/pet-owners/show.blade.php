@extends('admin.dashboard')

@section('page-title', $petOwner->user->first_name . ' ' . $petOwner->user->last_name)
@section('page-description', 'View pet owner details')

@section('content')
<style>
    .detail-container {
        max-width: 900px;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: var(--primary-orange);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .owner-profile {
        background: var(--white);
        border-radius: 15px;
        padding: 30px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 25px;
    }

    .profile-header {
        display: flex;
        gap: 30px;
        align-items: start;
        padding-bottom: 25px;
        border-bottom: 2px solid var(--soft-gray);
        margin-bottom: 25px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-orange), var(--accent-pink));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 40px;
        flex-shrink: 0;
    }

    .profile-info h2 {
        margin: 0 0 5px 0;
        font-family: 'Fredoka', sans-serif;
        color: var(--dark-text);
        font-size: 28px;
    }

    .profile-info p {
        margin: 5px 0;
        color: var(--light-text);
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .detail-section {
        background: var(--white);
        border-radius: 15px;
        padding: 20px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 25px;
    }

    .section-title {
        font-family: 'Fredoka', sans-serif;
        font-size: 18px;
        font-weight: 600;
        color: var(--dark-text);
        margin: 0 0 20px 0;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--soft-gray);
    }

    .detail-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 20px;
        padding: 15px 0;
        border-bottom: 1px solid var(--soft-gray);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: var(--dark-text);
        font-size: 14px;
    }

    .detail-value {
        color: var(--light-text);
        font-size: 14px;
    }

    .emergency-contact-card {
        background: var(--soft-gray);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid var(--primary-orange);
    }

    .emergency-contact-card strong {
        display: block;
        color: var(--dark-text);
        margin-bottom: 5px;
    }

    .emergency-contact-card .phone {
        color: var(--light-text);
        font-size: 13px;
    }

    .empty-message {
        color: var(--light-text);
        font-style: italic;
        padding: 20px;
        text-align: center;
    }

    .pets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .pet-card {
        background: var(--soft-gray);
        border-radius: 10px;
        padding: 15px;
        border-left: 4px solid var(--accent-green);
    }

    .pet-card-name {
        font-weight: 600;
        color: var(--dark-text);
        margin-bottom: 8px;
    }

    .pet-card-info {
        font-size: 13px;
        color: var(--light-text);
        line-height: 1.6;
    }
</style>

<a href="{{ route('admin.pet-owners.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Pet Owners
</a>

<div class="detail-container">
    <div class="owner-profile">
        <div class="profile-header">
            <div class="profile-avatar">
                {{ strtoupper(substr($petOwner->user->first_name, 0, 1)) }}{{ strtoupper(substr($petOwner->user->last_name, 0, 1)) }}
            </div>
            <div class="profile-info">
                <h2>{{ $petOwner->user->first_name }} {{ $petOwner->user->last_name }}</h2>
                <p><i class="fas fa-envelope"></i> {{ $petOwner->user->email }}</p>
                <p><i class="fas fa-phone"></i> {{ $petOwner->user->contact_number }}</p>
                <p><i class="fas fa-map-marker-alt"></i> {{ $petOwner->user->address }}</p>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('admin.pet-owners.edit', $petOwner) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Owner
            </a>
            <form action="{{ route('admin.pet-owners.destroy', $petOwner) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary" style="background: #ff6b6b; color: white;" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash"></i> Delete Owner
                </button>
            </form>
        </div>
    </div>

    <!-- Pets Section -->
    <div class="detail-section">
        <h3 class="section-title">
            <i class="fas fa-paw"></i> Pets ({{ $petOwner->pets->count() }})
        </h3>
        
        @if($petOwner->pets->isEmpty())
            <div class="empty-message">
                <i class="fas fa-paw" style="font-size: 32px; color: var(--soft-gray); margin-bottom: 10px; display: block;"></i>
                No pets registered yet
            </div>
        @else
            <div class="pets-grid">
                @foreach($petOwner->pets as $pet)
                    <div class="pet-card">
                        <div class="pet-card-name">{{ $pet->name }}</div>
                        <div class="pet-card-info">
                            <div><strong>Species:</strong> {{ $pet->species }}</div>
                            <div><strong>Breed:</strong> {{ $pet->breed }}</div>
                            @if($pet->birth_date)
                                <div><strong>Age:</strong> {{ \Carbon\Carbon::parse($pet->birth_date)->age }} years</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Emergency Contacts Section -->
    <div class="detail-section">
        <h3 class="section-title">
            <i class="fas fa-phone-alt"></i> Emergency Contacts
        </h3>
        
        @if($petOwner->emergency_contact_name)
            <div class="emergency-contact-card">
                <strong>{{ $petOwner->emergency_contact_name }}</strong>
                @if($petOwner->emergency_contact_relationship)
                    <div class="relationship">{{ $petOwner->emergency_contact_relationship }}</div>
                @endif
                @if($petOwner->emergency_contact_phone)
                    <div class="phone"><i class="fas fa-phone"></i> {{ $petOwner->emergency_contact_phone }}</div>
                @endif
            </div>
        @else
            <div class="empty-message">
                No emergency contacts registered
            </div>
        @endif
    </div>

    <!-- Notes Section -->
    @if($petOwner->notes)
        <div class="detail-section">
            <h3 class="section-title">
                <i class="fas fa-sticky-note"></i> Notes
            </h3>
            <p style="color: var(--light-text); line-height: 1.6; margin: 0;">
                {{ $petOwner->notes }}
            </p>
        </div>
    @endif
</div>

@endsection
