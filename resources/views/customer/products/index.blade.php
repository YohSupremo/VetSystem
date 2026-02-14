@extends('layout.base')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Pet Care Shop</h1>
            <p class="text-muted">Browse quality products for your pets</p>
        </div>
        <a href="{{ route('customer.cart.index') }}" class="btn btn-outline-primary position-relative">
            <i class="fas fa-shopping-cart me-2"></i>My Cart
            @if($cartItemCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $cartItemCount }}
                    <span class="visually-hidden">items in cart</span>
                </span>
            @endif
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($products->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-box-open fa-3x text-muted"></i>
            </div>
            <h5>No products available</h5>
            <p class="text-muted">Please check back later for new stock.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <!-- Product Image -->
                        <div class="position-relative">
                            @if($product->image_path)
                                <img src="{{ asset($product->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-paw fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            @if($product->quantity <= 0)
                                <span class="position-absolute top-0 end-0 badge bg-danger m-2">Out of Stock</span>
                            @elseif($product->quantity <= 5)
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">Low Stock</span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate" title="{{ $product->name }}">{{ $product->name }}</h5>
                            
                            <div class="mb-2">
                                <span class="badge bg-light text-dark border">{{ $product->category }}</span>
                            </div>
                            
                            <p class="card-text text-muted small flex-grow-1">
                                {{ Str::limit($product->description, 60) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="h5 mb-0 text-primary">₱{{ number_format($product->unit_price, 2) }}</span>
                                <small class="text-muted">{{ $product->quantity }} available</small>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top-0 p-3 pt-0">
                            @if($product->quantity > 0)
                                <form action="{{ route('customer.products.add-to-cart', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->quantity }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-cart-plus"></i> Add
                                        </button>
                                    </div>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100" disabled>Unavailable</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
