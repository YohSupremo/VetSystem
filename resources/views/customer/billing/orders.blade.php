@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'My Orders - PawCare')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
}

/* Orders Table */
.orders-table {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    overflow: hidden;
    position: relative;
}

.orders-table::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.orders-table table {
    background: transparent;
    margin: 0;
}

.orders-table th {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
    padding: 1rem;
}

.orders-table td {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: #000;
    padding: 1rem;
    vertical-align: middle;
}

.orders-table tbody tr:hover td {
    background: rgba(255, 255, 255, 0.1);
}

.status-badge {
    padding: 0.4rem 0.875rem;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
}

.status-completed {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.3);
    color: rgba(16, 185, 129, 0.9);
}

.status-shipped {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.3);
    color: rgba(59, 130, 246, 0.9);
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgba(239, 68, 68, 0.9);
}

.status-processing {
    background: rgba(147, 51, 234, 0.2);
    border-color: rgba(147, 51, 234, 0.3);
    color: rgba(147, 51, 234, 0.9);
}

.btn-action {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    color: #000;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--transition-smooth);
}

.btn-action:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.btn-action.danger {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgba(239, 68, 68, 0.9);
}

.btn-action.danger:hover {
    background: rgba(239, 68, 68, 0.3);
    border-color: rgba(239, 68, 68, 0.4);
    color: rgba(239, 68, 68, 0.9);
}

.items-list {
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.items-more {
    color: #555;
    font-size: 0.75rem;
    font-weight: 500;
}

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
}

.empty-state::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-purple), var(--pink));
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 4px 10px rgba(147, 51, 234, 0.3));
}

.empty-title {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
}

.empty-description {
    color: #333;
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@media (max-width: 768px) {
    .orders-table {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .btn-action {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .empty-state {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
}
</style>
@endpush

@section('content')
@include('layout.customer-navbar')
<div class="floating-orbs">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
</div>

<div class="customer-container">
    <main class="customer-main">
        <!-- Page Header -->
        <div class="page-header mb-5 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">My Orders</h1>
                <p class="page-subtitle">Track your product purchases</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('customer.billing.index') }}" class="btn-action">Invoices</a>
                <a href="{{ route('customer.billing.orders') }}" class="btn-action primary">My Orders</a>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h2 class="empty-title">No orders placed yet</h2>
                <p class="empty-description">Browse our shop and find the best products for your pet.</p>
                <a href="{{ route('customer.products.index') }}" class="btn-action primary">
                    <i class="fas fa-shopping-cart me-2"></i>Visit Shop
                </a>
            </div>
        @else
            <div class="orders-table">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
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
                                        <span class="items-list">
                                            @foreach($order->items as $item)
                                                {{ $item->quantity }}x {{ $item->inventoryItem->name ?? 'Item' }}{{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                        </span>
                                        @if($order->items->count() > 2)
                                            <div class="items-more">+{{ $order->items->count() - 2 }} more</div>
                                        @endif
                                    </td>
                                    <td class="fw-bold">₱{{ number_format($order->billing_total ?? $order->items->sum('total'), 2) }}</td>
                                    <td>
                                        @if($order->status === 'completed' || $order->status === 'fulfilled')
                                            <span class="status-badge status-completed">Completed</span>
                                        @elseif($order->status === 'shipped')
                                            <span class="status-badge status-shipped">Shipped</span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="status-badge status-cancelled">Cancelled</span>
                                        @else
                                            <span class="status-badge status-processing">Processing</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('customer.billing.order-details', $order->id) }}" class="btn-action">
                                            Details
                                        </a>
                                        @if(!in_array($order->status, ['cancelled', 'fulfilled', 'shipped', 'completed']))
                                            <form action="{{ route('customer.billing.cancel-order', $order->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to cancel this order? Any payments will be reversed.')">
                                                @csrf
                                                <button type="submit" class="btn-action danger">
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
    </main>
</div>
@endsection
