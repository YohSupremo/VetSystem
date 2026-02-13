@extends('admin.dashboard')

@section('page-title', 'Invoice Details')
@section('page-description', 'Invoice summary and payments')

@section('content')
<style>
    .billing-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .invoice-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .status-badge {
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.02em;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.5rem;
    }

    .summary-block {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.25rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.35rem 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.95rem;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #64748b;
        font-weight: 600;
    }

    .summary-value {
        color: #0f172a;
        font-weight: 600;
    }

    .table thead th {
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
    }

    .table td,
    .table th {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .table colgroup col.type-col { width: 18%; }
    .table colgroup col.desc-col { width: 34%; }
    .table colgroup col.qty-col { width: 10%; }
    .table colgroup col.unit-col { width: 18%; }
    .table colgroup col.total-col { width: 20%; }

    .table colgroup col.date-col { width: 24%; }
    .table colgroup col.method-col { width: 20%; }
    .table colgroup col.amount-col { width: 18%; }
    .table colgroup col.ref-col { width: 38%; }
</style>

<div class="content-header billing-hero">
    <div class="header-title">
        <h1><i class="fas fa-file-invoice"></i> Invoice {{ $invoice->invoice_number }}</h1>
        <p>View invoice details and payment history</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.billing.edit', $invoice->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.billing.payment', $invoice->id) }}" class="btn btn-primary ms-2">
            <i class="fas fa-credit-card"></i> Record Payment
        </a>
    </div>
</div>

@php
    $statusClass = match($invoice->status) {
        'paid' => 'bg-success text-white',
        'overdue' => 'bg-danger text-white',
        'partial' => 'bg-warning text-dark',
        'sent' => 'bg-info text-dark',
        default => 'bg-secondary text-white',
    };
@endphp

<div class="invoice-card">
    <div class="invoice-header">
        <div>
            <h4 class="mb-1">Invoice Summary</h4>
            <div class="text-muted">Issued {{ $invoice->invoice_date?->format('M d, Y') }}</div>
        </div>
        <span class="status-badge {{ $statusClass }}">{{ ucfirst($invoice->status) }}</span>
    </div>
    <div class="summary-grid">
        <div class="summary-block">
            <div class="summary-row">
                <span class="summary-label">Pet</span>
                <span class="summary-value">{{ $invoice->pet?->name ?? 'N/A' }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Owner</span>
                <span class="summary-value">{{ $invoice->petOwner?->full_name ?? 'N/A' }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Due Date</span>
                <span class="summary-value">{{ $invoice->due_date?->format('M d, Y') }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Notes</span>
                <span class="summary-value">{{ $invoice->notes ?: 'None' }}</span>
            </div>
        </div>
        <div class="summary-block">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Tax</span>
                <span class="summary-value">{{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Discount</span>
                <span class="summary-value">{{ number_format($invoice->discount_amount, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total</span>
                <span class="summary-value">{{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Paid</span>
                <span class="summary-value">{{ number_format($invoice->paid_amount, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Balance</span>
                <span class="summary-value">{{ number_format($invoice->balance, 2) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="invoice-card">
    <h5 class="mb-3">Line Items</h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <colgroup>
                <col class="type-col">
                <col class="desc-col">
                <col class="qty-col">
                <col class="unit-col">
                <col class="total-col">
            </colgroup>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceItems as $item)
                    <tr>
                        <td>{{ ucfirst($item->item_type) }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="invoice-card">
    <h5 class="mb-3">Payments</h5>
    @if($invoice->payments->isEmpty())
        <div class="text-muted">No payments recorded yet.</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <colgroup>
                    <col class="date-col">
                    <col class="method-col">
                    <col class="amount-col">
                    <col class="ref-col">
                </colgroup>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->transaction_id ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
