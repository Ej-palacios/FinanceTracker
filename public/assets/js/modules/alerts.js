class AlertSystem {
    constructor() {
        this.container = document.getElementById('globalAlerts');
        this.template = document.getElementById('alertTemplate');
        this.init();
    }

    init() {
        // Inicializar el contenedor si no existe
        if (!this.container) {
            this.createContainer();
        }
    }

    createContainer() {
        this.container = document.createElement('div');
        this.container.id = 'globalAlerts';
        this.container.className = 'position-fixed top-0 end-0 p-3';
        this.container.style.cssText = 'z-index: 9999; max-width: 400px;';
        document.body.appendChild(this.container);
    }

    show(message, type = 'info', title = null, duration = 5000) {
        const alertElement = this.createAlert(message, type, title, duration);
        this.container.appendChild(alertElement);

        // Auto-remover después de la duración
        setTimeout(() => {
            if (alertElement.parentNode) {
                this.removeAlert(alertElement);
            }
        }, duration);

        return alertElement;
    }

    createAlert(message, type, title, duration) {
        const clone = this.template.content.cloneNode(true);
        const alert = clone.querySelector('.alert');
        const icon = clone.querySelector('.alert-icon i');
        const heading = clone.querySelector('.alert-heading');
        const messageEl = clone.querySelector('.alert-message');
        const progressBar = clone.querySelector('.progress-bar');

        // Configurar estilos según el tipo
        const alertConfig = {
            'success': { class: 'alert-success', icon: 'bi-check-circle-fill', defaultTitle: 'Éxito' },
            'error': { class: 'alert-danger', icon: 'bi-x-circle-fill', defaultTitle: 'Error' },
            'warning': { class: 'alert-warning', icon: 'bi-exclamation-triangle-fill', defaultTitle: 'Advertencia' },
            'info': { class: 'alert-info', icon: 'bi-info-circle-fill', defaultTitle: 'Información' },
            'debug': { class: 'alert-secondary', icon: 'bi-bug-fill', defaultTitle: 'Debug' }
        };

        const config = alertConfig[type] || alertConfig.info;

        alert.classList.add(config.class);
        icon.classList.add(config.icon);
        heading.textContent = title || config.defaultTitle;
        messageEl.textContent = message;

        // Animación de progreso
        if (progressBar && duration > 0) {
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 100);
        }

        // Configurar cierre manual
        const closeBtn = alert.querySelector('.btn-close');
        closeBtn.addEventListener('click', () => this.removeAlert(alert));

        return alert;
    }

    removeAlert(alertElement) {
        alertElement.classList.remove('show');
        alertElement.classList.add('fade');
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.parentNode.removeChild(alertElement);
            }
        }, 300);
    }

    // Métodos rápidos
    success(message, title = null, duration = 5000) {
        return this.show(message, 'success', title, duration);
    }

    error(message, title = null, duration = 8000) {
        return this.show(message, 'error', title, duration);
    }

    warning(message, title = null, duration = 6000) {
        return this.show(message, 'warning', title, duration);
    }

    info(message, title = null, duration = 4000) {
        return this.show(message, 'info', title, duration);
    }

    debug(message, title = 'Debug', duration = 10000) {
        if (process.env.NODE_ENV === 'development') {
            return this.show(message, 'debug', title, duration);
        }
    }
}

// Instancia global
window.AlertSystem = new AlertSystem();

// Métodos globales rápidos
window.showSuccess = (message, title, duration) => AlertSystem.success(message, title, duration);
window.showError = (message, title, duration) => AlertSystem.error(message, title, duration);
window.showWarning = (message, title, duration) => AlertSystem.warning(message, title, duration);
window.showInfo = (message, title, duration) => AlertSystem.info(message, title, duration);
window.showDebug = (message, title, duration) => AlertSystem.debug(message, title, duration);