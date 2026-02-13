@extends('veterinarian.layout')

@section('title', 'Create Vaccination - PawCare')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="section-header">
                <h2 class="mb-3">Create Vaccination</h2>
                <a href="{{ route('veterinarian.patients.show', $pet->id) }}" class="btn-action">
                    <i class="fas fa-arrow-left me-2"></i>Back to Patient
                </a>
            </div>

            <form method="POST" action="{{ route('veterinarian.vaccinations.store', $pet->id) }}">
                @csrf
                <div class="row g-3">
                    <!-- Patient Information -->
                    <div class="col-12">
                        <div class="content-card" style="background-color: #f8f9fa; padding: 1rem;">
                            <h5 class="mb-3">Patient Information</h5>
                            <div class="row g-2">
                                <div class="col-md-6"><strong>Name:</strong></div>
                                <div class="col-md-6">{{ $pet->name }}</div>
                                
                                <div class="col-md-6"><strong>Species:</strong></div>
                                <div class="col-md-6">{{ $pet->species }}</div>
                                
                                <div class="col-md-6"><strong>Breed:</strong></div>
                                <div class="col-md-6">{{ $pet->breed ?: 'N/A' }}</div>
                                
                                <div class="col-md-6"><strong>Owner:</strong></div>
                                <div class="col-md-6">
                                    @if($pet->owner && $pet->owner->user)
                                        {{ $pet->owner->user->first_name }} {{ $pet->owner->user->last_name }}
                                    @else
                                        <span class="text-danger">Owner not found</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vaccine Information -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vaccine_name" class="form-label">Vaccine Name *</label>
                            <input type="text" class="form-control @error('vaccine_name') is-invalid @enderror" 
                                   id="vaccine_name" name="vaccine_name" 
                                   value="{{ old('vaccine_name') }}" required>
                            @error('vaccine_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="vaccine_type" class="form-label">Vaccine Type *</label>
                            <select class="form-select @error('vaccine_type') is-invalid @enderror" 
                                    id="vaccine_type" name="vaccine_type" required>
                                <option value="">Select Vaccine Type</option>
                                <option value="core" {{ old('vaccine_type') == 'core' ? 'selected' : '' }}>Core Vaccine</option>
                                <option value="non-core" {{ old('vaccine_type') == 'non-core' ? 'selected' : '' }}>Non-Core Vaccine</option>
                                <option value="booster" {{ old('vaccine_type') == 'booster' ? 'selected' : '' }}>Booster</option>
                                <option value="rabies" {{ old('vaccine_type') == 'rabies' ? 'selected' : '' }}>Rabies</option>
                            </select>
                            @error('vaccine_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="manufacturer" class="form-label">Manufacturer</label>
                            <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" 
                                   id="manufacturer" name="manufacturer" 
                                   value="{{ old('manufacturer') }}">
                            @error('manufacturer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="batch_number" class="form-label">Batch Number</label>
                            <input type="text" class="form-control @error('batch_number') is-invalid @enderror" 
                                   id="batch_number" name="batch_number" 
                                   value="{{ old('batch_number') }}">
                            @error('batch_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="administered_date" class="form-label">Vaccination Date *</label>
                            <input type="date" class="form-control @error('administered_date') is-invalid @enderror" 
                                   id="administered_date" name="administered_date" 
                                   value="{{ old('administered_date', now()->format('Y-m-d')) }}" required>
                            @error('administered_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="next_due_date" class="form-label">Next Due Date</label>
                            <input type="date" class="form-control @error('next_due_date') is-invalid @enderror" 
                                   id="next_due_date" name="next_due_date" 
                                   value="{{ old('next_due_date') }}">
                            @error('next_due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-action">
                                <i class="fas fa-save me-2"></i>Record Vaccination
                            </button>
                            <a href="{{ route('veterinarian.patients.show', $pet->id) }}" class="btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="content-card">
            <h5 class="mb-3">Vaccination Guidelines</h5>
            <div class="vaccination-guidelines">
                <div class="guideline-item">
                    <h6>Core Vaccines</h6>
                    <p>Essential vaccines for all pets (DHPP for dogs, FVRCP for cats)</p>
                </div>
                <div class="guideline-item">
                    <h6>Non-Core Vaccines</h6>
                    <p>Optional based on lifestyle and risk factors</p>
                </div>
                <div class="guideline-item">
                    <h6>Booster Schedule</h6>
                    <p>Typically every 1-3 years depending on vaccine type</p>
                </div>
                <div class="guideline-item">
                    <h6>Rabies Vaccination</h6>
                    <p>Legally required in most areas, usually annual</p>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('veterinarian.medical-records.create', $pet->id) }}" class="btn-action">
                    <i class="fas fa-file-medical me-2"></i>Add Medical Record
                </a>
                <a href="{{ route('veterinarian.prescriptions.create', $pet->id) }}" class="btn-action">
                    <i class="fas fa-prescription-bottle-alt me-2"></i>Write Prescription
                </a>
                <a href="{{ route('veterinarian.laboratory.create', $pet->id) }}" class="btn-action">
                    <i class="fas fa-microscope me-2"></i>Order Lab Test
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.vaccination-guidelines {
    max-height: 400px;
    overflow-y: auto;
}

.guideline-item {
    padding: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 0.5rem;
}

.guideline-item:last-child {
    border-bottom: none;
}

.guideline-item h6 {
    color: #4f46e5;
    margin-bottom: 0.25rem;
}

.guideline-item p {
    margin-bottom: 0;
    font-size: 0.875rem;
    color: #6b7280;
}
</style>
@endpush
