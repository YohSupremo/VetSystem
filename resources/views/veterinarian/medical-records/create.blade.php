@extends('veterinarian.layout')

@section('title', 'Create Medical Record - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Create Medical Record</h2>
        <div class="d-flex align-items-center gap-3">
            <div class="text-muted">
                <strong>Patient:</strong> {{ $pet->name }} ({{ $pet->species }} - {{ $pet->breed }})
            </div>
            <div class="text-muted">
                <strong>Owner:</strong> {{ $pet->owner->first_name }} {{ $pet->owner->last_name }}
            </div>
        </div>
        <a href="{{ route('veterinarian.patients.show', $pet->id) }}" class="btn-action">
            <i class="fas fa-arrow-left me-2"></i>Back to Patient
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

    <form action="{{ route('veterinarian.medical-records.store', $pet->id) }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="complaint" class="form-label">Chief Complaint *</label>
                    <textarea name="complaint" id="complaint" class="form-control" rows="2" required
                              placeholder="Main reason for visit...">{{ old('complaint') }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="examination_notes" class="form-label">Examination Notes *</label>
                    <textarea name="examination_notes" id="examination_notes" class="form-control" rows="4" required
                              placeholder="Findings from physical examination...">{{ old('examination_notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis *</label>
                    <textarea name="diagnosis" id="diagnosis" class="form-control" rows="3" required
                              placeholder="Medical diagnosis...">{{ old('diagnosis') }}</textarea>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="treatment_plan" class="form-label">Treatment Plan *</label>
                    <textarea name="treatment_plan" id="treatment_plan" class="form-control" rows="4" required
                              placeholder="Treatment and procedures performed...">{{ old('treatment_plan') }}</textarea>
                </div>
            </div>
        </div>

                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-action">
                <i class="fas fa-save me-2"></i>Save Medical Record
            </button>
            <a href="{{ route('veterinarian.patients.show', $pet->id) }}" class="btn-secondary">
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
