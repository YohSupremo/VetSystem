@extends('admin.dashboard')

@section('page-title', 'Surgery Records for ' . ($pet->name ?? 'Pet'))
@section('page-description', 'All surgeries for ' . ($pet->name ?? 'pet'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.surgeries.index') }}" class="btn btn-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left me-2"></i> Back to All Surgeries
            </a>
            <h2 class="h4 mb-0">Surgery Records - {{ $pet->name ?? 'N/A' }}</h2>
            <p class="text-muted">Owner: {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}</p>
        </div>
        <a href="{{ route('admin.surgeries.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Schedule New Surgery
        </a>
    </div>

    @if($surgeries->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Procedure</th>
                    <th>Surgeon</th>
                    <th>Scheduled Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($surgeries as $surgery)
                <tr>
                    <td>
                        <strong>{{ $surgery->procedure_name }}</strong>
                        @if($surgery->anesthesia_type)
                        <br><small class="text-muted">Anesthesia: {{ $surgery->anesthesia_type }}</small>
                        @endif
                    </td>
                    <td>{{ $surgery->surgeon ? 'Dr. ' . $surgery->surgeon->first_name . ' ' . $surgery->surgeon->last_name : 'N/A' }}</td>
                    <td>{{ $surgery->scheduled_date ? \Carbon\Carbon::parse($surgery->scheduled_date)->format('M d, Y H:i A') : 'N/A' }}</td>
                    <td>
                        @php
                            $statusColors = [
                                'scheduled' => 'primary',
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$surgery->status] ?? 'secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $surgery->status)) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.surgeries.show', $surgery->id) }}" class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.surgeries.edit', $surgery->id) }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.surgeries.destroy', $surgery->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this surgery?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($surgeries->hasPages())
    <nav aria-label="Page navigation" class="mt-4">
        {{ $surgeries->links() }}
    </nav>
    @endif
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i> No surgeries found for this pet.
        <a href="{{ route('admin.surgeries.create', ['pet_id' => $pet->id]) }}" class="alert-link">Schedule one now</a>
    </div>
    @endif
</div>
@endsection
