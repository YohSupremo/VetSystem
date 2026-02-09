@extends('admin.dashboard')

@section('page-title', 'Inventory Item Details')
@section('page-description', 'View detailed information about this inventory item')

@push('styles')
<style>
    .inventory-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .info-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
    }
    
    .info-card h5 {
        color: #2c3e50;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 500;
        color: #64748b;
    }
    
    .info-value {
        font-weight: 600;
        color: #0f172a;
    }
    
    .badge-low-stock {
        background: #dc3545;
        color: white;
    }
    
    .badge-expiring {
        background: #fd7e14;
        color: white;
    }
    
    .badge-good {
        background: #16a34a;
        color: white;
    }
    
    .stock-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    .stock-good { background: #28a745; }
    .stock-low { background: #ffc107; }
    .stock-critical { background: #dc3545; }

    .info-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .item-image {
        width: 100%;
        max-width: 280px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        object-fit: cover;
        background: #f8fafc;
    }

    .item-image-placeholder {
        width: 100%;
        max-width: 280px;
        height: 220px;
        border-radius: 16px;
        border: 1px dashed #cbd5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        background: #f8fafc;
        font-size: 2rem;
    }
</style>
@endpush

@section('content')
<div class="content-header inventory-hero">
    <div class="header-title">
        <div class="info-title">
            <h1 class="mb-0"><i class="fas fa-box"></i> {{ $item->name }}</h1>
        </div>
        <p class="text-muted">Inventory item details and stock information</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Item
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary ms-2">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

<div class="row">
    <!-- Item Information -->
    <div class="col-md-6">
        <div class="info-card">
            <h5><i class="fas fa-info-circle"></i> Item Information</h5>
            <div class="mb-3">
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="item-image">
                @else
                    <div class="item-image-placeholder"><i class="fas fa-image"></i></div>
                @endif
            </div>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $item->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Category:</span>
                <span class="info-value">
                    <span class="badge bg-secondary">{{ ucfirst($item->category) }}</span>
                </span>
            </div>
            @if($item->sku)
            <div class="info-row">
                <span class="info-label">SKU:</span>
                <span class="info-value">{{ $item->sku }}</span>
            </div>
            @endif
            @if($item->description)
            <div class="info-row">
                <span class="info-label">Description:</span>
                <span class="info-value">{{ $item->description }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Unit Price:</span>
                <span class="info-value">${{ number_format($item->unit_price, 2) }}</span>
            </div>
            @if($item->supplier)
            <div class="info-row">
                <span class="info-label">Supplier:</span>
                <span class="info-value">{{ $item->supplier->supplier_name }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Stock Information -->
    <div class="col-md-6">
        <div class="info-card">
            <h5><i class="fas fa-warehouse"></i> Stock Information</h5>
            <div class="info-row">
                <span class="info-label">Current Quantity:</span>
                <span class="info-value">
                    <strong>{{ $item->quantity }}</strong>
                    @if($item->isLowStock())
                        <span class="badge badge-low-stock ms-2">Low Stock</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Minimum Stock:</span>
                <span class="info-value">{{ $item->min_stock }}</span>
            </div>
            @if($item->requiresExpiryDate() && $item->expiry_date)
            <div class="info-row">
                <span class="info-label">Expiry Date:</span>
                <span class="info-value">
                    {{ $item->expiry_date->format('M d, Y') }}
                    @if($item->isExpired())
                        <span class="badge bg-danger ms-2">Expired</span>
                    @elseif($item->isExpiringSoon())
                        <span class="badge badge-expiring ms-2">Expiring Soon</span>
                    @endif
                </span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    @php
                        $stockStatus = 'good';
                        if ($item->isLowStock()) $stockStatus = 'low';
                        if ($item->quantity == 0) $stockStatus = 'critical';
                    @endphp
                    <span class="stock-indicator stock-{{ $stockStatus }}"></span>
                    @if($stockStatus == 'good')
                        <span class="badge badge-good">Good</span>
                    @elseif($stockStatus == 'low')
                        <span class="badge badge-low-stock">Low Stock</span>
                    @else
                        <span class="badge bg-danger">Out of Stock</span>
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="row">
    <div class="col-12">
        <div class="info-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Actions</h5>
                </div>
                <div>
                    <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Item
                    </a>
                    <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure you want to delete this item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Item
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
