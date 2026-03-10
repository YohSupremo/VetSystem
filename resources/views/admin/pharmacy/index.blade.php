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
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: #fff;
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #fff;
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
        border-spacing: 0;
    }

    .data-table thead th {
        background: #f8f9fa;
        padding: 1rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        text-align: left;
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .data-table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }

    .medication-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .medication-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }

    .medication-details {
        flex: 1;
    }

    .medication-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .medication-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .stock-info {
        text-align: center;
        min-width: 80px;
    }

    .stock-quantity {
        font-weight: 700;
        font-size: 1.1rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
    }

    .stock-normal {
        background: #d4edda;
        color: #155724;
    }

    .stock-low {
        background: #f8d7da;
        color: #721c24;
    }

    .stock-out {
        background: #f5c6cb;
        color: #856404;
    }

    .price {
        font-weight: 600;
        color: #2c3e50;
    }

    .expiry-info {
        text-align: center;
        min-width: 100px;
    }

    .expiry-normal {
        color: #28a745;
        font-weight: 500;
    }

    .expiry-warning {
        color: #ffc107;
        font-weight: 600;
    }

    .expiry-expired {
        color: #dc3545;
        font-weight: 700;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .status-ok {
        background: #d4edda;
        color: #155724;
    }

    .status-low {
        background: #fff3cd;
        color: #856404;
    }

    .status-expired {
        background: #f8d7da;
        color: #721c24;
    }

    .actions {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 6px;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        background: #e9ecef;
        color: #495057;
    }

    .btn-icon.edit:hover {
        background: #007bff;
        color: #fff;
    }

    .btn-icon.delete:hover {
        background: #dc3545;
        color: #fff;
    }

    .alert-badge {
        background: #dc3545;
        color: #fff;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        margin-left: 0.5rem;
    }
</style>
@endpush

@section('content')
@php $showTrash = request()->boolean('trash'); @endphp
<div class="card">
    <div class="content-header">
        <div class="header-title">
            <h1><i class="fas fa-capsules me-2"></i>Pharmacy Management</h1>
            <p class="mb-0">Manage clinic medications and inventory</p>
        </div>
        <div>
            @if($showTrash)
                <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back To Active
                </a>
            @else
                <a href="{{ route('admin.pharmacy.index', ['trash' => 1]) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-trash-restore"></i> View Trash
                </a>
            @endif
            <a href="{{ route('admin.pharmacy.dispense') }}" class="btn btn-success me-2">
                <i class="fas fa-pills"></i> Dispense Medication
            </a>
            <a href="{{ route('admin.pharmacy.alerts') }}" class="btn btn-warning me-2">
                <i class="fas fa-exclamation-triangle"></i> Inventory Alerts
                @if($lowStockCount > 0 || $expiredCount > 0)
                    <span class="alert-badge">{{ $lowStockCount + $expiredCount }}</span>
                @endif
            </a>
            @unless($showTrash)
                <a href="{{ route('admin.pharmacy.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Medication
                </a>
            @endunless
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Category Filter -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.pharmacy.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="category" class="form-label fw-semibold">Filter by Category</label>
                <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ $selectedCategory == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($selectedCategory)
                <div class="col-md-2">
                    <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear Filter
                    </a>
                </div>
            @endif
        </form>
    </div>

    <div class="table-responsive mt-4">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Category</th>
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
                    @php
                        $totalStock = $medication->inventoryStocks->sum('quantity');
                        $minStock = $medication->inventoryStocks->max('min_stock') ?? 10;
                        $earliestExpiry = $medication->inventoryStocks
                            ->whereNotNull('expiry_date')
                            ->min('expiry_date');
                        
                        $isLowStock = $totalStock <= $minStock;
                        $isExpired = $medication->isExpired();
                        $isExpiringSoon = $medication->isExpiringSoon();
                    @endphp
                    
                    <tr>
                        <td>
                            <div class="medication-info">
                                @if($medication->image_path)
                                    <img src="{{ asset($medication->image_path) }}" alt="{{ $medication->name }}" class="medication-image">
                                @else
                                    <div class="medication-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-capsules text-muted"></i>
                                    </div>
                                @endif
                                <div class="medication-details">
                                    <div class="medication-name">{{ $medication->name }}</div>
                                    <div class="medication-meta">{{ $medication->sku ?? 'No SKU' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $categoryColors = [
                                    'medicine' => 'bg-primary',
                                    'vaccine' => 'bg-success',
                                    'food' => 'bg-warning',
                                    'supply' => 'bg-info'
                                ];
                                $badgeColor = $categoryColors[$medication->category] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeColor }} text-white">{{ ucfirst($medication->category) }}</span>
                        </td>
                        <td class="stock-info">
                            @if($totalStock == 0)
                                <span class="stock-quantity stock-out">0</span>
                            @elseif($isLowStock)
                                <span class="stock-quantity stock-low">{{ $totalStock }}</span>
                            @else
                                <span class="stock-quantity stock-normal">{{ $totalStock }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $minStock }}</td>
                        <td class="price">₱{{ number_format($medication->unit_price, 2) }}</td>
                        <td class="expiry-info">
                            @if($earliestExpiry)
                                @if($isExpired)
                                    <span class="expiry-expired">{{ $earliestExpiry->format('M d, Y') }}</span>
                                @elseif($isExpiringSoon)
                                    <span class="expiry-warning">{{ $earliestExpiry->format('M d, Y') }}</span>
                                @else
                                    <span class="expiry-normal">{{ $earliestExpiry->format('M d, Y') }}</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($isExpired)
                                <span class="status-badge status-expired">Expired</span>
                            @elseif($isExpiringSoon)
                                <span class="status-badge status-low">Expiring Soon</span>
                            @elseif($isLowStock)
                                <span class="status-badge status-low">Low Stock</span>
                            @else
                                <span class="status-badge status-ok">OK</span>
                            @endif
                        </td>
                        <td class="actions">
                            <a href="{{ route('admin.pharmacy.show', $medication->id) }}" class="btn-icon" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($showTrash)
                                <form method="POST" action="{{ route('admin.pharmacy.restore', $medication->id) }}" onsubmit="return confirm('Restore this medication?')">
                                    @csrf
                                    <button type="submit" class="btn-icon" title="Restore" style="color:#0f766e;">
                                        <i class="fas fa-trash-restore"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.pharmacy.edit', $medication->id) }}" class="btn-icon edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.pharmacy.destroy', $medication->id) }}" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this medication?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <div class="h5 mb-0">No medications found</div>
                            <p class="text-muted">Get started by adding your first medication to the inventory.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

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
