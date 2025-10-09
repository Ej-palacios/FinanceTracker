document.addEventListener('DOMContentLoaded', function () {
    // Input masks and validation can be added here
    const amountInput = document.getElementById('amount');
    amountInput.addEventListener('input', function () {
        // Allow only numbers and decimal point
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    // Auto-completion example: could be extended with AJAX calls for categories/accounts
    // For now, just a placeholder for future enhancement

    // Additional UX improvements like keyboard shortcuts can be added here
});
