document.addEventListener('DOMContentLoaded', function() {
    // Marcar notificación como leída al hacer clic
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            const notificationId = this.getAttribute('data-notification-id');

            // Solo marcar como leída si no está leída
            if (this.classList.contains('unread')) {
                fetch(`/notificaciones/${notificationId}/leer`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        // Remover clase unread y badge
                        this.classList.remove('unread');
                        const badge = this.querySelector('.badge');
                        if (badge) badge.remove();
                    }
                });
            }
        });
    });

    // Marcar todas como leídas
    const markAllBtn = document.getElementById('markAllAsRead');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            fetch('/notificaciones/marcar-todas-leidas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    // Actualizar UI
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('unread');
                        const badge = item.querySelector('.badge');
                        if (badge) badge.remove();
                    });

                    // Ocultar botón
                    markAllBtn.remove();

                    // Actualizar contador en header
                    updateHeaderNotificationCount();
                }
            });
        });
    }

    function updateHeaderNotificationCount() {
        fetch('/notificaciones/contador-no-leidas')
            .then(response => response.json())
            .then(data => {
                // Actualizar badge en header
                const headerBadge = document.querySelector('.app-header .badge.bg-danger');
                if (data.count > 0) {
                    if (headerBadge) {
                        headerBadge.textContent = data.count;
                    }
                } else if (headerBadge) {
                    headerBadge.remove();
                }
            });
    }
});
