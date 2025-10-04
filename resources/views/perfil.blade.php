@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>Configuración de Perfil</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
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
                            <input type="email" class="form-control"
                                   id="email" name="email"
                                   value="{{ $user->email }}"
                                   disabled>
                            <small class="text-muted">El correo electrónico no se puede cambiar</small>
                        </div>
                        <button type="submit" class="btn btn-primary" @if($user->name_updated_at && $user->name_updated_at->diffInDays(now()) < 30) disabled @endif>
                            Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
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
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="dark_mode" name="dark_mode"
                                   {{ $user->dark_mode ? 'checked' : '' }}>
                            <label class="form-check-label" for="dark_mode">Modo Oscuro</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/modules/profile.js') }}"></script>
@endsection
