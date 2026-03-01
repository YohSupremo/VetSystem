@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('customer.billing.index') }}" class="text-decoration-none text-muted me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0">Invoice Details</h1>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <!-- Invoice Header -->
                    <div class="d-flex justify-content-between mb-5">
                        <div>
                            <h2 class="fw-bold text-primary">INVOICE</h2>
                            <p class="text-muted mb-0">#{{ $invoice->invoice_number ?: str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <div class="mt-2">
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success fs-6">PAID</span>
                                @elseif($invoice->status === 'cancelled')
                                    <span class="badge bg-secondary fs-6">CANCELLED</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6">PENDING</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <h5 class="fw-bold">{{ $clinicName ?? 'PawCare' }} Veterinary Clinic</h5>
                            <p class="text-muted small mb-0">123 Vet Street, Animal City</p>
                            <p class="text-muted small mb-0">Phone: (123) 456-7890</p>
                            <p class="text-muted small">Date: {{ date('M d, Y', strtotime($invoice->issue_date)) }}</p>
                        </div>
                    </div>

                    <!-- Client Info -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold">Bill To:</h6>
                            <h5 class="fw-bold mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            @if($invoice->pet)
                                <p class="text-muted mt-2">
                                    <small class="text-uppercase fw-bold">Patient:</small> {{ $invoice->pet->name }} ({{ $invoice->pet->species }})
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="table-responsive mb-5">
                        <table class="table table-borderless">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">Description</th>
                                    <th class="py-3 text-end">Quantity</th>
                                    <th class="py-3 text-end">Unit Price</th>
                                    <th class="py-3 text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->invoiceItems as $item)
                                    <tr class="border-bottom">
                                        <td class="py-3 ps-4">{{ $item->description }}</td>
                                        <td class="py-3 text-end">{{ $item->quantity }}</td>
                                        <td class="py-3 text-end">₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="py-3 text-end pe-4">₱{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3" class="text-end pt-3 fw-bold">Subtotal</td>
                                    <td class="text-end pt-3 pe-4">₱{{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                                @if($invoice->tax_amount > 0)
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Tax</td>
                                        <td class="text-end pe-4">₱{{ number_format($invoice->tax_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end pt-3 fw-bold fs-5">Total Amount</td>
                                    <td class="text-end pt-3 pe-4 fw-bold fs-5 text-primary">₱{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment History (if any) -->
                    @if($invoice->payments->count() > 0)
                        <div class="mb-5">
                            <h6 class="fw-bold mb-3">Payment History</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->payments as $payment)
                                            <tr>
                                                <td>{{ date('M d, Y', strtotime($payment->payment_date)) }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                                <td>{{ $payment->reference_number ?: '-' }}</td>
                                                <td class="text-end">₱{{ number_format($payment->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-3 d-print-none">
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-2"></i>Print Invoice
                        </button>
                        @if($invoice->status === 'pending' || $invoice->status === 'partial')
                            <a href="{{ route('customer.billing.pay', $invoice->id) }}" class="btn btn-primary px-4">
                                <i class="fas fa-credit-card me-2"></i>Pay Now
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
