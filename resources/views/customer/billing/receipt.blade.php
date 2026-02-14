@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4 text-success">
                        <i class="fas fa-check-circle fa-5x"></i>
                    </div>
                    
                    <h2 class="h3 fw-bold mb-2">Payment Successful!</h2>
                    <p class="text-muted mb-4">Thank you for your payment.</p>
                    
                    <div class="bg-light p-4 rounded-3 mb-4 text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Payment Ref:</span>
                            <span class="fw-bold">{{ $payment->reference_number ?: 'PAY-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Date:</span>
                            <span class="fw-bold">{{ date('d M Y, h:i A', strtotime($payment->created_at)) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Method:</span>
                            <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold text-uppercase">Amount Paid:</span>
                            <span class="fw-bold fs-5 text-success">₱{{ number_format($payment->amount, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.billing.show', $payment->invoice_id) }}" class="btn btn-primary">
                            View Invoice
                        </a>
                        <a href="{{ route('customer.billing.index') }}" class="btn btn-outline-secondary">
                            Back to Billing
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
