@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 text-center">
                    <h1 class="h3 mb-2">Secure Payment</h1>
                    <p class="text-muted">Invoice #{{ $invoice->invoice_number ?: str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold text-primary mb-2">
                            ₱{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}
                        </div>
                        <span class="badge bg-light text-dark">Amount Due</span>
                    </div>

                    <form action="{{ route('customer.billing.process-payment', $invoice->id) }}" method="POST">
                        @csrf
                        
                        <input type="hidden" name="amount" value="{{ $invoice->total_amount - $invoice->paid_amount }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Payment Method</label>
                            <div class="vstack gap-2">
                                <label class="card card-body border p-3 cursor-pointer hover-card">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_method" value="credit_card" class="form-check-input me-3" checked>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Credit / Debit Card</h6>
                                        </div>
                                        <div class="text-muted small">Visa, MC, Amex</div>
                                    </div>
                                </label>
                                <label class="card card-body border p-3 cursor-pointer hover-card">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_method" value="bank_transfer" class="form-check-input me-3">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><i class="fas fa-university me-2 text-primary"></i>Bank Transfer</h6>
                                        </div>
                                    </div>
                                </label>
                                <label class="card card-body border p-3 cursor-pointer hover-card">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_method" value="ewallet" class="form-check-input me-3">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><i class="fas fa-mobile-alt me-2 text-primary"></i>GCash / Maya</h6>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Card Details Simulation -->
                        <div class="mb-4 bg-light p-3 rounded">
                            <label class="form-label small fw-bold text-muted">Card Details (Simulated)</label>
                            <div class="row g-2">
                                <div class="col-12">
                                    <input type="text" class="form-control" placeholder="Card Number" disabled value="**** **** **** 4242">
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control" placeholder="MM/YY" disabled value="12/25">
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control" placeholder="CVC" disabled value="***">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Pay ₱{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}
                            </button>
                            <a href="{{ route('customer.billing.show', $invoice->id) }}" class="btn btn-link text-muted">Cancel Payment</a>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted"><i class="fas fa-lock me-1"></i> Payments are secure and encrypted.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
