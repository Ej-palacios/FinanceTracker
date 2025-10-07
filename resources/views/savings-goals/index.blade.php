@extends('layouts.app')

@section('title', 'Metas de Ahorro')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1>Metas de Ahorro</h1>
            <a href="{{ route('savings-goals.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Meta
            </a>
        </div>
    </div>

    @include('components.alerts')

    @if($goals->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-piggy-bank" style="font-size: 4rem; color: var(--text-muted);"></i>
                        <h4 class="mt-3" style="color: var(--text-primary);">No tienes metas de ahorro</h4>
                        <p style="color: var(--text-secondary);">Crea tu primera meta para empezar a ahorrar de manera organizada.</p>
                        <a href="{{ route('savings-goals.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Crear Primera Meta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($goals as $goal)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
                            <h5 class="card-title mb-0" style="color: var(--text-primary);">{{ $goal->name }}</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('savings-goals.show', $goal) }}">
                                        <i class="bi bi-eye"></i> Ver Detalles
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('savings-goals.edit', $goal) }}">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $goal->id }}, '{{ $goal->name }}')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($goal->description)
                                <p class="text-muted small mb-3">{{ Str::limit($goal->description, 100) }}</p>
                            @endif

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small" style="color: var(--text-secondary);">Progreso</span>
                                    <span class="small fw-bold" style="color: var(--text-primary);">
                                        {{ number_format($goal->progress_percentage, 1) }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $goal->isCompleted() ? 'bg-success' : 'bg-primary' }}"
                                         style="width: {{ min(100, $goal->progress_percentage) }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="small text-muted">Ahorrado</div>
                                    <div class="fw-bold" style="color: var(--text-primary);">
                                        {{ $currencySymbol }}{{ number_format((float) $goal->current_amount, 2) }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-muted">Meta</div>
                                    <div class="fw-bold" style="color: var(--text-primary);">
                                        {{ $currencySymbol }}{{ number_format((float) $goal->target_amount, 2) }}
                                    </div>
                                </div>
                            </div>

                            @if($goal->target_date)
                                <div class="mt-3 pt-3 border-top">
                                    <div class="small text-muted mb-1">Fecha límite</div>
                                    <div class="small {{ $goal->isOverdue() ? 'text-danger' : 'text-primary' }}">
                                        <i class="bi bi-calendar"></i>
                                        {{ $goal->target_date->format('d/m/Y') }}
                                        @if($goal->days_remaining !== null)
                                            @if($goal->days_remaining > 0)
                                                ({{ $goal->days_remaining }} días)
                                            @elseif($goal->days_remaining == 0)
                                                (¡Hoy!)
                                            @else
                                                (Vencida)
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer" style="background-color: var(--bg-secondary); border-top: 1px solid var(--border-color);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge {{ $goal->status === 'active' ? 'bg-success' : ($goal->status === 'paused' ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ $goal->status === 'active' ? 'Activa' : ($goal->status === 'paused' ? 'Pausada' : 'Completada') }}
                                </span>

                                @if(!$goal->isCompleted() && $goal->status === 'active')
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSavingsModal{{ $goal->id }}">
                                        <i class="bi bi-plus"></i> Agregar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Savings Modal -->
                @if(!$goal->isCompleted() && $goal->status === 'active')
                    <div class="modal fade" id="addSavingsModal{{ $goal->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content" style="background-color: var(--bg-card); color: var(--text-primary);">
                                <div class="modal-header">
                                    <h5 class="modal-title">Agregar Ahorros a "{{ $goal->name }}"</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('savings-goals.addSavings', $goal) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Monto a agregar</label>
                                            <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
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
            @endforeach
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--bg-card); color: var(--text-primary);">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro que deseas eliminar la meta "<span id="goalName"></span>"?</p>
                <p class="text-danger small">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function confirmDelete(goalId, goalName) {
    document.getElementById('goalName').textContent = goalName;
    document.getElementById('deleteForm').action = `{{ url('savings-goals') }}/${goalId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
@endsection
