@extends('admin.dashboard')

@section('page-title', 'Vaccination Records for ' . ($pet->name ?? 'Pet'))
@section('page-description', 'All vaccinations for ' . ($pet->name ?? 'pet'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.vaccinations.index') }}" class="btn btn-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left me-2"></i> Back to All Vaccinations
            </a>
            <h2 class="h4 mb-0">Vaccination Records - {{ $pet->name ?? 'N/A' }}</h2>
            <p class="text-muted">Owner: {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}</p>
        </div>
        <a href="{{ route('admin.vaccinations.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Record New Vaccination
        </a>
    </div>

    @if($vaccinations->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Vaccine Name</th>
                    <th>Vaccination Date</th>
                    <th>Next Due Date</th>
                    <th>Veterinarian</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vaccinations as $vaccination)
                <tr>
                    <td>
                        <strong>{{ $vaccination->vaccine_name }}</strong>
                        @if($vaccination->batch_number)
                        <br><small class="text-muted">Batch: {{ $vaccination->batch_number }}</small>
                        @endif
                    </td>
                    <td>{{ $vaccination->vaccination_date ? \Carbon\Carbon::parse($vaccination->vaccination_date)->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $vaccination->next_due_date ? \Carbon\Carbon::parse($vaccination->next_due_date)->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $vaccination->veterinarian ? 'Dr. ' . $vaccination->veterinarian->first_name . ' ' . $vaccination->veterinarian->last_name : 'N/A' }}</td>
                    <td>
                        @php
                            $nextDue = $vaccination->next_due_date ? \Carbon\Carbon::parse($vaccination->next_due_date) : null;
                            $today = \Carbon\Carbon::now();
                            if (!$nextDue) {
                                $statusColor = 'secondary';
                                $statusText = 'Unknown';
                            } elseif ($nextDue < $today) {
                                $statusColor = 'danger';
                                $statusText = 'Overdue';
                            } elseif ($nextDue->diffInDays($today) < 30) {
                                $statusColor = 'warning';
                                $statusText = 'Due Soon';
                            } else {
                                $statusColor = 'success';
                                $statusText = 'Up to Date';
                            }
                        @endphp
                        <span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.vaccinations.show', $vaccination->id) }}" class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.vaccinations.edit', $vaccination->id) }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.vaccinations.destroy', $vaccination->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this vaccination record?')">
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

    @if($vaccinations->hasPages())
    <nav aria-label="Page navigation" class="mt-4">
        {{ $vaccinations->links() }}
    </nav>
    @endif
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i> No vaccinations found for this pet.
        <a href="{{ route('admin.vaccinations.create', ['pet_id' => $pet->id]) }}" class="alert-link">Record one now</a>
    </div>
    @endif
</div>
@endsection
