@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Billing & Invoices - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
}

/* Stats Cards */
.stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
    transition: var(--transition-smooth);
}

.stat-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    pointer-events: none;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 60px rgba(31, 38, 135, 0.4);
    border-color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
}

.stat-card.outstanding {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
}

.stat-card.outstanding .stat-icon {
    background: rgba(255, 255, 255, 0.25);
    color: white;
}

.stat-card.paid .stat-icon {
    background: rgba(16, 185, 129, 0.2);
    color: rgba(16, 185, 129, 0.9);
}

.stat-card.cancelled .stat-icon {
    background: rgba(239, 68, 68, 0.2);
    color: rgba(239, 68, 68, 0.9);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
    color: #000;
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.25rem;
    color: #000;
}

.stat-card.paid .stat-value {
    color: rgba(16, 185, 129, 0.9);
}

.stat-card.cancelled .stat-value {
    color: rgba(239, 68, 68, 0.9);
}

/* Filter Section */
.filter-section {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
}

.filter-input, .filter-select {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 0.75rem;
    color: #000;
    font-weight: 600;
    transition: var(--transition-smooth);
}

.filter-input:focus, .filter-select:focus {
    outline: none;
    border-color: rgba(147, 51, 234, 0.4);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
}

.btn-filter {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    color: #000;
    font-weight: 600;
    transition: var(--transition-smooth);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-filter:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.btn-filter.primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border: none;
}

.btn-filter.primary:hover {
    color: white;
}

/* Category Tabs */
.category-tabs {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    padding: 1rem;
    margin-bottom: 2rem;
}

.nav-link {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    color: #000;
    font-weight: 600;
    transition: var(--transition-smooth);
    text-decoration: none;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.nav-link.active {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border: none;
}

/* Invoice Table */
.invoice-table {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.invoice-table::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.invoice-table table {
    background: transparent;
    margin: 0;
}

.invoice-table th {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
    padding: 1rem;
}

.invoice-table td {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: #000;
    padding: 1rem;
    vertical-align: middle;
}

.invoice-table tbody tr:hover td {
    background: rgba(255, 255, 255, 0.1);
}

.status-badge {
    padding: 0.4rem 0.875rem;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
}

.status-paid {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
    color: rgba(16, 185, 129, 0.9);
}

.status-pending {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.3);
    color: rgba(245, 158, 11, 0.9);
}

.status-cancelled {
    background: rgba(107, 114, 128, 0.2);
    border-color: rgba(107, 114, 128, 0.3);
    color: rgba(107, 114, 128, 0.9);
}

.category-badge {
    padding: 0.25rem 0.75rem;
    background: rgba(147, 51, 234, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(147, 51, 234, 0.2);
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--primary-purple);
}

.btn-action {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    color: #000;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--transition-smooth);
}

.btn-action:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.btn-action.primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border: none;
}

.btn-action.primary:hover {
    color: white;
}

.pet-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #000;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@media (max-width: 1024px) {
    .customer-container {
        padding: 1.5rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .page-subtitle {
        font-size: 1rem;
    }
    
    .stats-row {
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        padding: 1.5rem;
        min-height: 120px;
    }
    
    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        color: #000 !important;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="customer-container">
    <main class="customer-main">
        <!-- Page Header -->
        <div class="page-header mb-5 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Billing & Invoices</h1>
                <p class="page-subtitle">Manage your payments and orders</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('customer.billing.index') }}" class="btn-filter primary">Invoices</a>
                <a href="{{ route('customer.billing.orders') }}" class="btn-filter">My Orders</a>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card outstanding h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Outstanding</div>
                                <div class="stat-value">₱{{ number_format($invoiceStats['outstanding_amount'], 2) }}</div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card paid h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Total Paid</div>
                                <div class="stat-value">₱{{ number_format($invoiceStats['paid_amount'], 2) }}</div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card cancelled h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Cancelled Invoices</div>
                                <div class="stat-value">₱{{ number_format($invoiceStats['cancelled_amount'], 2) }}</div>
                                <small style="color: rgba(255, 255, 255, 0.7);">({{ $invoiceStats['cancelled_count'] }} invoices)</small>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('customer.billing.index') }}" class="row g-3">
                <input type="hidden" name="category" value="{{ $selectedCategory ?? 'all' }}">
                <div class="col-md-4">
                    <label class="filter-label">Status</label>
                    <select name="status" class="filter-select">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="filter-label">Start Date</label>
                    <input type="date" name="start_date" class="filter-input" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="filter-label">End Date</label>
                    <input type="date" name="end_date" class="filter-input" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn-filter primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('customer.billing.index') }}" class="btn-filter w-100">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <ul class="nav nav-pills flex-wrap gap-2">
                @foreach(($availableCategories ?? ['all']) as $categoryKey)
                    <li class="nav-item">
                        <a href="{{ route('customer.billing.index', array_merge(['status' => request('status'), 'start_date' => request('start_date'), 'end_date' => request('end_date')], ['category' => $categoryKey])) }}" class="nav-link {{ ($selectedCategory ?? 'all') === $categoryKey ? 'active' : '' }}">
                            {{ $categoryLabelMap[$categoryKey] ?? ucfirst(str_replace('_', ' ', $categoryKey)) }}
                            <span class="ms-1 badge {{ ($selectedCategory ?? 'all') === $categoryKey ? 'bg-light text-primary' : 'bg-secondary' }}">{{ $categoryCounts[$categoryKey] ?? 0 }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Invoices Table -->
        <div class="invoice-table">
            <div class="p-4">
                <h5 class="mb-0" style="color: #000; font-weight: 700;">Invoice History</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Invoice #</th>
                            <th>Date</th>
                            <th>Pet</th>
                            <th>Amount</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="ps-4 fw-bold">
                                    #{{ $invoice->invoice_number ?: str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>{{ date('M d, Y', strtotime($invoice->issue_date)) }}</td>
                                <td>
                                    @if($invoice->pet)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $invoice->pet->photo_url }}" class="pet-avatar me-2" alt="{{ $invoice->pet->name }}">
                                            {{ $invoice->pet->name }}
                                        </div>
                                    @else
                                        <span style="color: #000;">-</span>
                                    @endif
                                </td>
                                <td class="fw-bold">₱{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>
                                    <span class="category-badge">
                                        {{ $categoryLabelMap[$invoice->source_category] ?? ucfirst(str_replace('_', ' ', $invoice->source_category)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($invoice->status === 'paid')
                                        <span class="status-badge status-paid">Paid</span>
                                    @elseif($invoice->status === 'cancelled')
                                        <span class="status-badge status-cancelled">Cancelled</span>
                                    @else
                                        <span class="status-badge status-pending">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('customer.billing.show', $invoice->id) }}" class="btn-action">
                                        View
                                    </a>
                                    @if($invoice->status === 'pending' || $invoice->status === 'partial')
                                        <a href="{{ route('customer.billing.pay', $invoice->id) }}" class="btn-action primary ms-1">
                                            Pay
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    No invoices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection
