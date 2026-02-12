@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Edit Pet - PawCare')

@push('styles')
<style>
/* reuse styles from create view */
.photo-preview {
    width: 150px;
    height: 150px;
    border-radius: 1rem;
    object-fit: cover;
    margin: 0 auto 1rem;
    border: 3px solid var(--light-purple);
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')

<div class="customer-container">
    <main class="customer-main">
        <div class="form-card">
            <div class="form-header">
                <h2>Edit Pet</h2>
                <p>Update your pet's information</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mb-4">{{ session('error') }}</div>
            @endif

            <form action="{{ route('customer.pets.update', $pet->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Pet Name *</label>
                        <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $pet->name) }}" required>
                        @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="species">Species *</label>
                        <select id="species" name="species" class="form-select @error('species') is-invalid @enderror" required>
                            <option value="">Select species</option>
                            @foreach(['Dog','Cat','Bird','Rabbit','Hamster','Guinea Pig','Other'] as $s)
                                <option value="{{ $s }}" {{ old('species', $pet->species) == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('species')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="breed">Breed</label>
                        <input id="breed" type="text" name="breed" class="form-control @error('breed') is-invalid @enderror" value="{{ old('breed', $pet->breed) }}">
                        @error('breed')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="gender">Gender *</label>
                        <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select gender</option>
                            <option value="male" {{ old('gender', $pet->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $pet->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="unknown" {{ old('gender', $pet->gender) == 'unknown' ? 'selected' : '' }}>Unknown</option>
                        </select>
                        @error('gender')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="birth_date">Birth Date</label>
                        <input id="birth_date" type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', optional($pet->birth_date)->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}">
                        @error('birth_date')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="weight">Weight (kg)</label>
                        <input id="weight" type="number" step="0.1" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $pet->weight) }}" min="0" max="999.99">
                        @error('weight')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="color">Color</label>
                        <input id="color" type="text" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', $pet->color) }}">
                        @error('color')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="microchip_number">Microchip Number</label>
                        <input id="microchip_number" type="text" name="microchip_number" class="form-control @error('microchip_number') is-invalid @enderror" value="{{ old('microchip_number', $pet->microchip_number) }}">
                        @error('microchip_number')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Pet Photo</label>
                        <div class="photo-upload">
                            <div class="photo-upload-icon">📷</div>
                            <p class="mb-2">Click to upload photo</p>
                            <p class="text-muted small">JPG, PNG, GIF up to 2MB</p>
                            <!-- keep input reachable by browsers: avoid display:none which can prevent file dialog in some environments -->
                            <input type="file" id="photo" name="photo" accept="image/*" style="position: absolute; left: -9999px;" onchange="previewPhoto(event)">
                            <div style="margin-top:8px;">
                                <button type="button" id="chooseFileBtn" class="btn btn-secondary">Choose file</button>
                            </div>
                        </div>

                        <div id="photoPreview" class="text-center mt-3">
                            @if($pet->photo_path)
                                <img src="{{ $pet->photo_url }}" class="photo-preview" alt="Current photo">
                                <p class="text-muted small mt-2">Current photo</p>
                            @endif
                        </div>
                        @error('photo')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $pet->notes) }}</textarea>
                        @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end mt-4">
                    <a href="{{ route('customer.pets.show', $pet->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
            preview.innerHTML = `\n                <img src="${e.target.result}" class="photo-preview" alt="Pet photo preview">\n                <p class="text-muted small mt-2">Selected: ${file.name}</p>\n            `;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection