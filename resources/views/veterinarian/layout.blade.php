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
                    <button class="btn btn-light position-relative" id="vetNotificationsBtn" type="button" data-bs-toggle="modal" data-bs-target="#vetNotificationsModal">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="vetUnreadBadge"></span>
                    </button>
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

    <div class="modal fade" id="vetNotificationsModal" tabindex="-1" aria-labelledby="vetNotificationsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vetNotificationsLabel">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="vetNotificationsContainer">
                    <div class="text-center text-muted">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const badge = document.getElementById('vetUnreadBadge');
            const container = document.getElementById('vetNotificationsContainer');
            const modalEl = document.getElementById('vetNotificationsModal');

            function updateUnreadCount() {
                fetch('/veterinarian/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        if (!badge) return;
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    })
                    .catch(() => {});
            }

            function loadNotifications() {
                if (!container) return;
                container.innerHTML = '<div class="text-center text-muted">Loading...</div>';
                fetch('/veterinarian/notifications/get')
                    .then(response => response.json())
                    .then(notifications => {
                        if (!Array.isArray(notifications) || notifications.length === 0) {
                            container.innerHTML = '<div class="text-center text-muted">No notifications</div>';
                            return;
                        }

                        container.innerHTML = '';
                        notifications.forEach(notif => {
                            const item = document.createElement('div');
                            item.className = 'border rounded-3 p-3 mb-2 bg-light';
                            item.innerHTML = `
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="text-primary"><i class="fas fa-${notif.icon || 'bell'}"></i></div>
                                    <div>
                                        <div class="fw-semibold">${notif.title}</div>
                                        <div class="text-muted small">${notif.message}</div>
                                        <div class="text-muted small mt-1">${notif.time}</div>
                                    </div>
                                </div>
                            `;
                            container.appendChild(item);
                        });
                    })
                    .catch(() => {
                        container.innerHTML = '<div class="text-center text-danger">Error loading notifications</div>';
                    });
            }

            if (modalEl) {
                modalEl.addEventListener('show.bs.modal', loadNotifications);
            }

            updateUnreadCount();
        });
    </script>

    @stack('scripts')
</body>
</html>
