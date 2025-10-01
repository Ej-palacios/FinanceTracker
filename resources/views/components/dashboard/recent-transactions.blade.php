<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Transacciones Recientes</h5>
            <a href="{{ route('transacciones.index') }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th class="text-end">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('d/m/Y') }}</td>
                            <td>{{ $transaction->description ?? 'Sin descripción' }}</td>
                            <td>
                                <span class="badge bg-{{ $transaction->category->type === 'income' ? 'success' : 'danger' }}-subtle text-{{ $transaction->category->type === 'income' ? 'success' : 'danger' }}">
                                    {{ $transaction->category->name }}
                                </span>
                            </td>
                            <td class="text-end fw-bold {{ $transaction->category->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->category->type === 'income' ? '+' : '-' }} 
                                {{ $currencySymbol }} {{ number_format($transaction->amount, 2, '.', ',') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay transacciones recientes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>