document.addEventListener('DOMContentLoaded', function() {
    // Marcar notificación como leída al hacer clic
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            const notificationId = this.getAttribute('data-notification-id');

            // Marcar como leído via AJAX
            fetch(`/notificaciones/${notificationId}/leer`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    // Remover indicador visual
                    this.classList.remove('unread');
                    const indicator = this.querySelector('.notification-indicator');
                    if (indicator) indicator.remove();

                    // Actualizar contador
                    updateNotificationCount();
                }
            });
        });
    });

    // Marcar todas como leídas
    const markAllAsReadBtn = document.getElementById('markAllAsRead');
    if (markAllAsReadBtn) {
        markAllAsReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            fetch('/notificaciones/marcar-todas-leidas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    // Actualizar UI
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('unread');
                        const indicator = item.querySelector('.notification-indicator');
                        if (indicator) indicator.remove();
                    });

                    // Ocultar botón y actualizar contador
                    markAllAsReadBtn.remove();
                    updateNotificationCount();
                }
            });
        });
    }

    function updateNotificationCount() {
        // Actualizar el badge de notificaciones
        fetch('/notificaciones/contador-no-leidas')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.badge.bg-danger');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count;
                    } else {
                        // Crear badge si no existe
                        const bellBtn = document.querySelector('[data-bs-toggle="dropdown"]');
                        const newBadge = document.createElement('span');
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        newBadge.textContent = data.count;
                        bellBtn.appendChild(newBadge);
                    }
                } else if (badge) {
                    badge.remove();
                }
            });
    }

    // Actualizar contador cada 30 segundos
    setInterval(updateNotificationCount, 30000);
});
