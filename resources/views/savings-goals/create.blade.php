@extends('layouts.app')

@section('title', 'Crear Meta de Ahorro')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('savings-goals.index') }}">Metas de Ahorro</a></li>
                    <li class="breadcrumb-item active">Crear Nueva Meta</li>
                </ol>
            </nav>
            <h1>Crear Nueva Meta de Ahorro</h1>
        </div>
    </div>

    @include('components.alerts')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="card-header" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="card-title mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-piggy-bank"></i> Detalles de la Meta
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('savings-goals.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Nombre de la Meta <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Ej: Vacaciones en la playa, Nuevo teléfono, Fondo de emergencia">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="target_amount" class="form-label">Monto Objetivo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $currencySymbol }}</span>
                                    <input type="number" step="0.01" min="0.01" class="form-control @error('target_amount') is-invalid @enderror"
                                           id="target_amount" name="target_amount" value="{{ old('target_amount') }}" required
                                           placeholder="0.00">
                                </div>
                                @error('target_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Describe tu meta y por qué quieres lograrla...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="target_date" class="form-label">Fecha Límite (Opcional)</label>
                            <input type="date" class="form-control @error('target_date') is-invalid @enderror"
                                   id="target_date" name="target_date" value="{{ old('target_date') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            <div class="form-text">Deja vacío si no tienes una fecha específica en mente.</div>
                            @error('target_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Consejo:</strong> Establece metas realistas y divide montos grandes en objetivos más pequeños.
                                    Puedes editar esta meta en cualquier momento.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('savings-goals.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Crear Meta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Auto-format number inputs
document.getElementById('target_amount').addEventListener('input', function() {
    // Allow only numbers and decimal point
    this.value = this.value.replace(/[^0-9.]/g, '');
});

// Set minimum date for target_date
document.getElementById('target_date').min = new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString().split('T')[0];
</script>
@endsection
@endsection
