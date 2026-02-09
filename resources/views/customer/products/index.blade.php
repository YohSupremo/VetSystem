@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Shop Products - PawCare')

@push('styles')
<style>
.shop-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.shop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.shop-header h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.product-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 1.5rem;
    padding: 1.5rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(147, 51, 234, 0.15);
}

.product-image {
    width: 100%;
    height: 200px;
    border-radius: 1rem;
    overflow: hidden;
    background: linear-gradient(135deg, var(--light-purple), var(--secondary-purple));
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    position: relative;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-image:hover img {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.product-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.product-meta {
    font-size: 0.85rem;
    color: #6B7280;
    margin-bottom: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.product-category {
    background: rgba(167, 139, 250, 0.1);
    color: var(--primary-purple);
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.8rem;
    width: fit-content;
}

.product-price {
    font-weight: 700;
    font-size: 1.5rem;
    color: var(--primary-purple);
    margin-bottom: 0.5rem;
}

.product-stock {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stock-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #10b981;
}

.stock-low {
    background: #f59e0b;
}

.stock-out {
    background: #dc2626;
}

.product-actions {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quantity-input-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-input {
    width: 80px;
    text-align: center;
    border: 2px solid var(--light-purple);
    border-radius: 0.5rem;
    padding: 0.5rem;
    font-weight: 500;
}

.btn-add-to-cart {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    width: 100%;
}

.btn-add-to-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.btn-add-to-cart:disabled {
    background: linear-gradient(135deg, #9ca3af, #6b7280);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

@media (max-width: 768px) {
    .shop-container {
        padding: 1rem;
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

<div class="shop-container">
    <div class="shop-header">
        <div>
            <h1>🛍️ Pet Care Shop</h1>
            <p class="text-muted mb-0">Browse available products and add items to your cart.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('customer.cart.index') }}" class="btn-view position-relative">
                🛒 Cart
                @if($cartItemCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;">
                        {{ $cartItemCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('customer.dashboard') }}" class="btn-view">
                ← Dashboard
            </a>
        </div>
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

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($products->count())
        <div class="row g-3">
            @foreach($products as $product)
                <div class="col-md-6 col-lg-4">
                    <div class="product-card">
                        <div class="product-image">
                            @if($product->image_path)
                                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}">
                            @else
                                <div style="font-size: 3rem; color: rgba(255,255,255,0.7);">🛍️</div>
                            @endif
                            
                            @if($product->quantity <= 5)
                                <div class="product-badge">Low Stock</div>
                            @elseif($product->quantity <= 0)
                                <div class="product-badge stock-out">Out of Stock</div>
                            @endif
                        </div>
                        
                        <div class="product-content">
                            <div class="product-name">{{ $product->name }}</div>
                            
                            <div class="product-meta">
                                @if($product->category)
                                    <div class="product-category">{{ ucfirst($product->category) }}</div>
                                @endif
                                
                                @if($product->description)
                                    <div>{{ \Illuminate\Support\Str::limit($product->description, 100) }}</div>
                                @endif
                                
                                @if($product->sku)
                                    <div><strong>SKU:</strong> {{ $product->sku }}</div>
                                @endif
                            </div>
                            
                            <div class="product-price">₱{{ number_format($product->unit_price, 2) }}</div>
                            
                            <div class="product-stock">
                                <div class="stock-indicator {{ $product->quantity <= 5 ? 'stock-low' : ($product->quantity <= 0 ? 'stock-out' : '') }}"></div>
                                <span>{{ $product->quantity }} units available</span>
                            </div>
                        </div>
                        
                        <div class="product-actions">
                            @if(!$product->requires_prescription && $product->quantity > 0)
                                <form action="{{ route('customer.products.add-to-cart', $product->id) }}" method="POST" class="quantity-input-group" enctype="multipart/form-data">
                                    @csrf
                                    <input type="number" name="quantity" min="1" max="{{ $product->quantity }}" value="1" class="quantity-input">
                                    <button type="submit" class="btn-add-to-cart">
                                        🛒 Add to Cart
                                    </button>
                                </form>
                            @else
                                <button class="btn-add-to-cart" disabled>
                                    {{ $product->requires_prescription ? '📋 Prescription Required' : '🚫 Out of Stock' }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">�️</div>
            <h3>No products available</h3>
            <p>Please check back later for available pet care products.</p>
        </div>
    @endif
</div>
@endsection

