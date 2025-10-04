@props(['type' => 'info', 'message' => '', 'title' => null, 'dismissible' => true])

@php
    $alertConfig = [
        'success' => ['class' => 'alert-success', 'icon' => 'bi-check-circle-fill', 'defaultTitle' => 'Éxito'],
        'error' => ['class' => 'alert-danger', 'icon' => 'bi-x-circle-fill', 'defaultTitle' => 'Error'],
        'warning' => ['class' => 'alert-warning', 'icon' => 'bi-exclamation-triangle-fill', 'defaultTitle' => 'Advertencia'],
        'info' => ['class' => 'alert-info', 'icon' => 'bi-info-circle-fill', 'defaultTitle' => 'Información'],
        'debug' => ['class' => 'alert-secondary', 'icon' => 'bi-bug-fill', 'defaultTitle' => 'Debug']
    ];

    $config = $alertConfig[$type] ?? $alertConfig['info'];
    $displayTitle = $title ?? $config['defaultTitle'];
@endphp

@if($message)
<div class="alert {{ $config['class'] }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    <div class="d-flex align-items-center">
        <div class="alert-icon me-3">
            <i class="bi {{ $config['icon'] }}"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-1">{{ $displayTitle }}</h6>
            <p class="alert-message mb-0 small">{{ $message }}</p>
        </div>
        @if($dismissible)
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        @endif
    </div>
</div>
@endif
