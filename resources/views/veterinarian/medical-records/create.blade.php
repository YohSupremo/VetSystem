@extends('veterinarian.layout')

@section('title', 'Create Medical Record - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Create Medical Record</h2>
        <a href="#" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Patient
        </a>
    </div>

    <!-- Patient Info -->
    <div class="appointment-item mb-4">
        <div class="pet-avatar me-3">🐾</div>
        <div class="item-details">
            <h5>{{ $pet->name }}</h5>
            <p class="mb-1">{{ $pet->species }} • {{ $pet->breed }} • {{ $pet->age }} years old</p>
            <p class="text-muted">Owner: {{ $pet->owner->first_name }} {{ $pet->owner->last_name }}</p>
        </div>
    </div>

    <form action="#" method="POST">
        @csrf
        
        <div class="row g-3">
            <!-- Chief Complaint -->
            <div class="col-12">
                <label for="chief_complaint" class="form-label">
                    Chief Complaint <span class="text-danger">*</span>
                </label>
                <textarea id="chief_complaint" name="chief_complaint" rows="3" class="form-control" required
                          placeholder="Describe main reason for visit...">{{ old('chief_complaint') }}</textarea>
            </div>

            <!-- Symptoms -->
            <div class="col-12">
                <label for="symptoms" class="form-label">Symptoms</label>
                <textarea id="symptoms" name="symptoms" rows="3" class="form-control"
                          placeholder="Describe any symptoms patient is experiencing...">{{ old('symptoms') }}</textarea>
            </div>

            <!-- Physical Examination -->
            <div class="col-12">
                <label for="physical_examination" class="form-label">Physical Examination</label>
                <textarea id="physical_examination" name="physical_examination" rows="4" class="form-control"
                          placeholder="Record physical examination findings...">{{ old('physical_examination') }}</textarea>
            </div>

            <!-- Diagnosis -->
            <div class="col-12">
                <label for="diagnosis" class="form-label">
                    Diagnosis <span class="text-danger">*</span>
                </label>
                <textarea id="diagnosis" name="diagnosis" rows="3" class="form-control" required
                          placeholder="Enter diagnosis...">{{ old('diagnosis') }}</textarea>
            </div>

            <!-- Treatment -->
            <div class="col-12">
                <label for="treatment" class="form-label">
                    Treatment <span class="text-danger">*</span>
                </label>
                <textarea id="treatment" name="treatment" rows="3" class="form-control" required
                          placeholder="Describe treatment plan...">{{ old('treatment') }}</textarea>
            </div>

            <!-- Laboratory Results -->
            <div class="col-12">
                <label for="laboratory_results" class="form-label">Laboratory Results</label>
                <textarea id="laboratory_results" name="laboratory_results" rows="3" class="form-control"
                          placeholder="Enter any laboratory test results...">{{ old('laboratory_results') }}</textarea>
            </div>

            <!-- Follow-up Instructions -->
            <div class="col-12">
                <label for="follow_up_instructions" class="form-label">Follow-up Instructions</label>
                <textarea id="follow_up_instructions" name="follow_up_instructions" rows="3" class="form-control"
                          placeholder="Provide follow-up care instructions...">{{ old('follow_up_instructions') }}</textarea>
            </div>

            <!-- Notes -->
            <div class="col-12">
                <label for="notes" class="form-label">Additional Notes</label>
                <textarea id="notes" name="notes" rows="3" class="form-control"
                          placeholder="Any additional notes or observations...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="#" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Medical Record
            </button>
        </div>
    </form>
</div>
@endsection
