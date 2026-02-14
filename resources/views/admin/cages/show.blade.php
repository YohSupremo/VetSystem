@extends('admin.dashboard')

@section('page-title', 'Cage Details')
@section('page-description', 'View details and assignments for this cage')

@section('content')
<style>
    .details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 1rem;
    }

    /* Back Button */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #6B7280;
        font-size: 0.95rem;
        font-weight: 500;
        margin-bottom: 2rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .back-btn:hover {
        color: #1F2937;
        gap: 0.75rem;
    }

    .back-btn i {
        font-size: 0.875rem;
    }

    /* Grid Layout */
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 1.5rem;
    }

    /* Cage Info Card */
    .cage-info-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }

    .cage-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .cage-title i {
        color: #FF7E7E;
        font-size: 1.5rem;
    }

    .info-section {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .info-item {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #F3F4F6;
    }

    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #9CA3AF;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-label i {
        color: #FF7E7E;
        font-size: 0.875rem;
    }

    .info-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1F2937;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    .status-badge.available {
        background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
        color: #065F46;
    }

    .status-badge.occupied {
        background: linear-gradient(135deg, #FED7D7 0%, #FCA5A5 100%);
        color: #991B1B;
    }

    .status-badge.maintenance {
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        color: #92400E;
    }

    .status-badge i {
        font-size: 0.75rem;
    }

    /* QR Code Section */
    .qr-section {
        margin-top: 2rem;
        padding: 2rem;
        background: linear-gradient(135deg, #FFF5F5 0%, #FFFFFF 100%);
        border-radius: 16px;
        border: 2px dashed #FED7D7;
        text-align: center;
    }

    .qr-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #C53030;
        margin-bottom: 1rem;
        border: 1px solid #FED7D7;
    }

    .qr-badge i {
        color: #FF7E7E;
    }

    .qr-code-wrapper {
        background: white;
        padding: 1rem;
        border-radius: 12px;
        display: inline-block;
        margin: 1rem 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #E5E7EB;
    }

    .qr-code-wrapper img {
        display: block;
        width: 180px;
        height: 180px;
    }

    .qr-url {
        font-size: 0.7rem;
        color: #9CA3AF;
        word-break: break-all;
        background: white;
        padding: 0.75rem;
        border-radius: 8px;
        margin: 1rem 0;
        font-family: 'Courier New', monospace;
    }

    .btn-scan {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #FF7E7E 0%, #FF6B6B 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(255, 126, 126, 0.3);
        transition: all 0.2s;
    }

    .btn-scan:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 126, 126, 0.4);
    }

    /* Occupancy Card */
    .occupancy-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }

    .occupancy-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.5rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 1.5rem;
    }

    .occupancy-header i {
        color: #FF7E7E;
        font-size: 1.25rem;
    }

    /* Pet Banner */
    .pet-banner {
        padding: 2rem;
        background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);
        border-radius: 16px;
        border: 2px solid #A7F3D0;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .pet-avatar-large {
        width: 100px;
        height: 100px;
        border-radius: 20px;
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
        flex-shrink: 0;
    }

    .pet-info {
        flex: 1;
    }

    .pet-name {
        font-size: 1.75rem;
        font-weight: 700;
        color: #065F46;
        margin-bottom: 0.5rem;
    }

    .pet-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #047857;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    .pet-meta-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .pet-meta-item i {
        font-size: 0.875rem;
        opacity: 0.8;
    }

    .pet-meta-divider {
        width: 4px;
        height: 4px;
        background: #059669;
        border-radius: 50%;
    }

    .admission-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        color: #065F46;
        font-weight: 600;
        border: 1px solid #A7F3D0;
    }

    .admission-badge i {
        color: #10B981;
    }

    /* Details Grid */
    .details-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .detail-card {
        background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
    }

    .detail-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6B7280;
        margin-bottom: 1rem;
    }

    .detail-header i {
        color: #FF7E7E;
    }

    .detail-content {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .detail-content p {
        font-size: 1rem;
        font-weight: 600;
        color: #1F2937;
        margin: 0;
    }

    .detail-content .subtext {
        font-size: 0.875rem;
        font-weight: 400;
        color: #6B7280;
    }

    .notes-card {
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        border: 2px solid #FCD34D;
    }

    .notes-card .detail-header {
        color: #92400E;
    }

    .notes-card .detail-content p {
        color: #78350F;
        font-weight: 400;
        line-height: 1.6;
    }

    /* Empty State */
    .empty-occupancy {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .empty-icon i {
        font-size: 2.5rem;
        color: #9CA3AF;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: #6B7280;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .details-grid {
            grid-template-columns: 1fr;
        }

        .pet-banner {
            flex-direction: column;
            text-align: center;
        }
    }

    @media (max-width: 768px) {
        .details-container {
            padding: 1rem;
        }

        .cage-title {
            font-size: 1.5rem;
        }

        .pet-name {
            font-size: 1.5rem;
        }

        .pet-meta {
            flex-direction: column;
            gap: 0.5rem;
        }

        .pet-meta-divider {
            display: none;
        }

        .details-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="details-container">
    <!-- Back Button -->
    <a href="{{ route('admin.cages.index') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Cages</span>
    </a>

    <!-- Main Grid -->
    <div class="details-grid">
        <!-- Cage Info Card -->
        <div class="cage-info-card">
            <h2 class="cage-title">
                <i class="fas fa-warehouse"></i>
                {{ $cage->cage_code }}
            </h2>
            
            <div class="info-section">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Location
                    </div>
                    <div class="info-value">{{ $cage->location }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-ruler-combined"></i>
                        Size
                    </div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $cage->size)) }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-info-circle"></i>
                        Status
                    </div>
                    <span class="status-badge {{ $cage->status }}">
                        @if($cage->status === 'available')
                            <i class="fas fa-check-circle"></i>
                        @elseif($cage->status === 'occupied')
                            <i class="fas fa-paw"></i>
                        @else
                            <i class="fas fa-tools"></i>
                        @endif
                        {{ ucfirst($cage->status) }}
                    </span>
                </div>
            </div>
            
            <!-- QR Code Section -->
            <div class="qr-section">
                <div class="qr-badge">
                    <i class="fas fa-qrcode"></i>
                    <span>SCAN CODE</span>
                </div>
                
                <div class="qr-code-wrapper">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=360x360&margin=10&ecc=H&data={{ urlencode($scanUrl) }}" alt="QR Code">
                </div>
                
                <div class="qr-url">{{ $scanUrl }}</div>
                
                <a href="{{ $scanUrl }}" target="_blank" class="btn-scan">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Simulate Scan</span>
                </a>
            </div>
        </div>

        <!-- Occupancy Card -->
        <div class="occupancy-card">
            <h3 class="occupancy-header">
                <i class="fas fa-clipboard-list"></i>
                Current Occupancy
            </h3>
            
            @if($assignment)
                <!-- Pet Banner -->
                <div class="pet-banner">
                    <div class="pet-avatar-large">
                        {{ substr($assignment->pet->name, 0, 1) }}
                    </div>
                    
                    <div class="pet-info">
                        <h4 class="pet-name">{{ $assignment->pet->name }}</h4>
                        <div class="pet-meta">
                            <div class="pet-meta-item">
                                <i class="fas fa-dog"></i>
                                <span>{{ $assignment->pet->breed }}</span>
                            </div>
                            <div class="pet-meta-divider"></div>
                            <div class="pet-meta-item">
                                <i class="fas fa-{{ $assignment->pet->gender === 'male' ? 'mars' : 'venus' }}"></i>
                                <span>{{ ucfirst($assignment->pet->gender) }}</span>
                            </div>
                            <div class="pet-meta-divider"></div>
                            <div class="pet-meta-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span>{{ $assignment->pet->age ?? 'Unknown' }} years</span>
                            </div>
                        </div>
                        <div class="admission-badge">
                            <i class="fas fa-calendar-check"></i>
                            <span>Admitted {{ \Carbon\Carbon::parse($assignment->start_date)->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="details-info-grid">
                    <div class="detail-card">
                        <div class="detail-header">
                            <i class="fas fa-user"></i>
                            Owner Details
                        </div>
                        <div class="detail-content">
                            <p>{{ $assignment->pet->owner->name ?? 'Unknown' }}</p>
                            <p class="subtext">
                                <i class="fas fa-phone"></i> {{ $assignment->pet->owner->phone ?? 'No Phone' }}
                            </p>
                            <p class="subtext">
                                <i class="fas fa-envelope"></i> {{ $assignment->pet->owner->email ?? 'No Email' }}
                            </p>
                        </div>
                    </div>

                    <div class="detail-card notes-card">
                        <div class="detail-header">
                            <i class="fas fa-sticky-note"></i>
                            Assignment Notes
                        </div>
                        <div class="detail-content">
                            <p>{{ $assignment->notes ?? 'No specific notes for this assignment.' }}</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="empty-occupancy">
                    <div class="empty-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h4 class="empty-title">Cage is Available</h4>
                    <p class="empty-text">This cage is currently empty and ready for occupancy.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection