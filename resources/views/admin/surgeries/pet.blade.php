@extends('admin.dashboard')

@section('page-title', 'Surgery Records for ' . ($pet->name ?? 'Pet'))
@section('page-description', 'All surgeries for ' . ($pet->name ?? 'pet'))

@section('content')
<div class="container-fluid">
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
        <div>
            <a href="{{ route('admin.surgeries.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom:10px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h3 style="margin:0;">Surgery Records — {{ $pet->name ?? 'N/A' }}</h3>
            <div style="margin-top:6px; color: var(--light-text); font-size: 13px;">
                Owner: {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}
            </div>
        </div>
        <a href="{{ route('admin.surgeries.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Schedule New Surgery
        </a>
    </div>

    @if($surgeries->count() > 0)
    <div class="card-body">
    <div class="table-wrapper">
        <table class="simple-table">
            <thead>
                <tr>
                    <th>Procedure</th>
                    <th>Surgeon</th>
                    <th>Scheduled Date</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($surgeries as $surgery)
                @php $isVirtual = (bool) $surgery->getAttribute('is_virtual'); @endphp
                <tr>
                    <td>
                        <strong>{{ $surgery->procedure_name ?? 'Appointment (Surgery)' }}</strong>
                        @if(!$isVirtual && $surgery->anesthesia_type)
                        <br><small class="text-muted">Anesthesia: {{ $surgery->anesthesia_type }}</small>
                        @endif
                    </td>
                    <td>
                        @if($isVirtual && $surgery->appointment && $surgery->appointment->veterinarian)
                            Dr. {{ $surgery->appointment->veterinarian->first_name }} {{ $surgery->appointment->veterinarian->last_name }}
                        @else
                            {{ $surgery->surgeon ? 'Dr. ' . $surgery->surgeon->first_name . ' ' . $surgery->surgeon->last_name : 'N/A' }}
                        @endif
                    </td>
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
                    <td style="text-align:right;">
                        @if(!$isVirtual)
                            <a href="{{ route('admin.surgeries.show', $surgery->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.surgeries.edit', $surgery->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.surgeries.destroy', $surgery->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this surgery record? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm" style="background:#ff6b6b; color:white;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @elseif($surgery->appointment)
                            <a href="{{ route('admin.appointments.show', $surgery->appointment->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-calendar-check"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($surgeries->hasPages())
        <div style="margin-top:16px;">
            {{ $surgeries->links() }}
        </div>
    @endif
    </div>
    @else
    <div class="card-body">
        <div class="empty-state">
            <i class="fas fa-info-circle"></i>
            <p>No surgeries found for this pet.</p>
            <a href="{{ route('admin.surgeries.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary" style="margin-top:10px;">
                <i class="fas fa-plus"></i> Schedule one now
            </a>
        </div>
    </div>
    @endif
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

/* Status Badges */
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    display: inline-block;
}
.bg-primary { background-color: #4e73df; } /* Blue for Scheduled */
.bg-warning { background-color: #f6c23e; color: #333; } /* Yellow for In Progress */
.bg-success { background-color: #1cc88a; } /* Green for Completed */
.bg-danger { background-color: #e74a3b; } /* Red for Cancelled */
.bg-secondary { background-color: #858796; } /* Default */
</style>
@endsection
