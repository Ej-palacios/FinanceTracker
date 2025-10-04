@extends('layouts.app')

@section('title', 'Intercambios de Divisas')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/exchanges.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Intercambios de Divisas</h1>

    <!-- Estadísticas Rápidas -->
    <div class="row">
        @include('components.dashboard.stats-widget', [
            'title' => 'Intercambios Enviados',
            'value' => $sentRequests->total(),
            'icon' => 'send',
            'color' => 'primary',
            'footer' => $sentRequests->where('status', 'pending')->count() . ' pendientes',
            'footerIcon' => 'clock',
            'footerColor' => 'warning'
        ])

        @include('components.dashboard.stats-widget', [
            'title' => 'Intercambios Recibidos',
            'value' => $receivedRequests->total(),
            'icon' => 'inbox',
            'color' => 'info',
            'footer' => $receivedRequests->where('status', 'pending')->count() . ' por revisar',
            'footerIcon' => 'bell',
            'footerColor' => 'danger'
        ])

        @include('components.dashboard.stats-widget', [
            'title' => 'Intercambios Completados',
            'value' => $sentRequests->where('status', 'completed')->count() + $receivedRequests->where('status', 'completed')->count(),
            'icon' => 'check-circle',
            'color' => 'success',
            'footer' => 'Total procesados',
            'footerIcon' => 'arrow-repeat',
            'footerColor' => 'success'
        ])

        @include('components.dashboard.stats-widget', [
            'title' => 'Usuarios Disponibles',
            'value' => $users->count(),
            'icon' => 'people',
            'color' => 'warning',
            'footer' => 'Para intercambiar',
            'footerIcon' => 'search',
            'footerColor' => 'info'
        ])
    </div>

    <!-- Botón de Acción Principal -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8 text-md-start">
                            <h4 class="card-title mb-2">¿Quieres hacer un nuevo intercambio?</h4>
                            <p class="card-text text-muted mb-0">
                                Envía una solicitud de intercambio a otros usuarios del sistema.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('exchanges.create') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-arrow-left-right me-2"></i>Nuevo Intercambio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Solicitudes Recibidas -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-inbox me-2 text-primary"></i>Solicitudes Recibidas
                    </h5>
                    <span class="badge bg-primary">{{ $receivedRequests->total() }}</span>
                </div>
                <div class="card-body">
                    @if($receivedRequests->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($receivedRequests as $request)
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'danger') }}-subtle text-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'danger') }} rounded-circle">
                                                        <i class="bi bi-person fs-5"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $request->fromUser->name }}</h6>
                                                    <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>

                                            <div class="exchange-details mb-2">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="text-danger fw-bold">
                                                        -{{ number_format($request->from_amount, 2) }} {{ $request->from_currency }}
                                                    </span>
                                                    <i class="bi bi-arrow-right mx-2 text-muted"></i>
                                                    <span class="text-success fw-bold">
                                                        +{{ number_format($request->to_amount, 2) }} {{ $request->to_currency }}
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block mt-1">
                                                    Tasa: 1 {{ $request->from_currency }} = {{ number_format($request->exchange_rate, 4) }} {{ $request->to_currency }}
                                                </small>
                                            </div>

                                            @if($request->notes)
                                                <div class="alert alert-light border mt-2 py-2">
                                                    <small class="text-muted"><i class="bi bi-chat-text me-1"></i>{{ $request->notes }}</small>
                                                </div>
                                            @endif

                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>

                                                @if($request->status === 'pending')
                                                    <div class="btn-group btn-group-sm">
                                                        <form action="{{ route('exchanges.approve', $request) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="bi bi-check me-1"></i>Aprobar
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('exchanges.reject', $request) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <a href="{{ route('exchanges.show', $request) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($receivedRequests->hasPages())
                        <div class="mt-3">
                            {{ $receivedRequests->links() }}
                        </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No hay solicitudes recibidas</h5>
                            <p class="text-muted">Cuando otros usuarios te envíen solicitudes, aparecerán aquí.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Solicitudes Enviadas -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-send me-2 text-info"></i>Solicitudes Enviadas
                    </h5>
                    <span class="badge bg-info">{{ $sentRequests->total() }}</span>
                </div>
                <div class="card-body">
                    @if($sentRequests->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($sentRequests as $request)
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'danger') }}-subtle text-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'danger') }} rounded-circle">
                                                        <i class="bi bi-person fs-5"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $request->toUser->name }}</h6>
                                                    <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>

                                            <div class="exchange-details mb-2">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="text-danger fw-bold">
                                                        -{{ number_format($request->from_amount, 2) }} {{ $request->from_currency }}
                                                    </span>
                                                    <i class="bi bi-arrow-right mx-2 text-muted"></i>
                                                    <span class="text-success fw-bold">
                                                        +{{ number_format($request->to_amount, 2) }} {{ $request->to_currency }}
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block mt-1">
                                                    Tasa: 1 {{ $request->from_currency }} = {{ number_format($request->exchange_rate, 4) }} {{ $request->to_currency }}
                                                </small>
                                            </div>

                                            @if($request->transaction_number)
                                                <div class="alert alert-light border mt-2 py-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-receipt me-1"></i>
                                                        <strong>N° Transacción:</strong> {{ $request->transaction_number }}
                                                    </small>
                                                </div>
                                            @endif

                                            <div class="d-flex align-items-center justify-content-between mt-2">
                                                <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'completed' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>

                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('exchanges.show', $request) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i> Detalles
                                                    </a>
                                                    @if($request->status === 'pending')
                                                        <form action="{{ route('exchanges.reject', $request) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                <i class="bi bi-x"></i> Cancelar
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($sentRequests->hasPages())
                        <div class="mt-3">
                            {{ $sentRequests->links() }}
                        </div>
                        @endif

                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-send fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No hay solicitudes enviadas</h5>
                            <p class="text-muted">Crea tu primer intercambio para comenzar.</p>
                            <a href="{{ route('exchanges.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-arrow-left-right me-2"></i>Crear Intercambio
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Ayuda -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-2">¿Cómo funcionan los intercambios?</h5>
                            <p class="card-text text-muted mb-0">
                                1. Busca un usuario → 2. Propón un intercambio → 3. Espera aprobación → 4. ¡Intercambio completado!
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('exchanges.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-play-circle me-2"></i>Comenzar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/modules/exchanges.js') }}"></script>
@endsection
