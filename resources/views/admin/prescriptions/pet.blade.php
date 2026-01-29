@extends('admin.dashboard')

@section('page-title', 'Prescriptions - ' . $pet->name)
@section('page-description', 'View all prescriptions for ' . $pet->name)

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
        <div>
            <a href="{{ route('admin.prescriptions.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom:10px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h3 style="margin:0;">Prescriptions — {{ $pet->name }}</h3>
            <div style="margin-top:6px; color: var(--light-text); font-size: 13px;">
                Owner: {{ $pet->owner->user->first_name ?? '' }} {{ $pet->owner->user->last_name ?? 'Unknown' }} •
                Contact: {{ $pet->owner->user->contact_number ?? 'N/A' }}
            </div>
        </div>
        <a href="{{ route('admin.prescriptions.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Prescription
        </a>
    </div>

    <div class="card-body">
        @if($prescriptions->count() > 0)
            <div class="table-wrapper">
                <table class="simple-table">
                    <thead>
                        <tr>
                            <th>Medication</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Date</th>
                            <th>Dispensed</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescriptions as $prescription)
                            <tr>
                                <td><strong>{{ $prescription->medication }}</strong></td>
                                <td>{{ $prescription->dosage }}</td>
                                <td>{{ $prescription->frequency }}</td>
                                <td>{{ $prescription->duration_days }} days</td>
                                <td>{{ $prescription->created_at->format('M d, Y') }}</td>
                                <td>
                                    {{ $prescription->dispensed ? 'Yes' : 'No' }}
                                </td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.prescriptions.show', $prescription->id) }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.prescriptions.edit', $prescription->id) }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.prescriptions.destroy', $prescription->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="background:#ff6b6b; color:white;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($prescriptions->hasPages())
                <div style="margin-top:16px;">
                    {{ $prescriptions->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-info-circle"></i>
                <p>No prescriptions found for this pet.</p>
            </div>
        @endif
    </div>
</div>

<style>
.table-wrapper { overflow-x: auto; }
.simple-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
.simple-table thead th {
    text-align: left;
    font-size: 12px;
    color: var(--light-text);
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 0 12px 8px 12px;
    white-space: nowrap;
}
.simple-table tbody tr { background: var(--white); box-shadow: var(--shadow-soft); }
.simple-table tbody td { padding: 14px 12px; vertical-align: middle; color: var(--dark-text); }
.empty-state { text-align:center; padding: 40px 20px; color: var(--light-text); }
.empty-state i { font-size:48px; color: var(--soft-gray); display:block; margin-bottom: 12px; }
</style>
@endsection
