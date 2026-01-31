@extends('admin.dashboard')

@section('page-title', 'Pharmacy Management')
@section('page-description', 'Manage clinic medications and inventory')

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
<div class="card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 style="font-size: 1.2rem; font-weight: 600;">Medication Inventory</h3>
        <div>
            <a href="{{ route('admin.pharmacy.dispense') }}" class="btn btn-success me-2">
                <i class="fas fa-pills"></i> Dispense Medication
            </a>
            <a href="{{ route('admin.pharmacy.alerts') }}" class="btn btn-warning me-2">
                <i class="fas fa-exclamation-triangle"></i> Alerts
                @if($lowStockCount > 0 || $expiredCount > 0)
                    <span class="badge bg-danger ms-1">{{ $lowStockCount + $expiredCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.pharmacy.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Medication
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Min Stock</th>
                    <th>Price</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medications as $medication)
                    <tr>
                        <td>
                            <strong>{{ $medication->name }}</strong>
                            @if($medication->strength)
                                <br><small class="text-muted">{{ $medication->strength }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $medication->quantity <= $medication->min_stock ? 'bg-danger' : 'bg-success' }}">
                                {{ $medication->quantity }}
                            </span>
                        </td>
                        <td>{{ $medication->min_stock }}</td>
                        <td>${{ number_format($medication->unit_price, 2) }}</td>
                        <td>
                            @if($medication->expiry_date)
                                <span class="badge {{ $medication->isExpired() ? 'bg-danger' : ($medication->isExpiringSoon() ? 'bg-warning' : 'bg-info') }}">
                                    {{ $medication->expiry_date->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($medication->isExpired())
                                <span class="badge bg-danger">Expired</span>
                            @elseif($medication->isExpiringSoon())
                                <span class="badge bg-warning">Expiring Soon</span>
                            @elseif($medication->isLowStock())
                                <span class="badge bg-warning">Low Stock</span>
                            @else
                                <span class="badge bg-success">OK</span>
                            @endif
                        </td>
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
                        <td colspan="7" class="text-center">No medications found.</td>
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
