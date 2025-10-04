@extends('layouts.app')

@section('title', 'Notificaciones')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/notifications.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Notificaciones</h1>
                <div class="d-flex gap-2">
                    @if($notifications->where('is_read', false)->count() > 0)
                        <button class="btn btn-outline-primary" id="markAllAsRead">
                            <i class="bi bi-check-all me-2"></i>Marcar todas como leídas
                        </button>
                    @endif
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Todas las notificaciones</h5>
                        <span class="badge bg-primary">
                            {{ $notifications->total() }} total
                            @if($notifications->where('is_read', false)->count() > 0)
                                <span class="badge bg-danger ms-1">
                                    {{ $notifications->where('is_read', false)->count() }} no leídas
                                </span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <a href="{{ $notification->getUrl() }}"
                                   class="list-group-item list-group-item-action notification-item {{ $notification->is_read ? '' : 'unread' }}"
                                   data-notification-id="{{ $notification->id }}">
                                    <div class="d-flex w-100 align-items-start">
                                        <div class="notification-icon me-3">
                                            @switch($notification->type)
                                                @case('exchange_request')
                                                    <div class="bg-primary text-white rounded-circle p-2">
                                                        <i class="bi bi-arrow-left-right"></i>
                                                    </div>
                                                    @break
                                                @case('exchange_approved')
                                                    <div class="bg-success text-white rounded-circle p-2">
                                                        <i class="bi bi-check-circle"></i>
                                                    </div>
                                                    @break
                                                @case('exchange_rejected')
                                                    <div class="bg-danger text-white rounded-circle p-2">
                                                        <i class="bi bi-x-circle"></i>
                                                    </div>
                                                    @break
                                                @case('exchange_completed')
                                                    <div class="bg-info text-white rounded-circle p-2">
                                                        <i class="bi bi-check-all"></i>
                                                    </div>
                                                    @break
                                                @default
                                                    <div class="bg-secondary text-white rounded-circle p-2">
                                                        <i class="bi bi-info-circle"></i>
                                                    </div>
                                            @endswitch
                                        </div>
                                        <div class="notification-content flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1">{{ $notification->title }}</h6>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary">Nueva</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No hay notificaciones</h5>
                            <p class="text-muted">Cuando tengas notificaciones, aparecerán aquí.</p>
                        </div>
                    @endif
                </div>

                @if($notifications->hasPages())
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/modules/notifications.js') }}"></script>
@endsection
