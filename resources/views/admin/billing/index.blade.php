@extends('admin.dashboard')

@section('page-title', 'Billing & Invoices')
@section('page-description', 'Manage client invoices and billing information')

@section('content')
<style>
    .billing-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .stats-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 0.95rem 1rem;
        margin-bottom: 1rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .stats-card h3 {
        font-size: 1.45rem;
        margin: 0;
        font-weight: 700;
        color: #0f172a;
    }

    .stats-card p {
        margin: 0.2rem 0 0;
        color: #64748b;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .stats-card .stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1d4ed8;
        background: rgba(37, 99, 235, 0.1);
        font-size: 0.95rem;
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
        background: #f8fafc;
    }

    .table thead th {
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
        text-align: center;
    }

    .table tbody td {
        text-align: center;
        vertical-align: middle;
    }

    .table colgroup col.invoice-col { width: 20%; }
    .table colgroup col.pet-col { width: 12%; }
    .table colgroup col.owner-col { width: 16%; }
    .table colgroup col.total-col { width: 10%; }
    .table colgroup col.balance-col { width: 10%; }
    .table colgroup col.due-col { width: 12%; }
    .table colgroup col.status-col { width: 10%; }
    .table colgroup col.actions-col { width: 10%; }

    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.02em;
    }

    .action-group {
        display: flex;
        gap: 0.35rem;
        justify-content: center;
        flex-wrap: nowrap;
    }

    .action-group .btn {
        min-width: 36px;
        border-radius: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5f5;
        margin-bottom: 20px;
        display: block;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Mobile responsive improvements */
    @media (max-width: 768px) {
        .billing-hero {
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
        }

        .stats-card {
            padding: 0.75rem 0.85rem;
            margin-bottom: 0.75rem;
        }

        .stats-card h3 {
            font-size: 1.2rem;
        }

        .table-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            gap: 10px;
            align-items: stretch;
        }

        .table thead th {
            font-size: 0.7rem;
            padding: 8px 6px;
        }

        .table tbody td {
            font-size: 0.8rem;
            padding: 12px 6px;
        }

        .action-group {
            flex-direction: column;
            gap: 4px;
        }

        .action-group .btn {
            min-width: auto;
            width: 100%;
        }

        .status-badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .billing-hero {
            padding: 0.75rem 1rem;
        }

        .table-card {
            border-radius: 12px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .table thead {
            display: none;
        }

        .table tbody,
        .table tr,
        .table td {
            display: block;
            width: 100% !important;
        }

        .table tr {
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .table td {
            text-align: left !important;
            padding: 6px 0 !important;
            border: none !important;
            position: relative;
            padding-left: 40% !important;
            white-space: normal !important;
        }

        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            width: 35%;
            font-weight: 600;
            color: #64748b;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .action-group {
            flex-direction: row;
            justify-content: space-between;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        .action-group .btn {
            flex: 1;
            margin: 0 2px;
            padding: 6px 8px;
            font-size: 0.7rem;
        }
    }
</style>

<div class="content-header billing-hero">
    <div class="header-title">
        <h1><i class="fas fa-file-invoice-dollar"></i> Billing & Invoices</h1>
        <p>Manage client invoices and billing information</p>
    </div>
    <div class="header-actions">
        @if($showTrash)
            <a href="{{ route('admin.billing.index', request()->except('trash', 'page')) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back To Active
            </a>
        @else
            <a href="{{ route('admin.billing.index', array_merge(request()->query(), ['trash' => 1])) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-trash-restore"></i> View Trash
            </a>
        @endif
        <a href="{{ route('admin.billing.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Invoice
        </a>
    </div>
</div>

<div class="row mb-4 g-2">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $totalInvoices }}</h3>
                    <p>Total Invoices</p>
                </div>
                <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stats-card" style="background: linear-gradient(135deg, #ecfdf3 0%, #dcfce7 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $paidInvoices }}</h3>
                    <p>Paid Invoices</p>
                </div>
                <div class="stat-icon" style="color:#15803d;background:rgba(34,197,94,0.12);"><i class="fas fa-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stats-card" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ $overdueInvoices }}</h3>
                    <p>Overdue</p>
                </div>
                <div class="stat-icon" style="color:#c2410c;background:rgba(234,88,12,0.12);"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ number_format($totalRevenue, 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="stat-icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3>{{ number_format($paidAmount, 2) }}</h3>
                    <p>Paid Amount</p>
                </div>
                <div class="stat-icon" style="color:#15803d;background:rgba(34,197,94,0.12);"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div>
            <h5 class="mb-0">Invoices</h5>
            <small class="text-muted">Showing {{ $invoices->count() }} of {{ $invoices->total() }}</small>
        </div>
    </div>
    @if($invoices->isEmpty())
        <div class="empty-state">
            <i class="fas fa-file-invoice"></i>
            <h3>No invoices yet</h3>
            <p>Create your first invoice to start tracking payments.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <colgroup>
                    <col class="invoice-col">
                    <col class="pet-col">
                    <col class="owner-col">
                    <col class="total-col">
                    <col class="balance-col">
                    <col class="due-col">
                    <col class="status-col">
                    <col class="actions-col">
                </colgroup>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pet</th>
                        <th>Owner</th>
                        <th>Total</th>
                        <th>Balance</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        @php
                            $statusClass = match($invoice->status) {
                                'paid' => 'bg-success text-white',
                                'overdue' => 'bg-danger text-white',
                                'partial' => 'bg-warning text-dark',
                                'pending' => 'bg-info text-dark',
                                'cancelled' => 'bg-secondary text-white',
                                default => 'bg-secondary text-white',
                            };
                        @endphp
                        <tr>
                            <td data-label="Invoice">
                                <strong>{{ $invoice->invoice_number }}</strong>
                                <div class="text-muted small">{{ $invoice->invoice_date?->format('M d, Y') }}</div>
                            </td>
                            <td data-label="Pet">{{ $invoice->pet?->name ?? 'N/A' }}</td>
                            <td data-label="Owner">{{ $invoice->petOwner?->full_name ?? 'N/A' }}</td>
                            <td data-label="Total">{{ number_format($invoice->total_amount, 2) }}</td>
                            <td data-label="Balance">{{ number_format($invoice->balance, 2) }}</td>
                            <td data-label="Due Date">{{ $invoice->due_date?->format('M d, Y') }}</td>
                            <td data-label="Status"><span class="status-badge {{ $statusClass }}">{{ ucfirst($invoice->status) }}</span></td>
                            <td data-label="Actions">
                                <div class="action-group">
                                    @if($showTrash)
                                        <form action="{{ route('admin.billing.restore', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Restore this invoice?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.billing.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.billing.edit', $invoice->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.billing.payment', $invoice->id) }}" class="btn btn-sm btn-outline-success" title="Payment">
                                            <i class="fas fa-credit-card"></i>
                                        </a>
                                        <form action="{{ route('admin.billing.destroy', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this invoice?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
