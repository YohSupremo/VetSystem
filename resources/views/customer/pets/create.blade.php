@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Register New Pet - PawCare')

@push('styles')
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.customer-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
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

.customer-main {
    padding: 2rem;
    max-width: 900px;
    margin: 0 auto;
}

.form-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 3rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.form-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.form-header h2 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.form-header p {
    color: #6B7280;
    font-size: 1rem;
}

.form-label {
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-control, .form-select {
    border-radius: 1rem;
    border: 2px solid var(--light-purple);
    padding: 0.75rem 1.25rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
}

.form-control:focus, .form-select:focus {
    border-color: var(--secondary-purple);
    box-shadow: 0 0 0 4px rgba(167, 139, 250, 0.1);
    background: white;
}

.form-control.is-invalid {
    border-color: #EF4444;
}

.text-danger {
    color: #EF4444;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    border: none;
    border-radius: 1rem;
    padding: 0.85rem 2rem;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(147, 51, 234, 0.4);
    color: white;
}

.btn-secondary {
    background: white;
    border: 2px solid var(--light-purple);
    border-radius: 1rem;
    padding: 0.85rem 2rem;
    font-weight: 600;
    color: var(--primary-purple);
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: var(--light-purple);
    border-color: var(--secondary-purple);
    color: var(--primary-purple);
    transform: translateY(-2px);
}

.photo-upload {
    border: 3px dashed var(--light-purple);
    border-radius: 1.5rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: rgba(167, 139, 250, 0.05);
}

.photo-upload:hover {
    border-color: var(--secondary-purple);
    background: rgba(167, 139, 250, 0.1);
}

.photo-upload-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--primary-purple);
}

.photo-preview {
    width: 150px;
    height: 150px;
    border-radius: 1rem;
    object-fit: cover;
    margin: 0 auto 1rem;
    border: 3px solid var(--light-purple);
}

.alert {
    border-radius: 1rem;
    padding: 1rem 1.5rem;
    border: none;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

@media (max-width: 768px) {
    .customer-main {
        padding: 1rem;
    }
    
    .form-card {
        padding: 2rem 1.5rem;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="customer-container">
    <!-- Main Content -->
    <main class="customer-main">
        <div class="form-card">
            <div class="form-header">
                <h2>Register New Pet</h2>
                <p>Add your furry friend to our family</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('customer.pets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-3">
                    <!-- Pet Name -->
                    <div class="col-md-6">
                        <label class="form-label" for="name">Pet Name *</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter your pet's name"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Species -->
                    <div class="col-md-6">
                        <label class="form-label" for="species">Species *</label>
                        <select
                            id="species"
                            name="species"
                            class="form-select @error('species') is-invalid @enderror"
                            required
                        >
                            <option value="">Select species</option>
                            <option value="Dog" {{ old('species') == 'Dog' ? 'selected' : '' }}>Dog</option>
                            <option value="Cat" {{ old('species') == 'Cat' ? 'selected' : '' }}>Cat</option>
                            <option value="Bird" {{ old('species') == 'Bird' ? 'selected' : '' }}>Bird</option>
                            <option value="Rabbit" {{ old('species') == 'Rabbit' ? 'selected' : '' }}>Rabbit</option>
                            <option value="Hamster" {{ old('species') == 'Hamster' ? 'selected' : '' }}>Hamster</option>
                            <option value="Guinea Pig" {{ old('species') == 'Guinea Pig' ? 'selected' : '' }}>Guinea Pig</option>
                            <option value="Other" {{ old('species') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('species')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Breed -->
                    <div class="col-md-6">
                        <label class="form-label" for="breed">Breed</label>
                        <input
                            id="breed"
                            type="text"
                            name="breed"
                            class="form-control @error('breed') is-invalid @enderror"
                            placeholder="Enter breed (if known)"
                            value="{{ old('breed') }}"
                        >
                        @error('breed')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div class="col-md-6">
                        <label class="form-label" for="gender">Gender *</label>
                        <select
                            id="gender"
                            name="gender"
                            class="form-select @error('gender') is-invalid @enderror"
                            required
                        >
                            <option value="">Select gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="unknown" {{ old('gender') == 'unknown' ? 'selected' : '' }}>Unknown</option>
                        </select>
                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Birth Date -->
                    <div class="col-md-6">
                        <label class="form-label" for="birth_date">Birth Date</label>
                        <input
                            id="birth_date"
                            type="date"
                            name="birth_date"
                            class="form-control @error('birth_date') is-invalid @enderror"
                            value="{{ old('birth_date') }}"
                            max="{{ now()->format('Y-m-d') }}"
                        >
                        @error('birth_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Weight -->
                    <div class="col-md-6">
                        <label class="form-label" for="weight">Weight (kg)</label>
                        <input
                            id="weight"
                            type="number"
                            step="0.1"
                            name="weight"
                            class="form-control @error('weight') is-invalid @enderror"
                            placeholder="Enter weight"
                            value="{{ old('weight') }}"
                            min="0"
                            max="999.99"
                        >
                        @error('weight')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div class="col-md-6">
                        <label class="form-label" for="color">Color</label>
                        <input
                            id="color"
                            type="text"
                            name="color"
                            class="form-control @error('color') is-invalid @enderror"
                            placeholder="Enter color description"
                            value="{{ old('color') }}"
                        >
                        @error('color')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                
                    <!-- Photo Upload -->
                    <div class="col-12">
                        <label class="form-label">Pet Photo</label>
                        <div class="photo-upload" onclick="document.getElementById('photo').click()">
                            <div class="photo-upload-icon">📷</div>
                            <p class="mb-2">Click to upload photo</p>
                            <p class="text-muted small">JPG, PNG, GIF up to 2MB</p>
                            <input
                                type="file"
                                id="photo"
                                name="photo"
                                class="d-none"
                                accept="image/*"
                                onchange="previewPhoto(event)"
                            >
                        </div>
                        <div id="photoPreview" class="text-center mt-3"></div>
                        @error('photo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label" for="notes">Additional Notes</label>
                        <textarea
                            id="notes"
                            name="notes"
                            class="form-control @error('notes') is-invalid @enderror"
                            rows="4"
                            placeholder="Any special information about your pet"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex gap-3 justify-content-end mt-4">
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Register Pet</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('photoPreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" class="photo-preview" alt="Pet photo preview">
                <p class="text-muted small mt-2">Selected: ${file.name}</p>
            `;
        }
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}
</script>
@endsection
