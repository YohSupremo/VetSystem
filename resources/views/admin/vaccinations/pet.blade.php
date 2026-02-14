@extends('admin.dashboard')

@section('page-title', 'Vaccination Records for ' . ($pet->name ?? 'Pet'))
@section('page-description', 'All vaccinations for ' . ($pet->name ?? 'pet'))

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
        <div>
            <a href="{{ route('admin.vaccinations.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom:10px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h3 style="margin:0;">Vaccination Records — {{ $pet->name ?? 'N/A' }}</h3>
            <div style="margin-top:6px; color: var(--light-text); font-size: 13px;">
                Owner: {{ $pet->owner->user->first_name ?? 'Unknown' }} {{ $pet->owner->user->last_name ?? '' }}
            </div>
        </div>
        <a href="{{ route('admin.vaccinations.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Record New Vaccination
        </a>
    </div>

    <div class="card-body">
        @if($vaccinations->count() > 0)
            <div class="table-wrapper">
                <table class="simple-table">
                    <thead>
                        <tr>
                            <th>Vaccine</th>
                            <th>Vaccinated</th>
                            <th>Next Due</th>
                            <th>Veterinarian</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vaccinations as $vaccination)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color: var(--dark-text);">
                                        {{ $vaccination->vaccine->vaccine_name ?? 'N/A' }}
                                    </div>
                                    @if($vaccination->batch_number)
                                        <div style="font-size:12px; color: var(--light-text);">
                                            Batch: {{ $vaccination->batch_number }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $vaccination->administered_date ? \Carbon\Carbon::parse($vaccination->administered_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $vaccination->next_due_date ? \Carbon\Carbon::parse($vaccination->next_due_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $vaccination->administeredBy ? 'Dr. ' . $vaccination->administeredBy->first_name . ' ' . $vaccination->administeredBy->last_name : 'N/A' }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.vaccinations.show', $vaccination->id) }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.vaccinations.edit', $vaccination->id) }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.vaccinations.destroy', $vaccination->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this vaccination record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="background:#ff6b6b; color:white; border:none;">
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
                <div style="margin-top:16px;">
                    {{ $vaccinations->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-info-circle"></i>
                <p>No vaccinations found for this pet.</p>
                <a href="{{ route('admin.vaccinations.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary" style="margin-top:10px;">
                    <i class="fas fa-plus"></i> Record one now
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.table-wrapper {
    overflow-x: auto;
}

.simple-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
}

.simple-table thead th {
    text-align: left;
    font-size: 12px;
    color: var(--light-text);
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 0 12px 8px 12px;
    white-space: nowrap;
}

.simple-table tbody tr {
    background: var(--white);
    box-shadow: var(--shadow-soft);
}

.simple-table tbody td {
    padding: 14px 12px;
    vertical-align: middle;
    color: var(--dark-text);
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--light-text);
}

.empty-state i {
    font-size: 48px;
    color: var(--soft-gray);
    display: block;
    margin-bottom: 12px;
}
</style>
@endsection
