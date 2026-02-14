@extends('admin.dashboard')

@section('content')
<div class="staff-form-wrapper">
    <!-- Enhanced Header -->
    <div class="content-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="header-text">
                <h1>Add Staff Member</h1>
                <p>Create a new staff account for your veterinary clinic</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ url('admin/staff') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Back to list
            </a>
        </div>
    </div>

    <!-- Enhanced Form Card -->
    <div class="form-card">
        <form method="POST" action="{{ url('admin/staff/store') }}">
            @csrf
            
            <!-- Personal Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon personal">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="section-title">
                        <h3>Personal Information</h3>
                        <p>Basic details about the staff member</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">First Name</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <input 
                                type="text" 
                                name="first_name" 
                                value="{{ old('first_name') }}" 
                                class="form-control @error('first_name') error @enderror"
                                placeholder="Enter first name"
                                >
                        </div>
                        @error('first_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Last Name</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <input 
                                type="text" 
                                name="last_name" 
                                value="{{ old('last_name') }}" 
                                class="form-control @error('last_name') error @enderror"
                                placeholder="Enter last name"
                                >
                        </div>
                        @error('last_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label required">Role</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-briefcase"></i>
                            </span>
                            <select 
                                name="role" 
                                class="form-control @error('role') error @enderror"
                                >
                                <option value="">Select a role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="veterinarian" {{ old('role') == 'veterinarian' ? 'selected' : '' }}>Veterinarian</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="reception" {{ old('role') == 'reception' ? 'selected' : '' }}>Reception</option>
                                <option value="pharmacy" {{ old('role') == 'pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                                <option value="groomer" {{ old('role') == 'groomer' ? 'selected' : '' }}>Groomer</option>
                                <option value="boarding" {{ old('role') == 'boarding' ? 'selected' : '' }}>Boarding</option>
                                
                            </select>
                        </div>
                        @error('role')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon contact">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="section-title">
                        <h3>Contact Information</h3>
                        <p>How to reach this staff member</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">Email Address</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                class="form-control @error('email') error @enderror"
                                placeholder="staff@example.com"
                                >
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Contact Number</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input 
                                type="tel" 
                                name="contact_number" 
                                value="{{ old('contact_number') }}" 
                                class="form-control @error('contact_number') error @enderror"
                                placeholder="+1 (555) 000-0000"
                                >
                        </div>
                        @error('contact_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label required">Address</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <textarea 
                                name="address" 
                                class="form-control textarea @error('address') error @enderror"
                                placeholder="Enter full address (Street, City, State, ZIP)"
                                rows="3"
                                >{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Credentials Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon credentials">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="section-title">
                        <h3>Account Credentials</h3>
                        <p>Login information for system access</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label required">Username</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-user-circle"></i>
                            </span>
                            <input 
                                type="text" 
                                name="username" 
                                value="{{ old('username') }}" 
                                class="form-control @error('username') error @enderror"
                                placeholder="Choose a unique username"
                                
                                autocomplete="off">
                        </div>
                        <p class="help-text">
                            <i class="fas fa-info-circle"></i>
                            Username must be unique and will be used for login
                        </p>
                        @error('username')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input 
                                type="password" 
                                name="password" 
                                class="form-control @error('password') error @enderror"
                                placeholder="Enter a strong password"
                                
                                autocomplete="new-password">
                        </div>
                        <div class="password-strength">
                            <div class="strength-bars">
                                <span class="strength-bar"></span>
                                <span class="strength-bar"></span>
                                <span class="strength-bar"></span>
                                <span class="strength-bar"></span>
                            </div>
                        </div>
                        <p class="help-text">
                            <i class="fas fa-shield-alt"></i>
                            At least 8 characters with uppercase, lowercase, and numbers
                        </p>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Confirm Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                class="form-control"
                                placeholder="Re-enter your password"
                                
                                autocomplete="new-password">
                        </div>
                        <p class="help-text match-indicator">
                            <i class="fas fa-check-circle"></i>
                            Passwords must match
                        </p>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="status-section">
                    <div class="status-card">
                        <div class="status-icon">
                            <i class="fas fa-toggle-on"></i>
                        </div>
                        <div class="status-content">
                            <h4>Account Status</h4>
                            <p>Set whether this account should be active immediately</p>
                        </div>
                        <div class="toggle-wrapper">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                id="is_active" 
                                class="toggle-input"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="toggle-label">
                                <span class="toggle-switch"></span>
                                <span class="toggle-text">
                                    <span class="status-active">Active</span>
                                    <span class="status-inactive">Inactive</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ url('admin/staff') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
                <button type="reset" class="btn btn-outline">
                    <i class="fas fa-redo"></i>
                    Reset Form
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i>
                    Create Staff Member
                </button>
            </div>
        </form>
    </div>
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
    --radius: 12px;
    --radius-sm: 8px;
}

/* Wrapper */
.staff-form-wrapper {
    max-width: 1100px;
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
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
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

/* Form Card */
.form-card {
    background: var(--surface);
    border-radius: var(--radius);
    padding: 2.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

/* Form Sections */
.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2.5rem;
    border-bottom: 2px solid var(--border-color);
}

.form-section:last-of-type {
    border-bottom: none;
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.75rem;
}

.section-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
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

.section-title h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
}

.section-title p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

/* Form Groups */
.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.form-label.required::after {
    content: "*";
    color: var(--danger-color);
    margin-left: 0.25rem;
}

/* Input Wrapper */
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: var(--text-secondary);
    font-size: 0.95rem;
    pointer-events: none;
    z-index: 1;
}

.form-control {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 2.75rem;
    font-size: 0.95rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-primary);
    transition: all 0.2s ease;
    outline: none;
    font-family: inherit;
}

.form-control.textarea {
    resize: vertical;
    min-height: 90px;
    padding-top: 0.875rem;
}

.form-control::placeholder {
    color: #9CA3AF;
}

.form-control:hover {
    border-color: #D1D5DB;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 123, 109, 0.1);
}

.form-control.error {
    border-color: var(--danger-color);
}

.form-control.error:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Select Dropdown */
select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236B7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 3rem;
}

/* Help Text */
.help-text {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    margin-top: 0.5rem;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.help-text i {
    font-size: 0.75rem;
}

/* Password Strength Indicator */
.password-strength {
    margin-top: 0.5rem;
}

.strength-bars {
    display: flex;
    gap: 0.375rem;
    height: 4px;
}

.strength-bar {
    flex: 1;
    background: #E5E7EB;
    border-radius: 2px;
    transition: background 0.3s ease;
}

/* Status Card */
.status-section {
    margin-top: 2rem;
}

.status-card {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #F9FAFB, #F3F4F6);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
}

.status-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366F1, #4F46E5);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.status-content {
    flex: 1;
}

.status-content h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
}

.status-content p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Toggle Switch */
.toggle-wrapper {
    flex-shrink: 0;
}

.toggle-input {
    display: none;
}

.toggle-label {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    user-select: none;
}

.toggle-switch {
    position: relative;
    width: 52px;
    height: 28px;
    background: #D1D5DB;
    border-radius: 14px;
    transition: background 0.3s ease;
    flex-shrink: 0;
}

.toggle-switch::before {
    content: "";
    position: absolute;
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    top: 3px;
    left: 3px;
    transition: transform 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.toggle-input:checked + .toggle-label .toggle-switch {
    background: var(--success-color);
}

.toggle-input:checked + .toggle-label .toggle-switch::before {
    transform: translateX(24px);
}

.toggle-text {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
}

.status-inactive {
    display: inline;
    color: var(--text-secondary);
}

.status-active {
    display: none;
    color: var(--success-color);
}

.toggle-input:checked + .toggle-label .status-inactive {
    display: none;
}

.toggle-input:checked + .toggle-label .status-active {
    display: inline;
}

/* Error Messages */
.error-message {
    font-size: 0.8125rem;
    color: var(--danger-color);
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-weight: 500;
}

.error-message::before {
    content: "⚠";
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
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

.btn-primary:active {
    transform: translateY(0);
}

.btn-secondary {
    background: var(--surface);
    color: var(--text-secondary);
    border-color: var(--border-color);
}

.btn-secondary:hover {
    background: #F9FAFB;
    border-color: #D1D5DB;
    color: var(--text-primary);
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

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 2px solid var(--border-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .staff-form-wrapper {
        padding: 1rem;
    }

    .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .header-content {
        width: 100%;
    }

    .header-icon {
        width: 56px;
        height: 56px;
        font-size: 1.5rem;
    }

    .header-text h1 {
        font-size: 1.5rem;
    }

    .form-card {
        padding: 1.5rem;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full-width {
        grid-column: 1;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .status-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .header-text h1 {
        font-size: 1.375rem;
    }

    .section-title h3 {
        font-size: 1.125rem;
    }

    .form-card {
        padding: 1.25rem;
    }
}

/* Print Styles */
@media print {
    .header-actions,
    .form-actions {
        display: none;
    }

    .form-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .toggle-wrapper,
    .password-strength {
        display: none;
    }
}
</style>
@endsection