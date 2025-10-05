<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="card-title mb-2 mb-md-0">Historial de Transacciones</h5>
            
            <div class="d-flex gap-2 flex-wrap">
                <div class="dropdown">
                    <button class="btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel"></i> Filtros
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Tipo de Transacción</h6></li>
                        <li><a class="dropdown-item" href="#" data-filter="transaction_type" data-value="all">Todos</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="transaction_type" data-value="income">Ingresos</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="transaction_type" data-value="expense">Gastos</a></li>
                        
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Filtrar por día</h6></li>
                        <li>
                            <div class="px-3 py-1">
                                <input type="number" min="1" max="31" class="form-control form-control-sm" 
                                       id="dayFilter" placeholder="Día (1-31)" 
                                       value="{{ request('day') }}">
                                <button class="btn btn-sm btn-primary mt-1 w-100" 
                                        onclick="applyDayFilter()">Aplicar</button>
                            </div>
                        </li>
                        
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Filtrar por mes</h6></li>
                        <li>
                            <div class="px-3 py-1">
                                <select class="form-select form-select-sm" id="monthFilter">
                                    <option value="">Seleccione un mes</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                <button class="btn btn-sm btn-primary mt-1 w-100" 
                                        onclick="applyMonthFilter()">Aplicar</button>
                            </div>
                        </li>
                        
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Rango de fechas</h6></li>
                        <li>
                            <div class="px-3 py-1">
                                <input type="date" class="form-control form-control-sm mb-1" 
                                       id="startDate" placeholder="Desde" 
                                       value="{{ request('start_date') }}">
                                <input type="date" class="form-control form-control-sm" 
                                       id="endDate" placeholder="Hasta" 
                                       value="{{ request('end_date') }}">
                                <button class="btn btn-sm btn-primary mt-1 w-100" 
                                        onclick="applyDateRangeFilter()">Aplicar</button>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <button id="clearFilters" class="btn btn-outline-danger">
                    <i class="bi bi-x-circle"></i> Limpiar
                </button>
                
                <a href="{{ route('transacciones.create') }}" class="submit-button">
                    <span class="button-text"><i class="bi bi-plus-circle"></i> Nueva</span>
                    <span class="button-glow"></span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        @if(request()->anyFilled(['transaction_type', 'day', 'month', 'start_date', 'end_date']))
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted">Filtros aplicados:</small>
                    @if(request('transaction_type') && request('transaction_type') != 'all')
                        <span class="badge bg-primary">
                            Tipo: {{ request('transaction_type') == 'income' ? 'Ingresos' : 'Gastos' }}
                            <a href="?{{ http_build_query(request()->except('transaction_type')) }}" class="text-white ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    
                    @if(request('day'))
                        <span class="badge bg-primary">
                            Día: {{ request('day') }}
                            <a href="?{{ http_build_query(request()->except('day')) }}" class="text-white ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    
                    @if(request('month'))
                        <span class="badge bg-primary">
                            Mes: {{ DateTime::createFromFormat('!m', request('month'))->format('F') }}
                            <a href="?{{ http_build_query(request()->except('month')) }}" class="text-white ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    
                    @if(request('start_date') && request('end_date'))
                        <span class="badge bg-primary">
                            Rango: {{ request('start_date') }} a {{ request('end_date') }}
                            <a href="?{{ http_build_query(request()->except(['start_date', 'end_date'])) }}" class="text-white ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                </div>
            </div>
        @endif
        
        @if($transactions->isEmpty())
            <div class="alert alert-info">No hay transacciones con los filtros aplicados</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Cuenta</th>
                            <th class="text-end">Monto</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>
                                    @if($transaction->description)
                                        <span data-bs-toggle="tooltip" title="{{ $transaction->description }}">
                                            {{ Str::limit($transaction->description, 25) }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sin descripción</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $transaction->category->type === 'income' ? 'success' : 'danger' }}-subtle text-{{ $transaction->category->type === 'income' ? 'success' : 'danger' }}">
                                        {{ $transaction->category->name }}
                                    </span>
                                </td>
                                <td>{{ $transaction->account->name }}</td>
                                <td class="text-end fw-bold {{ $transaction->category->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    @php
                                        $converted = \App\Service\CurrencyService::convertAndFormat($transaction->amount, $userCurrency ?? 'NIO');
                                    @endphp
                                    {{ $transaction->category->type === 'income' ? '+' : '-' }}
                                    {{ $converted['formatted'] }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Botón Show -->
                                        <a href="{{ route('transacciones.show', $transaction) }}" 
                                           class="btn btn-outline-info"
                                           data-bs-toggle="tooltip"
                                           title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <!-- Botón Edit -->
                                        <a href="{{ route('transacciones.edit', $transaction) }}" 
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <!-- Botón Delete -->
                                        <form action="{{ route('transacciones.destroy', $transaction) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger"
                                                    data-bs-toggle="tooltip"
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Eliminar esta transacción?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex justify-content-center">
                {{ $transactions->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(el => new bootstrap.Tooltip(el));

    // Manejar filtros
    document.querySelectorAll('[data-filter]').forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(window.location.href);
            url.searchParams.set(this.dataset.filter, this.dataset.value);
            window.location.href = url.toString();
        });
    });

    // Limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', function() {
        window.location.href = "{{ route('transacciones.index') }}";
    });
});

// Funciones para los filtros del dropdown
function applyDayFilter() {
    const day = document.getElementById('dayFilter').value;
    if (day && day >= 1 && day <= 31) {
        const url = new URL(window.location.href);
        url.searchParams.set('day', day);
        window.location.href = url.toString();
    }
}

function applyMonthFilter() {
    const month = document.getElementById('monthFilter').value;
    if (month) {
        const url = new URL(window.location.href);
        url.searchParams.set('month', month);
        window.location.href = url.toString();
    }
}

function applyDateRangeFilter() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    if (startDate && endDate) {
        const url = new URL(window.location.href);
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);
        window.location.href = url.toString();
    }
}
</script>
@endsection