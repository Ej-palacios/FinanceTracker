@extends('layouts.app')

@section('title', $savingsGoal->name . ' - Meta de Ahorro')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('savings-goals.index') }}">Metas de Ahorro</a></li>
                    <li class="breadcrumb-item active">{{ $savingsGoal->name }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h1>{{ $savingsGoal->name }}</h1>
                <div class="btn-group">
                    @if(!$savingsGoal->isCompleted())
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSavingsModal">
                            <i class="bi bi-plus-circle"></i> Agregar Ahorros
                        </button>
                    @endif
                    <a href="{{ route('savings-goals.edit', $savingsGoal) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    @if($savingsGoal->status === 'active')
                        <form action="{{ route('savings-goals.toggleStatus', $savingsGoal) }}" method="POST" class="d-inline">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-outline-warning">
                                <i class="bi bi-pause-circle"></i> Pausar
                            </button>
                        </form>
                    @elseif($savingsGoal->status === 'paused')
                        <form action="{{ route('savings-goals.toggleStatus', $savingsGoal) }}" method="POST" class="d-inline">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-outline-success">
                                <i class="bi bi-play-circle"></i> Reactivar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('components.alerts')

    <div class="row">
        <div class="col-lg-8">
            <div class="card" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="card-header" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="card-title mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-graph-up"></i> Progreso de la Meta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="display-4 fw-bold {{ $savingsGoal->isCompleted() ? 'text-success' : 'text-primary' }}">
                                    {{ number_format($savingsGoal->progress_percentage, 1) }}%
                                </div>
                                <div class="text-muted">Completado</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="h4 text-primary">
                                    {{ $currencySymbol }}{{ number_format($savingsGoal->remaining_amount, 2) }}
                                </div>
                                <div class="text-muted">Faltante</div>
                            </div>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 20px;">
                        <div class="progress-bar {{ $savingsGoal->isCompleted() ? 'bg-success' : 'bg-primary' }}"
                             style="width: {{ min(100, $savingsGoal->progress_percentage) }}%">
                            <span class="progress-text">{{ number_format($savingsGoal->progress_percentage, 1) }}%</span>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h5 mb-0 text-primary">{{ $currencySymbol }}{{ number_format($savingsGoal->current_amount, 2) }}</div>
                                <small class="text-muted">Ahorrado</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 text-muted">{{ $currencySymbol }}{{ number_format($savingsGoal->target_amount, 2) }}</div>
                            <small class="text-muted">Objetivo</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($savingsGoal->description)
                <div class="card mt-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                    <div class="card-header" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                        <h6 class="card-title mb-0" style="color: var(--text-primary);">
                            <i class="bi bi-card-text"></i> Descripción
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="color: var(--text-primary);">{{ $savingsGoal->description }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                <div class="card-header" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="card-title mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-info-circle"></i> Información
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Estado:</strong>
                        <span class="badge ms-2 {{ $savingsGoal->status === 'active' ? 'bg-success' : ($savingsGoal->status === 'paused' ? 'bg-warning' : 'bg-secondary') }}">
                            {{ $savingsGoal->status === 'active' ? 'Activa' : ($savingsGoal->status === 'paused' ? 'Pausada' : 'Completada') }}
                        </span>
                    </div>

                    @if($savingsGoal->target_date)
                        <div class="mb-3">
                            <strong>Fecha límite:</strong>
                            <div class="{{ $savingsGoal->isOverdue() ? 'text-danger' : 'text-primary' }}">
                                {{ $savingsGoal->target_date->format('d/m/Y') }}
                                @if($savingsGoal->days_remaining !== null)
                                    <br><small>
                                        @if($savingsGoal->days_remaining > 0)
                                            ({{ $savingsGoal->days_remaining }} días restantes)
                                        @elseif($savingsGoal->days_remaining == 0)
                                            (¡Vence hoy!)
                                        @else
                                            (Vencida hace {{ abs($savingsGoal->days_remaining) }} días)
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Creada:</strong>
                        <div>{{ $savingsGoal->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if($savingsGoal->updated_at != $savingsGoal->created_at)
                        <div class="mb-3">
                            <strong>Última actualización:</strong>
                            <div>{{ $savingsGoal->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif

                    @if($savingsGoal->next_milestone)
                        <div class="alert alert-info mt-3" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                            <small>
                                <i class="bi bi-trophy"></i>
                                Próximo hito: {{ $savingsGoal->next_milestone }}%
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            @if(!$savingsGoal->isCompleted())
                <div class="card mt-3" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                    <div class="card-header" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                        <h6 class="card-title mb-0" style="color: var(--text-primary);">
                            <i class="bi bi-lightbulb"></i> Consejos
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Establece recordatorios semanales</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Automatiza transferencias pequeñas</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Celebra cada hito alcanzado</li>
                            <li class="mb-0"><i class="bi bi-check-circle text-success"></i> Revisa tu progreso regularmente</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Savings Modal -->
@if(!$savingsGoal->isCompleted() && $savingsGoal->status === 'active')
    <div class="modal fade" id="addSavingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: var(--bg-card); color: var(--text-primary);">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Ahorros</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('savings-goals.addSavings', $savingsGoal) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Monto a agregar</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $currencySymbol }}</span>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
                            </div>
                            <div class="form-text">
                                Disponible en ahorros: {{ $currencySymbol }}{{ number_format(Auth::user()->getSavingsAccount()->balance, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Agregar Ahorros</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@section('scripts')
<script>
// Progress bar animation
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-text');

    if (progressBar && progressText) {
        const targetWidth = progressBar.style.width;
        progressBar.style.width = '0%';

        setTimeout(() => {
            progressBar.style.transition = 'width 1.5s ease-in-out';
            progressBar.style.width = targetWidth;
        }, 300);
    }
});
</script>
@endsection
@endsection
