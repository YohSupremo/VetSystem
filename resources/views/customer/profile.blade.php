@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'My Profile - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.profile-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
    padding: 2rem;
}

.customer-header {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
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

.welcome-text {
    color: var(--primary-purple);
    font-weight: 500;
    font-size: 1.1rem;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.logout-btn {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    color: var(--primary-purple);
    text-decoration: none;
    font-weight: 500;
    padding: 0.6rem 1.2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    transition: var(--transition-smooth);
}

.logout-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.3);
    color: var(--primary-purple);
    border-color: rgba(255, 255, 255, 0.3);
}

.customer-main {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Profile Card with Glassmorphism */
.profile-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 2.5rem;
    transition: var(--transition-smooth);
    position: relative;
    overflow: hidden;
}

.profile-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.profile-card:hover {
    box-shadow: 0 20px 40px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.3);
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.profile-header h2 {
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #000, #333);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
}

.profile-avatar-section {
    text-align: center;
    margin-bottom: 2rem;
}

.profile-avatar-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    border: 3px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
}

.form-control, .form-select {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(0, 0, 0, 0.2);
    color: #000;
    border-radius: 1rem;
    padding: 0.75rem 1rem;
    transition: var(--transition-smooth);
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.95);
    border-color: #667eea;
    color: #000;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-control:disabled, .form-select:disabled {
    background: rgba(255, 255, 255, 0.7);
    opacity: 1;
    color: #000;
}

.form-label {
    color: #000;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.emergency-contact-section {
    margin-top: 1.25rem;
    margin-bottom: 0.25rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border: none;
    border-radius: 1rem;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: var(--transition-smooth);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 1rem;
    padding: 0.75rem 2rem;
    font-weight: 600;
    color: #000;
    transition: var(--transition-smooth);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    color: #000;
}

.deactivate-section {
    margin-top: 1.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid rgba(0, 0, 0, 0.12);
}

.btn-deactivate {
    background: rgba(220, 38, 38, 0.12);
    border: 1px solid rgba(220, 38, 38, 0.35);
    color: #991b1b;
    border-radius: 1rem;
    padding: 0.65rem 1.25rem;
    font-weight: 700;
    transition: var(--transition-smooth);
}

.btn-deactivate:hover {
    background: rgba(220, 38, 38, 0.2);
    color: #7f1d1d;
    transform: translateY(-1px);
}

.alert-success {
    background: rgba(34, 197, 94, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #000;
    border-radius: 1rem;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .customer-header {
        padding: 1rem;
    }
    
    .customer-main {
        padding: 1rem;
    }
    
    .profile-card {
        padding: 1.5rem;
    }
    
    .logo-section h1 {
        font-size: 1.5rem;
    }
    
    .welcome-text {
        font-size: 1rem;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="customer-container">
    <!-- Main Content -->
    <main class="customer-main">
        <div class="profile-card">
            <div class="profile-header">
                <h2>My Profile</h2>
                <div>
                    <button class="btn btn-secondary" id="editBtn" onclick="enableEditMode()">
                        <i class="fas fa-edit me-2"></i>Edit
                    </button>
                    <button class="btn btn-secondary" id="cancelBtn" onclick="disableEditMode()" style="display: none;">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                </div>
            </div>

            <form id="profileForm" method="POST" action="{{ route('customer.profile.update') }}">
                @csrf
                @method('PUT')
                
                <!-- Profile Avatar -->
                <div class="profile-avatar-section">
                    <div class="profile-avatar-circle" id="avatarDisplay">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}?t={{ time() }}" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ substr($user->first_name ?? 'P', 0, 1) }}{{ substr($user->last_name ?? 'O', 0, 1) }}
                        @endif
                    </div>
                    <div class="mt-3" id="uploadSection" style="display: none;">
                        <label for="profile_picture" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-camera me-2"></i>Change Profile Picture
                        </label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;" onchange="previewImage(event)">
                        <small class="d-block text-muted mt-2">Allowed formats: JPG, PNG, GIF (Max 5MB)</small>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name ?? '' }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name ?? '' }}" disabled>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" disabled>{{ $user->address ?? '' }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="contact_number">Contact Number</label>
                        <input type="tel" class="form-control" id="contact_number" name="contact_number" value="{{ $user->contact_number ?? '' }}" disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email ?? '' }}" disabled>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ $user->username ?? '' }}" disabled placeholder="Enter your username">
                        <small class="text-muted d-block mt-1">Your unique username for login</small>
                    </div>

                    <div class="col-12 emergency-contact-section">
                        <h5 class="mb-1" style="color:#000; font-weight:700;">Emergency Contact</h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="emergency_contact_name">Contact Name</label>
                        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $petOwner->emergency_contact_name ?? '') }}" disabled placeholder="Enter emergency contact name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="emergency_contact_phone">Contact Number</label>
                        <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $petOwner->emergency_contact_phone ?? '') }}" disabled placeholder="Enter emergency contact number">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="emergency_contact_relationship">Relationship</label>
                        <select class="form-select" id="emergency_contact_relationship" name="emergency_contact_relationship" disabled>
                            <option value="">Select relationship</option>
                            <option value="spouse" {{ old('emergency_contact_relationship', $petOwner->emergency_contact_relationship ?? '') === 'spouse' ? 'selected' : '' }}>Spouse</option>
                            <option value="parent" {{ old('emergency_contact_relationship', $petOwner->emergency_contact_relationship ?? '') === 'parent' ? 'selected' : '' }}>Parent</option>
                            <option value="sibling" {{ old('emergency_contact_relationship', $petOwner->emergency_contact_relationship ?? '') === 'sibling' ? 'selected' : '' }}>Sibling</option>
                            <option value="relative" {{ old('emergency_contact_relationship', $petOwner->emergency_contact_relationship ?? '') === 'relative' ? 'selected' : '' }}>Relative</option>
                            <option value="friend" {{ old('emergency_contact_relationship', $petOwner->emergency_contact_relationship ?? '') === 'friend' ? 'selected' : '' }}>Friend</option>
                            <option value="neighbor" {{ old('emergency_contact_relationship', $petOwner->emergency_contact_relationship ?? '') === 'neighbor' ? 'selected' : '' }}>Neighbor</option>
                            <option value="other" {{ old('emergency_contact_relationship', $petOwner->emergency_contact_relationship ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4" id="saveSection" style="display: none;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>

            <div class="text-center deactivate-section">
                <form action="{{ route('customer.profile.deactivate') }}" method="POST" onsubmit="return confirm('Are you sure you want to deactivate your account? You will be logged out immediately.');">
                    @csrf
                    <button type="submit" class="btn btn-deactivate">
                        <i class="fas fa-user-slash me-2"></i>Deactivate Account
                    </button>
                </form>
                <small class="text-muted d-block mt-2">This will disable your login until an administrator reactivates your account.</small>
            </div>
        </div>
    </main>
</div>

<script>
function enableEditMode() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, textarea, select');
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveSection = document.getElementById('saveSection');
    const uploadSection = document.getElementById('uploadSection');
    
    inputs.forEach(input => {
        input.disabled = false;
    });
    
    editBtn.style.display = 'none';
    cancelBtn.style.display = 'inline-block';
    saveSection.style.display = 'block';
    if (uploadSection) {
        uploadSection.style.display = 'block';
    }
}

function disableEditMode() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, textarea, select');
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveSection = document.getElementById('saveSection');
    const uploadSection = document.getElementById('uploadSection');
    const fileInput = document.getElementById('profile_picture');
    const avatarDisplay = document.getElementById('avatarDisplay');
    
    inputs.forEach(input => {
        input.disabled = true;
    });
    
    // Reset file input and avatar display
    fileInput.value = '';
    const initials = '{{ substr($user->first_name ?? 'P', 0, 1) }}{{ substr($user->last_name ?? 'O', 0, 1) }}';
    @if($user->profile_picture)
        const originalImage = '{{ asset('storage/' . $user->profile_picture) }}?t={{ time() }}';
        avatarDisplay.innerHTML = `<img src="${originalImage}" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
    @else
        avatarDisplay.innerHTML = initials;
    @endif
    
    editBtn.style.display = 'inline-block';
    cancelBtn.style.display = 'none';
    saveSection.style.display = 'none';
    if (uploadSection) {
        uploadSection.style.display = 'none';
    }
}

function previewImage(event) {
    const file = event.target.files[0];
    const avatarDisplay = document.getElementById('avatarDisplay');
    
    if (file) {
        // Check file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            event.target.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.match('image.*')) {
            alert('Please select an image file');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            avatarDisplay.innerHTML = `<img src="${e.target.result}" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
        }
        reader.readAsDataURL(file);
    }
}

// Form submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show loading state
    const saveBtn = document.querySelector('#saveSection button[type="submit"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    saveBtn.disabled = true;
    
    // Create FormData to handle file upload
    const formData = new FormData(this);
    
    // Submit using fetch to handle file upload
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        const isJson = contentType.includes('application/json');
        const payload = isJson ? await response.json() : { message: await response.text() };

        if (!response.ok) {
            const errors = payload.errors || {};
            const firstError = Object.values(errors).flat()[0];
            throw new Error(firstError || payload.message || 'Error updating profile. Please try again.');
        }

        return payload;
    })
    .then(data => {
        const existingAlerts = document.querySelectorAll('.profile-card .alert');
        existingAlerts.forEach(el => el.remove());

        if (data.success) {
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${data.message || 'Profile updated successfully!'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const card = document.querySelector('.profile-card');
            card.insertBefore(alert, card.firstChild);
            
            // Update profile picture immediately if it was updated
            if (data.profile_picture_updated && data.profile_picture_url) {
                // Just reload the page to show the updated picture
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                // Disable editing mode for non-picture updates
                disableEditMode();
            }
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        } else {
            // Show error message
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>
                ${data.message || 'Error updating profile. Please try again.'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const card = document.querySelector('.profile-card');
            card.insertBefore(alert, card.firstChild);
        }
    })
    .catch(error => {
        console.error('Error:', error);

        const existingAlerts = document.querySelectorAll('.profile-card .alert');
        existingAlerts.forEach(el => el.remove());

        // Show error message
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>
            ${error.message || 'Error updating profile. Please try again.'}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const card = document.querySelector('.profile-card');
        card.insertBefore(alert, card.firstChild);
    })
    .finally(() => {
        // Reset button state
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
});
</script>
@endsection
