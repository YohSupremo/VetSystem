@extends('admin.dashboard')

@section('page-title', 'Financial Report')
@section('page-description', 'Revenue, invoices, and payment insights')

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
        <h1><i class="fas fa-coins"></i> Financial Report</h1>
        <p>Track revenue performance and outstanding invoices</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <a href="{{ route('admin.reports.export', ['reportType' => 'financial', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-primary">
            <i class="fas fa-file-export"></i> Export CSV
        </a>
    </div>
</div>

<div class="report-card">
    <form class="row g-3 align-items-end" method="GET">
        <div class="col-md-4">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
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
            <div class="text-muted small">Total Invoices</div>
            <div class="h4 mb-0">{{ $totalInvoices }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Total Revenue</div>
            <div class="h4 mb-0">PHP {{ number_format($totalRevenue ?? 0, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Paid Amount</div>
            <div class="h4 mb-0">PHP {{ number_format($paidAmount ?? 0, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Outstanding</div>
            <div class="h4 mb-0">PHP {{ number_format($outstandingAmount ?? 0, 2) }}</div>
        </div>
    </div>
</div>

<div class="report-card border-danger" style="border-left: 4px solid #dc3545;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Cancelled Invoices</h5>
        <a href="{{ route('admin.reports.cancelled-invoices', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-list"></i> View Details
        </a>
    </div>
    <div class="stat-grid">
        <div class="stat-card" style="background: #fff5f5;">
            <div class="text-muted small">Cancelled Invoices</div>
            <div class="h4 mb-0 text-danger">{{ $cancelledCount }}</div>
        </div>
        <div class="stat-card" style="background: #fff5f5;">
            <div class="text-muted small">Amount Cancelled</div>
            <div class="h4 mb-0 text-danger">PHP {{ number_format($cancelledAmount ?? 0, 2) }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Revenue by Month</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByMonth as $row)
                            <tr>
                                <td>{{ $row->month }}</td>
                                <td class="text-end">PHP {{ number_format($row->revenue ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Payment Methods</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th class="text-end">Count</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $method)
                            <tr>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $method->payment_method) }}</td>
                                <td class="text-end">{{ $method->count }}</td>
                                <td class="text-end">PHP {{ number_format($method->total ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No payments recorded.</td></tr>
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
            <h5 class="mb-3">Top Services</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th class="text-end">Count</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topServices as $service)
                            <tr>
                                <td>{{ $service->item_type }}</td>
                                <td class="text-end">{{ $service->count }}</td>
                                <td class="text-end">PHP {{ number_format($service->total ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No service data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="report-card">
            <h5 class="mb-3">Outstanding Invoices</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Owner</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($outstandingInvoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->petOwner?->full_name ?? 'N/A' }}</td>
                                <td class="text-end">PHP {{ number_format($invoice->balance ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No outstanding invoices.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
