<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Application de gestion de facturation">
    <title>@yield('title', 'Facturation')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap5.min.css" rel="stylesheet" />

    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --card-shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 60px;
        }

        /* Navbar Styles - Horizontal Desktop Menu */
        .main-navbar {
            background: #ffffff;
            box-shadow: var(--card-shadow);
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar-container {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            padding: 0;
            height: 50px;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            padding: 0 1rem;
            height: 50px;
            margin: 0;
        }

        .navbar-brand:hover {
            color: #0a58ca !important;
        }

        .navbar-brand i {
            font-size: 1.5rem;
        }

        /* Horizontal Menu */
        .navbar-menu {
            display: flex;
            align-items: stretch;
            justify-content: flex-start;
            gap: 0;
            height: 50px;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            list-style: none;
            display: flex;
            align-items: stretch;
        }

        .nav-link {
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--secondary-color) !important;
            padding: 0 0.75rem !important;
            border-radius: 0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            text-decoration: none;
            white-space: nowrap;
            height: 50px;
            border-bottom: 2px solid transparent;
        }
        
        .nav-link i {
            font-size: 1rem;
            line-height: 1;
        }
        
        .nav-link span {
            line-height: 1;
        }

        .nav-link:hover {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--primary-color) !important;
        }

        .nav-link.active {
            background-color: var(--primary-color);
            color: #ffffff !important;
        }

        /* User Section */
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .user-badge.admin {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }

        .user-badge.employe {
            background-color: rgba(13, 202, 240, 0.1);
            color: var(--info-color);
        }

        /* Mobile Toggle */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            font-size: 1.25rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        /* Cards */
        .modern-card {
            background: #ffffff;
            border: none;
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            box-shadow: var(--card-shadow-hover);
        }

        .modern-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .modern-card .card-body {
            padding: 1.25rem;
        }

        /* Stat Cards */
        .stat-card {
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }

        .stat-card.success { border-left-color: var(--success-color); }
        .stat-card.warning { border-left-color: var(--warning-color); }
        .stat-card.danger { border-left-color: var(--danger-color); }
        .stat-card.info { border-left-color: var(--info-color); }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-card .stat-label {
            font-size: 0.875rem;
            color: var(--secondary-color);
            margin-top: 0.25rem;
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-icon {
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--secondary-color);
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 0.875rem 1rem;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.05);
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02);
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.375rem;
            color: var(--dark-text);
        }

        /* Page Headers */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-text);
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: var(--secondary-color);
            margin-top: 0.25rem;
        }

        /* Alert Messages */
        .alert {
            border-radius: 0.5rem;
            border: none;
            padding: 1rem 1.25rem;
        }

        .alert-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--success-color);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.15);
            color: #997404;
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.1);
            color: #055160;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8125rem;
        }

        /* Pagination */
        .pagination {
            margin: 0;
        }

        .page-link {
            border-radius: 0.375rem;
            margin: 0 0.125rem;
            font-weight: 500;
        }

        /* Select2 Overrides */
        .select2-container--bootstrap5 .select2-selection {
            border-radius: 0.5rem;
            min-height: 42px;
        }

        .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered {
            padding: 0.5rem 0.75rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            .navbar-menu {
                display: none;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                background: #ffffff;
                padding: 1rem;
                flex-direction: column;
                box-shadow: var(--card-shadow);
                gap: 0.25rem;
            }

            .navbar-menu.show {
                display: flex;
            }

            .navbar-menu .nav-item {
                width: 100%;
            }

            .navbar-menu .nav-link {
                padding: 0.75rem 1rem !important;
            }

            .navbar-user {
                display: none;
            }

            .navbar-user.show {
                display: flex;
                position: absolute;
                top: auto;
                bottom: -60px;
                right: 1rem;
                background: #ffffff;
                padding: 0.5rem;
                border-radius: 0.5rem;
                box-shadow: var(--card-shadow);
            }

            .mobile-menu-btn {
                display: block;
            }
        }

        @media (min-width: 992px) {
            .mobile-menu-btn {
                display: none;
            }

            .navbar-menu,
            .navbar-user {
                display: flex !important;
            }
        }

        @media (max-width: 575.98px) {
            .stat-card {
                padding: 1rem;
            }

            .stat-card .stat-value {
                font-size: 1.5rem;
            }

            .stat-card .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.25rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }
        }

        /* Bootstrap 5.3 Subtle Backgrounds Fix */
        .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
        .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
        .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.15) !important; }
        .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
        .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
        .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
        
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-warning { color: #997404 !important; }
        .text-danger { color: var(--danger-color) !important; }
        .text-info { color: #055160 !important; }
        
        .border-warning { border-color: var(--warning-color) !important; }
        
        /* Dropdown menu improvements */
        .dropdown-menu {
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: var(--card-shadow-hover);
        }
        
        /* Card footer fix */
        .card-footer {
            background-color: #ffffff;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        /* Form check improvements */
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Button group */
        .btn-group .btn {
            border-radius: 0.375rem;
        }
        
        .btn-check:checked + .btn {
            background-color: var(--primary-color);
            color: #fff;
        }

        /* Badge Colors */
        .bg-primary { background-color: #0d6efd !important; color: #fff; }
        .bg-secondary { background-color: #6c757d !important; color: #fff; }
        .bg-success { background-color: #198754 !important; color: #fff; }
        .bg-danger { background-color: #dc3545 !important; color: #fff; }
        .bg-warning { background-color: #ffc107 !important; color: #000; }
        .bg-info { background-color: #0dcaf0 !important; color: #000; }
        .bg-light { background-color: #f8f9fa !important; color: #000; }
        .bg-dark { background-color: #212529 !important; color: #fff; }

        /* Nav Tabs */
        .nav-tabs {
            border-bottom: 2px solid rgba(0,0,0,0.05);
        }
        
        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.75rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: var(--primary-color);
        }
        
        .nav-tabs .nav-link.active {
            background: transparent;
            border: none;
            border-bottom: 2px solid var(--primary-color);
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        /* Modal Improvements */
        .modal-header {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.25rem;
        }
        
        .modal-footer {
            border-top: 1px solid rgba(0,0,0,0.05);
            padding: 1rem 1.25rem;
        }
        
        .modal-content {
            border: none;
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow-hover);
        }

        /* Navbar Logo */
        .navbar-logo-img {
            max-height: 28px;
            width: auto;
        }

        /* Dropdown items */
        .dropdown-item:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }

        /* Form switch */
        .form-switch .form-check-input {
            width: 2.5em;
            margin-left: -2.5em;
        }
    </style>

    @stack('head')
</head>
<body>

    {{-- Navbar --}}
    <nav class="main-navbar">
        <div class="navbar-container">
            {{-- Logo & Brand --}}
            <a class="navbar-brand" href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('dashboard') : route('dashboard.employe')) : url('/') }}">
                @if($app_settings?->logo)
                    <img src="{{ asset('logos/'.$app_settings->logo) }}" class="navbar-logo-img me-2" alt="Logo">
                @else
                    <i class="bi bi-receipt"></i>
                @endif
                <span>{{ $app_settings?->company_name ?? 'Facturation' }}</span>
            </a>

            {{-- Mobile Toggle Button --}}
            <button class="navbar-toggler mobile-menu-btn" type="button" onclick="toggleMobileMenu()">
                <i class="bi bi-list"></i>
            </button>

            {{-- Horizontal Navigation Menu --}}
            @auth
            <ul class="navbar-menu" id="mainMenu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}" 
                       href="{{ auth()->user()->isAdmin() ? route('dashboard') : route('dashboard.employe') }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clients*') ? 'active' : '' }}" 
                       href="{{ route('clients.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Clients</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('factures*') ? 'active' : '' }}" 
                       href="{{ route('factures.index') }}">
                        <i class="bi bi-file-text"></i>
                        <span>Documents</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('produits*') ? 'active' : '' }}" 
                       href="{{ route('produits.index') }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Produits</span>
                    </a>
                </li>

                @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('historique*') ? 'active' : '' }}" 
                       href="{{ route('historique.index') }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Historique</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('settings*') ? 'active' : '' }}" 
                       href="{{ route('settings.edit') }}">
                        <i class="bi bi-gear"></i>
                        <span>Parametres</span>
                    </a>
                </li>
                @endif
            </ul>

            {{-- User Menu --}}
            <div class="navbar-user" id="userMenu">
                <span class="user-badge {{ auth()->user()->isAdmin() ? 'admin' : 'employe' }}">
                    <i class="bi bi-person-circle"></i>
                    {{ auth()->user()->name }}
                    @if(auth()->user()->isAdmin())
                        <i class="bi bi-shield-check"></i>
                    @endif
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-icon" title="Deconnexion">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </nav>

    {{-- Mobile Menu Script --}}
    <script>
        function toggleMobileMenu() {
            document.getElementById('mainMenu').classList.toggle('show');
            document.getElementById('userMenu').classList.toggle('show');
        }
    </script>

    {{-- Main Content --}}
    <main class="py-4" style="margin-top: 50px;">
        <div class="container">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <div>{{ session('warning') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="fixed-bottom bg-white py-2 border-top">
        <div class="container">
            <div class="text-center text-muted small mb-0">
                {{ $app_settings?->company_name ?? 'Wels' }} &copy; {{ date('Y') }}
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/fr.js"></script>

    <!-- Auto-hide Flash Messages -->
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                language: 'fr',
                placeholder: 'Selectionnez...'
            });
        });
    </script>

    @stack('scripts')

</body>
</html>
