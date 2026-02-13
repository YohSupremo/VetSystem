@extends('veterinarian.layout')

@section('title', 'Medical Records - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Medical Records</h2>
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

    @if($medicalRecords->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Owner</th>
                        <th>Chief Complaint</th>
                        <th>Diagnosis</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicalRecords as $record)
                        <tr>
                            <td>{{ $record->visit_date->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('veterinarian.patients.show', $record->pet->id) }}" 
                                   class="text-decoration-none">
                                    {{ $record->pet->name }}
                                </a>
                            </td>
                            <td>{{ $record->pet->owner->first_name }} {{ $record->pet->owner->last_name }}</td>
                            <td>{{ Str::limit($record->complaint, 50) }}</td>
                            <td>{{ Str::limit($record->diagnosis, 50) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('veterinarian.medical-records.show', [$record->pet->id, $record->id]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('veterinarian.medical-records.edit', [$record->pet->id, $record->id]) }}" 
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
            {{ $medicalRecords->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-file-medical fa-3x mb-3"></i>
            <h4>No Medical Records Found</h4>
            <p class="text-muted">
                @if($petId)
                    No medical records have been created for this patient yet.
                @else
                    No medical records have been created yet.
                @endif
            </p>
            @if($petId)
                <a href="{{ route('veterinarian.medical-records.create', $petId) }}" class="btn-action">
                    <i class="fas fa-plus me-2"></i>Create First Medical Record
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
