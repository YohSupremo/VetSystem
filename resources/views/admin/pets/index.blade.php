@extends('admin.dashboard')

@section('page-title', 'Pets Registry')
@section('page-description', 'Manage all pets in the clinic')

@section('content')
<style>
    /* ─── Header Section ─── */
    .pets-header {
        background: var(--white);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-soft);
    }

    .pets-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .pets-header-title h3 {
        margin: 0;
        font-family: 'Fredoka', sans-serif;
        font-size: 26px;
        font-weight: 600;
        color: var(--dark-text);
    }

    .pets-header-title p {
        margin: 4px 0 0 0;
        color: var(--light-text);
        font-size: 14px;
    }

    .pets-header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .pets-search-bar {
        display: flex;
        gap: 0;
        align-items: center;
        background: var(--white);
        border-radius: 12px;
        padding: 4px;
        border: 2px solid var(--soft-gray);
        transition: all 0.3s ease;
        outline: none;
    }

    .pets-search-bar:focus-within {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 4px rgba(255, 140, 66, 0.1);
    }

    .pets-search-bar input {
        border: none;
        background: transparent;
        padding: 10px 14px;
        font-size: 14px;
        flex: 1;
        min-width: 0;
        outline: none;
        color: var(--dark-text);
        font-family: 'DM Sans', sans-serif;
    }

    .pets-search-bar input::placeholder {
        color: #A0AEC0;
    }

    .pets-search-bar button,
    .pets-search-bar a {
        border: none;
        background: var(--primary-orange);
        color: white;
        padding: 10px 16px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .pets-search-bar button:hover,
    .pets-search-bar a:hover {
        background: #e87a35;
    }

    .pets-search-bar .clear-btn {
        background: #E2E8F0;
        color: var(--dark-text);
    }

    .pets-search-bar .clear-btn:hover {
        background: #CBD5E0;
    }

    .btn-add-pet {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--primary-orange), var(--accent-pink));
        color: white;
        padding: 12px 22px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    }

    .btn-add-pet:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
        color: white;
    }

    .btn-trash-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        background: var(--soft-gray);
        color: var(--dark-text);
        transition: all 0.2s ease;
    }

    .btn-trash-toggle:hover {
        background: #E2E8F0;
        color: var(--dark-text);
    }

    /* ─── Stats Bar ─── */
    .pets-stats {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--soft-gray);
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
        color: var(--dark-text);
    }

    .stat-pill i {
        font-size: 14px;
    }

    .stat-pill.dogs i { color: #FF8C42; }
    .stat-pill.cats i { color: #9B7EDE; }
    .stat-pill.birds i { color: #4A90E2; }
    .stat-pill.others i { color: #5FD068; }

    /* ─── Pet Grid ─── */
    .pets-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 22px;
    }

    .pet-card {
        background: var(--white);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.04);
        position: relative;
    }

    .pet-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 40px rgba(255, 140, 66, 0.15);
        border-color: rgba(255, 140, 66, 0.1);
    }

    .pet-image {
        width: 100%;
        height: 210px;
        background: linear-gradient(135deg, var(--primary-orange), var(--accent-pink));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 60px;
        overflow: hidden;
        position: relative;
    }

    .pet-image::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: linear-gradient(to top, rgba(0,0,0,0.15), transparent);
        pointer-events: none;
    }

    .pet-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pet-card:hover .pet-image img {
        transform: scale(1.06);
    }

    .pet-image .fallback-icon {
        display: none;
        font-size: 4rem;
        opacity: 0.8;
    }

    .pet-info {
        padding: 22px;
    }

    .pet-name-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .pet-name {
        font-family: 'Fredoka', sans-serif;
        font-size: 21px;
        font-weight: 600;
        color: var(--dark-text);
        margin: 0;
    }

    .species-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .species-badge.dog {
        background: rgba(255, 140, 66, 0.12);
        color: #e07830;
    }

    .species-badge.cat {
        background: rgba(155, 126, 222, 0.12);
        color: #7c5fc5;
    }

    .species-badge.bird {
        background: rgba(74, 144, 226, 0.12);
        color: #3a7bcc;
    }

    .species-badge.other {
        background: rgba(95, 208, 104, 0.12);
        color: #3ea549;
    }

    .pet-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 16px;
        margin-bottom: 16px;
        font-size: 13px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .detail-item .detail-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #A0AEC0;
        font-weight: 600;
    }

    .detail-item .detail-value {
        color: var(--dark-text);
        font-weight: 500;
    }

    .pet-owner {
        background: linear-gradient(135deg, #f7f8fa, #f0f2f5);
        padding: 12px 14px;
        border-radius: 12px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .owner-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-orange), var(--accent-pink));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .owner-info {
        display: flex;
        flex-direction: column;
    }

    .owner-info .owner-label {
        font-size: 11px;
        color: #A0AEC0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .owner-info .owner-name {
        font-size: 14px;
        color: var(--dark-text);
        font-weight: 600;
    }

    .card-actions {
        display: flex;
        gap: 8px;
    }

    .card-actions .action-btn {
        flex: 1;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        font-family: 'DM Sans', sans-serif;
    }

    .action-btn.view-btn {
        background: rgba(74, 144, 226, 0.1);
        color: #4A90E2;
    }

    .action-btn.view-btn:hover {
        background: #4A90E2;
        color: white;
    }

    .action-btn.edit-btn {
        background: rgba(255, 140, 66, 0.1);
        color: var(--primary-orange);
    }

    .action-btn.edit-btn:hover {
        background: var(--primary-orange);
        color: white;
    }

    .action-btn.delete-btn {
        background: rgba(255, 107, 107, 0.1);
        color: #ff6b6b;
    }

    .action-btn.delete-btn:hover {
        background: #ff6b6b;
        color: white;
    }

    .action-btn.restore-btn {
        background: rgba(95, 208, 104, 0.1);
        color: #5FD068;
    }

    .action-btn.restore-btn:hover {
        background: #5FD068;
        color: white;
    }

    /* ─── Empty State ─── */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--light-text);
        background: var(--white);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
    }

    .empty-state .empty-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255, 140, 66, 0.1), rgba(255, 107, 157, 0.1));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px auto;
    }

    .empty-state .empty-icon i {
        font-size: 40px;
        color: var(--primary-orange);
    }

    .empty-state h3 {
        font-family: 'Fredoka', sans-serif;
        font-size: 22px;
        color: var(--dark-text);
        margin-bottom: 8px;
    }

    .empty-state p {
        color: var(--light-text);
        font-size: 15px;
        margin-bottom: 24px;
    }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .pets-header {
            padding: 18px 16px;
        }

        .pets-header-top {
            flex-direction: column;
            gap: 14px;
        }

        .pets-header-actions {
            width: 100%;
        }

        .pets-search-bar {
            width: 100%;
        }

        .pets-search-bar input {
            min-width: 0;
            flex: 1;
        }

        .btn-add-pet {
            width: 100%;
            justify-content: center;
        }

        .btn-trash-toggle {
            width: 100%;
            justify-content: center;
        }

        .pets-container {
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }

        .pet-image {
            height: 170px;
        }

        .pet-info {
            padding: 16px;
        }

        .pet-name {
            font-size: 18px;
        }

        .card-actions {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 576px) {
        .pets-container {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .pet-card {
            border-radius: 14px;
        }

        .pet-image {
            height: 180px;
        }

        .pet-info {
            padding: 16px;
        }

        .pet-details-grid {
            gap: 6px 12px;
        }

        .pets-stats {
            gap: 8px;
        }

        .stat-pill {
            font-size: 12px;
            padding: 6px 12px;
        }
    }

    @media (max-width: 480px) {
        .pet-image {
            height: 150px;
        }

        .card-actions .action-btn {
            padding: 8px 10px;
            font-size: 12px;
        }
    }
</style>

{{-- ─── Header ─── --}}
<div class="pets-header">
    <div class="pets-header-top">
        <div class="pets-header-title">
            <h3><i class="fas fa-paw" style="color: var(--primary-orange); margin-right: 8px;"></i>All Pets</h3>
            <p>{{ count($pets) }} pets registered in the clinic</p>
        </div>
        <div class="pets-header-actions">
            @if($showTrash)
                <a href="{{ route('admin.pets.index', request()->except('trash', 'page')) }}" class="btn-trash-toggle">
                    <i class="fas fa-arrow-left"></i> Back To Active
                </a>
            @else
                <a href="{{ route('admin.pets.index', array_merge(request()->query(), ['trash' => 1])) }}" class="btn-trash-toggle">
                    <i class="fas fa-trash-alt"></i> View Trash
                </a>
            @endif

            <a href="{{ route('admin.pets.create') }}" class="btn-add-pet">
                <i class="fas fa-plus"></i> Add New Pet
            </a>
        </div>
    </div>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('admin.pets.index') }}">
        <div class="pets-search-bar">
            <i class="fas fa-search" style="padding-left: 12px; color: #A0AEC0;"></i>
            <input
                type="text"
                name="filter[search]"
                value="{{ request('filter.search') }}"
                placeholder="Search by pet name, species, breed, or owner..."
            >
            <button type="submit"><i class="fas fa-search"></i> Search</button>
            @if(request('filter.search'))
                <a href="{{ route('admin.pets.index') }}" class="clear-btn"><i class="fas fa-times"></i> Clear</a>
            @endif
        </div>
    </form>

    {{-- Quick Stats --}}
    @if(!$pets->isEmpty())
        <div class="pets-stats" style="margin-top: 16px;">
            @php
                $grouped = collect($pets)->groupBy(function($pet) {
                    return strtolower($pet->species);
                });
            @endphp
            @foreach($grouped as $species => $group)
                @php
                    $badgeClass = match($species) {
                        'dog' => 'dogs',
                        'cat' => 'cats',
                        'bird' => 'birds',
                        default => 'others',
                    };
                    $icon = match($species) {
                        'dog' => 'fa-dog',
                        'cat' => 'fa-cat',
                        'bird' => 'fa-dove',
                        default => 'fa-paw',
                    };
                @endphp
                <span class="stat-pill {{ $badgeClass }}">
                    <i class="fas {{ $icon }}"></i> {{ count($group) }} {{ ucfirst($species) }}{{ count($group) !== 1 ? 's' : '' }}
                </span>
            @endforeach
        </div>
    @endif
</div>

{{-- ─── Content ─── --}}
@if($pets->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-paw"></i>
        </div>
        <h3>No Pets Found</h3>
        <p>{{ request('filter.search') ? 'Try a different search term.' : 'Start by registering your first pet.' }}</p>
        @if(!request('filter.search'))
            <a href="{{ route('admin.pets.create') }}" class="btn-add-pet">
                <i class="fas fa-plus"></i> Add First Pet
            </a>
        @endif
    </div>
@else
    <div class="pets-container">
        @foreach($pets as $pet)
            <div class="pet-card">
                <div class="pet-image">
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" onerror="this.style.display='none'; this.parentElement.querySelector('.fallback-icon').style.display='flex';">
                    <span class="fallback-icon" style="display:none; width:100%; height:100%; align-items:center; justify-content:center; font-size:4rem;">🐾</span>
                </div>

                <div class="pet-info">
                    <div class="pet-name-row">
                        <h4 class="pet-name">{{ $pet->name }}</h4>
                        @php
                            $speciesLower = strtolower($pet->species);
                            $badgeClass = match($speciesLower) {
                                'dog' => 'dog',
                                'cat' => 'cat',
                                'bird' => 'bird',
                                default => 'other',
                            };
                            $speciesIcon = match($speciesLower) {
                                'dog' => 'fa-dog',
                                'cat' => 'fa-cat',
                                'bird' => 'fa-dove',
                                default => 'fa-paw',
                            };
                        @endphp
                        <span class="species-badge {{ $badgeClass }}">
                            <i class="fas {{ $speciesIcon }}"></i> {{ ucfirst($pet->species) }}
                        </span>
                    </div>

                    <div class="pet-details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Breed</span>
                            <span class="detail-value">{{ $pet->breed }}</span>
                        </div>
                        @if($pet->birth_date)
                            <div class="detail-item">
                                <span class="detail-label">Age</span>
                                <span class="detail-value">{{ $pet->birth_date->age }} {{ $pet->birth_date->age === 1 ? 'year' : 'years' }}</span>
                            </div>
                        @endif
                        <div class="detail-item">
                            <span class="detail-label">Gender</span>
                            <span class="detail-value">{{ ucfirst($pet->gender) }}</span>
                        </div>
                        @if($pet->weight)
                            <div class="detail-item">
                                <span class="detail-label">Weight</span>
                                <span class="detail-value">{{ $pet->weight }} kg</span>
                            </div>
                        @endif
                        @if($pet->microchip_number)
                            <div class="detail-item" style="grid-column: span 2;">
                                <span class="detail-label">Microchip</span>
                                <span class="detail-value">{{ $pet->microchip_number }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pet-owner">
                        <div class="owner-avatar">
                            {{ strtoupper(substr($pet->owner->user->first_name, 0, 1)) }}{{ strtoupper(substr($pet->owner->user->last_name, 0, 1)) }}
                        </div>
                        <div class="owner-info">
                            <span class="owner-label">Owner</span>
                            <span class="owner-name">{{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}</span>
                        </div>
                    </div>

                    <div class="card-actions">
                        @if($showTrash)
                            <form action="{{ route('admin.pets.restore', $pet->id) }}" method="POST" style="flex: 1;">
                                @csrf
                                <button type="submit" class="action-btn restore-btn" style="width: 100%;" onclick="return confirm('Restore this pet?')">
                                    <i class="fas fa-undo"></i> Restore
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.pets.show', $pet) }}" class="action-btn view-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('pets.qr.public', $pet->id) }}" class="action-btn view-btn" target="_blank">
                                <i class="fas fa-qrcode"></i> QR
                            </a>
                            <a href="{{ route('admin.pets.edit', $pet) }}" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.pets.destroy', $pet) }}" method="POST" style="flex: 1;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" style="width: 100%;" onclick="return confirm('Are you sure you want to delete this pet?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
