class AlertSystem {
    constructor() {
        this.container = document.getElementById('globalAlerts');
        this.toastContainer = document.getElementById('toastContainer');
        this.template = document.getElementById('alertTemplate');
        this.toastTemplate = document.getElementById('toastTemplate');
        this.init();
    }

    init() {
        // Inicializar el contenedor de alertas si no existe
        if (!this.container) {
            this.createContainer();
        }
        // Inicializar el contenedor de toasts si no existe
        if (!this.toastContainer) {
            this.createToastContainer();
        }
    }

    createContainer() {
        this.container = document.createElement('div');
        this.container.id = 'globalAlerts';
        this.container.className = 'position-fixed top-0 end-0 p-3';
        this.container.style.cssText = 'z-index: 9999; max-width: 400px;';
        document.body.appendChild(this.container);
    }

    createToastContainer() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.id = 'toastContainer';
        this.toastContainer.className = 'toast-container position-fixed bottom-0 start-50 translate-middle-x p-3';
        this.toastContainer.style.cssText = 'z-index: 9999;';
        document.body.appendChild(this.toastContainer);
    }

    show(message, type = 'info', title = null, duration = 5000) {
        const alertElement = this.createAlert(message, type, title, duration);
        this.container.appendChild(alertElement);

        // Auto-remover después de la duración (solo si duration > 0)
        if (duration > 0) {
            setTimeout(() => {
                if (alertElement.parentNode) {
                    this.removeAlert(alertElement);
                }
            }, duration);
        }

        return alertElement;
    }

    showToast(message, type = 'info', title = null, duration = 3000) {
        const toastElement = this.createToast(message, type, title, duration);
        this.toastContainer.appendChild(toastElement);

        // Auto-remover después de la duración (solo si duration > 0)
        if (duration > 0) {
            setTimeout(() => {
                if (toastElement.parentNode) {
                    this.removeToast(toastElement);
                }
            }, duration);
        }

        return toastElement;
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

    createToast(message, type, title, duration) {
        const clone = this.toastTemplate.content.cloneNode(true);
        const toast = clone.querySelector('.toast');
        const icon = clone.querySelector('.toast-icon i');
        const heading = clone.querySelector('.toast-heading');
        const messageEl = clone.querySelector('.toast-message');

        // Configurar estilos según el tipo
        const toastConfig = {
            'success': { class: 'toast-success', icon: 'bi-check-circle-fill', defaultTitle: 'Éxito' },
            'error': { class: 'toast-error', icon: 'bi-x-circle-fill', defaultTitle: 'Error' },
            'warning': { class: 'toast-warning', icon: 'bi-exclamation-triangle-fill', defaultTitle: 'Advertencia' },
            'info': { class: 'toast-info', icon: 'bi-info-circle-fill', defaultTitle: 'Información' },
            'debug': { class: 'toast-debug', icon: 'bi-bug-fill', defaultTitle: 'Debug' }
        };

        const config = toastConfig[type] || toastConfig.info;

        toast.classList.add(config.class);
        icon.classList.add(config.icon);
        heading.textContent = title || config.defaultTitle;
        messageEl.textContent = message;

        // Configurar cierre manual
        const closeBtn = toast.querySelector('.btn-close');
        closeBtn.addEventListener('click', () => this.removeToast(toast));

        return toast;
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

    removeToast(toastElement) {
        toastElement.classList.add('toast-hide');
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.parentNode.removeChild(toastElement);
            }
        }, 500);
    }

    // Diálogo de confirmación persistente que devuelve una Promesa<boolean>
    confirm(message, title = '¿Confirmar acción?', { okText = 'Aceptar', cancelText = 'Cancelar' } = {}) {
        return new Promise((resolve) => {
            const alertEl = this.show(message, 'warning', title, 0);
            // Desactivar autocierre visual: sin progreso y sin botón de cierre
            const progress = alertEl.querySelector('.alert-progress');
            if (progress) progress.style.display = 'none';
            const closeBtn = alertEl.querySelector('.btn-close');
            if (closeBtn) closeBtn.style.display = 'none';

            // Pie con botones
            const footer = document.createElement('div');
            footer.className = 'mt-2 d-flex gap-2 justify-content-end';
            const btnCancel = document.createElement('button');
            btnCancel.className = 'btn btn-sm btn-secondary';
            btnCancel.textContent = cancelText;
            const btnOk = document.createElement('button');
            btnOk.className = 'btn btn-sm btn-primary';
            btnOk.textContent = okText;
            footer.appendChild(btnCancel);
            footer.appendChild(btnOk);
            alertEl.appendChild(footer);

            const cleanup = () => this.removeAlert(alertEl);

            btnCancel.addEventListener('click', () => { cleanup(); resolve(false); });
            btnOk.addEventListener('click', () => { cleanup(); resolve(true); });
        });
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

    // Métodos rápidos para toasts
    successToast(message, title = null, duration = 3000) {
        return this.showToast(message, 'success', title, duration);
    }

    errorToast(message, title = null, duration = 5000) {
        return this.showToast(message, 'error', title, duration);
    }

    warningToast(message, title = null, duration = 4000) {
        return this.showToast(message, 'warning', title, duration);
    }

    infoToast(message, title = null, duration = 3000) {
        return this.showToast(message, 'info', title, duration);
    }

    debugToast(message, title = 'Debug', duration = 5000) {
        if (process.env.NODE_ENV === 'development') {
            return this.showToast(message, 'debug', title, duration);
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
window.showConfirm = (message, title, options) => AlertSystem.confirm(message, title, options);

// Métodos globales rápidos para toasts
window.showSuccessToast = (message, title, duration) => AlertSystem.successToast(message, title, duration);
window.showErrorToast = (message, title, duration) => AlertSystem.errorToast(message, title, duration);
window.showWarningToast = (message, title, duration) => AlertSystem.warningToast(message, title, duration);
window.showInfoToast = (message, title, duration) => AlertSystem.infoToast(message, title, duration);
window.showDebugToast = (message, title, duration) => AlertSystem.debugToast(message, title, duration);
