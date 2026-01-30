@extends('admin.dashboard')

@section('page-title', 'Inventory Management')
@section('page-description', 'Manage clinic inventory, stock levels, and suppliers')

@push('styles')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .stats-card h3 {
        font-size: 2rem;
        margin: 0;
        font-weight: 600;
    }

    .stats-card p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
    }

    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .table-responsive {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .badge-low-stock {
        background: #dc3545;
        color: white;
    }

    .badge-expiring {
        background: #fd7e14;
        color: white;
    }

    .stock-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    .stock-good { background: #28a745; }
    .stock-low { background: #ffc107; }
    .stock-critical { background: #dc3545; }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-boxes"></i> Inventory Management</h1>
        <p>Track stock levels, manage suppliers, and monitor inventory</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Item
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <h3>{{ $totalItems }}</h3>
            <p>Total Items</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <h3>{{ $lowStockItems }}</h3>
            <p>Low Stock Items</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <h3>{{ $expiringSoonItems }}</h3>
            <p>Expiring Soon</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <h3>{{ $inventoryItems->total() }}</h3>
            <p>Active Items</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <label for="category" class="form-label">Category</label>
            <select name="category" id="category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ ucfirst($category) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="low_stock"
                       {{ request()->boolean('low_stock') ? 'checked' : '' }}>
                <label class="form-check-label" for="low_stock">
                    Show Low Stock Only
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="expiring_soon" value="1" id="expiring_soon"
                       {{ request()->boolean('expiring_soon') ? 'checked' : '' }}>
                <label class="form-check-label" for="expiring_soon">
                    Show Expiring Soon Only
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Inventory Table -->
<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Item</th>
                <th>Category</th>
                <th>Stock Level</th>
                <th>Min Stock</th>
                <th>Unit Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventoryItems as $item)
                <tr>
                    <td>
                        <div>
                            <strong>{{ $item->name }}</strong>
                            @if($item->sku)
                                <br><small class="text-muted">SKU: {{ $item->sku }}</small>
                            @endif
                            @if($item->manufacturer)
                                <br><small class="text-muted">{{ $item->manufacturer }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ ucfirst($item->category) }}</span>
                    </td>
                    <td>
                        <strong>{{ $item->quantity }}</strong>
                        @if($item->isLowStock())
                            <span class="badge badge-low-stock ms-2">Low</span>
                        @endif
                    </td>
                    <td>{{ $item->min_stock }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>
                        @php
                            $stockStatus = 'good';
                            if ($item->isLowStock()) $stockStatus = 'low';
                            if ($item->quantity == 0) $stockStatus = 'critical';
                        @endphp
                        <span class="stock-indicator stock-{{ $stockStatus }}"></span>
                        @if($item->isExpiringSoon())
                            <span class="badge badge-expiring">Expiring Soon</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.inventory.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No inventory items found</p>
                        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add First Item
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($inventoryItems->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $inventoryItems->appends(request()->query())->links() }}
    </div>
@endif
@endsection
