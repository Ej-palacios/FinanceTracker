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
