@extends('admin.dashboard')

@section('page-title', 'Prescriptions - ' . $pet->name)
@section('page-description', 'View all prescriptions for ' . $pet->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0">Prescriptions for {{ $pet->name }}</h2>
            <p class="text-muted mb-0">
                <i class="fas fa-user-tie me-1"></i> Owner: {{ $pet->owner->user->first_name ?? '' }} {{ $pet->owner->user->last_name ?? 'Unknown' }}
                <i class="fas fa-phone ms-3 me-1"></i> {{ $pet->owner->user->contact_number ?? 'N/A' }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to All
            </a>
            <a href="{{ route('admin.prescriptions.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
                <i class="fas fa-prescription me-2"></i>Add Prescription
            </a>
        </div>
    </div>

    <!-- Pet Info Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pet Information</h5>
                    <p class="mb-2"><strong>Species:</strong> {{ $pet->species }}</p>
                    <p class="mb-2"><strong>Breed:</strong> {{ $pet->breed ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Gender:</strong> {{ ucfirst($pet->gender) }}</p>
                    <p class="mb-0"><strong>Weight:</strong> {{ $pet->weight ?? 'N/A' }} kg</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescriptions Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Prescription Records</h5>
        </div>
        <div class="card-body">
            @if($prescriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prescriptions as $prescription)
                                <tr>
                                    <td>
                                        <strong>{{ $prescription->medication }}</strong>
                                    </td>
                                    <td>{{ $prescription->dosage }}</td>
                                    <td>{{ $prescription->frequency }}</td>
                                    <td>{{ $prescription->duration_days }} days</td>
                                    <td>{{ $prescription->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($prescription->dispensed)
                                            <span class="badge bg-success">Dispensed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.prescriptions.show', $prescription->id) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.prescriptions.edit', $prescription->id) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.prescriptions.destroy', $prescription->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($prescriptions->hasPages())
                    <div class="mt-4">
                        {{ $prescriptions->links() }}
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No prescriptions found for this pet.
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .badge {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
</style>
@endpush
@endsection
