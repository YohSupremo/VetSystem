@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Incident Reports</h1>
            <p class="text-muted">Report accidents or urgent medical incidents for your pets.</p>
        </div>
        <a href="{{ route('customer.incidents.create') }}" class="btn btn-primary">
            <i class="fas fa-exclamation-triangle me-2"></i>Report Incident
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($incidents->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-file-medical fa-3x text-muted"></i>
            </div>
            <h5>No incident reports yet</h5>
            <p class="text-muted">Create an incident report if your pet needs urgent attention.</p>
            <a href="{{ route('customer.incidents.create') }}" class="btn btn-outline-primary mt-2">
                Report Incident
            </a>
        </div>
    @else
        <div class="table-responsive bg-white shadow-sm rounded-3">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Incident #</th>
                        <th>Date</th>
                        <th>Pet</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Status</th>
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
                                <span class="badge bg-secondary text-capitalize">{{ $incident->status }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('customer.incidents.show', $incident->id) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
