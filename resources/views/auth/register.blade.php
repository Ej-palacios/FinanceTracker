@extends('layouts.guest')
@section('title', 'Crear Cuenta')

@section('content')
<div class="auth-container">
    <form class="modern-form" method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="form-title">Crear Cuenta</div>

        <div class="form-body">
            <!-- Campo Nombre -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg fill="none" viewBox="0 0 24 24" class="input-icon">
                        <circle stroke-width="1.5" stroke="currentColor" r="4" cy="8" cx="12"></circle>
                        <path stroke-linecap="round" stroke-width="1.5" stroke="currentColor" d="M5 20C5 17.2386 8.13401 15 12 15C15.866 15 19 17.2386 19 20"></path>
                    </svg>
                    <input id="name" name="name" type="text" class="form-input" placeholder="Juan Pérez" required autofocus>
                </div>
            </div>

            <!-- Campo Email -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg fill="none" viewBox="0 0 24 24" class="input-icon">
                        <path stroke-width="1.5" stroke="currentColor" d="M3 8L10.8906 13.2604C11.5624 13.7083 12.4376 13.7083 13.1094 13.2604L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z"></path>
                    </svg>
                    <input id="email" name="email" type="email" class="form-input" placeholder="ejemplo@correo.com" required>
                </div>
            </div>

            <!-- Campo Contraseña -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg fill="none" viewBox="0 0 24 24" class="input-icon">
                        <path stroke-width="1.5" stroke="currentColor" d="M12 10V14M8 6H16C17.1046 6 18 6.89543 18 8V16C18 17.1046 17.1046 18 16 18H8C6.89543 18 6 17.1046 6 16V8C6 6.89543 6.89543 6 8 6Z"></path>
                    </svg>
                    <input id="password" name="password" type="password" class="form-input" placeholder="••••••••" required>
                    <button class="password-toggle" type="button" aria-label="Mostrar contraseña">
                        <svg fill="none" viewBox="0 0 24 24" class="eye-icon">
                            <path stroke-width="1.5" stroke="currentColor" d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z"></path>
                            <circle stroke-width="1.5" stroke="currentColor" r="3" cy="12" cx="12"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Campo Confirmar Contraseña -->
            <div class="input-group">
                <div class="input-wrapper">
                    <svg fill="none" viewBox="0 0 24 24" class="input-icon">
                        <path stroke-width="1.5" stroke="currentColor" d="M12 10V14M8 6H16C17.1046 6 18 6.89543 18 8V16C18 17.1046 17.1046 18 16 18H8C6.89543 18 6 17.1046 6 16V8C6 6.89543 6.89543 6 8 6Z"></path>
                    </svg>
                    <input id="password-confirm" name="password_confirmation" type="password" class="form-input" placeholder="••••••••" required>
                </div>
            </div>
        </div>

        <button class="submit-button" type="submit">
            <span class="button-text">Registrar</span>
            <div class="button-glow"></div>
        </button>
        <div class="mb-3">
    <label for="currency" class="form-label">Moneda Principal</label>
    <select class="form-select" id="currency" name="currency" required>
        <option value="">Seleccionar moneda...</option>
        <option value="NIO">Córdoba Nicaragüense (NIO)</option>
        <option value="USD">Dólar Americano (USD)</option>
        <option value="EUR">Euro (EUR)</option>
    </select>
</div>

        <div class="form-footer">
            <a class="login-link" href="{{ route('login') }}">
                ¿Ya tienes cuenta? <span>Inicia sesión aquí</span>
            </a>
        </div>
    </form>
</div>
@endsection