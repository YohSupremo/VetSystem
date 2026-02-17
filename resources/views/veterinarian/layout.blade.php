<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Veterinarian Dashboard - PawCare')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body class="veterinarian-body">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="veterinarian-container">
        <!-- Header -->
        <header class="veterinarian-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo-section d-flex align-items-center gap-3">
                    <div class="paw-icon">🐾</div>
                    <div>
                        <h1 class="mb-0">VetPortal</h1>
                        <p class="welcome-text mb-0">Welcome back, Dr. Sarah!</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar">SJ</div>
                    <a href="/logout" class="logout-btn">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="veterinarian-main">
            <!-- Sidebar -->
            <aside class="veterinarian-sidebar">
                <nav class="sidebar-menu">
                    <h3>Main Menu</h3>
                    <ul>
                        <li><a href="{{ route('veterinarian.dashboard') }}" class="{{ request()->routeIs('veterinarian.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="{{ route('veterinarian.appointments.index') }}" class="{{ request()->routeIs('veterinarian.appointments.*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                        <li><a href="{{ route('veterinarian.patients.index') }}" class="{{ request()->routeIs('veterinarian.patients.*') ? 'active' : '' }}"><i class="fas fa-paw"></i> Patients</a></li>
                    </ul>
                    
                    <h3>Medical</h3>
                    <ul>
                        <li><a href="{{ route('veterinarian.medical-records.index') }}" class="{{ request()->routeIs('veterinarian.medical-records.*') ? 'active' : '' }}"><i class="fas fa-file-medical"></i> Medical Records</a></li>
                        <li><a href="{{ route('veterinarian.prescriptions.index') }}" class="{{ request()->routeIs('veterinarian.prescriptions.*') ? 'active' : '' }}"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a></li>
                        <li><a href="{{ route('veterinarian.vaccinations.index') }}" class="{{ request()->routeIs('veterinarian.vaccinations.*') ? 'active' : '' }}"><i class="fas fa-syringe"></i> Vaccinations</a></li>
                        <li><a href="{{ route('veterinarian.laboratory.index') }}" class="{{ request()->routeIs('veterinarian.laboratory.*') ? 'active' : '' }}"><i class="fas fa-microscope"></i> Laboratory</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- Content -->
            <main class="veterinarian-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
