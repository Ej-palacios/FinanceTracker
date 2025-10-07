@extends('layouts.app')

@section('title', 'Perfil')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endsection

@section('content')
<div class="container-fluid profile-page">
    <div class="row align-items-end mb-4">
        <div class="col-12">
            <h1 class="page-title">Configuración de Perfil</h1>
            <p class="page-subtitle">Administra tu información personal, preferencias y ahorros</p>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        <div class="col-12 col-lg-4 left-col">
            <!-- Información Personal -->
            <div class="card profile-card mb-3">
                <div class="card-header">
                    <div class="card-icon"><i class="bi bi-person"></i></div>
                    <h5 class="card-title">Información Personal</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   @if($user->name_updated_at && $user->name_updated_at->diffInDays(now()) < 30) disabled @endif>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($user->name_updated_at && $user->name_updated_at->diffInDays(now()) < 30)
                                <small class="text-muted">Podrás cambiar tu nombre nuevamente en {{ 30 - $user->name_updated_at->diffInDays(now()) }} días</small>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary"
                                @if($user->name_updated_at && $user->name_updated_at->diffInDays(now()) < 30) disabled @endif>
                            Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>

            <!-- Preferencias -->
            <div class="card profile-card">
                <div class="card-header">
                    <div class="card-icon"><i class="bi bi-sliders"></i></div>
                    <h5 class="card-title">Preferencias</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('perfil.preferences.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="currency" class="form-label">Moneda Principal</label>
                            <select class="form-select @error('currency') is-invalid @enderror"
                                    id="currency" name="currency" required>
                                <option value="NIO" {{ $user->currency === 'NIO' ? 'selected' : '' }}>Córdobas (NIO)</option>
                                <option value="USD" {{ $user->currency === 'USD' ? 'selected' : '' }}>Dólares (USD)</option>
                                <option value="EUR" {{ $user->currency === 'EUR' ? 'selected' : '' }}>Euros (EUR)</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="date_format" class="form-label">Formato de Fecha</label>
                            <select class="form-select @error('date_format') is-invalid @enderror"
                                    id="date_format" name="date_format" required>
                                <option value="d/m/Y" {{ $user->date_format === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2023)</option>
                                <option value="m/d/Y" {{ $user->date_format === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2023)</option>
                                <option value="Y-m-d" {{ $user->date_format === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2023-12-31)</option>
                            </select>
                            @error('date_format')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="dark_mode" name="dark_mode"
                                   {{ $user->dark_mode ? 'checked' : '' }}>
                            <label class="form-check-label" for="dark_mode">Modo Oscuro</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="notifications" name="notifications"
                                   {{ $user->notifications ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifications">Recibir Notificaciones</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="row g-3 g-md-4">
                <!-- Saldo Disponible -->
                <div class="col-12 col-md-6">
                    <div class="card profile-card metric-card success">
                        <div class="card-header">
                            <div class="card-icon"><i class="bi bi-wallet2"></i></div>
                            <h5 class="card-title">{{ $user->name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label metric-label">Saldo Disponible</label>
                                <p class="metric-value">{{ $currencySymbol }}{{ number_format($mainBalanceConverted, 2) }}</p>
                            </div>
                            <button type="button" class="btn btn-success" id="btnOpenAgregarAhorros" data-bs-toggle="modal" data-bs-target="#modalAgregarAhorros">
                                Agregar Ahorros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Ahorros -->
                <div class="col-12 col-md-6">
                    <div class="card profile-card metric-card warning">
                        <div class="card-header">
                            <div class="card-icon"><i class="bi bi-piggy-bank"></i></div>
                            <h5 class="card-title">Ahorros</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label metric-label">Saldo Ahorrado</label>
                                <p class="metric-value">{{ $currencySymbol }}{{ number_format($savingsBalanceConverted, 2) }}</p>
                            </div>
                            @if($savingsAccount->balance > 0)
                                <button type="button" class="btn btn-warning" id="btnOpenLiberarAhorros" data-bs-toggle="modal" data-bs-target="#modalLiberarAhorros">
                                    Liberar Ahorros
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Liberar Ahorros -->
    <div class="modal fade" id="modalLiberarAhorros" tabindex="-1" aria-labelledby="modalLiberarAhorrosLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: var(--bg-card); color: var(--text-primary);">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLiberarAhorrosLabel">Confirmar Liberar Ahorros</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('perfil.releaseSavings') }}" method="POST" id="formLiberarAhorros">
                        @csrf
                        <div class="mb-3">
                            <label for="release_amount" class="form-label">Monto a liberar</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $savingsBalanceConverted }}" class="form-control" id="release_amount" name="amount" required>
                        </div>
                        <p>¿Estás seguro que deseas liberar este monto de ahorros para que estén disponibles en tu cuenta principal?</p>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-warning" id="btnConfirmLiberar">Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Ahorros -->
    <div class="modal fade" id="modalAgregarAhorros" tabindex="-1" aria-labelledby="modalAgregarAhorrosLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: var(--bg-card); color: var(--text-primary);">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarAhorrosLabel">Agregar Ahorros</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('perfil.addSavings') }}" method="POST" id="formAgregarAhorros">
                        @csrf
                        <div class="mb-3">
                            <label for="add_amount" class="form-label">Monto a ahorrar</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $mainBalanceConverted }}" class="form-control" id="add_amount" name="amount" required>
                        </div>
                        <p>¿Deseas agregar este monto a tus ahorros desde tu cuenta principal?</p>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="btnConfirmAgregar">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/modules/profile.js') }}"></script>
<script>
(function() {
  const agregarModalEl = document.getElementById('modalAgregarAhorros');
  const liberarModalEl = document.getElementById('modalLiberarAhorros');
  let agregarModal = null;
  let liberarModal = null;

  const btnOpenAgregar = document.getElementById('btnOpenAgregarAhorros');
  const btnOpenLiberar = document.getElementById('btnOpenLiberarAhorros');
  const btnConfirmAgregar = document.getElementById('btnConfirmAgregar');
  const btnConfirmLiberar = document.getElementById('btnConfirmLiberar');

  const formAgregar = document.getElementById('formAgregarAhorros');
  const formLiberar = document.getElementById('formLiberarAhorros');

  function showPreWarning(message) {
    if (window.showWarning) {
      window.showWarning(message, 'Confirmación requerida', 4000);
    }
  }

  function confirmAction(message) {
    if (window.showConfirm) {
      return window.showConfirm(message, '¿Confirmar acción?');
    }
    return Promise.resolve(window.confirm(message));
  }

  if (btnOpenAgregar) {
    btnOpenAgregar.addEventListener('click', () => {
      showPreWarning('Por favor confirma antes de agregar ahorros.');
      if (!agregarModal && agregarModalEl && window.bootstrap) {
        agregarModal = new bootstrap.Modal(agregarModalEl);
      }
      if (agregarModal) agregarModal.show();
    });
  }

  if (btnOpenLiberar) {
    btnOpenLiberar.addEventListener('click', () => {
      showPreWarning('Por favor confirma antes de liberar ahorros.');
      if (!liberarModal && liberarModalEl && window.bootstrap) {
        liberarModal = new bootstrap.Modal(liberarModalEl);
      }
      if (liberarModal) liberarModal.show();
    });
  }

  if (btnConfirmAgregar && formAgregar) {
    btnConfirmAgregar.addEventListener('click', () => {
      confirmAction('¿Deseas agregar este monto a tus ahorros desde tu cuenta principal?')
        .then(accepted => { if (accepted) formAgregar.submit(); });
    });
  }

  if (btnConfirmLiberar && formLiberar) {
    btnConfirmLiberar.addEventListener('click', () => {
      confirmAction('¿Estás seguro que deseas liberar este monto de ahorros para que estén disponibles en tu cuenta principal?')
        .then(accepted => { if (accepted) formLiberar.submit(); });
    });
  }
})();
</script>
@endsection
