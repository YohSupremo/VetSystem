@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Register New Pet - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
}

/* Pet Create Form */
.pet-create-container {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.pet-create-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.form-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.form-body {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 2rem;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-input, .form-select, .form-textarea {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 0.75rem;
    color: #000;
    font-weight: 600;
    transition: var(--transition-smooth);
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: rgba(147, 51, 234, 0.4);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
}

.form-input::placeholder, .form-select::placeholder, .form-textarea::placeholder {
    color: #666;
}

/* Photo Upload Section */
.photo-upload-section {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    margin-bottom: 1.5rem;
    transition: var(--transition-smooth);
}

.photo-upload-section:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    background: rgba(255, 255, 255, 0.15);
}

.photo-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.2);
    margin: 0 auto 1rem;
    object-fit: cover;
    transition: var(--transition-smooth);
}

.photo-preview:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
}

.btn-upload {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    color: #000;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition-smooth);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-upload:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.3);
    color: #000;
    text-decoration: none;
}

/* Form Grid */
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

/* Submit Button */
.btn-submit {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.1rem;
    border: none;
    cursor: pointer;
    transition: var(--transition-smooth);
    text-align: center;
    display: inline-block;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
    color: white;
}

/* Error States */
.invalid-feedback {
    color: rgba(239, 68, 68, 0.9);
    font-size: 0.875rem;
    margin-top: 0.5rem;
    font-weight: 600;
}

.is-invalid {
    border-color: rgba(239, 68, 68, 0.5) !important;
}

.is-invalid:focus {
    border-color: rgba(239, 68, 68, 0.5) !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
}

/* Back Button */
.btn-back {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 0.75rem 1.5rem;
    color: #000;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition-smooth);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-back:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(255, 255, 255, 0.3);
    color: #000;
    text-decoration: none;
}

@media (max-width: 768px) {
    .pet-create-container {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        padding: 1rem;
    }
    
    .form-header {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .form-body {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        padding: 1.5rem;
    }
    
    .form-input, .form-select, .form-textarea {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .btn-upload {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .btn-submit {
        width: 100%;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="customer-container">
    <main class="customer-main">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <a href="{{ route('customer.pets.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="page-title">Register New Pet</h1>
        </div>

        <div class="pet-create-container">
            <div class="form-header">
                <a href="{{ route('customer.pets.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="page-title mb-0">Register New Pet</h1>
            </div>
            
            <div class="form-body">
                <form action="{{ route('customer.pets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Photo Upload -->
                    <div class="photo-upload-section">
                        <div class="photo-preview-container">
                            <img id="photo-preview" src="{{ asset('images/default-pet.svg') }}" 
                                 class="photo-preview"
                                 onerror="this.src='https://placehold.co/120x120?text=Pet+Photo'">
                        </div>
                        <label for="photo" class="btn-upload">
                            <i class="fas fa-camera me-1"></i> Upload Photo
                        </label>
                        <input type="file" name="photo" id="photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>

                    <!-- Basic Info -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">Pet Name *</label>
                            <input type="text" class="form-input" name="name" id="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="species" class="form-label">Species *</label>
                            <select name="species" id="species" class="form-select" required>
                                <option value="">Select species...</option>
                                <option value="Dog" {{ old('species') == 'Dog' ? 'selected' : '' }}>Dog</option>
                                <option value="Cat" {{ old('species') == 'Cat' ? 'selected' : '' }}>Cat</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="breed" class="form-label">Breed</label>
                            <input type="text" class="form-input" name="breed" id="breed" value="{{ old('breed') }}" placeholder="e.g. Labrador">
                        </div>

                        <div class="form-group">
                            <label for="birth_date" class="form-label">Date of Birth</label>
                            <input type="date" class="form-input" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" max="{{ date('Y-m-d') }}">
                        </div>

                        <div class="form-group">
                            <label for="calculated_age" class="form-label">Age (Auto-calculated)</label>
                            <input type="text" class="form-input" id="calculated_age" value="" readonly placeholder="Set a birth date">
                        </div>

                        <div class="form-group">
                            <label for="gender" class="form-label">Gender *</label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="color" class="form-label">Color/Markings</label>
                            <input type="text" class="form-input" name="color" id="color" value="{{ old('color') }}">
                        </div>

                        <div class="form-group">
                            <label for="weight" class="form-label">Weight (kg) *</label>
                            <input type="number" step="0.1" class="form-input @error('weight') is-invalid @enderror" name="weight" id="weight" value="{{ old('weight') }}" required>
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="registration_number" class="form-label">Microchip/Reg #</label>
                            <input type="text" class="form-input @error('registration_number') is-invalid @enderror" name="registration_number" id="registration_number" value="{{ old('registration_number') }}">
                            @error('registration_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="medical_history" class="form-label">Previous Medical History</label>
                        <textarea name="medical_history" id="medical_history" class="form-textarea" rows="3">{{ old('medical_history') }}</textarea>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn-submit">Add Pet</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
@endsection

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photo-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function calculateAgeFromBirthDate(dateValue) {
    if (!dateValue) return '';

    const birthDate = new Date(dateValue);
    const today = new Date();

    let years = today.getFullYear() - birthDate.getFullYear();
    let months = today.getMonth() - birthDate.getMonth();

    if (today.getDate() < birthDate.getDate()) {
        months--;
    }

    if (months < 0) {
        years--;
        months += 12;
    }

    if (years < 0) return '';
    if (years === 0) return months + ' month' + (months === 1 ? '' : 's');

    return years + ' year' + (years === 1 ? '' : 's');
}

document.addEventListener('DOMContentLoaded', function () {
    const birthDateInput = document.getElementById('birth_date');
    const ageInput = document.getElementById('calculated_age');

    if (!birthDateInput || !ageInput) return;

    const updateAge = () => {
        const ageText = calculateAgeFromBirthDate(birthDateInput.value);
        ageInput.value = ageText || 'Set a birth date';
    };

    birthDateInput.addEventListener('change', updateAge);
    updateAge();
});
</script>
