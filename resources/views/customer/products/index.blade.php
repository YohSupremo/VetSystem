@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Pet Care Shop - PawCare')

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

.product-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
    position: relative;
    overflow: hidden;
    transition: var(--transition-smooth);
}

.product-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    pointer-events: none;
}

.product-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 60px rgba(31, 38, 135, 0.4);
    border-color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
}

.product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
    border-radius: 20px 20px 0 0;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-smooth);
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    padding: 0.4rem 0.875rem;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.badge-out-of-stock {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
    color: rgba(239, 68, 68, 0.9);
}

.badge-low-stock {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.3);
    color: rgba(245, 158, 11, 0.9);
}

.product-body {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.product-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #000;
    margin-bottom: 0.5rem;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
}

.product-category {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(147, 51, 234, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(147, 51, 234, 0.2);
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 0.75rem;
}

.product-description {
    color: #333;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.product-stock {
    color: #555;
    font-size: 0.875rem;
    font-weight: 500;
}

.product-footer {
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-cart {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    color: #000;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: var(--transition-smooth);
    text-align: center;
    position: relative;
    overflow: hidden;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-cart::before {
    content: '';
    position: absolute;
    inset: -1px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(236, 72, 153, 0.2));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.btn-cart:hover::before {
    opacity: 1;
}

.btn-cart:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.btn-cart:disabled {
    background: rgba(107, 114, 128, 0.2);
    border-color: rgba(107, 114, 128, 0.3);
    color: rgba(107, 114, 128, 0.6);
    cursor: not-allowed;
}

.btn-cart:disabled:hover {
    transform: none;
    box-shadow: none;
}

.btn-cart:disabled::before {
    opacity: 0;
}

.cart-input {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px 0 0 8px;
    padding: 0.75rem;
    color: #000;
    font-weight: 600;
    text-align: center;
    transition: var(--transition-smooth);
}

.cart-input:focus {
    outline: none;
    border-color: rgba(147, 51, 234, 0.4);
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.2);
}

.input-group {
    display: flex;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(31, 38, 135, 0.15);
}

.input-group .cart-input {
    border-right: none;
    flex: 0 0 80px;
    max-width: 80px;
}

.input-group .btn-cart {
    border-left: none;
    border-radius: 0 12px 12px 0;
    flex: 1;
}

.btn-my-cart {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 0.75rem 1.5rem;
    color: #000;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: var(--transition-smooth);
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-my-cart::before {
    content: '';
    position: absolute;
    inset: -1px;
    border-radius: 50px;
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(236, 72, 153, 0.2));
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.btn-my-cart:hover::before {
    opacity: 1;
}

.btn-my-cart:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
    border-color: rgba(147, 51, 234, 0.4);
    color: #000;
    text-decoration: none;
}

.cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
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

.alert {
    background: rgba(16, 185, 129, 0.2);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-left: 4px solid rgba(16, 185, 129, 0.5);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #000;
    padding: 1.125rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.875rem;
    font-weight: 600;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
}

.alert-success {
    background: rgba(16, 185, 129, 0.2);
    border-left-color: rgba(16, 185, 129, 0.5);
    border-color: rgba(16, 185, 129, 0.3);
    color: #000;
}

.alert-danger {
    background: rgba(239, 68, 68, 0.2);
    border-left-color: rgba(239, 68, 68, 0.5);
    border-color: rgba(239, 68, 68, 0.3);
    color: #000;
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
    .product-card {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .btn-cart {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .btn-my-cart {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
    
    .cart-input {
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
                <h1 class="page-title">Pet Care Shop</h1>
                <p class="page-subtitle">Browse quality products for your pets</p>
            </div>
            <a href="{{ route('customer.cart.index') }}" class="btn-my-cart position-relative">
                <i class="fas fa-shopping-cart me-2"></i>My Cart
                @if($cartItemCount > 0)
                    <span class="cart-badge">{{ $cartItemCount }}</span>
                @endif
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($products->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h2 class="empty-title">No products available</h2>
                <p class="empty-description">Please check back later for new stock.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="product-card h-100">
                            <!-- Product Image -->
                            <div class="product-image">
                                @if($product->image_path)
                                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-paw fa-3x" style="color: rgba(147, 51, 234, 0.3);"></i>
                                    </div>
                                @endif
                                
                                @if($product->quantity <= 0)
                                    <span class="product-badge badge-out-of-stock">Out of Stock</span>
                                @elseif($product->quantity <= 5)
                                    <span class="product-badge badge-low-stock">Low Stock</span>
                                @endif
                            </div>

                            <div class="product-body">
                                <h5 class="product-title text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>
                                
                                <div class="mb-2">
                                    <span class="product-category">{{ $product->category }}</span>
                                </div>
                                
                                <p class="product-description">
                                    {{ Str::limit($product->description, 60) }}
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price">₱{{ number_format($product->unit_price, 2) }}</span>
                                    <small class="product-stock">{{ $product->quantity }} available</small>
                                </div>
                            </div>

                            <div class="product-footer">
                                @if($product->quantity > 0)
                                    <form action="{{ route('customer.products.add-to-cart', $product->id) }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="number" name="quantity" class="cart-input" value="1" min="1" max="{{ $product->quantity }}">
                                            <button type="submit" class="btn-cart">
                                                <i class="fas fa-cart-plus"></i> Add
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <button class="btn-cart" disabled>Unavailable</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</div>
@endsection
