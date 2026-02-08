@extends('admin.dashboard')

@section('page-title', 'Record Payment')
@section('page-description', 'Apply payment to an invoice')

@section('content')
<style>
    .billing-hero {
        background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 60%, #fff7ed 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }

    .payment-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.65rem 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15);
    }
</style>

<div class="content-header billing-hero">
    <div class="header-title">
        <h1><i class="fas fa-credit-card"></i> Record Payment</h1>
        <p>Invoice {{ $invoice->invoice_number }} · Balance {{ number_format($invoice->balance, 2) }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('admin.billing.show', $invoice->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Invoice
        </a>
    </div>
</div>

<form action="{{ route('admin.billing.payment.process', $invoice->id) }}" method="POST">
    @csrf
    <div class="payment-card">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="debit_card">Debit Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="online_payment">Online Payment</option>
                </select>
                @error('payment_method')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" max="{{ $invoice->balance }}" value="{{ old('amount', $invoice->balance) }}" required>
                @error('amount')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required>
                @error('payment_date')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="transaction_id" class="form-label">Transaction ID</label>
                <input type="text" class="form-control" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}">
            </div>
            <div class="col-12 mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.billing.show', $invoice->id) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Apply Payment</button>
    </div>
</form>
@endsection
