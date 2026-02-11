@extends('veterinarian.layout')

@section('title', 'Vaccinations - PawCare')

@section('content')
<div class="content-card">
    <div class="section-header">
        <h2 class="mb-3">Vaccinations</h2>
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

    @if($vaccinations->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Owner</th>
                        <th>Vaccine</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccinations as $vaccination)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $vaccination->vaccination_date->format('M j, Y') }}</strong>
                                    @if($vaccination->next_due_date)
                                        <br>
                                        <small class="text-muted">Next: {{ $vaccination->next_due_date->format('M j, Y') }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pet-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">🐾</div>
                                    <div>
                                        <strong>{{ $vaccination->pet->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $vaccination->pet->species }} • {{ $vaccination->pet->breed }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $vaccination->pet->owner->first_name }} {{ $vaccination->pet->owner->last_name }}
                                <br>
                                <small class="text-muted">{{ $vaccination->pet->owner->contact_number }}</small>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $vaccination->vaccine_name }}</strong>
                                    @if($vaccination->manufacturer)
                                        <br>
                                        <small class="text-muted">{{ $vaccination->manufacturer }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="status-badge {{ $vaccination->status }}">
                                    {{ ucfirst($vaccination->vaccine_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $vaccination->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $vaccination->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('veterinarian.vaccinations.show', [$vaccination->pet_id, $vaccination->id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('veterinarian.vaccinations.edit', [$vaccination->pet_id, $vaccination->id]) }}" class="btn btn-sm btn-outline-secondary">
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
            {{ $vaccinations->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">💉</div>
            <h3>No vaccinations found</h3>
            <p>No vaccination records have been created yet.</p>
        </div>
    @endif
</div>
@endsection
