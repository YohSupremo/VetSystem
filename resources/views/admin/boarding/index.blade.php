@extends('admin.dashboard')

@section('page-title', 'Boarding Management')
@section('page-description', 'Manage pet boarding and accommodations')

@push('styles')
<style>
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .dashboard-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        font-size: 1.5rem;
    }
    
    .card-info h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    
    .card-info p {
        margin: 0.25rem 0 0;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .header-title h1 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0 0 0.5rem;
        color: #2c3e50;
    }
    
    .header-title p {
        color: #6c757d;
        margin: 0;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
        cursor: pointer;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        color: white;
        text-decoration: none;
    }
    
    .search-input {
        padding: 0.65rem 1rem;
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        width: 250px;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        outline: none;
    }
    
    .content-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .section-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    
    .section-header h2 i {
        margin-right: 0.75rem;
        color: #4e73df;
    }
    
    .search-form {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .search-form input {
        flex: 1;
        max-width: 300px;
    }
    
    .search-form button {
        padding: 0.65rem 1rem;
        background: #4e73df;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .search-form button:hover {
        background: #224abe;
    }
    
    .delete-form {
        display: inline;
    }
</style>
@endpush
@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-home"></i> Pet Boarding Management</h1>
        <p>Manage all boarding activities and cage assignments in one place</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.boarding.new-boarding') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Boarding
        </a>
    </div>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #f6c23e 0%, #e0a800 100%);">
            <i class="fas fa-paw"></i>
        </div>
        <div class="card-info">
            <h3>{{ $currentBoardings ?? 0 }}</h3>
            <p>Current Boardings</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);">
            <i class="fas fa-home"></i>
        </div>
        <div class="card-info">
            <h3>{{ $availableCages ?? 0 }}</h3>
            <p>Available Cages</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="card-info">
            <h3>{{ $upcomingCheckouts ?? 0 }}</h3>
            <p>Checkouts Today</p>
        </div>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-list"></i> Current Boardings</h2>
        <div class="section-actions">
            <form method="GET" action="{{ route('admin.boarding.index') }}" class="search-form">
                <input type="text" name="search" class="search-input" placeholder="Search boardings..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.boarding.index') }}" class="btn btn-primary" style="background: #6c757d;">
                        Clear
                    </a>
                @endif
            </form>
        </div>
    </div>
    
    <div class="table-responsive">
        <table id="boardingsTable" class="data-table">
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Cage</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($boardings as $boarding)
                <tr>
                    <td>
                        <div class="pet-info">
                            @php $pet = $boarding->pet; @endphp
                            <img src="{{ $pet ? $pet->photo_url : 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\" viewBox=\"0 0 200 200\"><rect fill=\"#f0f0f0\" width=\"200\" height=\"200\"/><text x=\"50%\" y=\"50%\" font-size=\"80\" text-anchor=\"middle\" dominant-baseline=\"middle\" fill=\"#ccc\">🐾</text></svg>') }}" alt="{{ $pet->name ?? 'Pet' }}" class="pet-avatar" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiB2aWV3Qm94PSIwIDAgMjAwIDIwMCI+PHJlY3QgZmlsbD0iI2YwZjBmMCIgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1zaXplPSI4MCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0iI2NjYyI+8J+QrjwvdGV4dD48L3N2Zz4='">
                            <div>
                                <strong>{{ $pet?->name ?? 'N/A' }}</strong>
                                <span class="text-muted">{{ $pet?->breed ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ ($pet && $pet->owner && $pet->owner->user) ? $pet->owner->user->first_name . ' ' . $pet->owner->user->last_name : 'Unknown Owner' }}</td>
                    <td>
                        <span class="badge badge-cage">
                            {{ $boarding->cage?->cage_code ?? 'N/A' }}
                        </span>
                    </td>
                    <td>{{ $boarding->start_date ? \Carbon\Carbon::parse($boarding->start_date)->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $boarding->end_date ? \Carbon\Carbon::parse($boarding->end_date)->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        @php
                            $derivedStatus = $boarding->getAttribute('derived_status');
                            if ($derivedStatus) {
                                $statusText = $derivedStatus;
                                $statusClass = $boarding->getAttribute('derived_status_class') ?? 'secondary';
                            } else {
                                $isActive = $boarding->isActive();
                                $statusText = $isActive ? 'Active' : (now()->toDateString() > $boarding->end_date ? 'Completed' : 'Upcoming');
                                $statusClass = match ($statusText) {
                                    'Active' => 'success',
                                    'Upcoming' => 'warning',
                                    'Completed' => 'secondary',
                                    default => 'secondary',
                                };
                            }
                        @endphp
                        <span class="badge badge-{{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="actions">
                        @if($boarding->id)
                            <a href="{{ route('admin.boarding.show', $boarding->id) }}" class="btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.boarding.edit', $boarding->id) }}" class="btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.boarding.destroy', $boarding->id) }}" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this boarding record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon text-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @elseif($boarding->appointment)
                            <a href="{{ route('admin.appointments.show', $boarding->appointment->id) }}" class="btn-icon" title="View Appointment">
                                <i class="fas fa-calendar-check"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No boardings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>



<style>
.table-responsive {
    margin-top: 0.5rem;
}

.data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 6px;
}

.data-table thead th {
    padding: 0.75rem 1rem;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #6c757d;
    border-bottom: 1px solid #e9ecef;
}

.data-table tbody tr {
    background: #f9fafb;
}

.data-table tbody td {
    padding: 0.75rem 1rem;
    vertical-align: middle;
}

.pet-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pet-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.badge {
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.badge-cage {
    background: #4A90E2;
}

.badge-danger {
    background: #dc3545;
}

.badge-success { background-color: var(--accent-green); }
.badge-warning { background-color: #FFC107; color: #000; }
.badge-danger { background-color: #F44336; }
.badge-secondary { background-color: #6C757D; }

.actions {
    display: flex;
    gap: 6px;
    justify-content: flex-start;
}

.btn-icon {
    background: none;
    border: none;
    color: var(--light-text);
    cursor: pointer;
    padding: 6px;
    border-radius: 4px;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: var(--paw-medium);
    color: var(--primary-orange);
}

.btn-icon.text-danger:hover {
    color: #F44336;
}

.text-muted {
    color: var(--light-text);
    font-size: 0.85em;
    display: block;
}
</style>
@endsection
