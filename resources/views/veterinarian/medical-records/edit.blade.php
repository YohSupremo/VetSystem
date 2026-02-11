@extends('veterinarian.layout')

@section('title', 'Edit Medical Record - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Edit Medical Record</h2>
        <div class="d-flex align-items-center gap-3">
            <div class="text-muted">
                <strong>Patient:</strong> {{ $medicalRecord->pet->name }} ({{ $medicalRecord->pet->species }})
            </div>
            <div class="text-muted">
                <strong>Record Date:</strong> {{ $medicalRecord->record_date->format('M d, Y') }}
            </div>
        </div>
        <a href="{{ route('veterinarian.medical-records.show', [$medicalRecord->pet->id, $medicalRecord->id]) }}" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Record
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('veterinarian.medical-records.update', [$medicalRecord->pet->id, $medicalRecord->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="chief_complaint" class="form-label">Chief Complaint *</label>
                    <textarea name="chief_complaint" id="chief_complaint" class="form-control" rows="2" required
                              placeholder="Main reason for visit...">{{ old('chief_complaint', $medicalRecord->chief_complaint) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="symptoms" class="form-label">Symptoms *</label>
                    <textarea name="symptoms" id="symptoms" class="form-control" rows="4" required
                              placeholder="Describe the patient's symptoms...">{{ old('symptoms', $medicalRecord->symptoms) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="physical_exam" class="form-label">Physical Examination *</label>
                    <textarea name="physical_exam" id="physical_exam" class="form-control" rows="4" required
                              placeholder="Findings from physical examination...">{{ old('physical_exam', $medicalRecord->physical_exam) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis *</label>
                    <textarea name="diagnosis" id="diagnosis" class="form-control" rows="3" required
                              placeholder="Medical diagnosis...">{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="treatment" class="form-label">Treatment Plan *</label>
                    <textarea name="treatment" id="treatment" class="form-control" rows="4" required
                              placeholder="Treatment and procedures performed...">{{ old('treatment', $medicalRecord->treatment) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="lab_results" class="form-label">Laboratory Results</label>
                    <textarea name="lab_results" id="lab_results" class="form-control" rows="3"
                              placeholder="Lab test results and findings...">{{ old('lab_results', $medicalRecord->lab_results) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="follow_up_instructions" class="form-label">Follow-up Instructions</label>
                    <textarea name="follow_up_instructions" id="follow_up_instructions" class="form-control" rows="3"
                              placeholder="Instructions for follow-up care...">{{ old('follow_up_instructions', $medicalRecord->follow_up_instructions) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"
                              placeholder="Any additional notes or observations...">{{ old('notes', $medicalRecord->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-action">
                <i class="fas fa-save me-2"></i>Update Medical Record
            </button>
            <a href="{{ route('veterinarian.medical-records.show', [$medicalRecord->pet->id, $medicalRecord->id]) }}" class="btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--primary-purple);
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--light-purple);
    border-radius: 0.5rem;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
    resize: vertical;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-purple);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.alert li {
    margin-bottom: 0.5rem;
}

.text-muted {
    color: #6b7280;
    font-size: 0.9rem;
}

.text-muted strong {
    color: #374151;
}
</style>
@endpush
