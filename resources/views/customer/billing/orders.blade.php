@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">My Orders</h1>
            <p class="text-muted">Track your product purchases</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('customer.billing.index') }}" class="btn btn-outline-primary">Invoices</a>
            <a href="{{ route('customer.billing.orders') }}" class="btn btn-primary active">My Orders</a>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-shopping-bag fa-3x text-muted"></i>
            </div>
            <h5>No orders placed yet</h5>
            <p class="text-muted">Browse our shop and find the best products for your pet.</p>
            <a href="{{ route('customer.products.index') }}" class="btn btn-outline-primary mt-2">
                Visit Shop
            </a>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold">#{{ $order->id }}</td>
                                <td>{{ date('M d, Y', strtotime($order->created_at)) }}</td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                        @foreach($order->items as $item)
                                            {{ $item->quantity }}x {{ $item->inventoryItem->name ?? 'Item' }}{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    </span>
                                    @if($order->items->count() > 2)
                                        <small class="text-muted">+{{ $order->items->count() - 2 }} more</small>
                                    @endif
                                </td>
                                <td>₱{{ number_format($order->items->sum('total'), 2) }}</td>
                                <td>
                                    @if($order->status === 'completed' || $order->status === 'fulfilled')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="badge bg-info text-dark">Shipped</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-primary">Processing</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('customer.billing.order-details', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                        Details
                                    </a>
                                    @if(!in_array($order->status, ['cancelled', 'fulfilled', 'shipped', 'completed']))
                                        <form action="{{ route('customer.billing.cancel-order', $order->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to cancel this order? Any payments will be reversed.')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
