@extends('layouts.app')

@section('title', 'Editar Meta de Ahorro')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('savings-goals.index') }}">Metas de Ahorro</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('savings-goals.show', $savingsGoal) }}">{{ $savingsGoal->name }}</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
            <h1>Editar Meta: {{ $savingsGoal->name }}</h1>
        </div>
    </div>

    @include('components.alerts')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="card-header" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="card-title mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-pencil-square"></i> Editar Detalles
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('savings-goals.update', $savingsGoal) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">Nombre de la Meta <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $savingsGoal->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="target_amount" class="form-label">Monto Objetivo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $currencySymbol }}</span>
                                    <input type="number" step="0.01" min="{{ $savingsGoal->current_amount + 0.01 }}" class="form-control @error('target_amount') is-invalid @enderror"
                                           id="target_amount" name="target_amount" value="{{ old('target_amount', $savingsGoal->target_amount) }}" required>
                                </div>
                                <div class="form-text">Mínimo: {{ $currencySymbol }}{{ number_format($savingsGoal->current_amount + 0.01, 2) }}</div>
                                @error('target_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $savingsGoal->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="target_date" class="form-label">Fecha Límite (Opcional)</label>
                            <input type="date" class="form-control @error('target_date') is-invalid @enderror"
                                   id="target_date" name="target_date" value="{{ old('target_date', $savingsGoal->target_date?->format('Y-m-d')) }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            <div class="form-text">Deja vacío si no tienes una fecha específica en mente.</div>
                            @error('target_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $savingsGoal->status) === 'active' ? 'selected' : '' }}>Activa</option>
                                <option value="paused" {{ old('status', $savingsGoal->status) === 'paused' ? 'selected' : '' }}>Pausada</option>
                                <option value="completed" {{ old('status', $savingsGoal->status) === 'completed' ? 'selected' : '' }}>Completada</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Información actual:</strong>
                                    Ya has ahorrado {{ $currencySymbol }}{{ number_format($savingsGoal->current_amount, 2) }} de esta meta.
                                    @if($savingsGoal->progress_percentage > 0)
                                        Estás al {{ number_format($savingsGoal->progress_percentage, 1) }}% del objetivo.
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('savings-goals.show', $savingsGoal) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(!$savingsGoal->isCompleted())
                <div class="card mt-4 border-danger" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                    <div class="card-header bg-danger text-white">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Zona de Peligro
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Si deseas eliminar esta meta permanentemente, puedes hacerlo aquí. Esta acción no se puede deshacer.</p>
                        <form action="{{ route('savings-goals.destroy', $savingsGoal) }}" method="POST" onsubmit="return confirm('¿Estás seguro que deseas eliminar esta meta? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Eliminar Meta
                            </button>
                        </form>
                    </div>
                </div>
            @endif
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

// Update target amount minimum based on current savings
const currentAmount = {{ $savingsGoal->current_amount }};
document.getElementById('target_amount').min = (currentAmount + 0.01).toFixed(2);
</script>
@endsection
@endsection
