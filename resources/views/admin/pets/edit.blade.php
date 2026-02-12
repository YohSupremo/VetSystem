@extends('admin.dashboard')

@section('page-title', 'Edit ' . $pet->name)
@section('page-description', 'Update pet information')

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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
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

    .photo-preview {
        width: 120px;
        height: 120px;
        background: var(--white);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 15px;
        border: 2px solid var(--soft-gray);
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-preview i {
        font-size: 40px;
        color: var(--light-text);
    }

    .photo-upload {
        border: 2px dashed var(--primary-orange);
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: rgba(255, 140, 66, 0.05);
    }

    .photo-upload:hover {
        background: rgba(255, 140, 66, 0.1);
    }

    .photo-upload i {
        font-size: 32px;
        color: var(--primary-orange);
        margin-bottom: 10px;
        display: block;
    }

    .photo-upload p {
        margin: 0;
        font-size: 13px;
        color: var(--light-text);
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

    .pet-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--soft-gray);
    }

    .pet-header-photo {
        width: 80px;
        height: 80px;
        background: var(--soft-gray);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .pet-header-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pet-header-photo i {
        font-size: 32px;
        color: var(--light-text);
    }

    .pet-header-info h2 {
        margin: 0 0 5px 0;
        color: var(--dark-text);
        font-family: 'Fredoka', sans-serif;
    }

    .pet-header-info p {
        margin: 0;
        font-size: 13px;
        color: var(--light-text);
    }
</style>

<a href="{{ route('admin.pets.show', $pet->id) }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Pet Profile
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
    <!-- Pet Header -->
    <div class="pet-header">
        <div class="pet-header-photo">
            <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <i class="fas fa-paw" style="display:none; font-size: 4rem; color: #ccc;"></i>
        </div>
        <div class="pet-header-info">
            <h2>{{ $pet->name }}</h2>
            <p>{{ ucfirst($pet->species) }} • {{ $pet->breed }}</p>
            <p>Owner: {{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}</p>
        </div>
    </div>

    <form action="{{ route('admin.pets.update', $pet->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Owner Selection (read-only info) -->
        <div class="form-group">
            <label>Pet Owner</label>
            <input type="text" value="{{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}" 
                   disabled style="background: var(--soft-gray); cursor: not-allowed;">
            <small style="color: var(--light-text);">Owner cannot be changed. Contact administrator if needed.</small>
            <!-- Hidden field to submit owner_id since disabled fields don't submit -->
            <input type="hidden" name="owner_id" value="{{ $pet->owner_id }}">
        </div>

        <!-- Basic Information -->
        <div class="form-section">
            <h3><i class="fas fa-paw"></i> Basic Information</h3>

            <div class="form-group {{ $errors->has('name') ? 'error' : '' }}">
                <label for="name">Pet Name <span style="color: var(--accent-pink);">*</span></label>
                <input type="text" name="name" id="name" placeholder="e.g., Max" 
                       value="{{ old('name', $pet->name) }}" required>
                @if($errors->has('name'))
                    <div class="error-message">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div class="form-row">
                <div class="form-group {{ $errors->has('species') ? 'error' : '' }}">
                    <label for="species">Species <span style="color: var(--accent-pink);">*</span></label>
                    <select name="species" id="species" required>
                        <option value="dog" {{ old('species', $pet->species) == 'dog' ? 'selected' : '' }}>Dog</option>
                        <option value="cat" {{ old('species', $pet->species) == 'cat' ? 'selected' : '' }}>Cat</option>
                        <option value="rabbit" {{ old('species', $pet->species) == 'rabbit' ? 'selected' : '' }}>Rabbit</option>
                        <option value="bird" {{ old('species', $pet->species) == 'bird' ? 'selected' : '' }}>Bird</option>
                        <option value="hamster" {{ old('species', $pet->species) == 'hamster' ? 'selected' : '' }}>Hamster</option>
                        <option value="guinea_pig" {{ old('species', $pet->species) == 'guinea_pig' ? 'selected' : '' }}>Guinea Pig</option>
                        <option value="other" {{ old('species', $pet->species) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @if($errors->has('species'))
                        <div class="error-message">{{ $errors->first('species') }}</div>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('breed') ? 'error' : '' }}">
                    <label for="breed">Breed <span style="color: var(--accent-pink);">*</span></label>
                    <input type="text" name="breed" id="breed" placeholder="e.g., Golden Retriever" 
                           value="{{ old('breed', $pet->breed) }}" required>
                    @if($errors->has('breed'))
                        <div class="error-message">{{ $errors->first('breed') }}</div>
                    @endif
                </div>
            </div>

            <div class="form-row">
                <div class="form-group {{ $errors->has('gender') ? 'error' : '' }}">
                    <label for="gender">Gender <span style="color: var(--accent-pink);">*</span></label>
                    <select name="gender" id="gender" required>
                        <option value="unknown" {{ old('gender', $pet->gender) == 'unknown' ? 'selected' : '' }}>Unknown</option>
                        <option value="male" {{ old('gender', $pet->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $pet->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @if($errors->has('gender'))
                        <div class="error-message">{{ $errors->first('gender') }}</div>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('birth_date') ? 'error' : '' }}">
                    <label for="birth_date">Birth Date</label>
                    <input type="date" name="birth_date" id="birth_date" 
                           value="{{ old('birth_date', $pet->birth_date?->format('Y-m-d')) }}">
                    @if($errors->has('birth_date'))
                        <div class="error-message">{{ $errors->first('birth_date') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Physical Description -->
        <div class="form-section">
            <h3><i class="fas fa-palette"></i> Physical Description</h3>

            <div class="form-row">
                <div class="form-group {{ $errors->has('color') ? 'error' : '' }}">
                    <label for="color">Color/Markings</label>
                    <input type="text" name="color" id="color" placeholder="e.g., Brown and white" 
                           value="{{ old('color', $pet->color) }}">
                    @if($errors->has('color'))
                        <div class="error-message">{{ $errors->first('color') }}</div>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('weight') ? 'error' : '' }}">
                    <label for="weight">Weight (kg)</label>
                    <input type="number" name="weight" id="weight" placeholder="e.g., 25.5" 
                           step="0.1" min="0" value="{{ old('weight', $pet->weight) }}">
                    @if($errors->has('weight'))
                        <div class="error-message">{{ $errors->first('weight') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="form-section">
            <h3><i class="fas fa-qrcode"></i> Medical Information</h3>

            <div class="form-group {{ $errors->has('microchip_number') ? 'error' : '' }}">
                <label for="microchip_number">Microchip Number</label>
                <input type="text" name="microchip_number" id="microchip_number" 
                       placeholder="Enter microchip ID if available" 
                       value="{{ old('microchip_number', $pet->microchip_number) }}">
                <small style="color: var(--light-text);">Must be unique if provided</small>
                @if($errors->has('microchip_number'))
                    <div class="error-message">{{ $errors->first('microchip_number') }}</div>
                @endif
            </div>
        </div>

        <!-- Photo Upload -->
        <div class="form-section">
            <h3><i class="fas fa-camera"></i> Pet Photo</h3>

            <div>
                <p style="font-weight: 600; margin-bottom: 10px;">Current Photo:</p>
                <div class="photo-preview">
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <i class="fas fa-paw" style="display:none; font-size: 4rem; color: #ccc;"></i>
                </div>
            </div>

            <div class="photo-upload">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Click to upload or drag and drop</p>
                <p style="font-size: 11px; margin-top: 5px;">PNG, JPG, GIF up to 2MB</p>
                <input type="file" name="photo" id="photo" accept="image/*" style="position: absolute; left: -9999px;">
                <div style="margin-top:8px;">
                    <button type="button" id="adminChooseFileBtn" class="btn btn-secondary">Choose file</button>
                </div>
            </div>
            @if($errors->has('photo'))
                <div class="error-message">{{ $errors->first('photo') }}</div>
            @endif
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.pets.show', $pet->id) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<script>
    // Photo upload preview
    const photoUpload = document.querySelector('.photo-upload');
    const photoInput = document.getElementById('photo');
    const adminChooseBtn = document.getElementById('adminChooseFileBtn');

    adminChooseBtn.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            // Update the existing paragraphs instead of replacing innerHTML so the input stays in the DOM
            const paragraphs = photoUpload.querySelectorAll('p');
            if (paragraphs.length > 0) {
                paragraphs[0].textContent = file.name;
                if (paragraphs.length > 1) {
                    paragraphs[1].textContent = 'Ready to upload';
                }
            }
            // show a visual OK icon if present
            const icon = photoUpload.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-cloud-upload-alt');
                icon.classList.add('fa-check');
                icon.style.color = 'var(--accent-green)';
            }
        }
    });
</script>
@endsection
