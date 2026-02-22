<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vet Clinic')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @if(str_contains($bodyClass ?? '', 'customer-body'))
        <link rel="stylesheet" href="{{ asset('css/customer-ui.css') }}">
    @endif

    @stack('styles')
</head>
<body class="{{ $bodyClass ?? '' }}">
    @yield('content')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
