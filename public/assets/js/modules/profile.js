document.addEventListener('DOMContentLoaded', function() {
    // Sincronizar el botón del header con el estado del modo oscuro
    const darkModeToggle = document.querySelector('.dark-mode-toggle');
    const darkModeCheckbox = document.getElementById('dark_mode');

    if (darkModeToggle && darkModeCheckbox) {
        darkModeToggle.addEventListener('click', function() {
            // Actualizar el checkbox cuando se usa el botón del header
            darkModeCheckbox.checked = document.body.getAttribute('data-bs-theme') === 'dark';
        });

        darkModeCheckbox.addEventListener('change', function() {
            // Simular click en el botón del header cuando se cambia el checkbox
            if ((this.checked && document.body.getAttribute('data-bs-theme') !== 'dark') ||
                (!this.checked && document.body.getAttribute('data-bs-theme') === 'dark')) {
                darkModeToggle.click();
            }
        });
    }
});
