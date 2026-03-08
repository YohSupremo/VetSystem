@extends('admin.dashboard')

@section('page-title', 'User Details')
@section('page-description', 'User information and account details')

@section('content')
<style>
    .user-details-area {
        max-width: 1200px;
        margin: 0 auto;
    }

    .user-details-area .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .user-details-area .top-bar .user-header {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .user-details-area .top-bar .user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: white;
        font-weight: 700;
        font-family: 'Fredoka', sans-serif;
        box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    }

    .user-details-area .top-bar .user-info h3 {
        margin: 0;
        font-family: 'Fredoka', sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: #2D3748;
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .user-details-area .top-bar .user-info p {
        margin: 4px 0 0 0;
        color: #718096;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-details-area .top-bar .user-info p i {
        color: #FF8C42;
    }

    .user-details-area .action-buttons {
        display: flex;
        gap: 12px;
    }

    .user-details-area .btn {
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .user-details-area .btn-primary {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        color: white;
        box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    }

    .user-details-area .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 140, 66, 0.4);
        color: white;
    }

    .user-details-area .btn-secondary {
        background: white;
        color: #2D3748;
        border: 2px solid #E2E8F0;
    }

    .user-details-area .btn-secondary:hover {
        background: #F7FAFC;
        border-color: #CBD5E0;
        color: #2D3748;
    }

    .user-details-area .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    .user-details-area .detail-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid rgba(255, 140, 66, 0.08);
    }

    .user-details-area .detail-card h4 {
        font-family: 'Fredoka', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #2D3748;
        margin: 0 0 24px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-details-area .detail-card h4 i {
        color: #FF8C42;
        font-size: 20px;
    }

    .user-details-area .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 16px 0;
        border-bottom: 1px solid #F1F3F5;
    }

    .user-details-area .detail-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .user-details-area .detail-row:first-child {
        padding-top: 0;
    }

    .user-details-area .detail-label {
        font-weight: 600;
        color: #718096;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        width: 140px;
    }

    .user-details-area .detail-value {
        color: #2D3748;
        font-size: 15px;
        font-weight: 500;
        text-align: right;
        flex: 1;
    }

    .user-details-area .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        gap: 6px;
    }

    .user-details-area .role-badge.pet_owner {
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        color: #92400E;
    }

    .user-details-area .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .user-details-area .status-badge.active {
        background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
        color: #065F46;
    }

    .user-details-area .status-badge.inactive {
        background: linear-gradient(135deg, #F1F3F5, #E8E8EA);
        color: #6B7280;
    }

    .user-details-area .status-badge i {
        font-size: 8px;
    }

    .user-details-area .verification-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    .user-details-area .verification-status.verified {
        color: #065F46;
    }

    .user-details-area .verification-status.verified i {
        color: #10B981;
    }

    .user-details-area .verification-status.unverified {
        color: #92400E;
    }

    .user-details-area .verification-status.unverified i {
        color: #F59E0B;
    }

    @media (max-width: 768px) {
        .user-details-area .top-bar {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-details-area .action-buttons {
            width: 100%;
        }

        .user-details-area .btn {
            flex: 1;
            justify-content: center;
        }

        .user-details-area .details-grid {
            grid-template-columns: 1fr;
        }

        .user-details-area .detail-row {
            flex-direction: column;
            gap: 8px;
        }

        .user-details-area .detail-value {
            text-align: left;
        }
    }
</style>

<div class="user-details-area">
    <div class="top-bar">
        <div class="user-header">
            <div class="user-avatar">
                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
            </div>
            <div class="user-info">
                <h3>{{ $user->first_name }} {{ $user->last_name }}</h3>
                <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
            </div>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-card">
            <h4><i class="fas fa-user-circle"></i> Account Information</h4>
            
            <div class="detail-row">
                <div class="detail-label">Username</div>
                <div class="detail-value">{{ $user->username }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Email</div>
                <div class="detail-value">{{ $user->email }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Role</div>
                <div class="detail-value">
                    @php
                        $hasAnchoredPets = optional($user->petOwner)->pets && $user->petOwner->pets->isNotEmpty();
                        $displayRole = $hasAnchoredPets ? 'pet_owner' : 'registered_user';
                    @endphp
                    <span class="role-badge {{ $displayRole }}">
                        @if($displayRole === 'registered_user')
                            <i class="fas fa-user"></i>
                        @elseif($displayRole === 'pet_owner')
                            <i class="fas fa-paw"></i>
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                        {{ ucfirst(str_replace('_', ' ', $displayRole)) }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Account Status</div>
                <div class="detail-value">
                    <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                        <i class="fas fa-circle"></i>
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <h4><i class="fas fa-id-card"></i> Personal Information</h4>
            
            <div class="detail-row">
                <div class="detail-label">Full Name</div>
                <div class="detail-value">{{ $user->first_name }} {{ $user->last_name }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Contact Number</div>
                <div class="detail-value">{{ $user->contact_number ?? '—' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Address</div>
                <div class="detail-value">{{ $user->address ?? '—' }}</div>
            </div>
        </div>

        <div class="detail-card">
            <h4><i class="fas fa-shield-alt"></i> Verification Status</h4>
            
            <div class="detail-row">
                <div class="detail-label">Email Verified</div>
                <div class="detail-value">
                    <span class="verification-status {{ $user->email_verified ? 'verified' : 'unverified' }}">
                        <i class="fas fa-{{ $user->email_verified ? 'check-circle' : 'exclamation-circle' }}"></i>
                        {{ $user->email_verified ? 'Verified' : 'Not Verified' }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Phone Verified</div>
                <div class="detail-value">
                    <span class="verification-status {{ $user->phone_verified ? 'verified' : 'unverified' }}">
                        <i class="fas fa-{{ $user->phone_verified ? 'check-circle' : 'exclamation-circle' }}"></i>
                        {{ $user->phone_verified ? 'Verified' : 'Not Verified' }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Created</div>
                <div class="detail-value">{{ $user->created_at ? $user->created_at->format('M d, Y h:i A') : '—' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Last Updated</div>
                <div class="detail-value">{{ $user->updated_at ? $user->updated_at->format('M d, Y h:i A') : '—' }}</div>
            </div>
        </div>
    </div>
</div>

@endsection