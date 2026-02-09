@extends('layout.base')

@php($bodyClass = 'customer-body')

@section('title', 'Billing - PawCare')

@push('styles')
<style>
.billing-container {
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 2;
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.billing-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.billing-header h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.billing-nav {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid rgba(167, 139, 250, 0.2);
    padding-bottom: 1rem;
}

.nav-item {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    color: #6B7280;
}

.nav-item:hover, .nav-item.active {
    background: linear-gradient(135deg, var(--primary-purple), var(--pink));
    color: white;
    text-decoration: none;
}

.billing-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 2rem;
    padding: 2rem;
    border: 1px solid rgba(167, 139, 250, 0.2);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.1);
}

.empty-billing {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-billing-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
}

.empty-billing h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--primary-purple);
    margin-bottom: 1rem;
}

.empty-billing p {
    color: #6B7280;
    margin-bottom: 2rem;
    font-size: 1rem;
}

@media (max-width: 768px) {
    .billing-container {
        padding: 1rem;
    }
    
    .billing-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .billing-nav {
        flex-wrap: wrap;
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

<div class="billing-container">
    <div class="billing-header">
        <div>
            <h1>💳 Billing & Payments</h1>
            <p class="text-muted mb-0">Manage your invoices, payments, and orders</p>
        </div>
        <a href="{{ route('customer.dashboard') }}" class="btn-view">
            ← Dashboard
        </a>
    </div>

    <div class="billing-nav">
        <a href="{{ route('customer.billing.orders') }}" class="nav-item">
            📋 My Orders
        </a>
        <a href="#" class="nav-item">
            🧾 Invoices
        </a>
        <a href="#" class="nav-item">
            📄 Payment History
        </a>
    </div>

    <div class="billing-content">
        <div class="empty-billing">
            <div class="empty-billing-icon">💳</div>
            <h3>Billing Center</h3>
            <p>Choose an option above to view your orders, invoices, or payment history.</p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('customer.billing.orders') }}" class="btn-primary">
                    📋 View Orders
                </a>
                <a href="{{ route('customer.products.index') }}" class="btn-view">
                    🛍️ Go Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
