 <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
    
    <style>
        :root {
            /* Playful Pet Theme Colors */
            --primary-orange: #FF8C42;
            --primary-blue: #4A90E2;
            --accent-pink: #FF6B9D;
            --accent-purple: #9B7EDE;
            --accent-green: #5FD068;
            --warm-cream: #FFF8F0;
            --soft-gray: #F5F5F7;
            --dark-text: #2D3748;
            --light-text: #718096;
            --white: #FFFFFF;
            --shadow-soft: 0 4px 20px rgba(255, 140, 66, 0.1);
            --shadow-hover: 0 8px 30px rgba(255, 140, 66, 0.2);
            --paw-light: rgba(255, 140, 66, 0.05);
            --paw-medium: rgba(255, 140, 66, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, var(--warm-cream) 0%, var(--soft-gray) 100%);
            color: var(--dark-text);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 15% 20%, var(--paw-light) 0%, transparent 50%),
                radial-gradient(circle at 85% 40%, var(--paw-medium) 0%, transparent 50%),
                radial-gradient(circle at 45% 70%, var(--paw-light) 0%, transparent 50%),
                radial-gradient(circle at 70% 85%, var(--paw-medium) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .paw-print {
            position: fixed;
            font-size: 40px;
            color: var(--paw-light);
            animation: floatPaw 20s infinite ease-in-out;
            pointer-events: none;
            z-index: 1;
        }

        .paw-print:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; }
        .paw-print:nth-child(2) { top: 60%; right: 8%; animation-delay: 5s; }
        .paw-print:nth-child(3) { bottom: 15%; left: 12%; animation-delay: 10s; }
        .paw-print:nth-child(4) { top: 35%; right: 15%; animation-delay: 15s; }

        @keyframes floatPaw {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
            50% { transform: translateY(-20px) rotate(10deg); opacity: 0.6; }
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 2;
        }

        .sidebar {
            width: 280px;
            background: var(--white);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.05);
            padding: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            animation: slideInLeft 0.6s ease-out;
        }

        @keyframes slideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .logo-section {
            padding: 30px 25px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--accent-pink) 100%);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .logo-section::before {
            content: '🐾';
            position: absolute;
            font-size: 120px;
            opacity: 0.1;
            top: -20px;
            right: -20px;
            transform: rotate(-25deg);
        }

        .logo-section h1 {
            font-family: 'Fredoka', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 5px;
            position: relative;
            z-index: 2;
        }

        .logo-section p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-section {
            margin-bottom: 25px;
        }

        .nav-section-title {
            font-family: 'Fredoka', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--light-text);
            padding: 0 25px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 14px 25px;
            color: var(--dark-text);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
            font-size: 15px;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 0;
            background: linear-gradient(180deg, var(--primary-orange), var(--accent-pink));
            border-radius: 0 4px 4px 0;
            transition: height 0.3s ease;
        }

        .nav-item:hover {
            background: linear-gradient(90deg, rgba(255, 140, 66, 0.08), transparent);
            color: var(--primary-orange);
        }

        .nav-item:hover::before {
            height: 70%;
        }

        .nav-item.active {
            background: linear-gradient(90deg, rgba(255, 140, 66, 0.12), transparent);
            color: var(--primary-orange);
            font-weight: 600;
        }

        .nav-item.active::before {
            height: 70%;
        }

        .nav-item i {
            width: 24px;
            margin-right: 15px;
            font-size: 18px;
        }

        .nav-item .badge {
            margin-left: auto;
            background: var(--accent-pink);
            color: var(--white);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .main-content {
            flex: 1;
            padding: 35px 40px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .header-left h2 {
            font-family: 'Fredoka', sans-serif;
            font-size: 32px;
            color: var(--dark-text);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .header-left p {
            color: var(--light-text);
            font-size: 15px;
        }

        .header-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 12px 45px 12px 20px;
            border: 2px solid transparent;
            border-radius: 25px;
            background: var(--white);
            font-size: 14px;
            width: 280px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
            font-family: 'DM Sans', sans-serif;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-orange);
            box-shadow: var(--shadow-hover);
            width: 320px;
        }

        .search-box i {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
        }

        .icon-button {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--white);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: var(--shadow-soft);
        }

        .icon-button:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            background: var(--primary-orange);
            color: var(--white);
        }

        .icon-button .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 10px;
            height: 10px;
            background: var(--accent-pink);
            border-radius: 50%;
            border: 2px solid var(--white);
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-orange), var(--accent-pink));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 700;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-hover);
        }

        .content-wrapper {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .alert {
            padding: 16px 20px;
            border-radius: 16px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.4s ease-out;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(95, 208, 104, 0.15);
            color: var(--accent-green);
            border-left: 4px solid var(--accent-green);
        }

        .alert-error {
            background: rgba(255, 107, 157, 0.15);
            color: var(--accent-pink);
            border-left: 4px solid var(--accent-pink);
        }

        .alert-warning {
            background: rgba(255, 140, 66, 0.15);
            color: var(--primary-orange);
            border-left: 4px solid var(--primary-orange);
        }

        .alert-info {
            background: rgba(74, 144, 226, 0.15);
            color: var(--primary-blue);
            border-left: 4px solid var(--primary-blue);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'DM Sans', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange), var(--accent-pink));
            color: var(--white);
            box-shadow: var(--shadow-soft);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-secondary {
            background: var(--soft-gray);
            color: var(--dark-text);
        }

        .btn-secondary:hover {
            background: var(--primary-orange);
            color: var(--white);
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow-soft);
            margin-bottom: 25px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--soft-gray);
        }

        .card-header h3 {
            font-family: 'Fredoka', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--dark-text);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                transition: left 0.3s ease;
                z-index: 1000;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }

            .search-box input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background paw prints -->
    <div class="paw-print">🐾</div>
    <div class="paw-print">🐾</div>
    <div class="paw-print">🐾</div>
    <div class="paw-print">🐾</div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="logo-section">
                <h1>🐾 PawCare</h1>
                <p>Veterinary Admin Portal</p>
            </div>

            <nav class="nav-menu">
                <div class="nav-section">
                    <div class="nav-section-title">Main Menu</div>
                    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                   <a href="" class="nav-item {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}"> 
                        <i class="fas fa-calendar-check"></i>
                        <span>Appointments</span>
                        @if(isset($pendingAppointments) && $pendingAppointments > 0)
                            <span class="badge">{{ $pendingAppointments }}</span>
                        @endif
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.pets.*') ? 'active' : '' }}">
                        <i class="fas fa-paw"></i>
                        <span>Pets Registry</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.owners.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Pet Owners</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.queue.*') ? 'active' : '' }}">
                        <i class="fas fa-list-ul"></i>
                        <span>Queue Management</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Medical</div>
                    <a href="" class="nav-item {{ request()->routeIs('admin.medical-records.*') ? 'active' : '' }}">
                        <i class="fas fa-file-medical"></i>
                        <span>Medical Records</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.vaccinations.*') ? 'active' : '' }}">
                        <i class="fas fa-syringe"></i>
                        <span>Vaccinations</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.prescriptions.*') ? 'active' : '' }}">
                        <i class="fas fa-prescription"></i>
                        <span>Prescriptions</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.surgeries.*') ? 'active' : '' }}">
                        <i class="fas fa-procedures"></i>
                        <span>Surgeries</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Services</div>
                    <a href="" class="nav-item {{ request()->routeIs('admin.boarding.*') ? 'active' : '' }}">
                        <i class="fas fa-hotel"></i>
                        <span>Boarding</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.grooming.*') ? 'active' : '' }}">
                        <i class="fas fa-cut"></i>
                        <span>Grooming</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.pharmacy.*') ? 'active' : '' }}">
                        <i class="fas fa-capsules"></i>
                        <span>Pharmacy</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.laboratory.*') ? 'active' : '' }}">
                        <i class="fas fa-flask"></i>
                        <span>Laboratory</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="" class="nav-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i>
                        <span>Inventory</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.billing.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Billing</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                        <i class="fas fa-user-md"></i>
                        <span>Staff</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                    <a href="" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <h2>@yield('page-title', 'Dashboard')</h2>
                    <p>@yield('page-description', "Here's what's happening with your clinic today")</p>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <input type="text" placeholder="Search pets, owners, appointments..." id="globalSearch">
                        <i class="fas fa-search"></i>
                    </div>
                    <button class="icon-button" id="notificationsBtn">
                        <i class="fas fa-bell"></i>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="notification-dot"></span>
                        @endif
                    </button>
                    <button class="icon-button" id="messagesBtn">
                        <i class="fas fa-envelope"></i>
                    </button>
                    <div class="user-avatar" id="userMenuBtn">
                        {{ strtoupper(substr(Auth::user()->first_name ?? 'A', 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name ?? 'D', 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    {{ session('info') }}
                </div>
            @endif

            <!-- Main Content Area -->
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

   
</body>