@extends('admin.dashboard')

@section('page-title', 'Cancelled Invoices')
@section('page-description', 'View and filter all cancelled invoices')

@section('content')
<style>
    .report-hero {
        background: linear-gradient(120deg, #fff5f5 0%, #ffe0e0 60%, #ffd9d9 100%);
        border: 1px solid #fdbcbc;
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

    .stat-card.danger {
        background: #fff5f5;
        border-color: #fdbcbc;
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
        <h1><i class="fas fa-ban"></i> Cancelled Invoices</h1>
        <p>Manage and review all cancelled transaction records</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.reports.financial') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Financial Report
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="report-card">
    <div class="stat-grid">
        <div class="stat-card danger">
            <div class="text-muted small">Total Cancelled</div>
            <div class="h4 mb-0 text-danger">{{ $totalCancelled }}</div>
        </div>
        <div class="stat-card danger">
            <div class="text-muted small">Total Amount Cancelled</div>
            <div class="h4 mb-0 text-danger">PHP {{ number_format($totalCancelledAmount, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="text-muted small">Date Range</div>
            <div class="small fw-bold">{{ date('M d, Y', strtotime($startDate)) }} - {{ date('M d, Y', strtotime($endDate)) }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="report-card">
    <form class="row g-3 align-items-end" method="GET">
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sort By</label>
            <select name="sort_by" class="form-select">
                <option value="issue_date" {{ $sortBy === 'issue_date' ? 'selected' : '' }}>Date</option>
                <option value="invoice_number" {{ $sortBy === 'invoice_number' ? 'selected' : '' }}>Invoice #</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Order</label>
            <select name="sort_order" class="form-select">
                <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Newest</option>
                <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Oldest</option>
            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </form>
</div>

<!-- Invoices Table -->
<div class="report-card">
    @if($invoices->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-check-circle fa-3x text-success"></i>
            </div>
            <h5>No Cancelled Invoices</h5>
            <p class="text-muted">There are no cancelled invoices in this date range.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Pet Owner</th>
                        <th>Pet</th>
                        <th>Issue Date</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Cancellation</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td class="ps-4 fw-bold">
                                <span class="badge bg-light text-dark">{{ $invoice->invoice_number }}</span>
                            </td>
                            <td>
                                @if($invoice->petOwner)
                                    {{ $invoice->petOwner->user->first_name }} {{ $invoice->petOwner->user->last_name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($invoice->pet)
                                    <i class="fas fa-paw me-1"></i> {{ $invoice->pet->name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                {{ date('M d, Y', strtotime($invoice->issue_date)) }}
                            </td>
                            <td class="text-end fw-bold text-danger">
                                PHP {{ number_format($invoice->total_amount, 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">Cancelled</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.billing.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $invoices->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
