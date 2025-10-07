document.addEventListener('DOMContentLoaded', function () {
    const formTransaction = document.getElementById('formTransaction');
    const btnGuardarIngreso = document.getElementById('btnGuardarIngreso');
    const modalAhorro = new bootstrap.Modal(document.getElementById('modalAhorro'));
    const inputAhorro = document.getElementById('inputAhorro');
    const btnConfirmarAhorro = document.getElementById('btnConfirmarAhorro');

    // Input masks and validation can be added here
    const amountInput = document.getElementById('amount');
    amountInput.addEventListener('input', function () {
        // Allow only numbers and decimal point
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    // Auto-completion example: could be extended with AJAX calls for categories/accounts
    // For now, just a placeholder for future enhancement

    btnGuardarIngreso.addEventListener('click', function () {
        const type = document.getElementById('type').value;
        if (type === 'income') {
            inputAhorro.max = amountInput.value || 0;
            inputAhorro.value = 0;
            modalAhorro.show();
        } else {
            formTransaction.submit();
        }
    });

    btnConfirmarAhorro.addEventListener('click', function () {
        const ahorro = parseFloat(inputAhorro.value);
        const amount = parseFloat(amountInput.value);

        if (isNaN(ahorro) || ahorro < 0 || ahorro > amount) {
            alert('Por favor, ingresa un monto válido para ahorrar.');
            return;
        }

        const montoRestante = amount - ahorro;
        amountInput.value = montoRestante;

        formTransaction.submit();

        // Additional logic for saving the ahorro amount can be added here
    });

    // Additional UX improvements like keyboard shortcuts can be added here
});
