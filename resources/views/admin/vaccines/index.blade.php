@extends('admin.dashboard')

@section('page-title', 'Vaccine Management')
@section('page-description', 'Manage vaccine catalog')

@section('content')
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;"><i class="fas fa-syringe"></i> Vaccines</h3>
        <a href="{{ route('admin.vaccines.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Vaccine
        </a>
    </div>

    <div class="card-body">
       

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Vaccine Name</th>
                        <th>Manufacturer</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vaccines as $vaccine)
                        <tr>
                            <td><strong>{{ $vaccine->vaccine_name }}</strong></td>
                            <td>{{ $vaccine->manufacturer ?? 'N/A' }}</td>
                            <td>{{ Str::limit($vaccine->description, 50) ?? 'N/A' }}</td>
                            <td>
                                @if($vaccine->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.vaccines.edit', $vaccine->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.vaccines.destroy', $vaccine->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this vaccine?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <i class="fas fa-info-circle"></i> No vaccines found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $vaccines->links() }}
    </div>
</div>

<style>
.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.badge {
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #28a745;
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
}

.table thead th {
    background: #f8f9fa;
    font-weight: 600;
    padding: 1rem;
    border-bottom: 2px solid #dee2e6;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
}

.table-hover tbody tr:hover {
    background: #f8f9fa;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 13px;
}
</style>
@endsection
