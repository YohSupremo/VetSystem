@extends('admin.dashboard')

@section('page-title', 'Edit Pet Owner')
@section('page-description', 'Update pet owner information')

@section('content')
<style>
    .form-container {
        max-width: 700px;
        background: var(--white);
        border-radius: 15px;
        padding: 30px;
        box-shadow: var(--shadow-soft);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: var(--dark-text);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid var(--soft-gray);
        border-radius: 8px;
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-orange);
        box-shadow: 0 0 10px rgba(255, 140, 66, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .error-message {
        color: var(--accent-pink);
        font-size: 12px;
        margin-top: 5px;
    }

    .form-group.error input,
    .form-group.error select,
    .form-group.error textarea {
        border-color: var(--accent-pink);
    }

    .form-section {
        background: var(--soft-gray);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .form-section h3 {
        margin-top: 0;
        color: var(--dark-text);
        font-size: 16px;
        font-family: 'Fredoka', sans-serif;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .form-actions .btn {
        flex: 1;
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
</style>

<a href="{{ route('admin.pet-owners.show', $petOwner) }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Owner Profile
</a>

@if($errors->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="form-container">
    <h2 style="margin-top: 0; color: var(--dark-text); font-family: 'Fredoka', sans-serif;">Edit Pet Owner</h2>
    
    <form action="{{ route('admin.pet-owners.update', $petOwner) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- User Selection -->
        <div class="form-section">
            <h3><i class="fas fa-user"></i> User Assignment</h3>
            
            <div class="form-group {{ $errors->has('user_id') ? 'error' : '' }}">
                <label for="user_id">Select User <span style="color: var(--accent-pink);">*</span></label>
                <select name="user_id" id="user_id" required>
                    <option value="">-- Select a user --</option>
                    @foreach($allUsers ?? [] as $user)
                        <option value="{{ $user->id }}" {{ $petOwner->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @if($errors->has('user_id'))
                    <div class="error-message">{{ $errors->first('user_id') }}</div>
                @endif
            </div>
        </div>

        <!-- Pet Owner Information -->
        <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Pet Owner Information</h3>
            
            <div class="form-group {{ $errors->has('preferred_contact_method') ? 'error' : '' }}">
                <label for="preferred_contact_method">Preferred Contact Method</label>
                <select name="preferred_contact_method" id="preferred_contact_method">
                    <option value="">-- Select preference --</option>
                    <option value="email" {{ $petOwner->preferred_contact_method === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="sms" {{ $petOwner->preferred_contact_method === 'sms' ? 'selected' : '' }}>SMS</option>
                </select>
                @if($errors->has('preferred_contact_method'))
                    <div class="error-message">{{ $errors->first('preferred_contact_method') }}</div>
                @endif
            </div>

            <div class="form-group {{ $errors->has('notes') ? 'error' : '' }}">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" placeholder="Add any additional notes about this pet owner...">{{ old('notes', $petOwner->notes) }}</textarea>
                @if($errors->has('notes'))
                    <div class="error-message">{{ $errors->first('notes') }}</div>
                @endif
            </div>
        </div>

        <!-- Emergency Contacts -->
        <div class="form-section">
            <h3><i class="fas fa-phone-alt"></i> Emergency Contacts</h3>
            
            <div class="form-group {{ $errors->has('emergency_contact_name') ? 'error' : '' }}">
                <label for="emergency_contact_name">Emergency Contact Name</label>
                <input type="text" id="emergency_contact_name" name="emergency_contact_name" 
                       value="{{ old('emergency_contact_name', $petOwner->emergency_contact_name) }}" 
                       placeholder="Emergency contact name">
                @if($errors->has('emergency_contact_name'))
                    <div class="error-message">{{ $errors->first('emergency_contact_name') }}</div>
                @endif
            </div>

            <div class="form-row">
                <div class="form-group {{ $errors->has('emergency_contact_phone') ? 'error' : '' }}">
                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" 
                           value="{{ old('emergency_contact_phone', $petOwner->emergency_contact_phone) }}" 
                           placeholder="Emergency contact phone number">
                    @if($errors->has('emergency_contact_phone'))
                        <div class="error-message">{{ $errors->first('emergency_contact_phone') }}</div>
                    @endif
                </div>
                
                <div class="form-group {{ $errors->has('emergency_contact_relationship') ? 'error' : '' }}">
                    <label for="emergency_contact_relationship">Relationship</label>
                    <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" 
                           value="{{ old('emergency_contact_relationship', $petOwner->emergency_contact_relationship) }}" 
                           placeholder="e.g., Spouse, Parent, Friend">
                    @if($errors->has('emergency_contact_relationship'))
                        <div class="error-message">{{ $errors->first('emergency_contact_relationship') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.pet-owners.show', $petOwner) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>

@endsection
