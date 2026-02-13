@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Product Details - PawCare')

@push('styles')
<style>
.customer-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
}

.customer-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    border-radius: 0 0 2rem 2rem;
}

.logo-section .paw-icon {
    font-size: 2.5rem;
    animation: bounce 2s infinite;
}

.logo-section h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.customer-main {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.product-details {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.product-header {
    display: flex;
    gap: 2rem;
    margin-bottom: 2rem;
}

.product-image {
    width: 300px;
    height: 300px;
    border-radius: 1rem;
    object-fit: cover;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.product-info {
    flex: 1;
}

.product-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-text);
    margin-bottom: 0.5rem;
}

.product-category {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: var(--primary-purple);
    color: white;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-purple);
    margin-bottom: 1rem;
}

.product-description {
    color: var(--light-text);
    line-height: 1.6;
    margin-bottom: 2rem;
}

.product-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
}

.btn-secondary {
    background: var(--soft-gray);
    color: var(--dark-text);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-selector input {
    width: 60px;
    padding: 0.5rem;
    border: 2px solid var(--soft-gray);
    border-radius: 0.25rem;
    text-align: center;
}

.stock-info {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.in-stock {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}

.low-stock {
    background: rgba(255, 193, 7, 0.1);
    color: #f59e0b;
}

.out-of-stock {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

@media (max-width: 768px) {
    .product-header {
        flex-direction: column;
    }
    
    .product-image {
        width: 100%;
        height: 250px;
    }
    
    .product-actions {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
@endpush

@section('content')
<div class="customer-container">
    <header class="customer-header">
        <div class="logo-section">
            <a href="{{ route('customer.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                <i class="fas fa-paw paw-icon text-primary"></i>
                <h1 class="ms-3 mb-0">PawCare</h1>
            </a>
        </div>
        <div class="user-section">
            <span class="text-muted">Welcome, {{ $user->first_name }}</span>
        </div>
    </header>

    <main class="customer-main">
        <div class="product-details">
            <div class="product-header">
                @if($product->image_path)
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="product-image">
                @else
                <div class="product-image d-flex align-items-center justify-content-center bg-light">
                    <i class="fas fa-box fa-3x text-muted"></i>
                </div>
                @endif
                
                <div class="product-info">
                    <h1 class="product-title">{{ $product->name }}</h1>
                    <span class="product-category">{{ ucfirst($product->category) }}</span>
                    <div class="product-price">${{ number_format($product->unit_price, 2) }}</div>
                    
                    <div class="stock-info {{ $product->stock_quantity > 10 ? 'in-stock' : ($product->stock_quantity > 0 ? 'low-stock' : 'out-of-stock') }}">
                        @if($product->stock_quantity > 10)
                            In Stock ({{ $product->stock_quantity }} available)
                        @elseif($product->stock_quantity > 0)
                            Low Stock ({{ $product->stock_quantity }} left)
                        @else
                            Out of Stock
                        @endif
                    </div>
                    
                    <div class="product-actions">
                        @if($product->stock_quantity > 0)
                        <form action="{{ route('customer.products.add-to-cart', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            <div class="quantity-selector">
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                        @else
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-times"></i> Out of Stock
                        </button>
                        @endif
                        
                        <a href="{{ route('customer.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>

            @if($product->description)
            <div class="product-description">
                <h3>Description</h3>
                <p>{{ $product->description }}</p>
            </div>
            @endif
        </div>
    </main>
</div>
@endsection
