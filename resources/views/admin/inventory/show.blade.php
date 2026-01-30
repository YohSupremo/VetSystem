@extends('admin.dashboard')

@section('page-title', 'Inventory Item Details')
@section('page-description', 'View detailed information about this inventory item')

@push('styles')
<style>
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
        border-bottom: 1px solid #f8f9fa;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 500;
        color: #6c757d;
    }
    
    .info-value {
        font-weight: 600;
        color: #2c3e50;
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
        background: #28a745;
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
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="header-title">
        <h1><i class="fas fa-box"></i> {{ $item->name }}</h1>
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
