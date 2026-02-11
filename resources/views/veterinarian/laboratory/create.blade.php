@extends('veterinarian.layout')

@section('title', 'Order Laboratory Test - PawCare')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="section-header">
                <h2 class="mb-3">Order Laboratory Test</h2>
                <a href="{{ route('veterinarian.patients.show', $pet->id) }}" class="btn-action">
                    <i class="fas fa-arrow-left me-2"></i>Back to Patient
                </a>
            </div>

            <form method="POST" action="{{ route('veterinarian.laboratory.store', $pet->id) }}">
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

                    <!-- Test Information -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="test_name" class="form-label">Test Name *</label>
                            <input type="text" class="form-control @error('test_name') is-invalid @enderror" 
                                   id="test_name" name="test_name" 
                                   value="{{ old('test_name') }}" required>
                            @error('test_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="test_type" class="form-label">Test Type *</label>
                            <select class="form-select @error('test_type') is-invalid @enderror" 
                                    id="test_type" name="test_type" required>
                                <option value="">Select Test Type</option>
                                <option value="blood" {{ old('test_type') == 'blood' ? 'selected' : '' }}>Blood Test</option>
                                <option value="urine" {{ old('test_type') == 'urine' ? 'selected' : '' }}>Urine Test</option>
                                <option value="fecal" {{ old('test_type') == 'fecal' ? 'selected' : '' }}>Fecal Test</option>
                                <option value="skin" {{ old('test_type') == 'skin' ? 'selected' : '' }}>Skin Scraping</option>
                                <option value="biopsy" {{ old('test_type') == 'biopsy' ? 'selected' : '' }}>Biopsy</option>
                                <option value="xray" {{ old('test_type') == 'xray' ? 'selected' : '' }}>X-Ray</option>
                                <option value="ultrasound" {{ old('test_type') == 'ultrasound' ? 'selected' : '' }}>Ultrasound</option>
                                <option value="ct" {{ old('test_type') == 'ct' ? 'selected' : '' }}>CT Scan</option>
                                <option value="mri" {{ old('test_type') == 'mri' ? 'selected' : '' }}>MRI</option>
                            </select>
                            @error('test_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="specimen_type" class="form-label">Specimen Type *</label>
                            <select class="form-select @error('specimen_type') is-invalid @enderror" 
                                    id="specimen_type" name="specimen_type" required>
                                <option value="">Select Specimen Type</option>
                                <option value="blood" {{ old('specimen_type') == 'blood' ? 'selected' : '' }}>Blood</option>
                                <option value="urine" {{ old('specimen_type') == 'urine' ? 'selected' : '' }}>Urine</option>
                                <option value="feces" {{ old('specimen_type') == 'feces' ? 'selected' : '' }}>Feces</option>
                                <option value="skin" {{ old('specimen_type') == 'skin' ? 'selected' : '' }}>Skin</option>
                                <option value="tissue" {{ old('specimen_type') == 'tissue' ? 'selected' : '' }}>Tissue</option>
                                <option value="swab" {{ old('specimen_type') == 'swab' ? 'selected' : '' }}>Swab</option>
                                <option value="fluid" {{ old('specimen_type') == 'fluid' ? 'selected' : '' }}>Fluid</option>
                            </select>
                            @error('specimen_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="test_date" class="form-label">Test Date *</label>
                            <input type="date" class="form-control @error('test_date') is-invalid @enderror" 
                                   id="test_date" name="test_date" 
                                   value="{{ old('test_date', now()->format('Y-m-d')) }}" required>
                            @error('test_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="results" class="form-label">Results</label>
                            <textarea class="form-control @error('results') is-invalid @enderror" 
                                      id="results" name="results" rows="4">{{ old('results') }}</textarea>
                            @error('results')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="interpretation" class="form-label">Interpretation</label>
                            <textarea class="form-control @error('interpretation') is-invalid @enderror" 
                                      id="interpretation" name="interpretation" rows="3">{{ old('interpretation') }}</textarea>
                            @error('interpretation')
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
                                <i class="fas fa-save me-2"></i>Order Test
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
            <h5 class="mb-3">Common Laboratory Tests</h5>
            <div class="common-tests">
                <div class="test-item">
                    <h6>Blood Tests</h6>
                    <p>CBC, Chemistry Panel, Thyroid, Heartworm</p>
                </div>
                <div class="test-item">
                    <h6>Urine Tests</h6>
                    <p>Urinalysis, Culture & Sensitivity</p>
                </div>
                <div class="test-item">
                    <h6>Fecal Tests</h6>
                    <p>Parasite Exam, Giardia, Coccidia</p>
                </div>
                <div class="test-item">
                    <h6>Imaging</h6>
                    <p>X-Ray, Ultrasound, CT, MRI</p>
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
                <a href="{{ route('veterinarian.vaccinations.create', $pet->id) }}" class="btn-action">
                    <i class="fas fa-syringe me-2"></i>Record Vaccination
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.common-tests {
    max-height: 400px;
    overflow-y: auto;
}

.test-item {
    padding: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 0.5rem;
}

.test-item:last-child {
    border-bottom: none;
}

.test-item h6 {
    color: #4f46e5;
    margin-bottom: 0.25rem;
}

.test-item p {
    margin-bottom: 0;
    font-size: 0.875rem;
    color: #6b7280;
}
</style>
@endpush
