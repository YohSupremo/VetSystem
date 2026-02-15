@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('customer.billing.orders') }}" class="text-decoration-none text-muted me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0">Order #{{ $order->id }}</h1>
                <div class="ms-auto">
                    @if($order->status === 'completed' || $order->status === 'fulfilled')
                        <span class="badge bg-success fs-6">Completed</span>
                    @elseif($order->status === 'shipped')
                        <span class="badge bg-info text-dark fs-6">Shipped</span>
                    @elseif($order->status === 'cancelled')
                        <span class="badge bg-danger fs-6">Cancelled</span>
                    @else
                        <span class="badge bg-primary fs-6">Processing</span>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 fw-bold">
                    Order Summary
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Item</th>
                                    <th class="text-end py-3">Qty</th>
                                    <th class="text-end py-3 pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-1 me-3 border" style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $item->inventoryItem->name ?? 'Item' }}</div>
                                                    <div class="small text-muted">₱{{ number_format($item->unit_price, 2) }} / unit</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end align-middle">{{ $item->quantity }}</td>
                                        <td class="text-end align-middle pe-4 fw-bold">₱{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="2" class="text-end pt-3 fw-bold text-uppercase text-muted small">Total Amount</td>
                                    <td class="text-end pt-3 pe-4 fw-bold fs-5 text-primary">₱{{ number_format($order->items->sum('total'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Order Information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Order Date</span>
                            <span class="fw-bold">{{ date('F d, Y h:i A', strtotime($order->created_at)) }}</span>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Payment Status</span>
                            <span class="fw-bold">
                                {{ ucfirst($order->payment_status ?? 'Paid') }}
                            </span>
                        </div>
                        @if($order->notes)
                            <div class="col-12">
                                <span class="text-muted small d-block">Notes</span>
                                <p class="mb-0 bg-light p-2 rounded">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>

                    @if(!in_array($order->status, ['cancelled', 'shipped', 'fulfilled', 'completed'], true))
                        <hr class="my-4">
                        <div class="d-flex justify-content-end">
                            <form action="{{ route('customer.billing.cancel-order', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? Any payments will be reversed.')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">Cancel Order</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
