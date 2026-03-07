@push('styles')
<style>
.customer-navbar {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    position: sticky;
    top: 0;
    z-index: 1020;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
}

.customer-navbar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
}

.customer-navbar .navbar-brand {
    font-weight: 800;
    font-size: 1.35rem;
    background: linear-gradient(135deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 4px rgba(147, 51, 234, 0.1);
}

.customer-navbar .paw-icon {
    font-size: 1.8rem;
    margin-right: 0.5rem;
    filter: drop-shadow(0 2px 4px rgba(147, 51, 234, 0.2));
}

.customer-navbar .nav-link {
    font-weight: 600;
    color: #000;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    transition: var(--transition-smooth);
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.customer-navbar .nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.customer-navbar .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #000;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(31, 38, 135, 0.2);
    text-decoration: none;
}

.customer-navbar .nav-link:hover::before {
    left: 100%;
}

.customer-navbar .nav-link.active {
    background: linear-gradient(135deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
    color: #ffffff !important;
    box-shadow: 0 6px 20px rgba(147, 51, 234, 0.4);
    border: none;
    transform: translateY(-1px);
}

.customer-navbar .nav-link.active:hover {
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.5);
}

.customer-navbar .user-avatar-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
    box-shadow: 0 4px 12px rgba(147, 51, 234, 0.3);
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: var(--transition-smooth);
}

.customer-navbar .user-avatar-small:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(147, 51, 234, 0.4);
}

.customer-navbar .btn-logout {
    border-radius: 50px;
    padding: 0.5rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #000;
    transition: var(--transition-smooth);
    text-decoration: none;
}

.customer-navbar .btn-logout:hover {
    background: linear-gradient(135deg, var(--primary-purple, #6d28d9), var(--pink, #ec4899));
    color: #ffffff;
    border: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(147, 51, 234, 0.3);
    text-decoration: none;
}

.customer-navbar .text-muted {
    color: #333 !important;
    font-weight: 500;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .customer-navbar {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .customer-navbar .nav-link {
        background: rgba(255, 255, 255, 0.15) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #000 !important;
    }
    
    .customer-navbar .nav-link:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .customer-navbar .btn-logout {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(15px) !important;
        -webkit-backdrop-filter: blur(15px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: #000 !important;
    }
}

/* Navbar collapse animation */
.navbar-collapse {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    margin-top: 0.5rem;
    padding: 1rem;
}

@media (max-width: 991.98px) {
    .navbar-collapse {
        position: absolute;
        top: 100%;
        left: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
    }
}
</style>
@endpush

<nav class="navbar navbar-expand-lg customer-navbar shadow-sm">
    <div class="container-fluid px-3 px-md-4">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('customer.dashboard') }}">
            <span class="paw-icon">🐾</span>
            <span>{{ $clinicName ?? 'PawCare' }}</span>
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
                    <a class="nav-link {{ request()->routeIs('customer.incidents.*') ? 'active' : '' }}"
                       href="{{ route('customer.incidents.index') }}">
                        Incident Reports
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
                    <a href="{{ route('customer.profile') }}" class="btn btn-sm btn-outline-light" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); color: #000;">
                        <i class="fas fa-user me-1"></i>Profile
                    </a>
                    <div class="user-avatar-small">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}?t={{ time() }}" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                        @else
                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                        @endif
                    </div>
                @endisset

                <a href="/logout" class="btn btn-sm btn-outline-secondary btn-logout ms-1">
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

@include('partials.flash-messages', ['containerClass' => 'container mt-3 app-flash-themed'])

