@extends('admin.dashboard')

@push('styles')
<style>
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

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .dashboard-card {
        background: #fff;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: #fff;
        font-size: 1.25rem;
    }

    .card-info h3 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 700;
    }

    .card-info p {
        margin: 0.25rem 0 0;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .content-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
    }

    .section-header h2 {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0 0 1rem;
        color: #2c3e50;
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
        text-align: left;
        white-space: nowrap;
    }

    .data-table tbody tr {
        background: #f9fafb;
    }

    .data-table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e3f2fd;
        color: #1565c0;
    }

    .actions {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        color: var(--light-text);
    }

    .btn-icon:hover {
        background: var(--paw-medium);
        color: var(--primary-orange);
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-flask"></i> Laboratory Management</h1>
        <p>Manage lab tests and lab requisitions (schema-based).</p>
    </div>
    <div style="display:flex; gap: .75rem; flex-wrap: wrap;">
        <a href="{{ route('admin.laboratory.tests.index') }}" class="btn btn-primary" style="text-decoration:none;">
            <i class="fas fa-vials"></i> Lab Tests
        </a>
        <a href="{{ route('admin.laboratory.requisitions.create') }}" class="btn btn-primary" style="text-decoration:none; background: linear-gradient(135deg,#4caf50,#388e3c);">
            <i class="fas fa-plus"></i> New Requisition
        </a>
    </div>
</div>

<div class="dashboard-cards">
    <div class="dashboard-card">
        <div class="card-icon" style="background: var(--primary-blue);">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="card-info">
            <h3>{{ $pendingRequisitions ?? 0 }}</h3>
            <p>Pending Requisitions</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: var(--accent-green);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-info">
            <h3>{{ $completedRequisitions ?? 0 }}</h3>
            <p>Completed Requisitions</p>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-icon" style="background: #ff9800;">
            <i class="fas fa-vials"></i>
        </div>
        <div class="card-info">
            <h3>{{ $totalLabTests ?? 0 }}</h3>
            <p>Available Lab Tests</p>
        </div>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <h2>Lab Requisitions</h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Test</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisitions as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>{{ optional(optional($req->medicalRecord)->pet)->name ?? 'N/A' }}</td>
                        <td>
                            {{ optional(optional(optional(optional($req->medicalRecord)->pet)->owner)->user)->first_name ?? 'N/A' }}
                            {{ optional(optional(optional(optional($req->medicalRecord)->pet)->owner)->user)->last_name ?? '' }}
                        </td>
                        <td>{{ $req->test->test_name ?? 'N/A' }}</td>
                        <td><span class="badge">{{ $req->status }}</span></td>
                        <td>{{ optional($req->requested_date)->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.laboratory.requisitions.show', $req->id) }}" class="btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.laboratory.requisitions.edit', $req->id) }}" class="btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.laboratory.requisitions.destroy', $req->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this requisition?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon" title="Delete" style="color: #dc3545;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No lab requisitions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($requisitions) && method_exists($requisitions, 'links'))
        <div style="margin-top: 1rem;">
            {{ $requisitions->links() }}
        </div>
    @endif
</div>
@endsection