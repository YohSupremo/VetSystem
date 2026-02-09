@push('styles')
<style>
.customer-navbar {
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(167, 139, 250, 0.2);
    position: sticky;
    top: 0;
    z-index: 1020;
}

.customer-navbar .navbar-brand {
    font-weight: 700;
    font-size: 1.25rem;
    background: linear-gradient(135deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.customer-navbar .paw-icon {
    font-size: 1.6rem;
    margin-right: 0.4rem;
}

.customer-navbar .nav-link {
    font-weight: 500;
    color: #4B5563;
    padding: 0.4rem 0.9rem;
    border-radius: 9999px;
    transition: all 0.2s ease;
}

.customer-navbar .nav-link:hover {
    background: rgba(167, 139, 250, 0.10);
    color: var(--primary-purple, #6d28d9);
}

.customer-navbar .nav-link.active {
    background: linear-gradient(135deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(147, 51, 234, 0.25);
}

.customer-navbar .user-avatar-small {
    width: 36px;
    height: 36px;
    border-radius: 9999px;
    background: linear-gradient(135deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.customer-navbar .btn-logout {
    border-radius: 9999px;
    padding: 0.35rem 0.9rem;
    font-size: 0.85rem;
    border-color: rgba(167, 139, 250, 0.7);
    color: var(--primary-purple, #6d28d9);
}

.customer-navbar .btn-logout:hover {
    background: linear-gradient(135deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
    color: #ffffff;
}
</style>
@endpush

<nav class="navbar navbar-expand-lg customer-navbar shadow-sm">
    <div class="container-fluid px-3 px-md-4">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('customer.dashboard') }}">
            <span class="paw-icon">🐾</span>
            <span>PawCare</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#customerNavbar"
                aria-controls="customerNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="customerNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                       href="{{ route('customer.dashboard') }}">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.pets.*') ? 'active' : '' }}"
                       href="{{ route('customer.pets.index') }}">
                        My Pets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.appointments.*') ? 'active' : '' }}"
                       href="{{ route('customer.appointments.index') }}">
                        Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.medical-records.*') ? 'active' : '' }}"
                       href="{{ route('customer.medical-records.index') }}">
                        Medical Records
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.billing.*') ? 'active' : '' }}"
                       href="{{ route('customer.billing.index') }}">
                        Billing
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('customer.products.*') ? 'active' : '' }}"
                       href="{{ route('customer.products.index') }}">
                        Shop
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                @isset($user)
                    <span class="text-muted d-none d-md-inline" style="font-size: 0.9rem;">
                        Hi, {{ $user->first_name }}
                    </span>
                    <div class="user-avatar-small">
                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                    </div>
                @endisset

                <a href="/logout" class="btn btn-sm btn-outline-secondary btn-logout ms-1">
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

