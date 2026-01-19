@extends('admin.dashboard')

@section('page-title', 'Add New Pet Owner')
@section('page-description', 'Register a new pet owner in the system')

@section('content')
<style>
    .form-container {
        max-width: 600px;
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

    .emergency-contacts {
        background: var(--soft-gray);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .emergency-contacts h3 {
        margin-top: 0;
        color: var(--dark-text);
        font-size: 16px;
    }

    .contact-item {
        background: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid var(--primary-orange);
    }

    .btn-add-contact {
        background: var(--soft-gray);
        color: var(--primary-orange);
        border: 2px dashed var(--primary-orange);
        margin-bottom: 20px;
    }

    .btn-add-contact:hover {
        background: var(--primary-orange);
        color: white;
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

<a href="{{ route('admin.pet-owners.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Pet Owners
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
    <h2 style="margin-top: 0; color: var(--dark-text); font-family: 'Fredoka', sans-serif;">Register New Pet Owner</h2>
    
    <form action="{{ route('admin.pet-owners.store') }}" method="POST">
        @csrf

        <div class="form-group {{ $errors->has('user_id') ? 'error' : '' }}">
            <label for="user_id">Select Pet Owner User <span style="color: var(--accent-pink);">*</span></label>
            <select name="user_id" id="user_id" required>
                <option value="">-- Choose a user --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @if($errors->has('user_id'))
                <div class="error-message">{{ $errors->first('user_id') }}</div>
            @endif
        </div>

        <div class="form-group {{ $errors->has('notes') ? 'error' : '' }}">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" placeholder="Add any additional notes about this pet owner...">{{ old('notes') }}</textarea>
            @if($errors->has('notes'))
                <div class="error-message">{{ $errors->first('notes') }}</div>
            @endif
        </div>

        <div class="emergency-contacts">
            <h3><i class="fas fa-phone-alt"></i> Emergency Contacts (Optional)</h3>
            <div id="emergencyContactsContainer">
                <!-- Will be added dynamically -->
            </div>
            <button type="button" class="btn btn-add-contact" onclick="addEmergencyContact()">
                <i class="fas fa-plus"></i> Add Emergency Contact
            </button>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.pet-owners.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Owner
            </button>
        </div>
    </form>
</div>

<script>
    let contactCount = 0;

    function addEmergencyContact() {
        const container = document.getElementById('emergencyContactsContainer');
        const contactId = contactCount++;
        
        const html = `
            <div class="contact-item" id="contact-${contactId}">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="emergency_contacts[${contactId}][contact_name]" 
                           placeholder="Contact Name" required>
                    <input type="text" name="emergency_contacts[${contactId}][contact_number]" 
                           placeholder="Phone Number" required>
                    <button type="button" onclick="removeEmergencyContact(${contactId})" 
                            class="btn btn-secondary" style="padding: 8px 12px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
    }

    function removeEmergencyContact(contactId) {
        const element = document.getElementById(`contact-${contactId}`);
        if (element) {
            element.remove();
        }
    }
</script>
@endsection
