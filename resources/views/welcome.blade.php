@extends('layouts.welcome')

@section('title', 'La Comarca - Panel de Control
')

@section('content')

@if(session('logged_out'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let retries = 0;
        const checkAndShowLogout = () => {
            if (window.swToast) {
                swToast.fire({
                    icon: 'success',
                    title: '{{ session('logged_out') }}',
                    timer: 3000,
                    timerProgressBar: true,
                    position: 'top-end'
                });
            } else if (retries < 50) {
                retries++;
                setTimeout(checkAndShowLogout, 100);
            }
        };
        setTimeout(checkAndShowLogout, 100);
    });
</script>
@endif
<style>
    .welcome-card{
        max-width: 640px; /* antes 760px */
        margin: clamp(16px, 6vh, 48px) auto; /* menos margen vertical */
        padding: clamp(16px, 3vw, 28px); /* menos relleno interno */
        background: #ffffffde;
        border-radius: 16px; /* ligeramente menor */
        box-shadow: 0 8px 24px rgba(0,0,0,.07);
        text-align: center;
    }
   
    .logo-icon{ margin-bottom: 6px; }
    .logo-image{
        width: min(240px, 60%); /* más pequeño */
        height: auto;
        filter: drop-shadow(0 6px 16px rgba(0,0,0,.15));
        transition: transform .25s ease;
    }
    .logo-image:hover{ transform: scale(1.02); }
    .welcome-title{
        font-size: clamp(24px, 3.2vw, 36px); /* más compacto */
        margin: -40px 0 6px; /* menos superposición y separación */
        font-weight: 800;
        letter-spacing: .2px;
        color: #1f2937;
    }
    .welcome-subtitle{
        color: #4b5563;
        font-size: clamp(13px, 1.6vw, 16px);
        margin: 0 auto 18px; /* menos espacio inferior */
        line-height: 1.55;
    }

    /* Estilos del formulario de login */
    /* Integrar el formulario dentro del mismo contenedor (welcome-card) */
    .login-form-container {
        background: transparent; /* sin fondo propio */
        padding: 0;              /* sin padding extra */
        border-radius: 0;        /* sin bordes redondeados propios */
        margin-top: 16px;        /* separación mínima dentro del card */
        border: none;            /* sin borde propio */
        max-width: 100%;         /* ocupa el ancho del card */
        margin-left: 0;
        margin-right: 0;
    }

    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px; /* antes 6px */
        font-size: 13px; /* antes 14px */
    }

    .form-input {
        width: 100%;
        padding: 10px 12px; /* antes 12px 14px */
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px; /* antes 14px */
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        font-family: inherit;
        box-sizing: border-box;
    }
    .password-wrapper{
        position: relative;
    }
    .toggle-password{
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
    }
    .toggle-password:hover{ color: #16a34a; }

    .form-input:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }

    .error-message {
        color: #dc2626;
        font-size: 13px;
        margin-top: 4px;
    }

    .remember-group {
        display: flex;
        align-items: center;
        margin-bottom: 16px; /* antes 24px */
        justify-content: space-between;
    }

    .remember-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #16a34a;
    }

    .remember-label {
        font-size: 14px;
        color: #6b7280;
        cursor: pointer;
    }

    .login-button {
        width: 100%;
        padding: 10px; /* antes 12px */
        background: linear-gradient(135deg, #485a1a, #0d5e2a);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px; /* antes 15px */
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(22, 163, 74, 0.25);
        filter: saturate(1.05);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .feature-icons{
        display: flex;
        justify-content: center;
        gap: clamp(16px, 3vw, 28px);
        margin-top: 26px;
        color: #6b7280;
    }
    .feature-icons i{
        font-size: clamp(20px, 3vw, 26px);
        padding: 10px;
        border-radius: 10px;
        background: #f3f4f6;
        transition: background .18s ease, color .18s ease;
    }
    .feature-icons i:hover{
        background: #e5f7ed;
        color: #16a34a;
    }

    .forgot-password {
      text-align: right;
        margin-top: 15px;
    }

    .forgot-password a {
        color: #16a34a;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
    }

    .forgot-password a:hover {
        text-decoration: underline;
    }

    @auth
    .login-form-container {
        display: none;
    }
    @endauth

    .auth-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .btn-gestionar{
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        border-radius: 12px;
        background: linear-gradient(135deg, #485a1a, #0d5e2a);
        color: #ffffff !important;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(22, 163, 74, .25);
        transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }
    .btn-gestionar:hover{
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(22, 163, 74, .30);
        filter: saturate(1.05);
    }
    @auth
    .auth-buttons {
        display: flex;
    }
    @endauth

    @guest
    .auth-buttons {
        display: none;
    }
    @endguest
</style>

<div class="welcome-card">
    <div class="logo-icon">
        <img src="{{ asset('images/logo_comarca.png') }}" alt="Logo La Comarca" class="logo-image" loading="lazy">
    </div>

    <h1 class="welcome-title">¡Bienvenido!</h1>
    
    <p class="welcome-subtitle">
        Sistema de administración <strong>La Comarca Gastro Park</strong><br>
    </p>

    <!-- Botones de usuario autenticado -->
    <div class="auth-buttons">
        <a href="{{ route('dashboard') }}" class="btn-gestionar" title="Ir al panel de control">
            <i class="fas fa-tachometer-alt"></i>
            Panel de Control
        </a>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-gestionar" style="background:linear-gradient(135deg,#7c2d12,#c2410c);" title="Cerrar sesión">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar Sesión
            </button>
        </form>
    </div>

    <!-- Formulario de login para usuarios no autenticados -->
    <div class="login-form-container">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="correo@ejemplo.com" />
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                                <div class="password-wrapper">
                                    <input id="password" type="password" name="password" class="form-input" required autocomplete="current-password" placeholder="••••••••" />
                                    <button type="button" class="toggle-password" aria-label="Mostrar u ocultar contraseña" onclick="(function(){const i=document.getElementById('password'); const b=event.currentTarget; const icon=b.querySelector('i'); const is=i.type==='password'; i.type=is?'text':'password'; icon.classList.toggle('fa-eye', !is); icon.classList.toggle('fa-eye-slash', is); })()">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="login-button">
                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                Iniciar Sesión
            </button>

             <!-- Remember Me y Forgot Password -->
            <div class="remember-group">
               
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">¿Olvidaste tu contraseña?</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection