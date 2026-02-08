@extends('admin.dashboard')

@section('page-title', 'Inventory Report')
@section('page-description', 'Stock health and dispensing trends')

@section('content')
<style>
    .report-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .report-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.9rem 1rem;
        background: #f8fafc;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 0.75rem;
    }
</style>

<div class="content-header report-hero">
    <div class="header-title">
        <h1><i class="fas fa-warehouse"></i> Inventory Report</h1>
        <p>Monitor stock levels, expiry risk, and dispensing volume</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
    </div>
</div>

<div class="report-card">
    <form class="row g-3 align-items-end" method="GET">
        <div class="col-md-4">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') ?? now()->subMonth()->toDateString() }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') ?? now()->toDateString() }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">View</label>
            <select name="report_type" class="form-select">
                <option value="summary" {{ $reportType === 'summary' ? 'selected' : '' }}>Summary</option>
                <option value="detailed" {{ $reportType === 'detailed' ? 'selected' : '' }}>Detailed</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100"><i class="fas fa-filter"></i> Apply</button>
        </div>
    </form>
</div>

<div class="report-card">
    <div class="stat-grid">
        <div class="stat-card">
            <div class="text-muted small">Total Items</div>
            <div class="h4 mb-0">{{ $totalItems }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Low Stock</div>
            <div class="h4 mb-0">{{ $lowStockItems }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Expired</div>
            <div class="h4 mb-0">{{ $expiredItems }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Expiring Soon</div>
            <div class="h4 mb-0">{{ $expiringSoonItems }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Inventory Value</div>
            <div class="h4 mb-0">PHP {{ number_format($totalValue ?? 0, 2) }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="report-card">
            <h5 class="mb-3">Low Stock Items</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockList as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No low stock items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="report-card">
            <h5 class="mb-3">Expired Items</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiredList as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="text-end">{{ $item->expiry_date?->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No expired items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="report-card">
            <h5 class="mb-3">Expiring Soon</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiringSoonList as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="text-end">{{ $item->expiry_date?->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No items expiring soon.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Dispensing Trends (30 days)</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Count</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dispensingTrends as $trend)
                            <tr>
                                <td>{{ $trend->date }}</td>
                                <td class="text-end">{{ $trend->count }}</td>
                                <td class="text-end">PHP {{ number_format($trend->total ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No dispensing data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Top Dispensed Medications</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Medication</th>
                            <th class="text-end">Dispenses</th>
                            <th class="text-end">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topMedications as $med)
                            <tr>
                                <td>{{ $med->inventoryItem?->name ?? 'Unknown' }}</td>
                                <td class="text-end">{{ $med->count }}</td>
                                <td class="text-end">{{ $med->total_quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No dispensing data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
