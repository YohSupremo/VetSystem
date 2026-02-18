@extends('admin.dashboard')

@section('page-title', 'Incident Reports')
@section('page-description', 'Review reported incidents and respond quickly')

@section('content')
<style>
    .incident-table td,
    .incident-table th {
        vertical-align: middle;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
</style>


<div class="card">
    <div class="card-header">
        <h3>Incident Reports</h3>
        <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Incident
        </a>
    </div>
    <div class="card-body p-0">
        @if($incidents->isEmpty())
            <div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>No incidents reported</h3>
                <p>New incident reports will appear here.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table incident-table mb-0">
                    <thead>
                        <tr>
                            <th>Incident #</th>
                            <th>Date</th>
                            <th>Pet</th>
                            <th>Type</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Reported By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidents as $incident)
                            <tr>
                                <td>{{ $incident->incident_number }}</td>
                                <td>{{ optional($incident->incident_date)->format('M d, Y h:i A') }}</td>
                                <td>{{ $incident->pet->name ?? 'N/A' }}</td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $incident->incident_type) }}</td>
                                <td class="text-capitalize">{{ $incident->severity }}</td>
                                <td>
                                    <span class="status-badge bg-light">{{ $incident->status }}</span>
                                </td>
                                <td>{{ $incident->reportedBy->first_name ?? 'N/A' }} {{ $incident->reportedBy->last_name ?? '' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.incidents.show', $incident->id) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                    <a href="{{ route('admin.incidents.edit', $incident->id) }}" class="btn btn-sm btn-outline-secondary">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.incidents.destroy', $incident->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this incident?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
