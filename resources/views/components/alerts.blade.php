<!-- Sistema de Alertas Globales -->
<div id="globalAlerts" class="position-fixed top-0 end-0 p-3" style="z-index: 200000; max-width: 400px; pointer-events: auto;">
    <!-- Las alertas se insertarán aquí dinámicamente -->
</div>

<!-- Contenedor para Toasts -->
<div id="toastContainer" class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 200000; pointer-events: auto;">
    <!-- Los toasts se insertarán aquí dinámicamente -->
</div>

<!-- Template para Alertas -->
<template id="alertTemplate">
    <div class="alert alert-dismissible fade show mb-3 shadow-lg" role="alert">
        <div class="d-flex align-items-center">
            <div class="alert-icon me-3">
                <i class="bi"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1"></h6>
                <p class="alert-message mb-0 small"></p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <div class="alert-progress mt-2">
            <div class="progress" style="height: 3px;">
                <div class="progress-bar" style="width: 100%; transition: width 5s linear;"></div>
            </div>
        </div>
    </div>
</template>

<!-- Template para Toasts -->
<template id="toastTemplate">
    <div class="toast toast-show mb-2 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <div class="toast-icon me-2">
                <i class="bi"></i>
            </div>
            <strong class="me-auto toast-heading"></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <span class="toast-message"></span>
        </div>
    </div>
</template>
