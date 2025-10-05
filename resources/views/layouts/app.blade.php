<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FinanceTracker - @yield('title')</title>

    <!-- Bootstrap 5.3 CDN -->
    <link rel="stylesheet" href="{{ asset('assets/css/loader.css') }}" media="print" onload="this.media='all'">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/alerts.css') }}"> <!-- NUEVO -->
    <link rel="stylesheet" href="{{ asset('assets/css/header.css') }}"> <!-- NUEVO -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/light-theme.css') }}">
    @yield('styles')
</head>
<body>
    <!-- Incluir el componente de transición -->
    @include('components.loader')

    <!-- Sistema de Alertas Globales -->
    @include('components.alerts') <!-- NUEVO -->

    <div class="app-container">
        @include('components.shared.sidebar')

        <div class="main-content">
            @include('components.shared.header')

            <main class="content-wrapper">
                <!-- Alertas de Sesión (Laravel) -->
                <div class="container-fluid mt-3">
                    @if(session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @endif

                    @if(session('error'))
                        <x-alert type="error" :message="session('error')" />
                    @endif

                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            <x-alert type="error" :message="$error" title="Error de Validación" />
                        @endforeach
                    @endif
                </div>

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript Core -->
    <script src="{{ asset('assets/js/core/api.js') }}"></script>
    <script src="{{ asset('assets/js/core/storage.js') }}"></script>
    <script src="{{ asset('assets/js/core/validator.js') }}"></script>

    <!-- JavaScript Modules -->
    <script src="{{ asset('assets/js/modules/alerts.js') }}"></script> <!-- NUEVO -->
    <script src="{{ asset('assets/js/modules/darkMode.js') }}"></script>
    <script src="{{ asset('assets/js/modules/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/modules/loader.js') }}"></script>
    <script src="{{ asset('assets/js/modules/layout.js') }}"></script>
    <script src="{{ asset('assets/js/modules/header.js') }}"></script>

    @yield('scripts')
</body>
</html>
