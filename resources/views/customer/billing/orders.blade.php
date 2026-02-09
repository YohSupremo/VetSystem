@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'My Orders - PawCare')

@push('styles')
<style>
.orders-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.orders-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.orders-header h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.order-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 1.5rem;
    padding: 1.5rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
    transition: all 0.3s ease;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.15);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
}

.order-number {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-purple);
}

.order-date {
    color: #6B7280;
    font-size: 0.9rem;
}

.order-status {
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-draft {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.status-confirmed {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.status-fulfilled {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
}

.status-cancelled {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.order-items {
    margin-bottom: 1rem;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: rgba(167, 139, 250, 0.05);
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
}

.item-name {
    font-weight: 500;
    color: var(--primary-purple);
}

.item-details {
    color: #6B7280;
    font-size: 0.9rem;
}

.order-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(167, 139, 250, 0.2);
    font-weight: 600;
    font-size: 1.1rem;
    color: var(--primary-purple);
}

.order-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.btn-view-order, .btn-cancel-order {
    padding: 0.6rem 1.2rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-view-order {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
}

.btn-view-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.4);
    text-decoration: none;
    color: white;
}

.btn-cancel-order {
    background: transparent;
    color: #dc2626;
    border: 1px solid #dc2626;
}

.btn-cancel-order:hover {
    background: #dc2626;
    color: white;
    text-decoration: none;
}

.empty-orders {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.empty-orders-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
}

.empty-orders h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 1rem;
}

.empty-orders p {
    color: #6B7280;
    margin-bottom: 2rem;
    font-size: 1rem;
}

@media (max-width: 768px) {
    .orders-container {
        padding: 1rem;
    }
    
    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .order-actions {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>

<div class="orders-container">
    <div class="orders-header">
        <div>
            <h1>📋 My Orders</h1>
            <p class="text-muted mb-0">View and manage your order history</p>
        </div>
        <a href="{{ route('customer.dashboard') }}" class="btn-view">
            ← Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($orders->count() > 0)
        @foreach($orders as $order)
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-number">Order #{{ $order->id }}</div>
                        <div class="order-date">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    <span class="order-status status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="order-items">
                    @foreach($order->items as $item)
                        <div class="order-item">
                            <div>
                                <div class="item-name">{{ $item->description }}</div>
                                <div class="item-details">
                                    @if($item->item_type === 'inventory' && $item->inventoryItem)
                                        Category: {{ ucfirst($item->inventoryItem->category) }}
                                    @endif
                                    @if($item->item_type === 'inventory' && $item->inventoryItem && $item->inventoryItem->requires_prescription)
                                        • Prescription Required
                                    @endif
                                    Qty: {{ $item->quantity }} × ₱{{ number_format($item->unit_price, 2) }}
                                </div>
                            </div>
                            <div>
                                <strong>₱{{ number_format($item->total, 2) }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="order-total">
                    <span>Total Amount:</span>
                    <span>₱{{ number_format($order->items->sum('total'), 2) }}</span>
                </div>

                @if($order->notes)
                    <div style="margin-top: 1rem; padding: 1rem; background: rgba(167, 139, 250, 0.05); border-radius: 0.5rem; font-size: 0.9rem; color: #6B7280;">
                        <strong>Notes:</strong> {{ $order->notes }}
                    </div>
                @endif

                <div class="order-actions">
                    <a href="{{ route('customer.billing.order-details', $order->id) }}" class="btn-view-order">
                        👁️ View Details
                    </a>
                    
                    @if(in_array($order->status, ['draft', 'confirmed']))
                        <form action="{{ route('customer.billing.cancel-order', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-cancel-order">
                                ❌ Cancel Order
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-orders">
            <div class="empty-orders-icon">📋</div>
            <h3>No orders yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here.</p>
            <a href="{{ route('customer.products.index') }}" class="btn-primary">
                🛍️ Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection
