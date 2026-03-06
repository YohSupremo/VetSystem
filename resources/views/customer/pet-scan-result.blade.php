@extends('layout.base')
@php($bodyClass = 'customer-body')

@section('title', 'Pet Scan Result')

@section('content')
@include('layout.customer-navbar')

<style>
    .scan-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 1rem;
    }

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

    .scan-success-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
    }

    .pet-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .pet-header {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        align-items: center;
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
        overflow: hidden;
    }

    .pet-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pet-avatar i {
        font-size: 3rem;
        color: white;
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

    .pet-reg {
        font-size: 0.875rem;
        color: #6B7280;
        font-family: 'Courier New', monospace;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .info-item {
        padding: 1rem;
        background: #F9FAFB;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6B7280;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1F2937;
    }

    .cage-info {
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        border: 2px solid #FCD34D;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .cage-info h4 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #92400E;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cage-detail {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .cage-detail strong {
        color: #92400E;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        transition: all 0.2s;
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
        color: white;
    }

    @media (max-width: 768px) {
        .pet-header {
            flex-direction: column;
            text-align: center;
        }

        .pet-name {
            font-size: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="scan-container">
    <div class="scan-header">
        <h1 class="scan-title">Pet Scan Result</h1>
        <div class="scan-success-badge">
            <i class="fas fa-check-circle"></i>
            <span>Scan Recorded Successfully</span>
        </div>
    </div>

    <div class="pet-card">
        <div class="pet-header">
            <div class="pet-avatar">
                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}">
            </div>
            <div class="pet-details">
                <h2 class="pet-name">{{ $pet->name }}</h2>
                <div class="pet-reg">Registration: {{ $pet->registration_number }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Species</div>
                <div class="info-value">{{ $pet->species ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Breed</div>
                <div class="info-value">{{ $pet->breed ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Age</div>
                <div class="info-value">{{ $pet->age ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Weight</div>
                <div class="info-value">{{ $pet->weight ? $pet->weight . ' kg' : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Gender</div>
                <div class="info-value">{{ $pet->gender ? ucfirst($pet->gender) : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Color</div>
                <div class="info-value">{{ $pet->color ?? 'N/A' }}</div>
            </div>
        </div>

        @if($assignment)
            <div class="cage-info">
                <h4>
                    <i class="fas fa-home"></i>
                    Current Cage Assignment
                </h4>
                <div class="cage-detail">
                    <strong>Cage:</strong>
                    <span>{{ $assignment->cage->cage_code ?? $assignment->cage->name ?? 'N/A' }}</span>
                </div>
                <div class="cage-detail">
                    <strong>Location:</strong>
                    <span>{{ $assignment->cage->location ?? 'N/A' }}</span>
                </div>
                <div class="cage-detail">
                    <strong>Check-in Time:</strong>
                    <span>{{ $assignment->check_in_time ? $assignment->check_in_time->format('M d, Y h:i A') : 'N/A' }}</span>
                </div>
                <div class="cage-detail">
                    <strong>Expected Check-out:</strong>
                    <span>{{ $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
        @endif
    </div>

    <div class="text-center">
        <a href="{{ route('customer.pets.scan') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Back to Caged Pets
        </a>
    </div>
</div>
@endsection
