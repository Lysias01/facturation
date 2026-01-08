<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Facturation')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .table-responsive .table td, .table-responsive .table th {
            vertical-align: middle;
        }
        .select2-container--bootstrap5 .select2-selection {
            min-height: 38px;
            padding: .375rem .75rem;
        }
        .alert {
            transition: opacity 0.5s ease;
        }
    </style>

    @stack('head')
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm mb-4">
    <div class="container">
        <span class="me-3">
            @if($app_settings && $app_settings->logo && file_exists(storage_path('app/public/'.$app_settings->logo)))
                <img src="{{ asset('storage/'.$app_settings->logo) }}" alt="Logo" height="40">
            @else
                <span class="me-3">Logo</span>
            @endif
        </span>
        <h1 class="h4 mb-0">{{ $app_settings?->company_name }}</h1>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div id="navMenu" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/clients') }}">Clients</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/factures') }}">Factures</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('historique.index') }}">Historique</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/produits') }}">Produits</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/settings') }}">Paramètres</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">

    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success" id="flash-message">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" id="flash-message">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Disparition auto messages flash -->
<script>
    $(document).ready(()=>{
        const flash = document.getElementById('flash-message');
        if(flash){
            setTimeout(()=>{flash.style.opacity='0'; flash.style.display='none';},5000);
        }
    });
</script>

@stack('scripts')

<script>
    setTimeout(() => {
        const flashes = document.querySelectorAll('.fixed');
        flashes.forEach(f => f.style.display = 'none');
    }, 5000); // disparaît après 5 secondes
</script>

</body>
</html>
