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

    .btn-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        padding: 0.6rem 1.4rem;
        border-radius: 8px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #fff;
        text-decoration: none;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
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

    .btn-icon.text-danger:hover {
        color: #c82333;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-capsules"></i> Pharmacy Management</h1>
        <p>Manage clinic medications.</p>
    </div>
    <div>
        <a href="{{ route('admin.pharmacy.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Medication
        </a>
    </div>
</div>

<div class="card">
    <h2 style="font-size:1.2rem; font-weight:600; margin-bottom:1rem;">Medication List</h2>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medications as $medication)
                    <tr>
                        <td>{{ $medication->name }}</td>
                        <td>{{ $medication->sku ?? '—' }}</td>
                        <td>{{ $medication->unit_price !== null ? number_format($medication->unit_price, 2) : '—' }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.pharmacy.show', $medication->id) }}" class="btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pharmacy.edit', $medication->id) }}" class="btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.pharmacy.destroy', $medication->id) }}" class="delete-form">
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
                        <td colspan="4" class="text-center">No medications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('.delete-form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var ok = confirm('Are you sure you want to delete this medication?');
            if (!ok) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection
