@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Invoice Details - PawCare')

@push('styles')
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.customer-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
}

.logo-section .paw-icon {
    font-size: 2.5rem;
    animation: bounce 2s infinite;
}

.logo-section h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.customer-main {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.invoice-details {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--soft-gray);
}

.invoice-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--dark-text);
}

.invoice-number {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-purple);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: rgba(255, 193, 7, 0.1);
    color: #f59e0b;
}

.status-partial {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.status-paid {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}

.status-overdue {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.status-cancelled {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
}

.invoice-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.invoice-section {
    background: var(--soft-gray);
    padding: 1.5rem;
    border-radius: 0.5rem;
}

.invoice-section h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.invoice-section h3 i {
    color: var(--primary-purple);
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 0.875rem;
    color: var(--light-text);
}

.detail-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--dark-text);
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
}

.items-table th {
    background: var(--primary-purple);
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
}

.items-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--soft-gray);
}

.items-table tr:last-child td {
    border-bottom: none;
}

.total-section {
    background: var(--soft-gray);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
}

.total-row.total {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-purple);
    border-top: 2px solid var(--primary-purple);
    padding-top: 1rem;
    margin-top: 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
}

.btn-secondary {
    background: var(--soft-gray);
    color: var(--dark-text);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .invoice-sections {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<div class="customer-container">
    <header class="customer-header">
        <div class="logo-section">
            <a href="{{ route('customer.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                <i class="fas fa-paw paw-icon text-primary"></i>
                <h1 class="ms-3 mb-0">PawCare</h1>
            </a>
        </div>
        <div class="user-section">
            <span class="text-muted">Welcome, {{ $user->first_name }}</span>
        </div>
    </header>

    <main class="customer-main">
        <div class="invoice-details">
            <div class="invoice-header">
                <div>
                    <h2 class="invoice-title">Invoice Details</h2>
                    <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                </div>
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>

            <div class="invoice-sections">
                <!-- Invoice Information -->
                <div class="invoice-section">
                    <h3><i class="fas fa-file-invoice"></i> Invoice Information</h3>
                    <div class="detail-item">
                        <span class="detail-label">Issue Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Due Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span>
                    </div>
                    @if($invoice->tax_rate > 0)
                    <div class="detail-item">
                        <span class="detail-label">Tax Rate</span>
                        <span class="detail-value">{{ $invoice->tax_rate }}%</span>
                    </div>
                    @endif
                    @if($invoice->discount_amount > 0)
                    <div class="detail-item">
                        <span class="detail-label">Discount</span>
                        <span class="detail-value">${{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                    @endif
                </div>

                <!-- Related Information -->
                <div class="invoice-section">
                    <h3><i class="fas fa-link"></i> Related Information</h3>
                    @if($invoice->appointment)
                    <div class="detail-item">
                        <span class="detail-label">Appointment</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($invoice->appointment->appointment_date)->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @if($invoice->pet)
                    <div class="detail-item">
                        <span class="detail-label">Pet</span>
                        <span class="detail-value">{{ $invoice->pet->name }}</span>
                    </div>
                    @endif
                    @if($invoice->order)
                    <div class="detail-item">
                        <span class="detail-label">Order</span>
                        <span class="detail-value">#{{ $invoice->order->id }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Items -->
            @if($invoice->items->count() > 0)
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-list"></i> Invoice Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->unit_price, 2) }}</td>
                        <td>${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <!-- Totals -->
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2) }}</span>
                </div>
                @if($invoice->tax_rate > 0)
                <div class="total-row">
                    <span>Tax ({{ $invoice->tax_rate }}%)</span>
                    <span>${{ number_format($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }) * ($invoice->tax_rate / 100), 2) }}</span>
                </div>
                @endif
                @if($invoice->discount_amount > 0)
                <div class="total-row">
                    <span>Discount</span>
                    <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="total-row total">
                    <span>Total</span>
                    <span>${{ number_format($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }) + ($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }) * ($invoice->tax_rate / 100)) - $invoice->discount_amount, 2) }}</span>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes)
            <div class="invoice-section" style="margin-top: 2rem;">
                <h3><i class="fas fa-notes-medical"></i> Notes</h3>
                <p>{{ $invoice->notes }}</p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('customer.billing.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Billing
                </a>
                
                @if(in_array($invoice->status, ['pending', 'partial']))
                <a href="{{ route('customer.billing.pay', $invoice->id) }}" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i> Pay Invoice
                </a>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
