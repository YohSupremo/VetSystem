@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Pay Invoice - PawCare')

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
    max-width: 800px;
    margin: 0 auto;
}

.payment-form {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.payment-header {
    text-align: center;
    margin-bottom: 2rem;
}

.payment-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--dark-text);
    margin-bottom: 0.5rem;
}

.invoice-summary {
    background: var(--soft-gray);
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.invoice-summary h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 1rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-item.total {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-purple);
    border-top: 2px solid var(--primary-purple);
    padding-top: 1rem;
    margin-top: 0.5rem;
}

.form-section {
    margin-bottom: 2rem;
}

.form-section h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section h3 i {
    color: var(--primary-purple);
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.payment-method {
    padding: 1rem;
    border: 2px solid var(--soft-gray);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.payment-method:hover {
    border-color: var(--primary-purple);
    background: rgba(167, 139, 250, 0.05);
}

.payment-method.selected {
    border-color: var(--primary-purple);
    background: rgba(167, 139, 250, 0.1);
}

.payment-method i {
    font-size: 2rem;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.payment-method span {
    display: block;
    font-weight: 600;
    color: var(--dark-text);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--soft-gray);
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary-purple);
    box-shadow: 0 0 10px rgba(167, 139, 250, 0.1);
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
    flex: 1;
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
    .payment-methods {
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
        <form action="{{ route('customer.billing.process-payment', $invoice->id) }}" method="POST">
            @csrf
            <div class="payment-form">
                <div class="payment-header">
                    <h2 class="payment-title">Pay Invoice</h2>
                    <p>Invoice #{{ $invoice->invoice_number }}</p>
                </div>

                <!-- Invoice Summary -->
                <div class="invoice-summary">
                    <h3>Invoice Summary</h3>
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>${{ number_format($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2) }}</span>
                    </div>
                    @if($invoice->tax_rate > 0)
                    <div class="summary-item">
                        <span>Tax ({{ $invoice->tax_rate }}%)</span>
                        <span>${{ number_format($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }) * ($invoice->tax_rate / 100), 2) }}</span>
                    </div>
                    @endif
                    @if($invoice->discount_amount > 0)
                    <div class="summary-item">
                        <span>Discount</span>
                        <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="summary-item total">
                        <span>Total Amount</span>
                        <span>${{ number_format($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }) + ($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }) * ($invoice->tax_rate / 100)) - $invoice->discount_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-section">
                    <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
                    <div class="payment-methods">
                        @foreach($paymentMethods as $method)
                        <div class="payment-method" onclick="selectPaymentMethod('{{ $method }}')">
                            @if($method == 'cash')
                            <i class="fas fa-money-bill-wave"></i>
                            @elseif($method == 'credit_card')
                            <i class="fas fa-credit-card"></i>
                            @elseif($method == 'debit_card')
                            <i class="fas fa-credit-card"></i>
                            @elseif($method == 'bank_transfer')
                            <i class="fas fa-university"></i>
                            @elseif($method == 'check')
                            <i class="fas fa-money-check"></i>
                            @elseif($method == 'mobile_payment')
                            <i class="fas fa-mobile-alt"></i>
                            @else
                            <i class="fas fa-receipt"></i>
                            @endif
                            <span>{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="payment_method" id="payment_method" required>
                </div>

                <!-- Payment Details -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Payment Details</h3>
                    <div class="form-group">
                        <label for="amount">Payment Amount</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required 
                               value="{{ $invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }) + ($invoice->items->sum(function($item) { return $item->quantity * $item->unit_price; }) * ($invoice->tax_rate / 100)) - $invoice->discount_amount }}">
                    </div>
                    <div class="form-group">
                        <label for="reference_number">Reference Number (Optional)</label>
                        <input type="text" name="reference_number" id="reference_number" placeholder="Check number, transaction ID, etc.">
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Additional payment notes..."></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('customer.billing.show', $invoice->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Invoice
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-lock"></i> Process Payment
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>

@push('scripts')
<script>
function selectPaymentMethod(method) {
    // Remove selected class from all methods
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selected class to clicked method
    event.currentTarget.classList.add('selected');
    
    // Set hidden input value
    document.getElementById('payment_method').value = method;
}

// Set default payment method
document.addEventListener('DOMContentLoaded', function() {
    const firstMethod = document.querySelector('.payment-method');
    if (firstMethod) {
        firstMethod.click();
    }
});
</script>
@endpush
@endsection
