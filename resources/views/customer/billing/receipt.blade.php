@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Payment Receipt - PawCare')

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

.receipt-container {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    position: relative;
}

.receipt-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--soft-gray);
}

.receipt-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-text);
    margin-bottom: 0.5rem;
}

.receipt-subtitle {
    font-size: 1.1rem;
    color: var(--light-text);
}

.success-icon {
    font-size: 4rem;
    color: #22c55e;
    margin-bottom: 1rem;
}

.receipt-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.receipt-section {
    background: var(--soft-gray);
    padding: 1.5rem;
    border-radius: 0.5rem;
}

.receipt-section h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.receipt-section h3 i {
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

.payment-summary {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(16, 185, 129, 0.1));
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-top: 1rem;
    border: 2px solid #22c55e;
}

.payment-amount {
    font-size: 1.5rem;
    font-weight: 700;
    color: #22c55e;
    text-align: center;
    margin-bottom: 1rem;
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

.btn-success {
    background: linear-gradient(135deg, #22c55e, #10b981);
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.print-button {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: var(--soft-gray);
    color: var(--dark-text);
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.print-button:hover {
    background: var(--primary-purple);
    color: white;
}

@media (max-width: 768px) {
    .receipt-sections {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .print-button {
        position: static;
        margin-bottom: 1rem;
        display: block;
        text-align: center;
    }
}

@media print {
    .customer-header,
    .action-buttons,
    .print-button {
        display: none;
    }
    
    .receipt-container {
        box-shadow: none;
        border: 1px solid #ddd;
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
        <div class="receipt-container">
            <a href="javascript:window.print()" class="print-button">
                <i class="fas fa-print"></i> Print Receipt
            </a>

            <div class="receipt-header">
                <i class="fas fa-check-circle success-icon"></i>
                <h2 class="receipt-title">Payment Successful!</h2>
                <p class="receipt-subtitle">Thank you for your payment</p>
            </div>

            <div class="receipt-sections">
                <!-- Payment Information -->
                <div class="receipt-section">
                    <h3><i class="fas fa-credit-card"></i> Payment Information</h3>
                    <div class="detail-item">
                        <span class="detail-label">Payment ID</span>
                        <span class="detail-value">#{{ $payment->id }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Method</span>
                        <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                    </div>
                    @if($payment->reference_number)
                    <div class="detail-item">
                        <span class="detail-label">Reference Number</span>
                        <span class="detail-value">{{ $payment->reference_number }}</span>
                    </div>
                    @endif
                </div>

                <!-- Invoice Information -->
                <div class="receipt-section">
                    <h3><i class="fas fa-file-invoice"></i> Invoice Information</h3>
                    <div class="detail-item">
                        <span class="detail-label">Invoice Number</span>
                        <span class="detail-value">{{ $payment->invoice->invoice_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Invoice Date</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($payment->invoice->issue_date)->format('M d, Y') }}</span>
                    </div>
                    @if($payment->invoice->appointment)
                    <div class="detail-item">
                        <span class="detail-label">Appointment</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($payment->invoice->appointment->appointment_date)->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @if($payment->invoice->pet)
                    <div class="detail-item">
                        <span class="detail-label">Pet</span>
                        <span class="detail-value">{{ $payment->invoice->pet->name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="payment-summary">
                <div class="payment-amount">
                    ${{ number_format($payment->amount, 2) }}
                </div>
                <p style="text-align: center; margin: 0; color: #6b7280;">
                    Payment has been successfully processed and your invoice has been marked as paid.
                </p>
            </div>

            <!-- Notes -->
            @if($payment->notes)
            <div class="receipt-section" style="margin-top: 2rem;">
                <h3><i class="fas fa-notes-medical"></i> Notes</h3>
                <p>{{ $payment->notes }}</p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('customer.billing.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Billing
                </a>
                <a href="{{ route('customer.billing.show', $payment->invoice->id) }}" class="btn btn-primary">
                    <i class="fas fa-eye"></i> View Invoice
                </a>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-success">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
        </div>
    </main>
</div>
@endsection
