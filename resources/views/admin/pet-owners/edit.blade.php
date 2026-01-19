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

    .emergency-contacts {
        margin-bottom: 20px;
    }

    .contact-item {
        background: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid var(--primary-orange);
    }

    .btn-add-contact {
        background: var(--primary-orange);
        color: white;
        margin-top: 10px;
    }

    .btn-add-contact:hover {
        background: var(--accent-pink);
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

        <!-- User Information -->
        <div class="form-section">
            <h3><i class="fas fa-user"></i> Owner Information</h3>

            <div class="form-group {{ $errors->has('first_name') ? 'error' : '' }}">
                <label for="first_name">First Name <span style="color: var(--accent-pink);">*</span></label>
                <input type="text" name="first_name" id="first_name" 
                       value="{{ old('first_name', $petOwner->user->first_name) }}" required>
                @if($errors->has('first_name'))
                    <div class="error-message">{{ $errors->first('first_name') }}</div>
                @endif
            </div>

            <div class="form-group {{ $errors->has('last_name') ? 'error' : '' }}">
                <label for="last_name">Last Name <span style="color: var(--accent-pink);">*</span></label>
                <input type="text" name="last_name" id="last_name" 
                       value="{{ old('last_name', $petOwner->user->last_name) }}" required>
                @if($errors->has('last_name'))
                    <div class="error-message">{{ $errors->first('last_name') }}</div>
                @endif
            </div>

            <div class="form-group {{ $errors->has('email') ? 'error' : '' }}">
                <label for="email">Email <span style="color: var(--accent-pink);">*</span></label>
                <input type="email" name="email" id="email" 
                       value="{{ old('email', $petOwner->user->email) }}" required>
                @if($errors->has('email'))
                    <div class="error-message">{{ $errors->first('email') }}</div>
                @endif
            </div>

            <div class="form-group {{ $errors->has('contact_number') ? 'error' : '' }}">
                <label for="contact_number">Contact Number <span style="color: var(--accent-pink);">*</span></label>
                <input type="text" name="contact_number" id="contact_number" 
                       value="{{ old('contact_number', $petOwner->user->contact_number) }}" required>
                @if($errors->has('contact_number'))
                    <div class="error-message">{{ $errors->first('contact_number') }}</div>
                @endif
            </div>

            <div class="form-group {{ $errors->has('address') ? 'error' : '' }}">
                <label for="address">Address <span style="color: var(--accent-pink);">*</span></label>
                <textarea name="address" id="address" required>{{ old('address', $petOwner->user->address) }}</textarea>
                @if($errors->has('address'))
                    <div class="error-message">{{ $errors->first('address') }}</div>
                @endif
            </div>
        </div>

        <!-- Notes -->
        <div class="form-group {{ $errors->has('notes') ? 'error' : '' }}">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" placeholder="Add any additional notes about this pet owner...">{{ old('notes', $petOwner->notes) }}</textarea>
            @if($errors->has('notes'))
                <div class="error-message">{{ $errors->first('notes') }}</div>
            @endif
        </div>

        <!-- Emergency Contacts -->
        <div class="form-section">
            <h3><i class="fas fa-phone-alt"></i> Emergency Contacts</h3>
            
            <div class="emergency-contacts" id="emergencyContactsContainer">
                @foreach($petOwner->emergencyContacts as $index => $contact)
                    <div class="contact-item" id="contact-{{ $index }}">
                        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" name="emergency_contacts[{{ $index }}][contact_name]" 
                                   value="{{ $contact->contact_name }}" placeholder="Contact Name" required>
                            <input type="text" name="emergency_contacts[{{ $index }}][contact_number]" 
                                   value="{{ $contact->contact_number }}" placeholder="Phone Number" required>
                            <button type="button" onclick="removeEmergencyContact({{ $index }})" 
                                    class="btn btn-secondary" style="padding: 8px 12px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button type="button" class="btn btn-add-contact" onclick="addEmergencyContact()">
                <i class="fas fa-plus"></i> Add Emergency Contact
            </button>
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

<script>
    let contactCount = {{ $petOwner->emergencyContacts->count() }};

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
