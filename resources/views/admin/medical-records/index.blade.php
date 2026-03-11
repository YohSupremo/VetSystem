@extends('admin.dashboard')

@section('page-title', 'Medical Records')
@section('page-description', 'Manage pet medical records and examinations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-file-medical"></i> Medical Records</h3>
                    <div class="d-flex gap-2">
                        @if($showTrash ?? false)
                            <a href="{{ route('admin.medical-records.index', request()->except('trash', 'page')) }}" class="btn btn-secondary btn-medical-secondary">
                                <i class="fas fa-arrow-left"></i> Back To Active
                            </a>
                        @else
                            <a href="{{ route('admin.medical-records.index', array_merge(request()->query(), ['trash' => 1])) }}" class="btn btn-secondary btn-medical-secondary">
                                <i class="fas fa-trash-restore"></i> View Trash
                            </a>
                        @endif
                        <a href="{{ route('admin.chronic-conditions.index') }}" class="btn btn-secondary btn-medical-secondary">
                            <i class="fas fa-notes-medical"></i> Chronic Conditions
                        </a>
                        <a href="{{ route('admin.pet-allergies.index') }}" class="btn btn-secondary btn-medical-secondary">
                            <i class="fas fa-allergies"></i> Pet Allergies
                        </a>
                        <a href="{{ route('admin.medical-records.create') }}" class="btn btn-primary btn-medical-primary">
                            <i class="fas fa-plus"></i> Add New Patient Record
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Search Bar --}}
                    <form method="GET" action="{{ route('admin.medical-records.index') }}" class="mb-3">
                        @if($showTrash ?? false)
                            <input type="hidden" name="trash" value="1">
                        @endif
                        <div class="medical-search-bar">
                            <i class="fas fa-search" style="padding-left: 12px; color: #A0AEC0;"></i>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search by pet name, owner, vet, complaint, or diagnosis..."
                            >
                            <button type="submit"><i class="fas fa-search"></i> Search</button>
                            @if(request('search'))
                                <a href="{{ route('admin.medical-records.index', ($showTrash ?? false) ? ['trash' => 1] : []) }}" class="clear-btn"><i class="fas fa-times"></i> Clear</a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Pet</th>
                                    <th>Owner</th>
                                    <th>Veterinarian</th>
                                    <th>Visit Date</th>
                                    <th>Complaint</th>
                                    <th>Diagnosis</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr>
                                        <td>{{ $record->id }}</td>
                                        <td>
                                            <strong>{{ $record->pet->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $record->pet->species ?? '' }}</small>
                                        </td>
                                        <td>
                                            @if($record->pet && $record->pet->owner && $record->pet->owner->user)
                                                {{ $record->pet->owner->user->first_name }} {{ $record->pet->owner->user->last_name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($record->veterinarian)
                                                <span class="badge badge-info">
                                                    Dr. {{ $record->veterinarian->first_name }} {{ $record->veterinarian->last_name }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $record->visit_date ? $record->visit_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ Str::limit($record->complaint, 40) }}</td>
                                        <td>{{ $record->diagnosis ? Str::limit($record->diagnosis, 40) : '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($showTrash ?? false)
                                                    <form action="{{ route('admin.medical-records.restore', $record->id) }}" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Restore this medical record?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.medical-records.pet', $record->pet_id) }}" 
                                                       class="btn btn-sm btn-primary" title="View Full History">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                    <a href="{{ route('admin.medical-records.show', $record->id) }}" 
                                                       class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.medical-records.edit', $record->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.medical-records.destroy', $record->id) }}" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to delete this medical record? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No medical records found.</p>
                                            <a href="{{ route('admin.medical-records.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Create First Record
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($records->hasPages())
                        <div class="mt-4">
                            {{ $records->links() }}
                        </div>
                    @endif

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background: linear-gradient(135deg, rgba(255, 247, 237, 0.95), rgba(253, 242, 248, 0.95));
    color: #1F2937;
    padding: 20px 25px;
    border-radius: 10px 10px 0 0 !important;
    border-bottom: 1px solid rgba(251, 146, 60, 0.2);
}

.card-title {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.card-body {
    padding: 25px;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    padding: 15px 20px !important;
    white-space: nowrap;
}

.table tbody td {
    padding: 15px 20px !important;
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: #f9f9f9;
}

.badge {
    padding: 6px 10px;
    font-size: 12px;
}

.badge-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.btn {
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #FB923C 0%, #EC4899 100%);
    border-color: transparent;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #F97316 0%, #DB2777 100%);
    border-color: transparent;
}

.btn-medical-primary {
    border: none;
    border-radius: 12px;
    font-weight: 700;
    box-shadow: 0 10px 20px rgba(236, 72, 153, 0.24);
}

.btn-medical-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 24px rgba(236, 72, 153, 0.3);
}

.btn-medical-secondary {
    border-radius: 12px;
    border: 1px solid #FED7AA;
    background: #FFF7ED;
    color: #C2410C;
    font-weight: 700;
}

.btn-medical-secondary:hover {
    background: #FFEDD5;
    border-color: #FDBA74;
    color: #9A3412;
}

.btn-group .btn {
    margin: 0 2px;
}

.alert {
    border-radius: 8px;
    border: none;
}

.medical-search-bar {
    display: flex;
    gap: 0;
    align-items: center;
    background: #fff;
    border-radius: 12px;
    padding: 4px;
    border: 2px solid #F5F5F7;
    transition: all 0.3s ease;
}

.medical-search-bar:focus-within {
    border-color: #FB923C;
    box-shadow: 0 0 0 4px rgba(251, 146, 60, 0.1);
}

.medical-search-bar input {
    border: none;
    background: transparent;
    padding: 10px 14px;
    font-size: 14px;
    flex: 1;
    min-width: 0;
    outline: none;
    color: #1F2937;
    font-family: 'DM Sans', sans-serif;
}

.medical-search-bar input::placeholder {
    color: #A0AEC0;
}

.medical-search-bar button,
.medical-search-bar a {
    border: none;
    background: #FB923C;
    color: white;
    padding: 10px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.medical-search-bar button:hover,
.medical-search-bar a:hover {
    background: #e87a35;
}

.medical-search-bar .clear-btn {
    background: #E2E8F0;
    color: #1F2937;
}

.medical-search-bar .clear-btn:hover {
    background: #CBD5E0;
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .table-responsive {
        font-size: 14px;
    }

    .medical-search-bar input {
        font-size: 16px;
    }
}
</style>
@endsection
