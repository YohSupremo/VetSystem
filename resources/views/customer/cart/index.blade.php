@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Shopping Cart - PawCare')

@push('styles')
<style>
.cart-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.cart-header h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cart-summary {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.cart-item {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 1.5rem;
    padding: 1.5rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
    transition: all 0.3s ease;
}

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.15);
}

.item-image {
    width: 80px;
    height: 80px;
    border-radius: 1rem;
    background: linear-gradient(135deg, var(--light-purple), var(--secondary-purple));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.2);
}

.item-details h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.item-details p {
    color: #6B7280;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-control input {
    width: 60px;
    text-align: center;
    margin-bottom: 0.5rem;
}

.btn-remove {
    background: transparent;
    color: #dc2626;
    border: 1px solid #dc2626;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.btn-remove:hover {
    background: #dc2626;
    color: white;
    text-decoration: none;
}

.btn-checkout {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    text-decoration: none;
    padding: 1rem 2rem;
    border-radius: 1rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-block;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    width: 100%;
    text-align: center;
    border: none;
    cursor: pointer;
}

.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    text-decoration: none;
    color: white;
}

.btn-clear-cart {
    background: transparent;
    color: #6B7280;
    border: 1px solid #6B7280;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.btn-clear-cart:hover {
    background: #6B7280;
    color: white;
    text-decoration: none;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
}

.summary-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-purple);
}

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.empty-cart-icon {
    font-size: 5rem;
    margin-bottom: 2rem;
    background: linear-gradient(135deg, var(--light-purple), var(--secondary-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.empty-cart h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 1rem;
}

.empty-cart p {
    color: #6B7280;
    margin-bottom: 2rem;
    font-size: 1rem;
}

@media (max-width: 768px) {
    .cart-container {
        padding: 1rem;
    }
    
    .cart-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .quantity-control {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .quantity-control input {
        margin-bottom: 0.5rem;
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

<div class="cart-container">
    <div class="cart-header">
        <div>
            <h1>🛒 Shopping Cart</h1>
            <p class="text-muted mb-0">Review your items before checkout</p>
        </div>
        <a href="{{ route('customer.products.index') }}" class="btn-view">
            ← Continue Shopping
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
    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @if($cart->isEmpty())
        <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <h3>Your cart is empty</h3>
            <p>Browse our products and add items to your cart</p>
            <a href="{{ route('customer.products.index') }}" class="btn-primary">🛍️ Start Shopping</a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                @foreach($cart->cartItems as $item)
                    <div class="cart-item">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="item-image">
                                    🛍️
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="item-details">
                                    <h4>{{ $item->inventoryItem->name }}</h4>
                                    <p class="text-capitalize">{{ $item->inventoryItem->category }}</p>
                                    @if($item->inventoryItem->description)
                                        <p>{{ \Illuminate\Support\Str::limit($item->inventoryItem->description, 80) }}</p>
                                    @endif
                                    <p><strong>Stock available:</strong> {{ $item->inventoryItem->quantity }}</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <p><strong>Price:</strong></p>
                                <p>₱{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <div class="col-md-2">
                                <p><strong>Quantity:</strong></p>
                                <form action="{{ route('customer.cart.update', $item->id) }}" method="POST" class="quantity-control">
                                    @csrf
                                    <input type="number" name="quantity" min="1" max="{{ $item->inventoryItem->quantity }}" value="{{ $item->quantity }}" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-outline-primary mt-1">Update</button>
                                </form>
                            </div>
                            <div class="col-md-2 text-end">
                                <p><strong>Total:</strong></p>
                                <p>₱{{ number_format($item->total, 2) }}</p>
                                <form action="{{ route('customer.cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-remove">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h3 class="mb-3">Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Items ({{ $cart->total_items }})</span>
                        <span>₱{{ number_format($cart->cartItems->sum(function($item) { return $item->quantity * $item->unit_price; }), 2) }}</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($cart->total, 2) }}</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax (if applicable)</span>
                        <span>₱0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Total</span>
                        <span>₱{{ number_format($cart->total, 2) }}</span>
                    </div>

                    <form action="{{ route('customer.cart.checkout') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Any special instructions or notes..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn-checkout mb-2">
                            ✅ Proceed to Checkout
                        </button>
                        
                        <div class="text-center">
                            <form action="{{ route('customer.cart.clear') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-clear-cart">Clear Cart</button>
                            </form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
