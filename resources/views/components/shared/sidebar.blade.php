<aside class="app-sidebar">
    <div class="sidebar-header p-3">
        <div class="sidebar-brand">
            <i class="bi bi-cash-coin"></i>
            <h5 class="sidebar-title">FinanceTracker</h5>
        </div>
        <button class="btn-close sidebar-close d-lg-none"></button>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span class="link-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('transacciones.*') ? 'active' : '' }}" 
               href="{{ route('transacciones.index') }}">
                <i class="bi bi-arrow-left-right"></i>
                <span class="link-text">Transacciones</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('savings-goals.*') ? 'active' : '' }}" 
               href="{{ route('savings-goals.index') }}">
                <i class="bi bi-piggy-bank"></i>
                <span class="link-text d-flex align-items-center gap-2">
                    <span>Metas de Ahorro</span>
                    @php
                        $activeGoals = Auth::user()->activeSavingsGoals()->count();
                    @endphp
                    @if($activeGoals > 0)
                        <span class="badge bg-success">{{ $activeGoals }}</span>
                    @endif
                </span>
            </a>
        </li>
        <!-- Nuevo item para Depósitos -->
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('deposits.*') ? 'active' : '' }}"
               href="{{ route('deposits.index') }}">
                <i class="bi bi-currency-exchange"></i>
                <span class="link-text d-flex align-items-center gap-2">
                    <span>Depósitos</span>
                    @php
                        $pendingCount = \App\Models\ExchangeRequest::where('to_user_id', auth()->id())
                            ->where('status', 'pending')
                            ->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge bg-danger">{{ $pendingCount }}</span>
                    @endif
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('reportes.index') ? 'active' : '' }}" href="{{ route('reportes.index') }}">
                <i class="bi bi-graph-up"></i>
                <span class="link-text">Reportes</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('perfil') ? 'active' : '' }}" href="{{ route('perfil') }}">
                <i class="bi bi-person"></i>
                <span class="link-text">Perfil</span>
            </a>
        </li>

        <!-- Enlaces públicos -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('servicios') }}">
                <i class="bi bi-gear"></i>
                <span class="link-text">Servicios</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('contacto') }}">
                <i class="bi bi-envelope"></i>
                <span class="link-text">Contacto</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer p-3">
        <div class="user-profile">
            <i class="bi bi-person-circle"></i>
            <div class="user-details">
                <span class="user-name">{{ Auth::user()->name }}</span>
                <span class="user-role">ID: {{ Auth::user()->user_id ?? Auth::user()->id }}</span>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>
