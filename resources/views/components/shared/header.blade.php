<header class="app-header navbar navbar-expand" style="border-bottom: 1px solid var(--border-color);">
    <div class="container-fluid">
        <button class="btn sidebar-toggle me-2 d-lg-none">
            <i class="bi bi-list"></i>
        </button>

        <a class="navbar-brand" href="{{ route('dashboard') }}" style="color: var(--text-primary);">
            <i class="bi bi-cash-coin me-2"></i>
            <span class="d-none d-sm-inline">FinanceTracker</span>
        </a>

        <div class="d-flex align-items-center ms-auto">
            <!-- Botón de Notificaciones -->
            <div class="dropdown me-3">
                <button class="btn position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    @php
                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                            <span class="visually-hidden">Notificaciones no leídas</span>
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-notifications" aria-labelledby="notificationsDropdown">
                    <li class="dropdown-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Notificaciones</span>
                            @if($unreadCount > 0)
                                <button class="btn btn-sm btn-outline-secondary" id="markAllAsRead">
                                    Marcar todas como leídas
                                </button>
                            @endif
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>

                    <!-- Lista de Notificaciones -->
                    <div class="notifications-list" style="max-height: 400px; overflow-y: auto;">
                        @forelse(auth()->user()->notifications()->latest()->limit(10)->get() as $notification)
                            <li>
                                <a class="dropdown-item notification-item {{ $notification->is_read ? '' : 'unread' }}"
                                   href="{{ $notification->getUrl() }}"
                                   data-notification-id="{{ $notification->id }}">
                                    <div class="d-flex w-100">
                                        <div class="notification-icon me-3">
                                            @switch($notification->type)
                                                @case('exchange_request')
                                                    <i class="bi bi-arrow-left-right text-primary"></i>
                                                    @break
                                                @case('exchange_approved')
                                                    <i class="bi bi-check-circle text-success"></i>
                                                    @break
                                                @case('exchange_rejected')
                                                    <i class="bi bi-x-circle text-danger"></i>
                                                    @break
                                                @default
                                                    <i class="bi bi-info-circle text-info"></i>
                                            @endswitch
                                        </div>
                                        <div class="notification-content flex-grow-1">
                                            <h6 class="mb-1">{{ $notification->title }}</h6>
                                            <p class="mb-1 text-muted small">{{ $notification->message }}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(!$notification->is_read)
                                            <div class="notification-indicator">
                                                <span class="badge bg-primary rounded-circle" style="width: 8px; height: 8px;"></span>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </li>
                            @if(!$loop->last)
                                <li><hr class="dropdown-divider m-0"></li>
                            @endif
                        @empty
                            <li class="px-3 py-4 text-center">
                                <i class="bi bi-bell-slash fs-2 text-muted mb-2"></i>
                                <p class="text-muted mb-0">No hay notificaciones</p>
                            </li>
                        @endforelse
                    </div>

                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-center text-primary" href="{{ route('notifications.index') }}">
                            <i class="bi bi-list-ul me-1"></i>Ver todas las notificaciones
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Botón de Tema Oscuro -->
            <button class="btn dark-mode-toggle me-2 p-0" type="button" role="switch" aria-checked="false" aria-label="Cambiar tema">
                <span class="switch-wrapper">
                    <input class="switch-check" id="darkModeSwitch" type="checkbox">
                    <label class="switch-label" for="darkModeSwitch">
                        <span class="switch-handle"></span>
                    </label>
                </span>
            </button>

            <!-- Dropdown de Usuario -->
            <div class="dropdown">
                <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('perfil') }}">Perfil</a></li>
                    <li><a class="dropdown-item" href="{{ route('notifications.index') }}">
                        <i class="bi bi-bell me-2"></i>Notificaciones
                        @if($unreadCount > 0)
                            <span class="badge bg-danger float-end">{{ $unreadCount }}</span>
                        @endif
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/header.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('assets/js/modules/header.js') }}"></script>
@endsection
