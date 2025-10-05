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

    // Optimized loader for smoother performance
    const loader = document.getElementById('custom-loader');

    function showLoader() {
        if (!loader) return;
        loader.style.display = 'flex';
        // Use requestAnimationFrame for smoother animation
        requestAnimationFrame(() => {
            loader.classList.add('show');
        });
        document.body.classList.add('no-scroll');
    }

    function hideLoader() {
        if (!loader) return;
        loader.classList.remove('show');
        // Wait for transition to complete before hiding
        setTimeout(() => {
            loader.style.display = 'none';
            document.body.classList.remove('no-scroll');
        }, 300);
    }

    // Global loader functions for use throughout the app
    window.showLoader = showLoader;
    window.hideLoader = hideLoader;

    // Show loader on form submit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            showLoader();
        });
    });
});
