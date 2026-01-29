@extends('admin.dashboard')

@section('page-title', 'Prescription Details')
@section('page-description', 'View prescription details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Prescription Details</h2>
        <div>
            <a href="{{ route('admin.prescriptions.pet', $prescription->pet->id) }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
            <a href="{{ route('admin.prescriptions.edit', $prescription->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <form action="{{ route('admin.prescriptions.destroy', $prescription->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Pet & Owner Information -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pet Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Pet Name:</strong>
                        <a href="{{ route('admin.pets.show', $prescription->pet->id) }}">{{ $prescription->pet->name }}</a>
                    </p>
                    <p class="mb-2"><strong>Species:</strong> {{ $prescription->pet->species }}</p>
                    <p class="mb-2"><strong>Breed:</strong> {{ $prescription->pet->breed ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Gender:</strong> {{ ucfirst($prescription->pet->gender) }}</p>
                    <p class="mb-0"><strong>Weight:</strong> {{ $prescription->pet->weight ?? 'N/A' }} kg</p>
                </div>
            </div>
        </div>

        <!-- Owner Information -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Owner Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Owner Name:</strong>
                        <a href="{{ route('admin.pet-owners.show', $prescription->pet->owner->id) }}">
                            {{ $prescription->pet->owner->user->first_name ?? '' }} {{ $prescription->pet->owner->user->last_name ?? 'Unknown' }}
                        </a>
                    </p>
                    <p class="mb-2">
                        <strong>Contact Number:</strong> {{ $prescription->pet->owner->user->contact_number ?? 'N/A' }}
                    </p>
                    <p class="mb-2">
                        <strong>Email:</strong> {{ $prescription->pet->owner->user->email ?? 'N/A' }}
                    </p>
                    <p class="mb-0">
                        <strong>Address:</strong> {{ $prescription->pet->owner->user->address ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescription Details -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Prescription Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-3">
                        <strong>Medication:</strong><br>
                        <span class="badge bg-primary" style="font-size: 1rem;">{{ $prescription->medication }}</span>
                    </p>
                    <p class="mb-3">
                        <strong>Dosage:</strong><br>
                        {{ $prescription->dosage }}
                    </p>
                    <p class="mb-0">
                        <strong>Frequency:</strong><br>
                        {{ $prescription->frequency }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-3">
                        <strong>Duration:</strong><br>
                        {{ $prescription->duration_days }} days
                    </p>
                    <p class="mb-3">
                        <strong>Dispensed Status:</strong><br>
                        @if($prescription->dispensed)
                            <span class="badge bg-success">Dispensed on {{ $prescription->dispensed_at->format('M d, Y H:i') }}</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </p>
                    <p class="mb-0">
                        <strong>Created:</strong><br>
                        {{ $prescription->created_at->format('M d, Y H:i') }}
                    </p>
                </div>
            </div>

            @if($prescription->instructions)
                <hr>
                <p>
                    <strong>Instructions:</strong><br>
                    {{ $prescription->instructions }}
                </p>
            @endif
        </div>
    </div>

    <!-- Medical Record Information -->
    @if($prescription->medicalRecord)
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Associated Medical Record</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Visit Date:</strong> 
                    <a href="{{ route('admin.medical-records.show', $prescription->medicalRecord->id) }}">
                        {{ $prescription->medicalRecord->visit_date->format('M d, Y') }}
                    </a>
                </p>
                <p class="mb-2">
                    <strong>Veterinarian:</strong> 
                    @if($prescription->medicalRecord->veterinarian)
                        Dr. {{ $prescription->medicalRecord->veterinarian->first_name }} {{ $prescription->medicalRecord->veterinarian->last_name }}
                    @else
                        N/A
                    @endif
                </p>
                <p class="mb-2">
                    <strong>Complaint:</strong> {{ $prescription->medicalRecord->complaint ?? 'N/A' }}
                </p>
                <p class="mb-0">
                    <strong>Diagnosis:</strong> {{ $prescription->medicalRecord->diagnosis ?? 'N/A' }}
                </p>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-radius: 8px 8px 0 0;
    }

    .badge {
        padding: 0.5rem 0.75rem;
    }

    a {
        text-decoration: none;
        color: #007bff;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
@endpush
@endsection
