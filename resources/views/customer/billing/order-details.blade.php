@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Order Details - PawCare')

@push('styles')
<style>
.order-details-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
    padding: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.order-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
    margin-bottom: 2rem;
}

.order-title {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1rem;
}

.order-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.meta-item {
    display: flex;
    flex-direction: column;
}

.meta-label {
    font-size: 0.85rem;
    color: #6B7280;
    margin-bottom: 0.25rem;
}

.meta-value {
    font-weight: 600;
    color: var(--primary-purple);
}

.order-status {
    padding: 0.75rem 1.5rem;
    border-radius: 9999px;
    font-size: 0.9rem;
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

.order-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid rgba(167, 139, 250, 0.2);
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: rgba(167, 139, 250, 0.05);
    border-radius: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.order-item:hover {
    background: rgba(167, 139, 250, 0.1);
    transform: translateY(-2px);
}

.item-info {
    flex: 1;
}

.item-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.item-details {
    color: #6B7280;
    font-size: 0.9rem;
}

.item-pricing {
    text-align: right;
    min-width: 120px;
}

.item-quantity {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.item-total {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-purple);
}

.order-summary {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(167, 139, 250, 0.1);
}

.summary-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--primary-purple);
}

.order-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-back, .btn-cancel {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    font-size: 1rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-back {
    background: transparent;
    color: var(--primary-purple);
    border: 2px solid var(--light-purple);
}

.btn-back:hover {
    background: var(--light-purple);
    text-decoration: none;
    color: var(--primary-purple);
}

.btn-cancel {
    background: transparent;
    color: #dc2626;
    border: 2px solid #dc2626;
}

.btn-cancel:hover {
    background: #dc2626;
    color: white;
    text-decoration: none;
}

.notes-section {
    background: rgba(167, 139, 250, 0.05);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .order-details-container {
        padding: 1rem;
    }
    
    .order-meta {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .order-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .item-pricing {
        text-align: left;
        min-width: auto;
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

<div class="order-details-container">
    <div class="order-header">
        <h1 class="order-title">📋 Order Details</h1>
        
        <div class="order-meta">
            <div class="meta-item">
                <span class="meta-label">Order Number</span>
                <span class="meta-value">#{{ $order->id }}</span>
            </div>
            
            <div class="meta-item">
                <span class="meta-label">Date</span>
                <span class="meta-value">{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
            
            <div class="meta-item">
                <span class="meta-label">Status</span>
                <span class="order-status status-{{ $order->status }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            
            <div class="meta-item">
                <span class="meta-label">Order Type</span>
                <span class="meta-value">{{ ucfirst($order->order_type) }}</span>
            </div>
        </div>
    </div>

    <div class="order-content">
        <h2 class="section-title">🛍️ Order Items</h2>
        
        @foreach($order->items as $item)
            <div class="order-item">
                <div class="item-info">
                    <div class="item-name">{{ $item->description }}</div>
                    <div class="item-details">
                        @if($item->item_type === 'inventory' && $item->inventoryItem)
                            Category: {{ ucfirst($item->inventoryItem->category) }}
                        @endif
                        @if($item->item_type === 'inventory' && $item->inventoryItem && $item->inventoryItem->requires_prescription)
                            • Prescription Required
                        @endif
                    </div>
                </div>
                
                <div class="item-pricing">
                    <div class="item-quantity">
                        Qty: {{ $item->quantity }} × ₱{{ number_format($item->unit_price, 2) }}
                    </div>
                    <div class="item-total">
                        ₱{{ number_format($item->total, 2) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="order-summary">
        <h2 class="section-title">💰 Order Summary</h2>
        
        <div class="summary-row">
            <span>Subtotal</span>
            <span>₱{{ number_format($order->items->sum('total'), 2) }}</span>
        </div>
        
        <div class="summary-row">
            <span>Tax (if applicable)</span>
            <span>₱0.00</span>
        </div>
        
        <div class="summary-row">
            <span>Total Amount</span>
            <span>₱{{ number_format($order->items->sum('total'), 2) }}</span>
        </div>
    </div>

    @if($order->notes)
        <div class="order-content">
            <h2 class="section-title">📝 Order Notes</h2>
            <div class="notes-section">
                {{ $order->notes }}
            </div>
        </div>
    @endif

    <div class="order-actions">
        <a href="{{ route('customer.billing.orders') }}" class="btn-back">
            ← Back to Orders
        </a>
        
        @if(in_array($order->status, ['draft', 'confirmed']))
            <form action="{{ route('customer.billing.cancel-order', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-cancel">
                    ❌ Cancel Order
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
