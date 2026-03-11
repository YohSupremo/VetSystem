@extends('admin.dashboard')

@section('page-title', 'Cage QR Scanner')
@section('page-description', 'Scan cage QR codes to view and manage assignments')

@section('content')
<style>
    .scan-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* Header */
    .scan-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .scan-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 0.5rem;
    }

    .cage-code-display {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #FF7E7E 0%, #FF6B6B 100%);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.125rem;
        box-shadow: 0 4px 12px rgba(255, 126, 126, 0.3);
    }

    .cage-code-display i {
        font-size: 1rem;
    }

    /* Status Banner */
    .status-banner {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid #E5E7EB;
    }

    .status-header {
        padding: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
        border-bottom: 1px solid #E5E7EB;
    }

    .status-header.occupied {
        background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);
        border-bottom-color: #A7F3D0;
    }

    .status-header.vacant {
        background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
    }

    .status-info h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 0.25rem;
    }

    .status-label {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        background: #F3F4F6;
        color: #6B7280;
    }

    .status-badge {
        padding: 0.75rem 1.75rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .status-badge.occupied {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
    }

    .status-badge.vacant {
        background: white;
        color: #6B7280;
        border: 2px solid #E5E7EB;
    }

    /* Pet Information Card */
    .pet-card {
        padding: 2rem;
        background: white;
    }

    .pet-header {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .pet-avatar {
        flex-shrink: 0;
        width: 120px;
        height: 120px;
        border-radius: 20px;
        background: linear-gradient(135deg, #FF7E7E 0%, #FF6B6B 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 700;
        box-shadow: 0 8px 24px rgba(255, 126, 126, 0.3);
        position: relative;
    }

    .pet-avatar::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 22px;
        background: linear-gradient(135deg, rgba(255, 126, 126, 0.3) 0%, rgba(255, 107, 107, 0.3) 100%);
        z-index: -1;
    }

    .pet-details {
        flex: 1;
    }

    .pet-name {
        font-size: 2rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 0.5rem;
    }

    .pet-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #6B7280;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }

    .pet-meta-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .pet-meta-item i {
        color: #FF7E7E;
        font-size: 0.875rem;
    }

    .pet-meta-divider {
        width: 4px;
        height: 4px;
        background: #D1D5DB;
        border-radius: 50%;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .info-card {
        background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
        padding: 1.25rem;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6B7280;
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
        font-size: 1rem;
        font-weight: 600;
        color: #1F2937;
        margin-bottom: 0.25rem;
    }

    .info-subtext {
        font-size: 0.875rem;
        color: #6B7280;
    }

    /* Notes Section */
    .notes-section {
        margin-top: 2rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        border-radius: 12px;
        border: 2px solid #FCD34D;
    }

    .notes-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #92400E;
        margin-bottom: 0.75rem;
    }

    .notes-header i {
        color: #F59E0B;
    }

    .notes-content {
        font-size: 0.95rem;
        color: #78350F;
        line-height: 1.6;
    }

    /* Nursing Care Section */
    .nursing-care-section {
        padding: 2rem;
        background: white;
        border-top: 1px solid #E5E7EB;
    }

    .care-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 1.5rem;
    }

    .care-header i {
        color: #FF7E7E;
        font-size: 1.125rem;
    }

    .care-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.25rem;
    }

    .care-card {
        background: white;
        border: 2px solid #E5E7EB;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.2s;
    }

    .care-card:hover {
        border-color: #FF7E7E;
        box-shadow: 0 4px 12px rgba(255, 126, 126, 0.15);
    }

    .care-card.feeding {
        border-color: #A7F3D0;
        background: linear-gradient(135deg, #ECFDF5 0%, #FFFFFF 100%);
    }

    .care-card.medication {
        border-color: #FCA5A5;
        background: linear-gradient(135deg, #FEF2F2 0%, #FFFFFF 100%);
    }

    .care-card.diet {
        border-color: #FDE68A;
        background: linear-gradient(135deg, #FFFBEB 0%, #FFFFFF 100%);
    }

    .care-card.schedule {
        border-color: #C4B5FD;
        background: linear-gradient(135deg, #F5F3FF 0%, #FFFFFF 100%);
    }

    .care-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid;
    }

    .care-card.feeding .care-card-header {
        border-bottom-color: #A7F3D0;
    }

    .care-card.medication .care-card-header {
        border-bottom-color: #FCA5A5;
    }

    .care-card.diet .care-card-header {
        border-bottom-color: #FDE68A;
    }

    .care-card.schedule .care-card-header {
        border-bottom-color: #C4B5FD;
    }

    .care-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
    }

    .care-card.feeding .care-icon {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
    }

    .care-card.medication .care-icon {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        color: white;
    }

    .care-card.diet .care-icon {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        color: white;
    }

    .care-card.schedule .care-icon {
        background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
        color: white;
    }

    .care-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1F2937;
    }

    .care-content {
        font-size: 0.95rem;
        color: #4B5563;
        line-height: 1.6;
        white-space: pre-line;
    }

    .care-times {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .time-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .care-card.feeding .time-badge {
        background: #D1FAE5;
        color: #065F46;
    }

    .care-card.medication .time-badge {
        background: #FEE2E2;
        color: #991B1B;
    }

    .care-card.schedule .time-badge {
        background: #EDE9FE;
        color: #5B21B6;
    }

    .no-care-info {
        text-align: center;
        padding: 2rem;
        color: #9CA3AF;
        font-style: italic;
        background: #F9FAFB;
        border-radius: 12px;
        border: 2px dashed #E5E7EB;
    }

    /* Medical Records */
    .medical-section {
        padding: 2rem;
        background: #F9FAFB;
        border-top: 1px solid #E5E7EB;
    }

    .medical-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 1.5rem;
    }

    .medical-header i {
        color: #FF7E7E;
        font-size: 1.125rem;
    }

    .medical-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .medical-record {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        transition: all 0.2s;
    }

    .medical-record:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .medical-record-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 0.75rem;
    }

    .diagnosis {
        font-size: 1rem;
        font-weight: 600;
        color: #1F2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .diagnosis-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #EF4444;
        font-size: 0.875rem;
    }

    .record-date {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6B7280;
        background: #F3F4F6;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
    }

    .treatment {
        font-size: 0.875rem;
        color: #6B7280;
        line-height: 1.5;
    }

    .no-records {
        text-align: center;
        padding: 3rem 2rem;
        color: #9CA3AF;
        font-style: italic;
    }

    /* Empty State */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
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
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .empty-icon i {
        font-size: 2.5rem;
        color: #9CA3AF;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: #6B7280;
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #FF7E7E 0%, #FF6B6B 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(255, 126, 126, 0.3);
        transition: all 0.2s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 126, 126, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .scan-container {
            padding: 0.75rem;
        }

        .scan-title {
            font-size: 1.35rem;
        }

        .cage-code-display {
            font-size: 0.95rem;
            padding: 0.4rem 1.1rem;
        }

        .status-header {
            flex-direction: column;
            gap: 0.75rem;
            text-align: center;
            padding: 1.25rem;
        }

        .status-info h2 {
            font-size: 1.2rem;
        }

        .pet-card {
            padding: 1.25rem;
        }

        .pet-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.25rem;
        }

        .pet-avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
            border-radius: 16px;
        }

        .pet-avatar::after {
            border-radius: 18px;
        }

        .pet-name {
            font-size: 1.35rem;
        }

        .pet-meta {
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.4rem 0.75rem;
        }

        .pet-meta-divider {
            display: none;
        }

        .info-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .info-card {
            padding: 1rem;
        }

        .notes-section {
            padding: 1.1rem;
            margin-top: 1.25rem;
        }

        /* Nursing Care */
        .nursing-care-section {
            padding: 1.25rem;
        }

        .care-header {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .care-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .care-card {
            padding: 1.15rem;
        }

        .care-card-header {
            margin-bottom: 0.75rem;
            padding-bottom: 0.6rem;
            gap: 0.6rem;
        }

        .care-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
            border-radius: 9px;
        }

        .care-title {
            font-size: 0.92rem;
        }

        .care-content {
            font-size: 0.88rem;
        }

        .care-times {
            gap: 0.4rem;
        }

        .time-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }

        /* Medical Records */
        .medical-section {
            padding: 1.25rem;
        }

        .medical-header {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .medical-record {
            padding: 1rem;
        }

        .medical-record-header {
            flex-direction: column;
            gap: 0.5rem;
        }

        .record-date {
            align-self: flex-start;
        }

        .diagnosis {
            font-size: 0.92rem;
        }

        .treatment {
            font-size: 0.82rem;
        }

        /* Empty State */
        .empty-state {
            padding: 2.5rem 1.25rem;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
        }

        .empty-icon i {
            font-size: 2rem;
        }

        .empty-title {
            font-size: 1.2rem;
        }
    }
</style>

<div class="scan-container">
    <!-- Header -->
    <div class="scan-header">
        <h1 class="scan-title">Cage Scan Result</h1>
        <div class="cage-code-display">
            <i class="fas fa-qrcode"></i>
            <span>{{ $cage->cage_code }}</span>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="status-banner">
        <div class="status-header {{ $assignment ? 'occupied' : 'vacant' }}">
            <div class="status-info">
                <h2>Cage Status</h2>
                <span class="status-label">{{ ucfirst($cage->status) }}</span>
            </div>
            <div>
                <span class="status-badge {{ $assignment ? 'occupied' : 'vacant' }}">
                    {{ $assignment ? 'Occupied' : 'Vacant' }}
                </span>
            </div>
        </div>

        @if($assignment && $assignment->pet)
            <!-- Pet Information -->
            <div class="pet-card">
                <div class="pet-header">
                    <!-- Pet Avatar -->
                    <div class="pet-avatar">
                        {{ substr($assignment->pet->name, 0, 1) }}
                    </div>
                    
                    <!-- Pet Details -->
                    <div class="pet-details">
                        <h3 class="pet-name">{{ $assignment->pet->name }}</h3>
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
                                <span>{{ $assignment->pet->age }} years old</span>
                            </div>
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-card">
                                <div class="info-label">
                                    <i class="fas fa-user"></i>
                                    Owner
                                </div>
                                <div class="info-value">{{ $assignment->pet->owner->name ?? 'Unknown' }}</div>
                                <div class="info-subtext">{{ $assignment->pet->owner->phone ?? 'No Phone' }}</div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">
                                    <i class="fas fa-calendar-check"></i>
                                    Admitted
                                </div>
                                <div class="info-value">{{ \Carbon\Carbon::parse($assignment->start_date)->format('M d, Y') }}</div>
                                <div class="info-subtext">{{ \Carbon\Carbon::parse($assignment->start_date)->diffForHumans() }}</div>
                            </div>
                        </div>

                        @if($assignment->notes)
                        <div class="notes-section">
                            <div class="notes-header">
                                <i class="fas fa-sticky-note"></i>
                                Boarding/Hospital Notes
                            </div>
                            <div class="notes-content">{{ $assignment->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Nursing Care Instructions -->
            <div class="nursing-care-section">
                <h3 class="care-header">
                    <i class="fas fa-notes-medical"></i>
                    Nursing Care Instructions
                </h3>
                
                @php
                    $hasCareInfo = $assignment->feeding_schedule || 
                                   $assignment->feeding_times || 
                                   $assignment->medication_instructions || 
                                   $assignment->medication_times || 
                                   $assignment->special_diet_notes ||
                                   $assignment->daily_rate;
                @endphp

                @if($hasCareInfo)
                    <div class="care-grid">
                        <!-- Feeding Schedule -->
                        @if($assignment->feeding_schedule || $assignment->feeding_times)
                        <div class="care-card feeding">
                            <div class="care-card-header">
                                <div class="care-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="care-title">Feeding Schedule</div>
                            </div>
                            <div class="care-content">
                                {{ $assignment->feeding_schedule ?? 'Standard feeding schedule' }}
                            </div>
                            @if($assignment->feeding_times)
                            <div class="care-times">
                                @foreach(explode(',', $assignment->feeding_times) as $time)
                                    <span class="time-badge">
                                        <i class="fas fa-clock"></i> {{ trim($time) }}
                                    </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Medication Instructions -->
                        @if($assignment->medication_instructions || $assignment->medication_times)
                        <div class="care-card medication">
                            <div class="care-card-header">
                                <div class="care-icon">
                                    <i class="fas fa-pills"></i>
                                </div>
                                <div class="care-title">Medication Instructions</div>
                            </div>
                            <div class="care-content">
                                {{ $assignment->medication_instructions ?? 'See medication schedule' }}
                            </div>
                            @if($assignment->medication_times)
                            <div class="care-times">
                                @foreach(explode(',', $assignment->medication_times) as $time)
                                    <span class="time-badge">
                                        <i class="fas fa-clock"></i> {{ trim($time) }}
                                    </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Special Diet Notes -->
                        @if($assignment->special_diet_notes)
                        <div class="care-card diet">
                            <div class="care-card-header">
                                <div class="care-icon">
                                    <i class="fas fa-apple-alt"></i>
                                </div>
                                <div class="care-title">Special Diet Notes</div>
                            </div>
                            <div class="care-content">
                                {{ $assignment->special_diet_notes }}
                            </div>
                        </div>
                        @endif

                        <!-- Check-in/Check-out & Daily Rate -->
                        <div class="care-card schedule">
                            <div class="care-card-header">
                                <div class="care-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="care-title">Stay Information</div>
                            </div>
                            <div class="care-content">
                                <strong>Check-in:</strong> 
                                {{ $assignment->check_in_time ? \Carbon\Carbon::parse($assignment->check_in_time)->format('M d, Y h:i A') : 'Not recorded' }}
                                <br>
                                <strong>Expected Check-out:</strong> 
                                @if($assignment->end_date)
                                    {{ \Carbon\Carbon::parse($assignment->end_date)->format('M d, Y') }}
                                    @if($assignment->check_out_time)
                                        {{ \Carbon\Carbon::parse($assignment->check_out_time)->format('h:i A') }}
                                    @else
                                        <span style="color: #6B7280; font-size: 0.875rem;">(Time: TBD)</span>
                                    @endif
                                @else
                                    Not set
                                @endif
                                @if($assignment->check_out_time)
                                    <br>
                                    <strong>Actual Check-out:</strong> 
                                    {{ \Carbon\Carbon::parse($assignment->check_out_time)->format('M d, Y h:i A') }}
                                @endif
                                @if($assignment->daily_rate)
                                <br><br>
                                <strong>Daily Rate:</strong> 
                                <span style="color: #059669; font-weight: 700; font-size: 1.125rem;">
                                    ₱{{ number_format($assignment->daily_rate, 2) }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="no-care-info">
                        <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                        No specific care instructions have been recorded for this assignment.
                    </div>
                @endif
            </div>

            <!-- Medical Records -->
            <div class="medical-section">
                <h3 class="medical-header">
                    <i class="fas fa-file-medical"></i>
                    Recent Medical Records
                </h3>
                
                @if($assignment->pet->medicalRecords->count() > 0)
                    <div class="medical-list">
                        @foreach($assignment->pet->medicalRecords as $record)
                        <div class="medical-record">
                            <div class="medical-record-header">
                                <div class="diagnosis">
                                    <div class="diagnosis-icon">
                                        <i class="fas fa-stethoscope"></i>
                                    </div>
                                    <span>{{ $record->diagnosis }}</span>
                                </div>
                                <span class="record-date">{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</span>
                            </div>
                            <div class="treatment">{{ Str::limit($record->treatment_plan, 120) }}</div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-records">
                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                        No recent medical records found.
                    </div>
                @endif
            </div>

        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="empty-title">Available for Occupancy</h3>
                <p class="empty-text">This cage is currently empty and ready for a new pet assignment.</p>
                <a href="{{ route('admin.boarding.index') }}" class="btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Go to Boarding Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
@endsection