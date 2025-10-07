<form action="{{ $action }}" method="POST" id="formTransaction">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif
    <div class="mb-3">
        <label for="type" class="form-label">Tipo</label>
        <select class="form-select" id="type" name="type" required>
            <option value="income" {{ old('type', $transaction->type ?? '') == 'income' ? 'selected' : '' }}>Ingreso</option>
            <option value="expense" {{ old('type', $transaction->type ?? '') == 'expense' ? 'selected' : '' }}>Gasto</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="amount" class="form-label">Monto</label>
        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $transaction->amount ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label for="category_id" class="form-label">Categoría</label>
        <select class="form-select" id="category_id" name="category_id" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" data-type="{{ $category->type }}" {{ old('category_id', $transaction->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="account_id" class="form-label">Cuenta</label>
        <select class="form-select" id="account_id" name="account_id" required>
            @foreach($accounts as $account)
                @if($account->type !== 'savings')
                <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id ?? '') == $account->id ? 'selected' : '' }}>
                    {{ $account->name }} ({{ $account->balance }})
                </option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="date" class="form-label">Fecha</label>
        <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $transaction->date ?? date('Y-m-d')) }}" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Descripción (opcional)</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $transaction->description ?? '') }}</textarea>
    </div>
    <button type="button" class="btn btn-primary" id="btnGuardarIngreso">Guardar Transacción</button>
</form>

<!-- Modal para elegir cuánto ahorrar -->
<div class="modal fade" id="modalAhorro" tabindex="-1" aria-labelledby="modalAhorroLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--bg-card); color: var(--text-primary);">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAhorroLabel">¿Cuánto deseas ahorrar?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="number" step="0.01" class="form-control" id="inputAhorro" min="0" max="{{ old('amount', $transaction->amount ?? 0) }}" placeholder="Monto a ahorrar">
                <small class="text-muted">Puedes ahorrar hasta el monto ingresado.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarAhorro">Confirmar Ahorro</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="{{ asset('assets/js/modules/transaction-form-enhancements.js') }}"></script>
@endsection
