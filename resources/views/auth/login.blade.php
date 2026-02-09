@extends('layouts.welcome')

@section('title', 'La Comarca - Inicio de Sesión
')

@section('content')
    <div class="welcome-card">
        <div class="logo-icon">
            <img src="{{ asset('images/logo_comarca.png') }}" alt="Logo La Comarca" class="logo-img">
        </div>
        {{-- <h1 class="welcome-title">¡Bienvenido!</h1>
        <p class="welcome-subtitle">Sistema de administración La Comarca Gastro Park</p> --}}

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control"
                    required
                    autofocus
                    autocomplete="username"
                >
                @error('email')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control"
                        required
                        autocomplete="current-password"
                    >
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Mostrar contraseña">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me">Recuérdame</label>
            </div>

            <button type="submit" class="btn-gestionar w-100">
                Iniciar Sesión
            </button>

            <div class="text-center mt-3">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-success fw-semibold">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .logo-img {
       max-width: 319px;
        height: auto;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,.2));
        margin: -105px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    if (!passwordInput || !toggleBtn) return;

    const icon = toggleBtn.querySelector('i');
    toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

        // Toggle icon
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');

        // Update aria label
        toggleBtn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
});
</script>
@endpush
