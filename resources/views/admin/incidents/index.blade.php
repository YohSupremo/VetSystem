@extends('admin.dashboard')

@section('page-title', 'Incident Reports')
@section('page-description', 'Review reported incidents and respond quickly')

@section('content')
<style>
    .incident-card {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(251, 146, 60, 0.18);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
    }

    .incident-card .card-header {
        background: linear-gradient(135deg, rgba(255, 247, 237, 0.95), rgba(253, 242, 248, 0.95));
        border-bottom: 1px solid rgba(251, 146, 60, 0.2);
        padding: 1.05rem 1.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .incident-card .card-header h3 {
        margin: 0;
        font-weight: 800;
        background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .incident-add-btn {
        border: none;
        border-radius: 12px;
        padding: 0.58rem 1rem;
        font-weight: 700;
        background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
        box-shadow: 0 10px 20px rgba(236, 72, 153, 0.24);
    }

    .incident-add-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 24px rgba(236, 72, 153, 0.3);
    }

    .incident-table {
        margin-bottom: 0;
    }

    .incident-table td,
    .incident-table th {
        vertical-align: middle;
        padding: 0.95rem 0.65rem;
    }

    .incident-table thead th {
        font-weight: 800;
        font-size: 0.92rem;
        color: #374151;
        border-bottom: 1px solid #E5E7EB;
        background: #FFFBF7;
        white-space: nowrap;
    }

    .incident-table tbody tr:hover {
        background: #FFF7ED;
    }

    .incident-number {
        font-weight: 700;
        color: #374151;
    }

    .incident-type,
    .incident-severity {
        font-weight: 600;
        color: #4B5563;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
        border: 1px solid transparent;
    }

    .status-open {
        background: #FFF7ED;
        color: #C2410C;
        border-color: #FDBA74;
    }

    .status-in-progress {
        background: #FDF2F8;
        color: #BE185D;
        border-color: #F9A8D4;
    }

    .status-resolved {
        background: #ECFDF5;
        color: #047857;
        border-color: #6EE7B7;
    }

    .status-closed {
        background: #F3F4F6;
        color: #374151;
        border-color: #D1D5DB;
    }

    .status-under-review {
        background: #EFF6FF;
        color: #1D4ED8;
        border-color: #93C5FD;
    }

    .status-default {
        background: #F9FAFB;
        color: #4B5563;
        border-color: #E5E7EB;
    }

    .incident-actions {
        white-space: nowrap;
    }

    .incident-actions .btn {
        border-radius: 10px;
        font-weight: 700;
        padding: 0.33rem 0.7rem;
        border-width: 1px;
    }

    .incident-actions .btn-outline-primary {
        color: #DB2777;
        border-color: #F9A8D4;
    }

    .incident-actions .btn-outline-primary:hover {
        background: #FDF2F8;
        color: #BE185D;
        border-color: #F472B6;
    }

    .incident-actions .btn-outline-secondary {
        color: #7C3AED;
        border-color: #DDD6FE;
    }

    .incident-actions .btn-outline-secondary:hover {
        background: #F5F3FF;
        color: #6D28D9;
        border-color: #C4B5FD;
    }

    .incident-actions .btn-outline-danger {
        color: #DC2626;
        border-color: #FECACA;
    }

    .incident-actions .btn-outline-danger:hover {
        background: #FEF2F2;
        color: #B91C1C;
        border-color: #FCA5A5;
    }

    .incident-pagination {
        border-top: 1px solid #F3F4F6;
        background: #FFFEFC;
    }

    .incident-empty-state {
        padding: 3.5rem 1.25rem;
        text-align: center;
        color: #6B7280;
    }

    .incident-empty-state i {
        font-size: 2.2rem;
        margin-bottom: 0.8rem;
        color: #FB923C;
    }

    .incident-empty-state h3 {
        font-size: 1.2rem;
        color: #374151;
        margin-bottom: 0.4rem;
    }
</style>


<div class="card incident-card">
    <div class="card-header">
        <h3>Incident Reports</h3>
        <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary incident-add-btn">
            <i class="fas fa-plus me-2"></i>New Incident
        </a>
    </div>
    <div class="card-body p-0">
        @if($incidents->isEmpty())
            <div class="incident-empty-state">
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
                            @php
                                $statusClass = strtolower(str_replace(' ', '-', (string) $incident->status));
                                if (!in_array($statusClass, ['open', 'in-progress', 'resolved', 'closed', 'under-review'])) {
                                    $statusClass = 'default';
                                }
                            @endphp
                            <tr>
                                <td class="incident-number">{{ $incident->incident_number }}</td>
                                <td>{{ optional($incident->incident_date)->format('M d, Y h:i A') }}</td>
                                <td>{{ $incident->pet->name ?? 'N/A' }}</td>
                                <td class="text-capitalize incident-type">{{ str_replace('_', ' ', $incident->incident_type) }}</td>
                                <td class="text-capitalize incident-severity">{{ $incident->severity }}</td>
                                <td>
                                    <span class="status-badge status-{{ $statusClass }}">{{ $incident->status }}</span>
                                </td>
                                <td>
                                    {{ $incident->reportedBy->first_name ?? 'N/A' }} {{ $incident->reportedBy->last_name ?? '' }}
                                    @if($incident->reportedBy)
                                        <span class="badge bg-info ms-1 text-capitalize">{{ $incident->reportedBy->role }}</span>
                                    @endif
                                </td>
                                <td class="text-end incident-actions">
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
            <div class="p-3 incident-pagination">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
