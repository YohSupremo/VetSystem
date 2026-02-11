@extends('veterinarian.layout')

@section('title', 'Laboratory - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Laboratory Tests</h2>
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

    @if($labTests->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Owner</th>
                        <th>Test Type</th>
                        <th>Test Name</th>
                        <th>Specimen</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labTests as $labTest)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $labTest->test_date->format('M j, Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $labTest->created_at->format('g:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pet-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">🐾</div>
                                    <div>
                                        <strong>{{ $labTest->pet->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $labTest->pet->species }} • {{ $labTest->pet->breed }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $labTest->pet->owner->first_name }} {{ $labTest->pet->owner->last_name }}
                                <br>
                                <small class="text-muted">{{ $labTest->pet->owner->contact_number }}</small>
                            </td>
                            <td>
                                <span class="status-badge {{ $labTest->status }}">
                                    {{ ucfirst($labTest->test_type) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $labTest->test_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $labTest->specimen_type }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge specimen">
                                    {{ ucfirst($labTest->specimen_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $labTest->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $labTest->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('veterinarian.laboratory.show', [$labTest->pet_id, $labTest->id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('veterinarian.laboratory.edit', [$labTest->pet_id, $labTest->id]) }}" class="btn btn-sm btn-outline-secondary">
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
            {{ $labTests->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🔬</div>
            <h3>No lab tests found</h3>
            <p>No laboratory tests have been created yet.</p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.status-badge.specimen {
    background-color: #e3f2fd;
    color: #1565c0;
}
</style>
@endpush
