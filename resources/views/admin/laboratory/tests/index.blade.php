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
        gap: 1rem;
        flex-wrap: wrap;
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
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%);
        border: none;
        padding: 0.6rem 1.4rem;
        border-radius: 8px;
        font-weight: 500;
        color: #fff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-secondary {
        background: #6c757d;
        border: none;
        padding: 0.6rem 1.4rem;
        border-radius: 8px;
        font-weight: 500;
        color: #fff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .content-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .section-header h2 {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
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
    .actions {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        color: var(--light-text);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-icon:hover {
        background: var(--paw-medium);
        color: var(--primary-orange);
    }
    .btn-icon.text-danger:hover {
        color: #c82333;
    }
    .delete-form { display: inline; }
    .badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e3f2fd;
        color: #1565c0;
        text-transform: capitalize;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-vials"></i> Lab Tests</h1>
        <p>Manage the list of available laboratory tests and prices.</p>
    </div>
    <div style="display:flex; gap:.75rem; flex-wrap:wrap;">
        <a href="{{ route('admin.laboratory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Laboratory
        </a>
        <a href="{{ route('admin.laboratory.tests.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Lab Test
        </a>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <h2>Available Tests</h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($labTests as $test)
                    <tr>
                        <td><strong>{{ $test->test_name }}</strong></td>
                        <td><span class="badge">{{ $test->category }}</span></td>
                        <td>{{ $test->standard_price !== null ? '₱' . number_format($test->standard_price, 2) : '—' }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.laboratory.tests.show', $test->id) }}" class="btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.laboratory.tests.edit', $test->id) }}" class="btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.laboratory.tests.destroy', $test->id) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon text-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No lab tests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $labTests->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('.delete-form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Delete this lab test?')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection

