@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Billing & Invoices</h1>
            <p class="text-muted">Manage your payments and orders</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('customer.billing.index') }}" class="btn btn-primary active">Invoices</a>
            <a href="{{ route('customer.billing.orders') }}" class="btn btn-outline-primary">My Orders</a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold mb-1">Outstanding</div>
                            <h2 class="mb-0">₱{{ number_format($invoiceStats['outstanding_amount'], 2) }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded p-2">
                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold mb-1">Total Paid</div>
                            <h2 class="mb-0 text-success">₱{{ number_format($invoiceStats['paid_amount'], 2) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded p-2">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold mb-1">Pending Invoices</div>
                            <h2 class="mb-0 text-warning">{{ $invoiceStats['pending_invoices'] }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <h5 class="card-title mb-0">Invoice History</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Date</th>
                        <th>Pet</th>
                        <th>Amount</th>
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
                                        @if($invoice->pet->photo_path)
                                            <img src="{{ asset($invoice->pet->photo_path) }}" class="rounded-circle me-2" width="24" height="24">
                                        @endif
                                        {{ $invoice->pet->name }}
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-bold">₱{{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                @if($invoice->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($invoice->status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('customer.billing.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                                @if($invoice->status === 'pending' || $invoice->status === 'partial')
                                    <a href="{{ route('customer.billing.pay', $invoice->id) }}" class="btn btn-sm btn-primary ms-1">
                                        Pay
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
