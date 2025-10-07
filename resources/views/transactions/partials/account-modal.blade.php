<div class="modal fade" id="newAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newAccountForm">
                <div class="modal-body">
                    <input type="hidden" name="from_transaction" value="1">
                    <div class="mb-3">
                        <label for="new_account_name" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="new_account_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_type" class="form-label">Tipo *</label>
                        <select class="form-select" id="account_type" name="type" required>
                            <option value="cash">Efectivo</option>
                            
                            <option value="credit">Crédito</option>
                            <option value="savings">Ahorros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="initial_balance" class="form-label">Saldo Inicial *</label>
                        <input type="number" step="0.01" class="form-control" id="initial_balance" name="initial_balance" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newAccountForm = document.getElementById('newAccountForm');
    if (!newAccountForm) return;

    newAccountForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: document.getElementById('new_account_name').value,
            type: document.getElementById('account_type').value,
            initial_balance: document.getElementById('initial_balance').value,
            from_transaction: true
        };

        fetch("{{ route('accounts.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            // Agregar nueva cuenta al select
            const select = document.getElementById('account_id');
            const option = new Option(
                `${data.name} (${formatAccountType(data.type)} - ${data.balance} {{ auth()->user()->currency ?? 'NIO' }})`, 
                data.id
            );
            select.add(option);
            select.value = data.id;
            
            // Cerrar modal y resetear
            bootstrap.Modal.getInstance(document.getElementById('newAccountModal')).hide();
            newAccountForm.reset();
            
            // Mostrar mensaje de éxito
            alert('Cuenta creada exitosamente');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al crear la cuenta');
        });
    });

    function formatAccountType(type) {
        const types = {
            'cash': 'Efectivo',
            'bank': 'Banco', 
            'credit': 'Crédito',
            'savings': 'Ahorros'
        };
        return types[type] || type;
    }
});
</script>