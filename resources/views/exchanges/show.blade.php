@extends('layouts.app')

@section('title', 'Detalles del Intercambio')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/exchange-show.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Detalles del Intercambio</h1>
                    <p class="text-muted mb-0">Información completa de la transacción entre usuarios</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('deposits.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                    @if(auth()->id() === $exchange->to_user_id && $exchange->status === \App\Models\ExchangeRequest::STATUS_PENDING)
                    <form action="{{ route('deposits.approve', $exchange) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Aprobar
                        </button>
                    </form>
                    <form action="{{ route('deposits.reject', $exchange) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-2"></i>Rechazar
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Tarjeta Principal del Intercambio -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <div class="text-center mb-3">
                        <div class="mb-3">
                            @switch($exchange->status)
                                @case('pending')
                                    <span class="badge bg-warning fs-6 px-3 py-2">
                                        <i class="bi bi-clock-history me-2"></i>PENDIENTE
                                    </span>
                                    @break
                                @case('approved')
                                    <span class="badge bg-info fs-6 px-3 py-2">
                                        <i class="bi bi-check me-2"></i>APROBADO
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        <i class="bi bi-check-circle me-2"></i>COMPLETADO
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger fs-6 px-3 py-2">
                                        <i class="bi bi-x-circle me-2"></i>RECHAZADO
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-secondary fs-6 px-3 py-2">
                                        <i class="bi bi-dash-circle me-2"></i>CANCELADO
                                    </span>
                                    @break
                            @endswitch
                        </div>

                        <div class="exchange-amounts mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <h3 class="text-primary mb-1">{{ number_format($exchange->from_amount, 2) }}</h3>
                                    <small class="text-muted">{{ $exchange->from_currency }}</small>
                                </div>
                                <div class="mx-4">
                                    <i class="bi bi-arrow-left-right fs-2 text-muted"></i>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-success mb-1">{{ number_format($exchange->to_amount, 2) }}</h3>
                                    <small class="text-muted">{{ $exchange->to_currency }}</small>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted mb-0">
                            <strong>N° de Transacción:</strong> {{ $exchange->transaction_number }}
                        </p>
                    </div>
                </div>

                <div class="card-body pt-0">
                    <!-- Información de las Partes -->
                    <div class="row g-3 mb-4">
                        <!-- Usuario Origen -->
                        <div class="col-md-6">
                            <div class="card border h-100">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-person-up me-2"></i>Usuario que Envía
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary-subtle text-primary rounded-circle p-3">
                                                <i class="bi bi-person fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="card-title mb-1">{{ $exchange->fromUser->name }}</h6>
                                            <p class="card-text text-muted mb-1 small">
                                                <i class="bi bi-envelope me-1"></i>{{ $exchange->fromUser->email }}
                                            </p>
                                            <p class="card-text text-muted mb-0 small">
                                                <i class="bi bi-id-card me-1"></i>ID: {{ $exchange->fromUser->user_id }}
                                            </p>
                                            <p class="card-text text-muted mb-0 small">
                                                <i class="bi bi-currency-exchange me-1"></i>Moneda: {{ $exchange->fromUser->currency }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Usuario Destino -->
                        <div class="col-md-6">
                            <div class="card border h-100">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="bi bi-person-down me-2"></i>Usuario que Recibe
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-success-subtle text-success rounded-circle p-3">
                                                <i class="bi bi-person fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="card-title mb-1">{{ $exchange->toUser->name }}</h6>
                                            <p class="card-text text-muted mb-1 small">
                                                <i class="bi bi-envelope me-1"></i>{{ $exchange->toUser->email }}
                                            </p>
                                            <p class="card-text text-muted mb-0 small">
                                                <i class="bi bi-id-card me-1"></i>ID: {{ $exchange->toUser->user_id }}
                                            </p>
                                            <p class="card-text text-muted mb-0 small">
                                                <i class="bi bi-currency-exchange me-1"></i>Moneda: {{ $exchange->toUser->currency }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles del Intercambio -->
                    <div class="row g-3">
                        <!-- Tasa de Cambio -->
                        <div class="col-md-4">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <div class="bg-info-subtle text-info rounded p-3 mb-3 mx-auto" style="width: 60px;">
                                        <i class="bi bi-graph-up fs-4"></i>
                                    </div>
                                    <h6 class="card-title text-muted mb-1">Tasa de Cambio</h6>
                                    <p class="card-text fw-bold mb-0">
                                        1 {{ $exchange->from_currency }} = {{ number_format($exchange->exchange_rate, 4) }} {{ $exchange->to_currency }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Fecha de Solicitud -->
                        <div class="col-md-4">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <div class="bg-warning-subtle text-warning rounded p-3 mb-3 mx-auto" style="width: 60px;">
                                        <i class="bi bi-calendar-plus fs-4"></i>
                                    </div>
                                    <h6 class="card-title text-muted mb-1">Solicitado</h6>
                                    <p class="card-text fw-bold mb-0">
                                        {{ $exchange->created_at->format('d/m/Y') }}
                                    </p>
                                    <small class="text-muted">{{ $exchange->created_at->format('H:i') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Fecha de Completación -->
                        <div class="col-md-4">
                            <div class="card border h-100">
                                <div class="card-body text-center">
                                    <div class="bg-success-subtle text-success rounded p-3 mb-3 mx-auto" style="width: 60px;">
                                        <i class="bi bi-calendar-check fs-4"></i>
                                    </div>
                                    <h6 class="card-title text-muted mb-1">
                                        @if($exchange->completed_at)
                                            Completado
                                        @else
                                            Estatus
                                        @endif
                                    </h6>
                                    <p class="card-text fw-bold mb-0">
                                        @if($exchange->completed_at)
                                            {{ $exchange->completed_at->format('d/m/Y') }}
                                        @else
                                            {{ ucfirst($exchange->status) }}
                                        @endif
                                    </p>
                                    @if($exchange->completed_at)
                                    <small class="text-muted">{{ $exchange->completed_at->format('H:i') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notas del Intercambio -->
                    @if($exchange->notes)
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-chat-text me-2"></i>Notas del Intercambio
                        </h6>
                        <div class="alert alert-light border">
                            <p class="mb-0">{{ $exchange->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Información de Transacciones Asociadas -->
                    @if($exchange->status === 'completed')
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-receipt me-2"></i>Transacciones Generadas
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white py-2">
                                        <small class="fw-bold">
                                            <i class="bi bi-arrow-down-left me-1"></i>Ingreso para {{ $exchange->toUser->name }}
                                        </small>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-success mb-0">+ {{ number_format($exchange->to_amount, 2) }} {{ $exchange->to_currency }}</h6>
                                                <small class="text-muted">Intercambio recibido</small>
                                            </div>
                                            <i class="bi bi-check-circle text-success fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white py-2">
                                        <small class="fw-bold">
                                            <i class="bi bi-arrow-up-right me-1"></i>Gasto para {{ $exchange->fromUser->name }}
                                        </small>
                                    </div>
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-primary mb-0">- {{ number_format($exchange->from_amount, 2) }} {{ $exchange->from_currency }}</h6>
                                                <small class="text-muted">Intercambio enviado</small>
                                            </div>
                                            <i class="bi bi-check-circle text-primary fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Reporte de Transferencia -->
                    @if($exchange->status === 'completed')
                    <div class="mt-4 pt-3 border-top">
                        <div class="alert alert-success border">
                            <div class="d-flex">
                                <i class="bi bi-check-circle-fill me-3 fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">¡Intercambio Completado Exitosamente!</h6>
                                    <p class="mb-1 fw-bold">
                                        Se realizó la transferencia al usuario <strong>{{ $exchange->toUser->name }}</strong>
                                        y su número de transacción es <strong>{{ $exchange->transaction_number }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        Fecha de completación: {{ $exchange->completed_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Timeline del Intercambio -->
                <div class="card-footer bg-transparent border-0">
                    <h6 class="text-muted mb-3">
                        <i class="bi bi-clock-history me-2"></i>Linea de Tiempo
                    </h6>
                    <div class="timeline">
                        <div class="timeline-item {{ $exchange->status !== 'pending' ? 'completed' : 'active' }}">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Solicitud Creada</h6>
                                <p class="text-muted mb-0 small">{{ $exchange->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($exchange->status === 'rejected' || $exchange->status === 'cancelled')
                        <div class="timeline-item completed">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Intercambio {{ ucfirst($exchange->status) }}</h6>
                                <p class="text-muted mb-0 small">
                                    @if($exchange->completed_at)
                                        {{ $exchange->completed_at->format('d/m/Y H:i') }}
                                    @else
                                        {{ $exchange->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($exchange->status === 'completed')
                        <div class="timeline-item completed">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Intercambio Aprobado</h6>
                                <p class="text-muted mb-0 small">{{ $exchange->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item completed">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Transacciones Generadas</h6>
                                <p class="text-muted mb-0 small">{{ $exchange->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($exchange->status === 'pending')
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Esperando Aprobación</h6>
                                <p class="text-muted mb-0 small">Pendiente de respuesta del usuario</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información de Seguridad -->
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="bi bi-shield-check text-success fs-2 mb-3"></i>
                    <h6 class="card-title">Transacción Segura</h6>
                    <p class="card-text text-muted small mb-0">
                        Todas las transacciones están protegidas y registradas en nuestro sistema.
                        El número de transacción <strong>{{ $exchange->transaction_number }}</strong> sirve como comprobante.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/modules/exchange-show.js') }}"></script>
@endsection
