@extends('layouts.app')

@section('title', 'Nuevo Intercambio')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/exchange-create.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Nuevo Intercambio</h1>
                <a href="{{ route('exchanges.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Tarjeta de Búsqueda -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-search me-2"></i>Buscar Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('exchanges.create') }}" method="GET" id="searchForm">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="search" class="form-label">Buscar por ID, Nombre o Email</label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control"
                                           id="search"
                                           name="search"
                                           value="{{ $search ?? '' }}"
                                           placeholder="Ej: 12345678, Juan Pérez, juan@email.com"
                                           required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                </div>
                                <small class="text-muted">
                                    Ingresa el ID de 8 dígitos, nombre o email del usuario
                                </small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="{{ route('exchanges.create') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Búsqueda en Tiempo Real (Opcional) -->
                    <div class="mt-3">
                        <label class="form-label">Búsqueda Rápida</label>
                        <input type="text"
                               class="form-control"
                               id="liveSearch"
                               placeholder="Escribe para buscar en tiempo real...">
                        <div id="liveSearchResults" class="mt-2" style="display: none;"></div>
                    </div>
                </div>
            </div>

            @if(isset($search) && $search && $users->count() > 0)
            <!-- Resultados de Búsqueda -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>Usuarios Encontrados ({{ $users->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="usersList">
                        @foreach($users as $foundUser)
                        <div class="col-md-6">
                            <div class="card user-card border h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary text-white rounded-circle p-3">
                                                <i class="bi bi-person fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="card-title mb-1">{{ $foundUser->name }}</h6>
                                            <p class="card-text text-muted mb-1 small">
                                                <i class="bi bi-id-card me-1"></i><strong>ID:</strong> {{ $foundUser->user_id }}
                                            </p>
                                            <p class="card-text text-muted mb-1 small">
                                                <i class="bi bi-envelope me-1"></i>{{ $foundUser->email }}
                                            </p>
                                            <p class="card-text text-muted mb-2 small">
                                                <i class="bi bi-currency-exchange me-1"></i><strong>Moneda:</strong> {{ $foundUser->currency }}
                                            </p>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm select-user"
                                                    data-user-id="{{ $foundUser->id }}"
                                                    data-user-name="{{ $foundUser->name }}"
                                                    data-user-currency="{{ $foundUser->currency }}"
                                                    data-user-display="{{ $foundUser->name }} (ID: {{ $foundUser->user_id }})">
                                                <i class="bi bi-arrow-right-circle me-1"></i>Seleccionar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if(isset($search) && $search && $users->count() === 0)
            <!-- Sin Resultados -->
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-search fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron usuarios</h5>
                    <p class="text-muted">Intenta con otro ID, nombre o email</p>
                </div>
            </div>
            @endif

            <!-- Formulario de Intercambio (Se muestra después de seleccionar usuario) -->
            <div class="card" id="exchangeFormCard" style="{{ isset($search) && $search ? '' : 'display: none;' }}">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-arrow-left-right me-2"></i>Detalles del Intercambio
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('exchanges.store') }}" method="POST" id="exchangeForm">
                        @csrf
                        <input type="hidden" id="to_user_id" name="to_user_id">

                        <!-- Información del Usuario Seleccionado -->
                        <div class="alert alert-info" id="selectedUserInfo" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="alert-heading mb-1">Usuario Seleccionado</h6>
                                    <p class="mb-0" id="selectedUserDisplay"></p>
                                </div>
                                <button type="button" class="btn-close" id="deselectUser"></button>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Monto y Moneda Origen -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_amount" class="form-label">Monto a Enviar</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="from_amount" name="from_amount"
                                               step="0.01" min="0.01" max="10000" required>
                                        <select class="form-select" id="from_currency" name="from_currency" required>
                                            <option value="NIO">NIO - Córdoba</option>
                                            <option value="USD">USD - Dólar</option>
                                            <option value="EUR">EUR - Euro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Monto Calculado -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Monto a Recibir (Calculado)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="to_amount" readonly>
                                        <span class="input-group-text" id="to_currency_display">---</span>
                                    </div>
                                    <small class="text-muted" id="exchange_rate_info">Tasa de cambio: ---</small>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Intercambio -->
                        <div class="alert alert-warning" id="exchange_info" style="display: none;">
                            <div class="d-flex">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Resumen del Intercambio</h6>
                                    <p class="mb-1" id="exchange_summary"></p>
                                    <small class="text-muted" id="exchange_details"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas (Opcional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Agregar un mensaje para el usuario..."></textarea>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="calculateBtn">
                                <i class="bi bi-calculator"></i> Calcular
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="bi bi-send"></i> Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(!isset($search) || !$search)
            <!-- Instrucciones -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-people fs-1 text-primary mb-3"></i>
                    <h5 class="text-primary">Busca un usuario para comenzar</h5>
                    <p class="text-muted">
                        Usa el buscador superior para encontrar usuarios por su ID de 8 dígitos, nombre o email.
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/modules/exchange-create.js') }}"></script>
@endsection
