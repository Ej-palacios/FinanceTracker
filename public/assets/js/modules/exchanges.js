document.addEventListener('DOMContentLoaded', function() {
    // Confirmación para acciones
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (this.querySelector('button[type="submit"]').textContent.includes('Rechazar') ||
                this.querySelector('button[type="submit"]').textContent.includes('Cancelar')) {
                if (!confirm('¿Estás seguro de que quieres realizar esta acción?')) {
                    e.preventDefault();
                }
            }
        });
    });
});
