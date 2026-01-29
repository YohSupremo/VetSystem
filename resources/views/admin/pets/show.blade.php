@extends('admin.dashboard')

@section('page-title', $pet->name)
@section('page-description', 'Pet Profile & Medical History')

@section('content')
<style>
    .pet-header {
        background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-pink) 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: var(--shadow-soft);
    }

    .pet-header-content {
        display: flex;
        gap: 30px;
        align-items: flex-start;
    }

    .pet-photo-large {
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
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
        margin: 0 0 10px 0;
        font-size: 32px;
        font-family: 'Fredoka', sans-serif;
    }

    .pet-info-header p {
        margin: 5px 0;
        font-size: 14px;
        opacity: 0.95;
    }

    .pet-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .pet-badges .badge {
        background: rgba(255, 255, 255, 0.3);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .card {
        background: var(--white);
        border-radius: 15px;
        padding: 25px;
        box-shadow: var(--shadow-soft);
    }

    .card h3 {
        margin-top: 0;
        color: var(--dark-text);
        font-family: 'Fredoka', sans-serif;
        font-size: 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card h3 i {
        color: var(--primary-orange);
        font-size: 18px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--soft-gray);
        margin-bottom: 15px;
    }

    .info-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    .info-label {
        font-weight: 600;
        color: var(--light-text);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: var(--dark-text);
        font-weight: 500;
    }

    .species-badge {
        display: inline-block;
        background: var(--primary-orange);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .gender-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .gender-badge.male {
        background: rgba(74, 144, 226, 0.2);
        color: #4A90E2;
    }

    .gender-badge.female {
        background: rgba(255, 107, 157, 0.2);
        color: var(--accent-pink);
    }

    .gender-badge.unknown {
        background: rgba(160, 160, 160, 0.2);
        color: #909090;
    }

    .owner-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: var(--soft-gray);
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .owner-avatar {
        width: 50px;
        height: 50px;
        background: var(--primary-orange);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        flex-shrink: 0;
    }

    .owner-info {
        flex: 1;
    }

    .owner-info p {
        margin: 0;
        font-size: 13px;
    }

    .owner-info p:first-child {
        font-weight: 600;
        color: var(--dark-text);
        font-size: 14px;
    }

    .owner-info p:last-child {
        color: var(--light-text);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        align-items: center;
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

    .medical-section {
        background: rgba(255, 140, 66, 0.05);
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid var(--primary-orange);
    }

    .medical-section p {
        margin: 0;
        color: var(--light-text);
        font-size: 13px;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .pet-header-content {
            flex-direction: column;
        }

        .pet-photo-large {
            width: 100px;
            height: 100px;
        }
    }
</style>

<a href="{{ route('admin.pets.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Pets
</a>

<!-- Pet Header -->
<div class="pet-header">
    <div class="pet-header-content">
        <div class="pet-photo-large">
            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <i class="fas fa-paw" style="display:none; font-size: 4rem; color: #ccc;"></i>
        </div>
        <div class="pet-info-header">
            <h1>{{ $pet->name }}</h1>
            <p><strong>{{ ucfirst($pet->species) }}</strong> • {{ $pet->breed }}</p>
            <p>Owner: <strong>{{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}</strong></p>
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

        <div class="info-row">
            <div class="info-label">Microchip</div>
            <div class="info-value">
                @if($pet->microchip_number)
                    <code>{{ $pet->microchip_number }}</code>
                @else
                    <em>Not registered</em>
                @endif
            </div>
        </div>

        <!-- Medical Records Section -->
        <h3 style="margin-top: 30px;"><i class="fas fa-clipboard-list"></i> Medical History</h3>
        <div class="medical-section">
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 12px;">
                <a href="{{ route('admin.medical-records.pet', $pet->id) }}" class="btn btn-secondary">
                    <i class="fas fa-history"></i> View Full History
                </a>
                <a href="{{ route('admin.medical-records.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Medical Record
                </a>
            </div>

            @if(isset($medicalRecords) && $medicalRecords->count() > 0)
                <div style="display:grid; gap:10px;">
                    @foreach($medicalRecords as $record)
                        <div style="background:#fff; border-radius:10px; padding:12px 14px; border:1px solid var(--soft-gray);">
                            <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</strong>
                                    <span style="color:var(--light-text); font-size: 13px;">
                                        • Dr. {{ $record->veterinarian?->first_name ?? 'N/A' }} {{ $record->veterinarian?->last_name ?? '' }}
                                    </span>
                                </div>
                                <a href="{{ route('admin.medical-records.show', $record->id) }}" class="btn btn-secondary" style="padding:6px 12px; font-size: 13px;">
                                    View
                                </a>
                            </div>
                            <div style="margin-top:6px; color:var(--dark-text);">
                                <span style="font-weight:600;">Complaint:</span>
                                <span>{{ \Illuminate\Support\Str::limit($record->complaint, 120) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>
                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                    No medical records found for this pet yet.
                </p>
            @endif
        </div>

        <h3 style="margin-top: 25px;"><i class="fas fa-calendar-check"></i> Appointments</h3>
        <div class="medical-section">
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 12px;">
                <a href="{{ route('admin.appointments.index', ['pet_id' => $pet->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> View All Appointments
                </a>
                <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Appointment
                </a>
            </div>

            @if(isset($appointments) && $appointments->count() > 0)
                <div style="display:grid; gap:10px;">
                    @foreach($appointments as $appt)
                        <div style="background:#fff; border-radius:10px; padding:12px 14px; border:1px solid var(--soft-gray);">
                            <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                                <div>
                                    <strong>
                                        {{ $appt->appointment_date ? \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') : 'TBD' }}
                                    </strong>
                                    <span style="color:var(--light-text); font-size: 13px;">
                                        • {{ $appt->type ? ucfirst(str_replace('_',' ', $appt->type)) : 'Type N/A' }}
                                        • {{ $appt->status ? ucfirst(str_replace('_',' ', $appt->status)) : 'Status N/A' }}
                                    </span>
                                </div>
                                <a href="{{ route('admin.appointments.show', $appt->id) }}" class="btn btn-secondary" style="padding:6px 12px; font-size: 13px;">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>
                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                    No appointments found for this pet yet.
                </p>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.pets.edit', $pet->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Pet
            </a>
            <form action="{{ route('admin.pets.destroy', $pet->id) }}" method="POST" 
                  style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this pet?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Pet
                </button>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Owner Information -->
        <div class="card">
            <h3><i class="fas fa-user"></i> Owner Information</h3>

            <div class="owner-card">
                <div class="owner-avatar">
                    {{ strtoupper(substr($pet->owner->user->first_name, 0, 1)) }}{{ strtoupper(substr($pet->owner->user->last_name, 0, 1)) }}
                </div>
                <div class="owner-info">
                    <p>{{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}</p>
                    <p>{{ $pet->owner->user->email }}</p>
                </div>
            </div>

            <a href="{{ route('admin.pet-owners.show', $pet->owner->id) }}" class="btn btn-secondary" 
               style="width: 100%; text-align: center;">
                <i class="fas fa-arrow-right"></i> View Owner Profile
            </a>
        </div>

        <!-- Additional Info -->
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

@endsection
