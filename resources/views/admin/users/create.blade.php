@extends('admin.dashboard')

@section('page-title', 'Create User')
@section('page-description', 'Create a new system user')

@section('content')
<style>
    .create-user-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .create-user-wrapper .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .create-user-wrapper .top-bar h3 {
        margin: 0;
        font-family: 'Fredoka', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: #2D3748;
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .create-user-wrapper .btn-secondary {
        background: linear-gradient(135deg, #F5F5F7, #E8E8EA);
        color: #2D3748;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .create-user-wrapper .btn-secondary:hover {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    }

    .create-user-wrapper .alert-danger {
        background: linear-gradient(135deg, rgba(255, 107, 157, 0.15), rgba(255, 107, 157, 0.08));
        border: 2px solid #FF6B9D;
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 28px;
    }

    .create-user-wrapper .alert-danger ul {
        margin: 0;
        padding-left: 20px;
        color: #d73a49;
        font-weight: 500;
    }

    .create-user-wrapper .alert-danger li {
        margin-bottom: 8px;
        font-size: 14px;
    }

    .create-user-wrapper .alert-danger li:last-child {
        margin-bottom: 0;
    }

    .form-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(255, 140, 66, 0.08);
    }

    .form-section {
        margin-bottom: 36px;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-family: 'Fredoka', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: #2D3748;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(255, 140, 66, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        font-size: 20px;
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .form-grid-full {
        grid-column: 1 / -1;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        font-size: 14px;
        color: #2D3748;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group label i {
        color: #FF8C42;
        font-size: 14px;
    }

    .form-group label .required {
        color: #FF6B9D;
        font-size: 16px;
        margin-left: auto;
    }

    
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"],
    .form-group input[type="tel"],
    .form-group textarea,
    .form-group select {
        padding: 14px 18px;
        border: 2px solid #E2E8F0;
        border-radius: 12px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.3s ease;
        background: #FAFAFA;
        width: 100%;
    }

    
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #FF8C42;
        background: white;
        box-shadow: 0 0 0 4px rgba(255, 140, 66, 0.1);
    }

    .form-group input[readonly] {
        background: #F1F3F5;
        color: #718096;
        cursor: not-allowed;
    }

    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding: 20px;
        background: linear-gradient(135deg, rgba(255, 140, 66, 0.05), rgba(255, 107, 157, 0.03));
        border-radius: 12px;
        border: 2px solid rgba(255, 140, 66, 0.1);
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        padding: 12px 16px;
        background: white;
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .checkbox-item:hover {
        background: rgba(255, 140, 66, 0.05);
        border-color: rgba(255, 140, 66, 0.2);
    }

    .checkbox-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #FF8C42;
    }

    .checkbox-item label {
        font-weight: 500;
        font-size: 14px;
        color: #2D3748;
        cursor: pointer;
        margin: 0;
        flex: 1;
    }

    .checkbox-item .checkbox-description {
        font-size: 12px;
        color: #718096;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        gap: 16px;
        justify-content: flex-end;
        margin-top: 32px;
        padding-top: 32px;
        border-top: 2px solid rgba(255, 140, 66, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #FF8C42, #FF6B9D);
        color: white;
        padding: 14px 32px;
        border-radius: 12px;
        border: none;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'DM Sans', sans-serif;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 140, 66, 0.4);
    }

    .btn-primary i {
        font-size: 16px;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 20px;
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        color: #92400E;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        width: 100%;
        justify-content: center;
    }

    .role-badge i {
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-card {
            padding: 24px;
        }

        .form-actions {
            flex-direction: column;
        }

        .create-user-wrapper .top-bar {
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }
    }
</style>

<div class="create-user-wrapper">
    <div class="top-bar">
        <div>
            <h3>Create New User</h3>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Users
        </a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <input type="hidden" name="role" value="registered_user">
        
        <div class="form-card">
            <!-- Account Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    Account Information
                </h4>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-user"></i>
                            Username
                            <span class="required">*</span>
                        </label>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Enter username" />
                        @error('username')
                            <div style="color:#d73a49; margin-top:6px; font-size:13px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-envelope"></i>
                            Email Address
                            <span class="required">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="user@example.com" />
                        @error('email')
                            <div style="color:#d73a49; margin-top:6px; font-size:13px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-lock"></i>
                            Password
                            <span class="required">*</span>
                        </label>
                        <input type="password" name="password" placeholder="Enter password" />
                        @error('password')
                            <div style="color:#d73a49; margin-top:6px; font-size:13px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-lock"></i>
                            Confirm Password
                            <span class="required">*</span>
                        </label>
                        <input type="password" name="password_confirmation" placeholder="Re-enter password" />
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-user-tag"></i>
                            User Role
                        </label>
                        <div class="role-badge">
                            <i class="fas fa-user"></i>
                            Registered User
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-id-card"></i>
                    Personal Information
                </h4>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-user"></i>
                            First Name
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Enter first name" />
                        @error('first_name')
                            <div style="color:#d73a49; margin-top:6px; font-size:13px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-user"></i>
                            Last Name
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Enter last name" />
                        @error('last_name')
                            <div style="color:#d73a49; margin-top:6px; font-size:13px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-phone"></i>
                            Contact Number
                        </label>
                        <input type="tel" name="contact_number" value="{{ old('contact_number') }}" placeholder="+63 (912) 000-0000" />
                        @error('contact_number')
                            <div style="color:#d73a49; margin-top:6px; font-size:13px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group form-grid-full">
                        <label>
                            <i class="fas fa-map-marker-alt"></i>
                            Address
                        </label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Enter full address" />
                        @error('address')
                            <div style="color:#d73a49; margin-top:6px; font-size:13px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Status Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-toggle-on"></i>
                    Account Status
                </h4>
                
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active">
                            <div>Active Account</div>
                            <div class="checkbox-description">User can log in and access the system</div>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="email_verified" value="1" id="email_verified" {{ old('email_verified') ? 'checked' : '' }}>
                        <label for="email_verified">
                            <div>Email Verified</div>
                            <div class="checkbox-description">Mark email address as verified</div>
                        </label>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" name="phone_verified" value="1" id="phone_verified" {{ old('phone_verified') ? 'checked' : '' }}>
                        <label for="phone_verified">
                            <div>Phone Verified</div>
                            <div class="checkbox-description">Mark phone number as verified</div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Create User
                </button>
            </div>
        </div>
    </form>
</div>

@endsection