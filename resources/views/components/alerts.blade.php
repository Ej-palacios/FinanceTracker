<!-- Sistema de Alertas Globales -->
<div id="globalAlerts" class="position-fixed top-0 end-0 p-3" style="z-index: 9999; max-width: 400px;">
    <!-- Las alertas se insertarán aquí dinámicamente -->
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