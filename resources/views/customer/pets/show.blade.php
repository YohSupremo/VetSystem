@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', $pet->name . ' - PawCare')

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

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
    color: var(--primary-purple);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.back-button:hover {
    opacity: 0.7;
    transform: translateX(-5px);
}

.pet-header {
    background: linear-gradient(135deg, var(--primary-purple) 0%, var(--pink) 100%);
    border-radius: 1.5rem;
    padding: 2.5rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.2);
}

.pet-header-content {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

.pet-photo-large {
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.pet-photo-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pet-photo-large i {
    font-size: 60px;
    opacity: 0.7;
}

.pet-info-header {
    flex: 1;
}

.pet-info-header h1 {
    margin: 0 0 0.5rem 0;
    font-size: 2.5rem;
    font-family: 'Fredoka', sans-serif;
}

.pet-info-header p {
    margin: 0.3rem 0;
    font-size: 1rem;
    opacity: 0.95;
}

.pet-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.7rem;
    margin-top: 1rem;
}

.pet-badges .badge {
    background: rgba(255, 255, 255, 0.3);
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
    border: 1px solid rgba(167, 139, 250, 0.2);
}

.card h3 {
    margin-top: 0;
    color: var(--dark-text);
    font-family: 'Fredoka', sans-serif;
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.7rem;
}

.card h3 i {
    color: var(--primary-purple);
    font-size: 1.3rem;
}

.info-row {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(167, 139, 250, 0.1);
    margin-bottom: 1rem;
}

.info-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: var(--light-text);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    color: var(--dark-text);
    font-weight: 500;
    font-size: 1rem;
}

.species-badge {
    display: inline-block;
    background: var(--primary-purple);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.gender-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.gender-badge.male {
    background: rgba(74, 144, 226, 0.2);
    color: #4A90E2;
}

.gender-badge.female {
    background: rgba(236, 72, 153, 0.2);
    color: var(--pink);
}

.gender-badge.unknown {
    background: rgba(160, 160, 160, 0.2);
    color: #909090;
}

.medical-section {
    background: rgba(147, 51, 234, 0.05);
    padding: 1.5rem;
    border-radius: 1rem;
    border-left: 4px solid var(--primary-purple);
}

.medical-section p {
    margin: 0;
    color: var(--light-text);
    font-size: 0.95rem;
    font-style: italic;
}

.medical-item {
    background: white;
    border-radius: 1rem;
    padding: 1rem;
    border: 1px solid rgba(167, 139, 250, 0.1);
    margin-bottom: 1rem;
}

.medical-item:last-child {
    margin-bottom: 0;
}

.medical-item-header {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}

.medical-item-date {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 1rem;
}

.medical-item-meta {
    color: var(--light-text);
    font-size: 0.9rem;
}

.medical-item-detail {
    margin-top: 0.7rem;
    color: var(--dark-text);
}

.medical-item-detail span {
    font-weight: 600;
    margin-right: 0.3rem;
}

.owner-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.2rem;
    background: rgba(147, 51, 234, 0.05);
    border-radius: 1rem;
    border: 1px solid rgba(167, 139, 250, 0.1);
    margin-bottom: 1.5rem;
}

.owner-avatar {
    width: 50px;
    height: 50px;
    background: var(--primary-purple);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    flex-shrink: 0;
    font-size: 1.2rem;
}

.owner-info {
    flex: 1;
}

.owner-info p {
    margin: 0;
    font-size: 0.95rem;
}

.owner-info p:first-child {
    font-weight: 600;
    color: var(--dark-text);
    font-size: 1rem;
}

.owner-info p:last-child {
    color: var(--light-text);
    font-size: 0.85rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--light-text);
}

.empty-state i {
    font-size: 2rem;
    margin-right: 0.5rem;
    color: var(--primary-purple);
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }

    .pet-header-content {
        flex-direction: column;
    }

    .pet-photo-large {
        width: 120px;
        height: 120px;
    }

    .pet-info-header h1 {
        font-size: 1.8rem;
    }

    .info-row {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')

<div class="customer-container">
    <div class="customer-header">
        <div class="logo-section">
            <span class="paw-icon">🐾</span>
            <h1>PawCare</h1>
        </div>
    </div>

    <div class="customer-main">
        <!-- Back Button -->
        <a href="{{ route('customer.pets.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to My Pets
        </a>

        <!-- Pet Header -->
        <div class="pet-header">
            <div class="pet-header-content">
                <div class="pet-photo-large">
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <i class="fas fa-paw" style="display:none;"></i>
                </div>
                <div class="pet-info-header">
                    <h1>{{ $pet->name }}</h1>
                    <p><strong>{{ ucfirst($pet->species) }}</strong> • {{ $pet->breed }}</p>
                    <div class="pet-badges">
                        <span class="badge">{{ $pet->age }} years old</span>
                        <span class="badge">{{ ucfirst($pet->gender) }}</span>
                        @if($pet->weight)
                            <span class="badge">{{ $pet->weight }}kg</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Main Information -->
            <div class="card">
                <h3><i class="fas fa-info-circle"></i> Pet Information</h3>

                <div class="info-row">
                    <div class="info-label">Name</div>
                    <div class="info-value">{{ $pet->name }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Species</div>
                    <div class="info-value">
                        <span class="species-badge">{{ ucfirst($pet->species) }}</span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Breed</div>
                    <div class="info-value">{{ $pet->breed }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Birth Date</div>
                    <div class="info-value">
                        @if($pet->birth_date)
                            {{ $pet->birth_date->format('F d, Y') }}
                        @else
                            <em>Not specified</em>
                        @endif
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Age</div>
                    <div class="info-value">{{ $pet->age }} years old</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Gender</div>
                    <div class="info-value">
                        <span class="gender-badge {{ $pet->gender }}">
                            @if($pet->gender === 'male')
                                <i class="fas fa-mars"></i> Male
                            @elseif($pet->gender === 'female')
                                <i class="fas fa-venus"></i> Female
                            @else
                                Unknown
                            @endif
                        </span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Color</div>
                    <div class="info-value">{{ $pet->color ?? 'Not specified' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Weight</div>
                    <div class="info-value">{{ $pet->weight ?? 'Not specified' }} kg</div>
                </div>

                @if($pet->microchip_number)
                    <div class="info-row">
                        <div class="info-label">Microchip</div>
                        <div class="info-value">
                            <code>{{ $pet->microchip_number }}</code>
                        </div>
                    </div>
                @endif

                <!-- Medical Records Section -->
                <h3 style="margin-top: 2.5rem;"><i class="fas fa-clipboard-list"></i> Medical History</h3>
                <div class="medical-section">
                    @if(isset($pet->medicalRecords) && $pet->medicalRecords->count() > 0)
                        <div style="display:grid; gap:1rem;">
                            @foreach($pet->medicalRecords as $record)
                                <div class="medical-item">
                                    <div class="medical-item-header">
                                        <div>
                                            <div class="medical-item-date">
                                                {{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}
                                            </div>
                                            <div class="medical-item-meta">
                                                Dr. {{ $record->veterinarian?->first_name ?? 'N/A' }} {{ $record->veterinarian?->last_name ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="medical-item-detail">
                                        <span>Complaint:</span>
                                        {{ \Illuminate\Support\Str::limit($record->complaint, 200) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-info-circle"></i>
                            <p>No medical records found for this pet yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Appointments Section -->
                <h3 style="margin-top: 2.5rem;"><i class="fas fa-calendar-check"></i> Appointments</h3>
                <div class="medical-section">
                    @if(isset($pet->appointments) && $pet->appointments->count() > 0)
                        <div style="display:grid; gap:1rem; margin-top: 1rem;">
                            @foreach($pet->appointments as $appt)
                                <div class="medical-item">
                                    <div class="medical-item-header">
                                        <div>
                                            <div class="medical-item-date">
                                                {{ $appt->appointment_date ? \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') : 'TBD' }}
                                            </div>
                                            <div class="medical-item-meta">
                                                {{ $appt->type ? ucfirst(str_replace('_',' ', $appt->type)) : 'Type N/A' }}
                                                • {{ $appt->status ? ucfirst(str_replace('_',' ', $appt->status)) : 'Status N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-info-circle"></i>
                            <p>No appointments found for this pet yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Vaccinations Section -->
                @if(isset($pet->vaccinations) && $pet->vaccinations->count() > 0)
                    <h3 style="margin-top: 2.5rem;"><i class="fas fa-syringe"></i> Vaccinations</h3>
                    <div class="medical-section">
                        <div style="display:grid; gap:1rem;">
                            @foreach($pet->vaccinations as $vaccination)
                                <div class="medical-item">
                                    <div class="medical-item-header">
                                        <div>
                                            <div class="medical-item-date">
                                                {{ $vaccination->vaccine_name }}
                                            </div>
                                            <div class="medical-item-meta">
                                                {{ \Carbon\Carbon::parse($vaccination->vaccination_date)->format('M d, Y') }}
                                                @if($vaccination->next_due_date)
                                                    • Next Due: {{ \Carbon\Carbon::parse($vaccination->next_due_date)->format('M d, Y') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Owner Information -->
                <div class="card">
                    <h3><i class="fas fa-user"></i> Owner Information</h3>

                    <div class="owner-card">
                        <div class="owner-avatar">
                            {{ strtoupper(substr($pet->owner->user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($pet->owner->user->last_name ?? '', 0, 1)) }}
                        </div>
                        <div class="owner-info">
                            <p>{{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}</p>
                            <p>{{ $pet->owner->user->email ?? 'No email' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card">
                    <h3><i class="fas fa-calendar"></i> Timeline</h3>

                    <div class="info-row">
                        <div class="info-label">Added</div>
                        <div class="info-value">{{ $pet->created_at->format('M d, Y') }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">{{ $pet->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
