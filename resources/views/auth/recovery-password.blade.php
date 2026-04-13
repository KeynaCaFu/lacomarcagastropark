@extends('layouts.welcome')

@section('title', 'La Comarca - Recuperar Contraseña')

@section('content')
<div class="recovery-container">
    <div class="recovery-card">
        <div class="recovery-header">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="recovery-logo">
            </div>
            <h2 class="recovery-title">Recuperar Contraseña</h2>
            <p class="recovery-subtitle">Te enviaremos una contraseña temporal a tu correo</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.recovery') }}" class="recovery-form">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-icon">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        placeholder="tu@email.com"
                    >
                    <i class="fas fa-envelope"></i>
                </div>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <span>Ingresa el correo asociado a tu cuenta para recibir una contraseña temporal</span>
            </div>

            <button type="submit" class="btn-recovery">
                <i class="fas fa-paper-plane"></i>
                Enviar Contraseña Temporal
            </button>

            <div class="recovery-footer">
                <p>¿Recuerdas tu contraseña? <a href="{{ route('login') }}" class="link-login">Volver al login</a></p>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .recovery-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #0C0C0E 0%, #1a1a1e 100%);
        padding: 20px;
    }

    .recovery-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 450px;
        padding: 40px;
        animation: slideInUp 0.5s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .recovery-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .logo-container {
        margin-bottom: 20px;
    }

    .recovery-logo {
        max-width: 120px;
        height: auto;
    }

    .recovery-title {
        font-size: 28px;
        font-weight: 700;
        color: #0C0C0E;
        margin: 0 0 10px;
    }

    .recovery-subtitle {
        font-size: 14px;
        color: #666;
        margin: 0;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #0C0C0E;
        margin-bottom: 8px;
    }

    .input-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .form-control {
        width: 100%;
        padding: 12px 40px 12px 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #e18018;
        box-shadow: 0 0 0 3px rgba(225, 128, 24, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ff6b6b;
    }

    .input-icon i {
        position: absolute;
        right: 12px;
        color: #999;
        font-size: 16px;
        pointer-events: none;
    }

    .form-error {
        display: block;
        color: #ff6b6b;
        font-size: 12px;
        margin-top: 4px;
    }

    .info-box {
        background-color: #f0f4ff;
        border-left: 4px solid #e18018;
        padding: 12px 14px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #555;
    }

    .info-box i {
        color: #e18018;
        flex-shrink: 0;
    }

    .btn-recovery {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #e18018, #915016);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-recovery:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(225, 128, 24, 0.3);
    }

    .btn-recovery:active {
        transform: translateY(0);
    }

    .recovery-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .recovery-footer p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }

    .link-login {
        color: #e18018;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
    }

    .link-login:hover {
        color: #915016;
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .recovery-card {
            padding: 30px 20px;
        }

        .recovery-title {
            font-size: 24px;
        }

        .recovery-logo {
            max-width: 100px;
        }

        .btn-recovery {
            font-size: 14px;
        }
    }
</style>
@endpush
