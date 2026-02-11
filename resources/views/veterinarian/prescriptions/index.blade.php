@extends('veterinarian.layout')

@section('title', 'Prescriptions - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Prescriptions</h2>
        @if($petId)
            <a href="{{ route('veterinarian.patients.show', $petId) }}" class="btn-action">
                <i class="fas fa-arrow-left me-2"></i>Back to Patient
            </a>
        @else
            <a href="{{ route('veterinarian.dashboard') }}" class="btn-action">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($prescriptions->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Owner</th>
                        <th>Medication</th>
                        <th>Diagnosis</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescriptions as $prescription)
                        <tr>
                            <td>{{ $prescription->prescription_date->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('veterinarian.patients.show', $prescription->pet->id) }}" 
                                   class="text-decoration-none">
                                    {{ $prescription->pet->name }}
                                </a>
                            </td>
                            <td>{{ $prescription->pet->owner->first_name }} {{ $prescription->pet->owner->last_name }}</td>
                            <td>{{ $prescription->medication->name }} ({{ $prescription->medication->strength }})</td>
                            <td>{{ Str::limit($prescription->diagnosis, 50) }}</td>
                            <td>
                                <span class="badge badge-{{ $prescription->status }}">
                                    {{ ucfirst($prescription->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('veterinarian.prescriptions.show', [$prescription->pet->id, $prescription->id]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('veterinarian.prescriptions.edit', [$prescription->pet->id, $prescription->id]) }}" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $prescriptions->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-prescription-bottle-alt fa-3x mb-3"></i>
            <h4>No Prescriptions Found</h4>
            <p class="text-muted">
                @if($petId)
                    No prescriptions have been created for this patient yet.
                @else
                    No prescriptions have been created yet.
                @endif
            </p>
            @if($petId)
                <a href="{{ route('veterinarian.prescriptions.create', $petId) }}" class="btn-action">
                    <i class="fas fa-plus me-2"></i>Create First Prescription
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table th {
    background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
}

.table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: #f9fafb;
}

.table-responsive {
    overflow-x: auto;
    border-radius: 0.5rem;
}

.btn-group {
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    font-size: 0.8rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.btn-outline-primary {
    background: transparent;
    border: 1px solid var(--primary-purple);
    color: var(--primary-purple);
}

.btn-outline-primary:hover {
    background: var(--primary-purple);
    color: white;
}

.btn-outline-secondary {
    background: transparent;
    border: 1px solid #6b7280;
    color: #6b7280;
}

.btn-outline-secondary:hover {
    background: #6b7280;
    color: white;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-active {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.badge-completed {
    background: linear-gradient(135deg, #dbeafe, #93c5fd);
    color: #1e40af;
}

.badge-discontinued {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6b7280;
}

.empty-state i {
    color: var(--light-purple);
    margin-bottom: 1rem;
}

.empty-state h4 {
    margin-bottom: 0.5rem;
    color: #374151;
}

.text-decoration-none {
    text-decoration: none;
    color: inherit;
}

.text-decoration-none:hover {
    color: var(--primary-purple);
}
</style>
@endpush
