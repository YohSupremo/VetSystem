@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('customer.pets.show', $pet->id) }}" class="text-decoration-none text-muted me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="h3 mb-0">Edit Pet: {{ $pet->name }}</h1>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customer.pets.update', $pet->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- Photo Upload -->
                            <div class="col-12 text-center mb-3">
                                <div class="mb-2">
                                     <img id="photo-preview" src="{{ $pet->photo_url }}" 
                                         class="rounded-circle bg-light border" width="120" height="120" style="object-fit: cover;"
                                         onerror="this.src='https://placehold.co/120x120?text=Pet+Photo'">
                                </div>
                                <label for="photo" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-camera me-1"></i> Change Photo
                                </label>
                                <input type="file" name="photo" id="photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>

                            <!-- Basic Info -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold">Pet Name *</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $pet->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="species" class="form-label fw-bold">Species *</label>
                                <select name="species" id="species" class="form-select" required>
                                    <option value="">Select species...</option>
                                    <option value="Dog" {{ old('species', $pet->species) == 'Dog' ? 'selected' : '' }}>Dog</option>
                                    <option value="Cat" {{ old('species', $pet->species) == 'Cat' ? 'selected' : '' }}>Cat</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="breed" class="form-label fw-bold">Breed</label>
                                <input type="text" class="form-control" name="breed" id="breed" value="{{ old('breed', $pet->breed) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="dob" class="form-label fw-bold">Date of Birth</label>
                                <input type="date" class="form-control" name="dob" id="dob" value="{{ old('dob', $pet->dob) }}" max="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label fw-bold">Gender *</label>
                                <select name="gender" id="gender" class="form-select" required>
                                    <option value="Male" {{ old('gender', $pet->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $pet->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="color" class="form-label fw-bold">Color/Markings</label>
                                <input type="text" class="form-control" name="color" id="color" value="{{ old('color', $pet->color) }}">
                            </div>

                            <div class="col-md-6">
                                <label for="weight" class="form-label fw-bold">Weight (kg) *</label>
                                <input type="number" step="0.1" class="form-control @error('weight') is-invalid @enderror" name="weight" id="weight" value="{{ old('weight', $pet->weight) }}" required>
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="registration_number" class="form-label fw-bold">Microchip/Reg #</label>
                                <input type="text" class="form-control @error('registration_number') is-invalid @enderror" name="registration_number" id="registration_number" value="{{ old('registration_number', $pet->registration_number) }}">
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="medical_history" class="form-label fw-bold">Medical History/Notes</label>
                                <textarea name="medical_history" id="medical_history" class="form-control" rows="3">{{ old('medical_history', $pet->medical_history) }}</textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Update Pet</button>
                        </div>
                    </form>
                    
                    <div class="mt-4 pt-3 border-top text-center">
                        <form action="{{ route('customer.pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('Are you sure? This will remove the pet profile.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Remove Pet Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
</script>
@endsection