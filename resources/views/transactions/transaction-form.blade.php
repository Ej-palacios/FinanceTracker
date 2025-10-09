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
        <div class="input-group">
            <select class="form-select" id="category_id" name="category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" data-type="{{ $category->type }}" {{ old('category_id', $transaction->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Agregar
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label for="account_id" class="form-label">Cuenta</label>
        <div class="input-group">
            <select class="form-select" id="account_id" name="account_id" required>
                @foreach($accounts as $account)
                    @if($account->type !== 'savings')
                    <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id ?? '') == $account->id ? 'selected' : '' }}>
                        {{ $account->name }} ({{ $account->balance }})
                    </option>
                    @endif
                @endforeach
            </select>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                <i class="fas fa-plus"></i> Agregar
            </button>
        </div>
    </div>
    <div class="mb-3">
        <label for="date" class="form-label">Fecha</label>
        <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $transaction->date ?? date('Y-m-d')) }}" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Descripción (opcional)</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $transaction->description ?? '') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Guardar Transacción</button>
</form>

<!-- Modal para agregar categoría -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--bg-card); color: var(--text-primary);">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Agregar Nueva Categoría</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="addCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryType" class="form-label">Tipo</label>
                        <select class="form-select" id="categoryType" name="type" required>
                            <option value="income">Ingreso</option>
                            <option value="expense">Gasto</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar cuenta -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: var(--bg-card); color: var(--text-primary);">
            <div class="modal-header">
                <h5 class="modal-title" id="addAccountModalLabel">Agregar Nueva Cuenta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="addAccountForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="accountName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="accountName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="accountType" class="form-label">Tipo</label>
                        <select class="form-select" id="accountType" name="type" required>
                            <option value="cash">Efectivo</option>
                            <option value="bank">Banco</option>
                            <option value="credit">Crédito</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="initialBalance" class="form-label">Saldo Inicial</label>
                        <input type="number" step="0.01" class="form-control" id="initialBalance" name="initial_balance" value="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="{{ asset('assets/js/modules/transaction-form-enhancements.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle add category form
    document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('from_transaction', '1');

        fetch('/categories', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                // Add to select
                const select = document.getElementById('category_id');
                const option = new Option(data.name, data.id, false, true);
                option.setAttribute('data-type', data.type);
                select.appendChild(option);
                select.value = data.id;
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
                // Reset form
                this.reset();
            } else {
                alert('Error al agregar categoría');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar categoría');
        });
    });

    // Handle add account form
    document.getElementById('addAccountForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('from_transaction', '1');

        fetch('/accounts', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                // Add to select
                const select = document.getElementById('account_id');
                const option = new Option(`${data.name} (${data.balance})`, data.id, false, true);
                select.appendChild(option);
                select.value = data.id;
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('addAccountModal')).hide();
                // Reset form
                this.reset();
            } else {
                alert('Error al agregar cuenta');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar cuenta');
        });
    });
});
</script>
@endsection
