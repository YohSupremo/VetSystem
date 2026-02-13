@extends('admin.dashboard')

@section('page-title', 'Inventory Alerts')
@section('page-description', 'View low stock and expired medication alerts')

@push('styles')
<style>
    .alerts-container {
        max-width: 900px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.75rem;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .page-header h1 {
        font-size: 1.6rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    .page-header p {
        color: #6c757d;
        margin: 0;
    }
    .alert-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .alert-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
        border-left: 4px solid #FF8C42;
        transition: all 0.3s ease;
    }
    .alert-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 140, 66, 0.1);
    }
    .alert-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .alert-title h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    .alert-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .alert-actions {
        display: flex;
        gap: 0.5rem;
    }
    .btn {
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.4rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-primary {
        background: linear-gradient(135deg, #FF8C42 0%, #FF6B1B 100%);
        color: #fff;
        border: none;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
    }
    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(90, 98, 123, 0.3);
    }
    .status-badge {
        background: #dc3545;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .alert-danger {
        background: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    .alert-warning {
        background: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }
    .alert-info {
        background: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    .medication-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .medication-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
        border-left: 4px solid #4e73df;
        transition: all 0.3s ease;
    }
    .medication-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.1);
    }
    .medication-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    .medication-title h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    .medication-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #6c757d;
    }
    .stock-status {
        font-size: 0.9rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }
    .status-low {
        background: #fff3cd;
        color: #155724;
    }
    .status-expired {
        background: #f8d7da;
        color: #721c24;
    }
    .status-expiring-soon {
        background: #ffeaa7;
        color: #856404;
    }
</style>
@endpush

@section('content')
<div class="alerts-container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-exclamation-triangle"></i> Inventory Alerts</h1>
            <p>View low stock and expired medication alerts</p>
        </div>
        <div>
            <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Pharmacy
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="alert-grid">
        @if($lowStockItems->isEmpty() && $expiredItems->isEmpty() && $expiringSoonItems->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-check-circle"></i>
                <strong>All Clear!</strong>
                <p>No inventory alerts at this time. All medications are properly stocked and within expiry dates.</p>
            </div>
        @else
            <!-- Low Stock Alerts -->
            @if(!$lowStockItems->isEmpty())
                <div class="alert-card alert-danger">
                    <div class="alert-header">
                        <div>
                            <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h3>
                            <div class="alert-meta">
                                <span><i class="fas fa-box"></i> {{ $lowStockItems->count() }} items</span>
                                <span><i class="fas fa-calendar"></i> {{ now()->format('M j, Y') }}</span>
                            </div>
                        </div>
                        <div class="alert-actions">
                            <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> View All Items
                            </a>
                        </div>
                    </div>
                    <div class="medication-info">
                        @foreach($lowStockItems as $item)
                            <div><strong>{{ $item->name }}</strong> ({{ $item->sku ?? 'N/A' }})</div>
                            <div><strong>Current Stock:</strong> {{ $item->quantity }}</div>
                            <div><strong>Min Stock:</strong> {{ $item->min_stock }}</div>
                            <div><strong>Location:</strong> {{ $item->location ?? 'N/A' }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Expired Items -->
            @if(!$expiredItems->isEmpty())
                <div class="alert-card alert-danger">
                    <div class="alert-header">
                        <div>
                            <h3><i class="fas fa-times-circle"></i> Expired Items</h3>
                            <div class="alert-meta">
                                <span><i class="fas fa-box"></i> {{ $expiredItems->count() }} items</span>
                                <span><i class="fas fa-calendar"></i> {{ now()->format('M j, Y') }}</span>
                            </div>
                        </div>
                        <div class="alert-actions">
                            <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> View All Items
                            </a>
                        </div>
                    </div>
                    <div class="medication-info">
                        @foreach($expiredItems as $item)
                            <div><strong>{{ $item->name }}</strong> ({{ $item->sku ?? 'N/A' }})</div>
                            <div><strong>Expired Date:</strong> {{ $item->expiry_date->format('M j, Y') }}</div>
                            <div><strong>Location:</strong> {{ $item->location ?? 'N/A' }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Expiring Soon Items -->
            @if(!$expiringSoonItems->isEmpty())
                <div class="alert-card alert-warning">
                    <div class="alert-header">
                        <div>
                            <h3><i class="fas fa-clock"></i> Expiring Soon</h3>
                            <div class="alert-meta">
                                <span><i class="fas fa-box"></i> {{ $expiringSoonItems->count() }} items</span>
                                <span><i class="fas fa-calendar"></i> {{ now()->format('M j, Y') }}</span>
                            </div>
                        </div>
                        <div class="alert-actions">
                            <a href="{{ route('admin.pharmacy.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> View All Items
                            </a>
                        </div>
                    </div>
                    <div class="medication-info">
                        @foreach($expiringSoonItems as $item)
                            <div><strong>{{ $item->name }}</strong> ({{ $item->sku ?? 'N/A' }})</div>
                            <div><strong>Expires:</strong> {{ $item->expiry_date->format('M j, Y') }}</div>
                            <div><strong>Days Until Expiry:</strong> {{ $item->expiry_date->diffInDays(now()) }} days</div>
                            <div><strong>Location:</strong> {{ $item->location ?? 'N/A' }}</div>
                            <div><strong>Stock Status:</strong>
                                <span class="stock-status status-expiring-soon">
                                    Expiring Soon
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>No medications found</strong>
                <p>There are no medications in the inventory to check for alerts.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
