@extends('admin.dashboard')

@section('page-title', 'Prescription Management')
@section('page-description', 'Create, view, and manage prescriptions')

@push('styles')
<style>
    .prescription-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
        margin-bottom: 1rem;
        border-left: 4px solid #FF8C42;
    }

    .status-pending {
        border-left-color: #ffc107;
    }

    .status-completed {
        border-left-color: #28a745;
    }

    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i class="fas fa-prescription-bottle"></i> Prescription Management</h1>
                    <p class="text-muted">Create, view, and manage prescriptions</p>
                </div>
                <div>
                    <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Pharmacy
                    </a>
                    <a href="{{ route('admin.pharmacy.prescriptions.create') }}" class="btn btn-primary ms-2">
                        <i class="fas fa-plus"></i> Create Prescription
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.pharmacy.prescriptions') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="owner_id" class="form-label">Pet Owner</label>
                <select name="owner_id" id="owner_id" class="form-select">
                    <option value="">All Owners</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>
                            {{ $owner->user->first_name }} {{ $owner->user->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label for="medication" class="form-label">Medication</label>
                <input type="text" name="medication" id="medication" class="form-control" value="{{ request('medication') }}" placeholder="Search medication...">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.pharmacy.prescriptions') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Prescriptions List -->
    @if($prescriptions->count() > 0)
        <div class="row">
            @foreach($prescriptions as $prescription)
                <div class="col-12">
                    <div class="prescription-card status-{{ $prescription->dispensed ? 'completed' : 'pending' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-3">
                                        <i class="fas fa-prescription-bottle text-primary"></i>
                                        {{ $prescription->medication }}
                                    </h5>
                                    @if($prescription->dispensed)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <strong>Pet:</strong> {{ $prescription->medicalRecord->pet->name }}
                                            ({{ $prescription->medicalRecord->pet->species }})
                                        </p>
                                        <p class="mb-1">
                                            <strong>Owner:</strong> {{ $prescription->medicalRecord->pet->owner->user->first_name }}
                                            {{ $prescription->medicalRecord->pet->owner->user->last_name }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Veterinarian:</strong> {{ $prescription->medicalRecord->veterinarian->first_name }}
                                            {{ $prescription->medicalRecord->veterinarian->last_name }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <strong>Dosage:</strong> {{ $prescription->dosage }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Frequency:</strong> {{ $prescription->frequency }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Duration:</strong> {{ $prescription->duration_days }} days
                                        </p>
                                        <p class="mb-1">
                                            <strong>Date:</strong> {{ $prescription->created_at->format('M j, Y') }}
                                        </p>
                                    </div>
                                </div>

                                @if($prescription->instructions)
                                    <div class="mt-2">
                                        <strong>Instructions:</strong>
                                        <p class="text-muted mb-0">{{ $prescription->instructions }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="ms-3">
                                <div class="btn-group-vertical">
                                    <a href="{{ route('admin.pharmacy.prescriptions.show', $prescription->id) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.pharmacy.prescriptions.edit', $prescription->id) }}"
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @if(!$prescription->dispensed)
                                        <form method="POST" action="{{ route('admin.pharmacy.prescriptions.destroy', $prescription->id) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this prescription?')"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $prescriptions->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-prescription-bottle fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No Prescriptions Found</h4>
            <p class="text-muted">No prescriptions match your current filters.</p>
            <a href="{{ route('admin.pharmacy.prescriptions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create First Prescription
            </a>
        </div>
    @endif
</div>
    <div class="header-actions">
        <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Pharmacy
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" class="row g-3">
        <div class="col-md-4">
            <label for="owner_id" class="form-label">Pet Owner</label>
            <select name="owner_id" id="owner_id" class="form-select">
                <option value="">All Owners</option>
                @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>
                        {{ $owner->user->first_name }} {{ $owner->user->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="date_from" class="form-label">From Date</label>
            <input type="date" class="form-control" id="date_from" name="date_from"
                   value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
            <label for="date_to" class="form-label">To Date</label>
            <input type="date" class="form-control" id="date_to" name="date_to"
                   value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Prescriptions List -->
@if($prescriptions->count() > 0)
    @foreach($prescriptions as $prescription)
        <div class="prescription-card">
            <div class="prescription-header">
                <div class="patient-info">
                    <h5 class="mb-1">
                        {{ $prescription->medicalRecord->pet->name }}
                        <small class="text-muted">({{ $prescription->medicalRecord->pet->species }})</small>
                    </h5>
                    <p class="mb-1 text-muted">
                        Owner: {{ $prescription->medicalRecord->pet->owner->user->first_name }}
                        {{ $prescription->medicalRecord->pet->owner->user->last_name }}
                    </p>
                    <p class="mb-0 text-muted">
                        Prescribed: {{ $prescription->created_at->format('M j, Y') }}
                        by Dr. {{ $prescription->medicalRecord->veterinarian->first_name }}
                    </p>
                </div>
                <div class="prescription-actions">
                    <a href="{{ route('admin.pharmacy.show-prescription', $prescription->id) }}"
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> Review & Fill
                    </a>
                </div>
            </div>

            <div class="prescription-details">
                <div class="detail-row">
                    <span class="detail-label">Medication:</span>
                    <span class="detail-value">{{ $prescription->medication_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Dosage:</span>
                    <span class="detail-value">{{ $prescription->dosage }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Frequency:</span>
                    <span class="detail-value">{{ $prescription->frequency }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">{{ $prescription->duration_days }} days</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quantity:</span>
                    <span class="detail-value">{{ $prescription->quantity }}</span>
                </div>
                @if($prescription->instructions)
                    <div class="detail-row">
                        <span class="detail-label">Instructions:</span>
                        <span class="detail-value">{{ $prescription->instructions }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $prescriptions->appends(request()->query())->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-prescription fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No Pending Prescriptions</h4>
        <p class="text-muted">All prescriptions have been filled or there are no outstanding prescriptions.</p>
        <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Pharmacy
        </a>
    </div>
@endif
@endsection