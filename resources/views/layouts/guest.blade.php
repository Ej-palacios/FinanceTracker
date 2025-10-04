<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FinanceTracker - @yield('title')</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/loader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/alerts.css') }}"> <!-- NUEVO -->
    
    @yield('styles')
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <!-- Incluir el componente de transiciones -->
    @include('components.loader')

    <!-- Sistema de Alertas Globales -->
    @include('components.alerts') <!-- NUEVO -->

    <div class="container-fluid px-3">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <!-- Alertas de Sesión -->
                @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showSuccess('{{ session('success') }}', 'Éxito');
                    });
                </script>
                @endif

                @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showError('{{ session('error') }}', 'Error');
                    });
                </script>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/modules/alerts.js') }}"></script> <!-- NUEVO -->
    <script src="{{ asset('assets/js/modules/darkMode.js') }}"></script>
    <script src="{{ asset('assets/js/core/loader.js') }}"></script>
    
    @yield('scripts')
</body>
</html>