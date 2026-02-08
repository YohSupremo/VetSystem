@extends('admin.dashboard')

@section('page-title', 'Inventory Management')
@section('page-description', 'Manage clinic inventory, stock levels, and suppliers')

@push('styles')
<style>
    .inventory-hero {
        background: linear-gradient(120deg, #eef2ff 0%, #f8fafc 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .inventory-hero::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -80px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.18), rgba(99, 102, 241, 0));
        pointer-events: none;
    }

    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .stats-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .stats-card h3 {
        font-size: 2rem;
        margin: 0;
        font-weight: 700;
        color: #0f172a;
    }

    .stats-card p {
        margin: 0.35rem 0 0;
        color: #64748b;
        font-weight: 500;
    }

    .stats-card .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1d4ed8;
        background: rgba(37, 99, 235, 0.1);
        font-size: 1.1rem;
    }

    .stats-card.low { background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%); }
    .stats-card.expiring { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); }
    .stats-card.active { background: linear-gradient(135deg, #ecfdf3 0%, #dcfce7 100%); }

    .filter-select {
        padding: 0.45rem 0.75rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        font-size: 0.9rem;
        min-width: 170px;
    }

    .table-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .table-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: left;
        background: #f8fafc;
    }

    .table-card-header-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-card-filters {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .table-responsive {
        background: transparent;
    }

    .table thead th {
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        text-align: center;
    }

    .table colgroup col.item-col { width: 24%; }
    .table colgroup col.category-col { width: 10%; }
    .table colgroup col.stock-col { width: 10%; }
    .table colgroup col.min-col { width: 8%; }
    .table colgroup col.price-col { width: 10%; }
    .table colgroup col.expiry-col { width: 16%; }
    .table colgroup col.status-col { width: 12%; }
    .table colgroup col.actions-col { width: 10%; }

    .table tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    .category-badge {
        padding: 0.4rem 0.7rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.02em;
    }

    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.02em;
    }

    .badge-low-stock {
        background: #dc2626;
        color: white;
    }

    .badge-expiring {
        background: #ea580c;
        color: white;
    }

    .stock-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    .stock-good { background: #22c55e; }
    .stock-low { background: #f59e0b; }
    .stock-critical { background: #ef4444; }

    .action-group .btn {
        border-radius: 10px;
    }

    .action-group {
        display: flex;
        gap: 0.35rem;
        justify-content: center;
        flex-wrap: nowrap;
    }

    .action-group .btn {
        min-width: 36px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        width: 100%;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5f5;
        margin-bottom: 20px;
        display: block;
    }

    .expiry-meta {
        display: inline-flex;
        flex-direction: column;
        gap: 0.25rem;
        align-items: center;
    }

    .expiry-date {
        font-weight: 600;
        color: #0f172a;
    }
</style>
@endpush

@section('content')
<div class="content-header inventory-hero">
    <div class="header-title">
        <span class="hero-pill"><i class="fas fa-warehouse"></i> Stock Overview</span>
        <h1 class="mt-2"><i class="fas fa-boxes"></i> Inventory Management</h1>
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
    <div class="col-lg-3 col-md-6">
        <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $totalItems }}</h3>
                    <p>Total Items</p>
                </div>
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card low">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $lowStockItems }}</h3>
                    <p>Low Stock Items</p>
                </div>
                <div class="stat-icon" style="color:#b91c1c;background:rgba(220,38,38,0.12);"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card expiring">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $expiringSoonItems }}</h3>
                    <p>Expiring Soon</p>
                </div>
                <div class="stat-icon" style="color:#c2410c;background:rgba(234,88,12,0.12);"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $expiredItems }}</h3>
                    <p>Expired Items</p>
                </div>
                <div class="stat-icon" style="color:#b91c1c;background:rgba(220,38,38,0.12);"><i class="fas fa-ban"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stats-card active">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $inventoryItems->total() }}</h3>
                    <p>Active Items</p>
                </div>
                <div class="stat-icon" style="color:#15803d;background:rgba(34,197,94,0.12);"><i class="fas fa-check"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Table -->
<div class="table-card">
    <div class="table-card-header">
        <div>
            <h5 class="mb-0">Inventory Items</h5>
            <small class="text-muted">Showing {{ $inventoryItems->count() }} of {{ $inventoryItems->total() }}</small>
        </div>
        <div class="table-card-header-actions">
            <form method="GET" class="table-card-filters">
                <select name="category" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </form>
        </div>
    </div>
    @if($inventoryItems->isEmpty())
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No inventory items yet</h3>
            <p>Add your first inventory item to see it listed here.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <colgroup>
                    <col class="item-col">
                    <col class="category-col">
                    <col class="stock-col">
                    <col class="min-col">
                    <col class="price-col">
                    <col class="expiry-col">
                    <col class="status-col">
                    <col class="actions-col">
                </colgroup>
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Stock Level</th>
                        <th>Min Stock</th>
                        <th>Unit Price</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventoryItems as $item)
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
                                @php
                                    $categoryKey = strtolower($item->category ?? '');
                                    $categoryStyles = [
                                        'medicine' => 'bg-primary text-white',
                                        'vaccine' => 'bg-info text-dark',
                                        'supply' => 'bg-secondary text-white',
                                        'food' => 'bg-warning text-dark',
                                        'other' => 'bg-light text-dark'
                                    ];
                                    $categoryClass = $categoryStyles[$categoryKey] ?? 'bg-light text-dark';
                                @endphp
                                <span class="category-badge {{ $categoryClass }}">{{ ucfirst($item->category) }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $item->quantity }}</strong>
                                </div>
                                @if($item->isLowStock())
                                    <div class="text-danger small">Low Stock</div>
                                @endif
                            </td>
                            <td>{{ $item->min_stock }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>
                                @if($item->expiry_date)
                                    <div class="expiry-meta">
                                        <span class="expiry-date">{{ $item->expiry_date->format('M d, Y') }}</span>
                                        @if($item->isExpired())
                                            <span class="status-badge bg-danger text-white">Expired</span>
                                        @elseif($item->isExpiringSoon())
                                            <span class="status-badge bg-warning text-dark">Expiring Soon</span>
                                        @else
                                            <span class="status-badge bg-success text-white">Valid</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $stockStatus = 'good';
                                    if ($item->isLowStock()) $stockStatus = 'low';
                                    if ($item->quantity == 0) $stockStatus = 'critical';
                                    $statusLabel = $stockStatus == 'good' ? 'In Stock' : ($stockStatus == 'low' ? 'Low Stock' : 'Out of Stock');
                                    $statusClass = $stockStatus == 'good' ? 'text-success' : ($stockStatus == 'low' ? 'text-warning' : 'text-danger');
                                @endphp
                                @if($item->isExpired())
                                    <span class="status-badge bg-danger text-white">Expired</span>
                                @elseif($item->isExpiringSoon())
                                    <span class="status-badge bg-warning text-dark">Expiring Soon</span>
                                @else
                                    <span class="status-badge bg-success text-white">Valid</span>
                                @endif
                                <div class="small {{ $statusClass }}">{{ $statusLabel }}</div>
                            </td>
                            <td>
                                <div class="btn-group action-group" role="group">
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
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Pagination -->
@if($inventoryItems->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $inventoryItems->appends(request()->query())->links() }}
    </div>
@endif
@endsection
