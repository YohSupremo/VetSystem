@extends('admin.dashboard')

@section('page-title', 'Staff Details')
@section('page-description', 'View staff member information')

@section('content')
<div class="staff-details-wrapper">
    <!-- Enhanced Header -->
    <div class="content-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="header-text">
                <h1>Staff Details</h1>
                <p>Complete profile overview for {{ $member->first_name ?? '' }} {{ $member->last_name ?? '' }}</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ url('admin/staff') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Back to list
            </a>
            <a href="{{ route('admin.staff.edit', ['id' => $member->id]) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Edit Profile
            </a>
        </div>
    </div>

    <!-- Profile Overview Card -->
    <div class="profile-card">
        <div class="profile-header">
            <div class="avatar-section">
                <div class="avatar-container">
                    <img src="{{ $member->avatar ?? '/images/default-avatar.png' }}" alt="avatar" class="avatar-image">
                    <div class="avatar-badge {{ optional($member)->is_active ? 'active' : 'inactive' }}">
                        <i class="fas {{ optional($member)->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    </div>
                </div>
                <div class="profile-name">
                    <h2>{{ $member->first_name ?? '-' }} {{ $member->last_name ?? '' }}</h2>
                    <p class="role-badge">
                        <i class="fas fa-briefcase"></i>
                        {{ $member->role ?? $member->position ?? 'Staff Member' }}
                    </p>
                </div>
            </div>
            <div class="status-badge {{ optional($member)->is_active ? 'status-active' : 'status-inactive' }}">
                <i class="fas {{ optional($member)->is_active ? 'fa-circle' : 'fa-circle' }}"></i>
                {{ optional($member)->is_active ? 'Active' : 'Inactive' }}
            </div>
        </div>

        <!-- Profile Details Grid -->
        <div class="details-grid">
            <!-- Personal Information Section -->
            <div class="detail-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>Personal Information</h3>
                </div>
                <div class="detail-items">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-user"></i>
                            First Name
                        </div>
                        <div class="detail-value">{{ $member->first_name ?? '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-user"></i>
                            Last Name
                        </div>
                        <div class="detail-value">{{ $member->last_name ?? '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-id-badge"></i>
                            Role/Position
                        </div>
                        <div class="detail-value">
                            <span class="role-tag">{{ $member->role ?? $member->position ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="detail-section">
                <div class="section-header">
                    <div class="section-icon contact">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <h3>Contact Information</h3>
                </div>
                <div class="detail-items">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </div>
                        <div class="detail-value">
                            @if($member->email ?? false)
                                <a href="mailto:{{ $member->email }}" class="contact-link">{{ $member->email }}</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-phone"></i>
                            Phone Number
                        </div>
                        <div class="detail-value">
                            @if($member->phone ?? $member->contact_number ?? false)
                                <a href="tel:{{ $member->phone ?? $member->contact_number }}" class="contact-link">{{ $member->phone ?? $member->contact_number }}</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Address
                        </div>
                        <div class="detail-value">{{ $member->address ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="detail-section">
                <div class="section-header">
                    <div class="section-icon credentials">
                        <i class="fas fa-key"></i>
                    </div>
                    <h3>Account Information</h3>
                </div>
                <div class="detail-items">
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-user-circle"></i>
                            Username
                        </div>
                        <div class="detail-value">{{ $member->username ?? '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-toggle-on"></i>
                            Account Status
                        </div>
                        <div class="detail-value">
                            <span class="status-indicator {{ optional($member)->is_active ? 'active' : 'inactive' }}">
                                <span class="status-dot"></span>
                                {{ optional($member)->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-calendar-plus"></i>
                            Member Since
                        </div>
                        <div class="detail-value">
                            @if(isset($member->created_at))
                                {{ $member->created_at->format('F j, Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes Section -->
            <div class="detail-section full-width">
                <div class="section-header">
                    <div class="section-icon notes">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <h3>Additional Notes</h3>
                </div>
                <div class="notes-content">
                    @if($member->notes ?? false)
                        <p>{{ $member->notes }}</p>
                    @else
                        <p class="empty-state">No additional notes available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Timeline (Optional) -->
        @if(isset($member->updated_at))
        <div class="activity-footer">
            <div class="activity-item">
                <i class="fas fa-clock"></i>
                <span>Last updated: <strong>{{ $member->updated_at->diffForHumans() }}</strong></span>
            </div>
            <div class="activity-item">
                <i class="fas fa-calendar"></i>
                <span>Updated on: <strong>{{ $member->updated_at->format('M j, Y \a\t g:i A') }}</strong></span>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Actions Card -->
    <div class="quick-actions-card">
        <h3>
            <i class="fas fa-bolt"></i>
            Quick Actions
        </h3>
        <div class="action-buttons">
           
            <a href="mailto:{{ $member->email ?? '' }}" class="action-btn email {{ !($member->email ?? false) ? 'disabled' : '' }}">
                <div class="action-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="action-text">
                    <strong>Send Email</strong>
                    <span>Contact via email</span>
                </div>
            </a>
            <a href="tel:{{ $member->phone ?? $member->contact_number ?? '' }}" class="action-btn call {{ !($member->phone ?? $member->contact_number ?? false) ? 'disabled' : '' }}">
                <div class="action-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="action-text">
                    <strong>Call</strong>
                    <span>Contact via phone</span>
                </div>
            </a>
           <form id="delete-form-{{ $member->id }}" action="{{ route('admin.staff.destroy', $member->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="action-btn delete">
                <div class="action-icon"><i class="fas fa-trash-alt"></i></div>
                <div class="action-text"><strong>Delete</strong><span>Remove from system</span></div>
            </button>
        </form>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="delete-form" method="POST" action="{{ url('admin/staff/delete/' . ($member->id ?? '')) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<style>
/* Base Variables */
:root {
    --primary-color: #FF7B6D;
    --primary-hover: #FF6B5B;
    --secondary-color: #6B7280;
    --success-color: #10B981;
    --danger-color: #EF4444;
    --warning-color: #F59E0B;
    --info-color: #3B82F6;
    --background: #F9FAFB;
    --surface: #FFFFFF;
    --border-color: #E5E7EB;
    --text-primary: #1F2937;
    --text-secondary: #6B7280;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --radius: 12px;
    --radius-sm: 8px;
}

/* Wrapper */
.staff-details-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Enhanced Header */
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--border-color);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.header-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #6366F1, #4F46E5);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.875rem;
    box-shadow: var(--shadow-md);
}

.header-text h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
}

.header-text p {
    font-size: 0.95rem;
    color: var(--text-secondary);
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

/* Profile Card */
.profile-card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

/* Profile Header */
.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2.5rem;
    background: linear-gradient(135deg, #F9FAFB, #F3F4F6);
    border-bottom: 2px solid var(--border-color);
}

.avatar-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.avatar-container {
    position: relative;
}

.avatar-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: var(--shadow-md);
}

.avatar-badge {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    font-size: 0.875rem;
}

.avatar-badge.active {
    background: var(--success-color);
    color: white;
}

.avatar-badge.inactive {
    background: var(--secondary-color);
    color: white;
}

.profile-name h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
}

.status-badge {
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-badge.status-active {
    background: #D1FAE5;
    color: #065F46;
}

.status-badge.status-active i {
    color: var(--success-color);
}

.status-badge.status-inactive {
    background: #F3F4F6;
    color: var(--secondary-color);
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    padding: 2.5rem;
}

.detail-section.full-width {
    grid-column: 1 / -1;
}

/* Detail Section */
.detail-section {
    background: #FAFBFC;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: 1.5rem;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--border-color);
}

.section-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
}

.section-icon.personal {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

.section-icon.contact {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
}

.section-icon.credentials {
    background: linear-gradient(135deg, #10B981, #059669);
}

.section-icon.notes {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.section-header h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

/* Detail Items */
.detail-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.detail-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.025em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-label i {
    font-size: 0.75rem;
    opacity: 0.7;
}

.detail-value {
    font-size: 1rem;
    color: var(--text-primary);
    font-weight: 500;
}

.contact-link {
    color: var(--primary-color);
    text-decoration: none;
    transition: all 0.2s ease;
}

.contact-link:hover {
    color: var(--primary-hover);
    text-decoration: underline;
}

.role-tag {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    background: linear-gradient(135deg, #EEF2FF, #E0E7FF);
    color: #4338CA;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: capitalize;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.875rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.875rem;
}

.status-indicator.active {
    background: #D1FAE5;
    color: #065F46;
}

.status-indicator.inactive {
    background: #F3F4F6;
    color: var(--secondary-color);
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

/* Notes Content */
.notes-content {
    background: white;
    padding: 1.25rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-color);
}

.notes-content p {
    margin: 0;
    line-height: 1.6;
    color: var(--text-primary);
}

.empty-state {
    color: var(--text-secondary);
    font-style: italic;
}

/* Activity Footer */
.activity-footer {
    display: flex;
    justify-content: space-around;
    padding: 1.25rem 2.5rem;
    background: linear-gradient(135deg, #F9FAFB, #F3F4F6);
    border-top: 2px solid var(--border-color);
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.activity-item i {
    color: var(--primary-color);
}

.activity-item strong {
    color: var(--text-primary);
}

/* Quick Actions Card */
.quick-actions-card {
    background: var(--surface);
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.quick-actions-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.625rem;
}

.quick-actions-card h3 i {
    color: var(--warning-color);
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #FAFBFC, #F3F4F6);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.action-btn:hover {
    border-color: var(--primary-color);
    background: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.action-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.action-btn.edit .action-icon {
    background: linear-gradient(135deg, #EEF2FF, #E0E7FF);
    color: #4F46E5;
}

.action-btn.email .action-icon {
    background: linear-gradient(135deg, #DBEAFE, #BFDBFE);
    color: #1D4ED8;
}

.action-btn.call .action-icon {
    background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
    color: #047857;
}

.action-btn.delete .action-icon {
    background: linear-gradient(135deg, #FEE2E2, #FECACA);
    color: #DC2626;
}

.action-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.action-text strong {
    font-size: 0.95rem;
    color: var(--text-primary);
}

.action-text span {
    font-size: 0.8125rem;
    color: var(--text-secondary);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    border-radius: var(--radius-sm);
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    white-space: nowrap;
}

.btn i {
    font-size: 0.875rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-hover), #FF5B4B);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.btn-outline {
    background: transparent;
    color: var(--text-primary);
    border-color: var(--border-color);
}

.btn-outline:hover {
    background: var(--background);
    border-color: var(--text-secondary);
}

/* Responsive Design */
@media (max-width: 968px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .staff-details-wrapper {
        padding: 1rem;
    }

    .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .header-actions {
        width: 100%;
        flex-direction: column;
    }

    .header-actions .btn {
        width: 100%;
    }

    .profile-header {
        flex-direction: column;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .avatar-section {
        flex-direction: column;
        text-align: center;
        width: 100%;
    }

    .profile-name {
        width: 100%;
        text-align: center;
    }

    .status-badge {
        align-self: flex-start;
    }

    .details-grid {
        padding: 1.5rem;
    }

    .activity-footer {
        flex-direction: column;
        gap: 1rem;
    }

    .action-buttons {
        grid-template-columns: 1fr;
    }
}

/* Print Styles */
@media print {
    .header-actions,
    .quick-actions-card {
        display: none;
    }

    .profile-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>
@endsection