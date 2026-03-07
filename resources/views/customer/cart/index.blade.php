@extends('layout.base')

@section('content')
@include('layout.customer-navbar')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Shopping Cart</h1>
        <a href="{{ route('customer.products.index') }}" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Continue Shopping
        </a>
    </div>

    @if($cart->cartItems->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-shopping-cart fa-3x text-muted"></i>
            </div>
            <h5>Your cart is empty</h5>
            <p class="text-muted">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('customer.products.index') }}" class="btn btn-primary mt-2">
                Browse Products
            </a>
        </div>
    @else
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3">Product</th>
                                        <th class="py-3">Price</th>
                                        <th class="py-3" style="width: 150px;">Quantity</th>
                                        <th class="text-end py-3">Total</th>
                                        <th class="text-center py-3" style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->cartItems as $item)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-3 border" style="width: 60px; height: 60px; display:flex; align-items:center; justify-content:center;">
                                                        @if($item->inventoryItem && $item->inventoryItem->image_path)
                                                            <img src="{{ asset($item->inventoryItem->image_path) }}" alt="{{ $item->inventoryItem->name }}" style="max-width: 100%; max-height: 100%;">
                                                        @else
                                                            <i class="fas fa-box fa-lg text-muted"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $item->inventoryItem->name ?? 'Unknown Item' }}</h6>
                                                        <small class="text-muted text-uppercase">{{ $item->inventoryItem->category ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                            <td>
                                                <form action="{{ route('customer.cart.update', $item->id) }}" method="POST" class="d-flex">
                                                    @csrf
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->inventoryItem->quantity }}" class="form-control form-control-sm text-center me-2" onchange="this.form.submit()">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary d-none"><i class="fas fa-sync"></i></button>
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold">₱{{ number_format($item->total, 2) }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('customer.cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Remove this item from cart?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Remove item">
                                                        Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary & Checkout -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal ({{ $cart->cartItems->sum('quantity') }} items)</span>
                            <span class="fw-bold">₱{{ number_format($cartSubtotal ?? $cart->cartItems->sum('total'), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax ({{ number_format($defaultTaxRate ?? 0, 2) }}%)</span>
                            <span class="fw-bold">₱{{ number_format($cartTaxAmount ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Shipping</span>
                            <span class="text-success">Free</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total</span>
                            <span class="fw-bold fs-5 text-primary">₱{{ number_format($cartGrandTotal ?? $cart->cartItems->sum('total'), 2) }}</span>
                        </div>
                        
                        <form action="{{ route('customer.cart.checkout') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="payment_method" class="form-label small fw-bold">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash (Pay on Pickup)</option>
                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="mobile_payment" {{ old('payment_method') == 'mobile_payment' ? 'selected' : '' }}>Mobile Payment (GCash/Maya)</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Online payments will be recorded as paid immediately.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label small fw-bold">Order Notes (Optional)</label>
                                <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="Special instructions...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm" onclick="return confirm('Place order now?')">
                                Proceed to Checkout
                            </button>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <form action="{{ route('customer.cart.clear') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link text-muted btn-sm text-decoration-none" onclick="return confirm('Empty your entire cart?')">
                                    Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
