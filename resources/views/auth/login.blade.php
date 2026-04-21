@extends('layouts.welcome')

@section('title', 'La Comarca - Iniciar Sesión')

@section('content')
<div class="login-container" id="authContainer">

    <div class="login-wrapper" id="authWrapper">
        {{-- PANEL LOGIN --}}
        <div class="login-panel" id="loginPanel">
            <div class="login-header">
                <a href="{{ route('plaza.index') }}" class="logo-link">
                    <img src="{{ asset('images/iconoblanco.png') }}" alt="Logo La Comarca" class="login-logo">
                </a>
            </div>
            <h2 class="login-title">Iniciar sesión</h2>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="sr-only">Correo electrónico</label>
                    <div class="input-icon">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            placeholder="Correo electrónico"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                        >
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="sr-only">Contraseña</label>
                    <div class="password-group">
                        <div class="input-icon">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Contraseña"
                                required
                                autocomplete="current-password"
                                onpaste="return true"
                                oncopy="return true"
                                oncut="return true"
                            >
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <button type="button" id="togglePassword" class="toggle-btn">
                            <i class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-checkbox">
                    <input type="checkbox" id="remember_me" name="remember">
                    <label for="remember_me">Recuérdame</label>
                </div>

                <button type="submit" class="btn-login">Iniciar sesión</button>

                <div class="password-recovery-options">
                    <button type="button" id="switchRecoveryBtn" class="forgot-password" title="Recuperar contraseña">
                         ¿Olvidaste tu contraseña?
                    </button>
                </div>

                <div class="divider-social">
                    <span>O continúa con</span>
                </div>

                <div class="google-container">
                    <a href="{{ route('auth.google') }}" class="btn-google" title="Iniciar sesión con Google">
                        <svg class="google-logo" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Continuar con Google</span>
                    </a>
                </div>

                {{-- ALERTA DE SESIÓN EXPIRADA --}}
                @error('error')
                    <div class="custom-alert custom-alert-danger" style="margin-top: 12px;">
                        <i class="fa-solid fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
               
            </form>

            {{-- <div class="panel-footer">
                ¿No tienes cuenta? 
                <button type="button" class="switch-btn" id="switchRegister">Registrate aquí</button>
            </div> --}}
            
            <!-- Botón para cambiar a registro en responsivo -->
            <div class="responsive-footer" id="loginFooter">
                <p>¿No tienes cuenta? <button type="button" class="switch-btn" id="switchRegisterResponsive">Registrate aquí</button></p>
            </div>

            {{-- ALERTAS DE ÉXITO EN LOGIN (Posicionadas al final para responsive) --}}
            @if (session('recovery-status'))
                <div class="custom-alert custom-alert-success" id="recoveryStatusAlert" style="margin-top: 10px;">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>{{ session('recovery-status') }}</span>
                </div>
            @endif

            {{-- ALERTAS DE ERROR EN LOGIN (Recuperación - Posicionadas al final para responsive) --}}
            @if (session('recovery-error'))
                <div class="custom-alert custom-alert-danger" id="recoveryErrorAlert" style="margin-top: 10px;">
                    <i class="fa-solid fa-times-circle"></i>
                    <span>{{ session('recovery-error') }}</span>
                </div>
            @endif

            @error('email', 'recovery')
                <div class="custom-alert custom-alert-danger" id="recoveryErrorAlert" style="margin-top: 10px;">
                    <i class="fa-solid fa-times-circle"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>
        <div class="register-panel" id="registerPanel">
            <div class="register-header">
                <a href="{{ route('plaza.index') }}" class="logo-link">
                    <img src="{{ asset('images/iconoblanco.png') }}" alt="Logo La Comarca" class="login-logoRegister">
                </a>
            </div>
            <h2 class="login-title">Registrarse como cliente</h2>
            {{-- <p class="register-subtitle">Tu rol será <strong>Cliente</strong> automáticamente</p> --}}

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="is_register" value="1">

                <div class="form-group">
                    <label for="full_name" class="sr-only">Nombre completo</label>
                    <div class="input-icon">
                        <input
                            id="full_name"
                            type="text"
                            name="full_name"
                            placeholder="Nombre completo"
                            value="{{ old('full_name') }}"
                            required
                            autocomplete="name"
                        >
                        <i class="fa-solid fa-user"></i>
                    </div>
                    @error('full_name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="reg_email" class="sr-only">Correo electrónico</label>
                    <div class="input-icon">
                        <input
                            id="reg_email"
                            type="email"
                            name="email"
                            placeholder="Correo electrónico"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="sr-only">Teléfono</label>
                    <div class="input-icon">
                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            placeholder="Teléfono (opcional)"
                            value="{{ old('phone') }}"
                            autocomplete="tel"
                        >
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    @error('phone')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="reg_password" class="sr-only">Contraseña</label>
                    <div class="input-icon">
                        <input
                            id="reg_password"
                            type="password"
                            name="password"
                            placeholder="Contraseña"
                            required
                            autocomplete="new-password"
                        >
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="sr-only">Confirmar contraseña</label>
                    <div class="input-icon">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Confirmar contraseña"
                            required
                            autocomplete="new-password"
                        >
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>

                <button type="submit" class="btn-register">Registrarme</button>

                <div class="divider-social">
                    <span>O continúa con</span>
                </div>

                <div class="google-container">
                    <a href="{{ route('auth.google') }}" class="btn-google" title="Registrarse con Google">
                        <svg class="google-logo" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Registrate con Google</span>
                    </a>
                </div>

                {{-- ALERTA DE SESIÓN EXPIRADA --}}
                @error('error')
                    <div class="custom-alert custom-alert-danger" style="margin-top: 12px;">
                        <i class="fa-solid fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </form>

            {{-- <div class="panel-footer">
                ¿Ya tienes cuenta? 
                <button type="button" class="switch-btn" id="switchLogin">Inicia sesión</button>
            </div> --}}
            
            <!-- Botón para cambiar a login en responsivo -->
            <div class="responsive-footer" id="registerFooter">
                <p>¿Ya tienes cuenta? <button type="button" class="switch-btn" id="switchLoginResponsive">Inicia sesión</button></p>
            </div>
        </div>

        {{-- PANEL RECUPERACIÓN DE CONTRASEÑA --}}
        <div class="recovery-panel" id="recoveryPanel">
            <div class="recovery-header">
                <a href="{{ route('plaza.index') }}" class="logo-link">
                    <img src="{{ asset('images/iconoblanco.png') }}" alt="Logo La Comarca" class="recovery-logo">
                </a>
            </div>
            <h2 class="recovery-title">Recuperar Contraseña</h2>
            <p class="recovery-subtitle">Te enviaremos una contraseña temporal a tu correo</p>

            <form method="POST" action="{{ route('password.recovery') }}" class="recovery-form">
                @csrf

                <div class="form-group">
                    <label for="recovery_email" class="sr-only">Correo electrónico</label>
                    <div class="input-icon">
                        <input
                            id="recovery_email"
                            type="email"
                            name="email"
                            placeholder="Correo electrónico"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            onpaste="return true"
                            oncopy="return true"
                            oncut="return true"
                        >
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    @error('email', 'recovery')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="info-box">
                    <i class="fa-solid fa-info-circle"></i>
                    <span>Recibirás una contraseña temporal en tu correo</span>
                </div>

                <button type="submit" class="btn-recovery">
                    <i class="fa-solid fa-paper-plane"></i>
                    Enviar Contraseña Temporal
                </button>
            </form>

            <!-- Botón para cambiar a login en responsivo -->
            <div class="responsive-footer" id="recoveryFooter">
                <p><button type="button" class="switch-btn" id="switchLoginFromRecoveryResponsive">Volver al login</button></p>
            </div>
        </div>

        {{-- OVERLAY ANIMATED --}}
        <div class="auth-overlay" id="authOverlay">
            {{-- Contenido para LOGIN --}}
            <div class="overlay-content overlay-login">
                <h3 class="overlay-title">¡Hola, Bienvenido!</h3>
                <p class="overlay-text">¿No tienes cuenta?</p>
                <button type="button" class="overlay-btn" id="overlayRegisterBtn">Regístrate aquí</button>
            </div>

            {{-- Contenido para REGISTRO --}}
            <div class="overlay-content overlay-register">
                <h3 class="overlay-title">¡Bienvenido de nuevo!</h3>
                <p class="overlay-text">¿Ya tienes cuenta?</p>
                <button type="button" class="overlay-btn" id="overlayLoginBtn">Iniciar sesión</button>
            </div>

            {{-- Contenido para RECUPERACIÓN --}}
            <div class="overlay-content overlay-recovery">
                <h3 class="overlay-title">¿Olvidaste tu Contraseña?</h3>
                <p class="overlay-text">Te ayudaremos a recuperar tu contraseña</p>
                <button type="button" class="overlay-btn" id="overlayLoginFromRecoveryBtn">Volver al login</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===== CUSTOM ALERT ===== */
    @keyframes slideDownAlert {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .custom-alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        font-size: 14px;
        font-weight: 500;
        position: relative;
        animation: slideDownAlert 0.4s ease-out;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .custom-alert-success {
        background: linear-gradient(135deg, rgba(225, 128, 24, 0.15), rgba(145, 80, 22, 0.1));
        border: 1px solid rgba(225, 128, 24, 0.3);
        color: #fbbf24;
    }

    .custom-alert-success i {
        color: #e18018;
    }

    .custom-alert-danger {
        background: linear-gradient(135deg, rgba(225, 128, 24, 0.15), rgba(145, 80, 22, 0.1));
        border: 1px solid rgba(225, 128, 24, 0.3);
        color: #fbbf24;
    }

    .custom-alert-danger i {
        color: #e18018;
    }

    .custom-alert i {
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .custom-alert span {
        flex: 1;
        line-height: 1.5;
    }

    .custom-alert-close {
        display: none !important;
    }


    .login-container {
        width: 100%;
        max-width: 960px;
        margin: 0 auto;
        padding: 20px;
    }

    .login-wrapper {
        position: relative;
        width: 100%;
        height: 650px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        display: flex;
    }

    /* ===== PANELES ===== */
    .login-panel,
    .register-panel,
    .recovery-panel {
        position: absolute;
        width: 50%;
        height: 100%;
        padding: 40px 35px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: all 0.6s ease;
        z-index: 2;
    }

    .login-panel {
        right: 0;
        background: #0C0C0E;
    }

    .register-panel {
        left: 0;
        background: #0C0C0E;
        transform: translateX(-100%);
    }

    .recovery-panel {
        left: 0;
        background: #0C0C0E;
        transform: translateX(-100%);
    }

    /* Panel visible */
    .login-container.show-register .login-panel {
        transform: translateX(100%);
    }

    .login-container.show-register .register-panel {
        transform: translateX(0);
    }

    .login-container.show-recovery .login-panel {
        transform: translateX(100%);
    }

    .login-container.show-recovery .recovery-panel {
        transform: translateX(0);
    }

    /* ===== OVERLAY ANIMADO ===== */
    .auth-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 50%;
        height: 100%;
        background: linear-gradient(135deg, #e18018, #915016);
        z-index: 3;
        transition: all 0.6s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .overlay-content {
        text-align: center;
        color: #fff;
        padding: 40px;
    }

    .overlay-login {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .overlay-register {
        display: none;
    }

    .overlay-recovery {
        display: none;
    }

    .login-container.show-register .overlay-login {
        display: none;
    }

    .login-container.show-register .overlay-register {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .login-container.show-recovery .overlay-login {
        display: none;
    }

    .login-container.show-recovery .overlay-recovery {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .overlay-title {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .overlay-text {
        font-size: 16px;
        margin-bottom: 25px;
        opacity: 0.95;
    }

    .overlay-btn {
        padding: 12px 32px;
        background: rgba(255, 255, 255, 0.25);
        border: 2px solid #fff;
        border-radius: 25px;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .overlay-btn:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: translateY(-2px);
    }

    .login-container.show-register .auth-overlay {
        transform: translateX(100%);
    }

    .login-container.show-recovery .auth-overlay {
        transform: translateX(100%);
    }

    /* ===== HEADERS ===== */
    .login-header,
    .register-header,
    .recovery-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .login-logo,
    .recovery-logo {
        max-width: 171px;
        height: auto;
        margin: 0;
        margin-top: -2px;
    }

    .login-logoRegister{
        max-width: 171px;
        height: auto;
        margin: 0;
        margin-top: -22px;;
    }

    /* ===== LOGO LINK ===== */
    .logo-link {
        display: inline-block;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .logo-link:hover {
        transform: scale(1.05);
        filter: brightness(1.1);
    }

    .logo-link:active {
        transform: scale(0.98);
    }

    .login-title,
    .recovery-title {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 20px 0 5px;
    }

    .login-panel .login-title {
        color: #fff;
        margin-top: 0;
    }

    .recovery-panel .recovery-title {
        color: #fff;
        margin-top: 0;
    }

    .recovery-subtitle {
        font-size: 14px;
        color: #b0b0b0;
        margin: 18px 0 0;
        padding: 8px;
    }

    .register-panel .login-title {
        color: #fff;
        margin-top: -15px;
    }

    .register-subtitle {
        font-size: 14px;
        color: #666;
        margin: 5px 0 0;
    }

    /* ===== FORMULARIOS ===== */
    .form-group {
        margin-bottom: 10px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #d0d0d0;
        margin-bottom: 8px;
    }

    .form-group label.sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }

    .login-panel .form-group label {
        color: #d0d0d0;
    }

    .register-panel .form-group label {
        color: #d0d0d0;
    }

    /* ===== INPUT CON ICONOS ===== */
    .input-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon i {
        position: absolute;
        right: 14px;
        font-size: 16px;
        color: #888;
        pointer-events: none;
        transition: color 0.3s;
    }

    .login-panel .input-icon i {
        color: #888;
    }

    .input-icon input:focus ~ i {
        color: #e18018;
    }

    .form-group input {
        width: 100%;
        padding: 14px 44px 14px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        font-family: inherit;
        transition: border-color 0.3s;
        letter-spacing: 0.3px;
    }

    .login-panel .form-group input {
        background: #2a2a2a;
        border: 1px solid #444;
        color: #fff;
    }

    .login-panel .form-group input::placeholder {
        color: #a0a0a0;
        opacity: 1;
    }

    .login-panel .form-group input:focus::placeholder {
        color: #888;
    }

    .login-panel .form-group input:focus {
        outline: none;
        border-color: #e18018;
        box-shadow: 0 0 0 3px rgba(225, 128, 24, 0.2);
    }

    .register-panel .form-group input {
        background: #2a2a2a;
        border: 1px solid #444;
        color: #fff;
    }

    .register-panel .form-group input::placeholder {
        color: #a0a0a0;
        opacity: 1;
    }

    .register-panel .form-group input:focus::placeholder {
        color: #888;
    }

    .register-panel .form-group input:focus {
        outline: none;
        border-color: #e18018;
        box-shadow: 0 0 0 3px rgba(225, 128, 24, 0.2);
    }

    .recovery-panel .form-group input {
        background: #2a2a2a;
        border: 1px solid #444;
        color: #fff;
    }

    .recovery-panel .form-group input::placeholder {
        color: #a0a0a0;
        opacity: 1;
    }

    .recovery-panel .form-group input:focus::placeholder {
        color: #888;
    }

    .recovery-panel .form-group input:focus {
        outline: none;
        border-color: #e18018;
        box-shadow: 0 0 0 3px rgba(225, 128, 24, 0.2);
    }

    .form-error {
        display: block;
        color: #ff6b6b;
        font-size: 12px;
        margin-top: 4px;
    }

    .login-panel .form-error {
        color: #ff8888;
    }

    .register-panel .form-error {
        color: #ff8888;
    }

    .password-group {
        position: relative;
        display: flex;
    }

    .password-group .input-icon {
        flex: 1;
        width: 100%;
    }

    .password-group .input-icon input {
        width: 100%;
        padding-right: 60px;
    }

    .toggle-btn {
        position: absolute;
       right: 29px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 16px;
        padding: 8px 12px;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-panel .toggle-btn {
        color: #b0b0b0;
        cursor: pointer;
    }

    .toggle-btn:hover {
        color: #e18018;
    }

    /* ===== CHECKBOX ===== */
    .form-checkbox {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .form-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        cursor: pointer;
    }

    .login-panel .form-checkbox input[type="checkbox"] {
        accent-color: #e18018;
    }

    .register-panel .form-checkbox input[type="checkbox"] {
        accent-color: #e18018;
    }

    .form-checkbox label {
        margin: 0;
        font-size: 13px;
        cursor: pointer;
        color: #d0d0d0;
    }

    .register-panel .form-checkbox label {
        color: #d0d0d0;
    }

    /* ===== BOTONES ===== */
    .btn-login,
    .btn-register,
    .btn-recovery {
        width: 100%;
        padding: 12px;
        margin-bottom: 12px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-login {
        background: linear-gradient(135deg, #e18018, #915016);
        color: #fff;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(225, 128, 24, 0.3);
    }

    .btn-register {
        background: linear-gradient(135deg, #e18018, #915016);
        color: #fff;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(225, 128, 24, 0.3);
    }

    .btn-recovery {
        background: linear-gradient(135deg, #e18018, #915016);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-recovery:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(225, 128, 24, 0.3);
    }

    .info-box {
        background-color: rgba(225, 128, 24, 0.1);
        border-left: 4px solid #e18018;
        padding: 12px 14px;
        border-radius: 6px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #d0d0d0;
    }

    .info-box i {
        color: #e18018;
        flex-shrink: 0;
    }

    .btn-register {
        background: linear-gradient(135deg, #e18018, #915016);
        color: #fff;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(225, 128, 24, 0.3);
    }

    /* ===== DIVIDER SOCIAL ===== */
    .divider-social {
        display: flex;
        align-items: center;
        margin: 12px 0;
        gap: 10px;
    }

    .divider-social::before,
    .divider-social::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #555;
    }

    .divider-social span {
        font-size: 13px;
        color: #a0a0a0;
        font-weight: 500;
    }

    /* ===== GOOGLE BUTTON ===== */
    .google-container {
        display: flex;
        justify-content: center;
        margin: 12px 0;
        width: 100%;
    }

    .btn-google {
       display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 60%;
        padding: 12px 16px;
        background: #d4b59e;
        border: 1px solid #d07d25;
        border-radius: 8px;
        color: #0a0b0c;
        font-size: 15px;
        font-weight: 511;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        pointer-events: auto !important;
        z-index: 10 !important;
        position: relative;
    }

    .btn-google:hover {
        background: #f8f9fa;
        border-color: #c6c6c6;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        transform: translateY(-1px);
    }

    .btn-google:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
    }

    .google-logo {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .btn-google span {
        font-size: 15px;
        font-weight: 500;
        color: #3c4043;
    }

    .forgot-password {
        display: block;
        text-align: center;
        color: #e18018;
        text-decoration: none;
        font-size: 13px;
        margin-top: 12px;
        transition: all 0.3s;
    }

    .forgot-password:hover {
        text-decoration: underline;
        color: #f09030;
    }

    .password-recovery-options {
        display: flex;
        justify-content: center;
        margin: 15px 0;
    }

    .password-recovery-options .forgot-password {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
        background: none;
        border: none;
        padding: 0;
    }

    /* ===== FOOTER (SWITCH) ===== */
    .panel-footer {
        text-align: center;
        color: #666;
        font-size: 13px;
        margin-top: auto;
    }

    .login-panel .panel-footer {
        color: #b0b0b0;
    }

    .register-panel .panel-footer {
        color: #b0b0b0;
    }

    .switch-btn {
        background: none;
        border: none;
        color: #e18018;
        font-weight: 600;
        cursor: pointer;
        font-size: 13px;
        margin-left: 4px;
    }

    .switch-btn:hover {
        text-decoration: underline;
    }

    /* ===== RESPONSIVE FOOTER ===== */
    .responsive-footer {
        display: none;
        text-align: center;
        color: #d0d0d0;
        font-size: 13px;
        margin-top: auto;
        padding-top: 10px;
    }

    .responsive-footer p {
        margin: 0;
    }

    .responsive-footer .switch-btn {
        margin-left: 4px;
    }

    /* ===== ANIMACIÓN RESPONSIVO ===== */
    @keyframes slideOutToLeft {
        0% {
            opacity: 1;
            transform: translateX(0);
        }
        100% {
            opacity: 0;
            transform: translateX(-100%);
        }
    }

    @keyframes slideOutToRight {
        0% {
            opacity: 1;
            transform: translateX(0);
        }
        100% {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    @keyframes slideInFromLeft {
        0% {
            opacity: 0;
            transform: translateX(100%);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInFromRight {
        0% {
            opacity: 0;
            transform: translateX(-100%);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .login-panel.slide-out-right,
    .register-panel.slide-out-right {
        animation: slideOutToRight 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .login-panel.slide-out-left,
    .register-panel.slide-out-left {
        animation: slideOutToLeft 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .login-panel.slide-in-right,
    .register-panel.slide-in-right {
        animation: slideInFromRight 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .login-panel.slide-in-left,
    .register-panel.slide-in-left {
        animation: slideInFromLeft 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    /* Fondo anaranjado durante la transición en responsivo */
    @media (max-width: 900px) {
        .login-wrapper.transitioning {
            background: linear-gradient(135deg, #e18018, #915016);
        }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1200px) {
        .login-wrapper {
            height: 650px;
        }

        .login-panel,
        .register-panel {
            padding: 40px 30px;
        }
    }

    @media (max-width: 900px) {
        .login-container {
            width: 100%;
            max-width: 100%;
        }

        .login-wrapper {
            height: 650px;
            border-radius: 0;
            box-shadow: none;
            display: block;
            overflow: hidden;
            background: #0C0C0E;
        }

        .login-panel,
        .register-panel,
        .recovery-panel {
            width: 100%;
            position: relative;
            padding: 40px 30px;
            min-height: auto;
            transform: none;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
        }

        .auth-overlay {
            display: none;
        }

        .login-panel {
            display: block;
        }

        .register-panel {
            display: none;
        }

        .recovery-panel {
            display: none;
        }

        .login-container.show-register .login-panel {
            display: none;
        }

        .login-container.show-register .register-panel {
            display: block;
        }

        .login-container.show-recovery .login-panel {
            display: none;
        }

        .login-container.show-recovery .recovery-panel {
            display: block;
        }

        .responsive-footer {
            display: block;
        }

        .login-header,
        .register-header,
        .recovery-header {
            margin-bottom: 20px;
        }

        .login-logo,
        .login-logoRegister,
        .recovery-logo {
            max-width: 140px;
            margin-top: 0 !important;
        }

        .login-title {
            font-size: 24px;
            margin-top: 10px !important;
        }

        .form-group input {
            padding: 12px 40px 12px 14px;
            font-size: 16px;
        }

        .btn-login,
        .btn-register,
        .btn-recovery {
            padding: 10px;
            font-size: 15px;
        }
    }

    @media (max-width: 768px) {
        .login-container {
            padding: 15px;
        }

        .login-wrapper {
            border-radius: 15px;
            height: 650px;
        }

        .login-panel,
        .register-panel {
            padding: 35px 20px;
            min-height: auto;
            max-height: none;
            justify-content: flex-start;
            padding-top: 30px;
        }

        .login-header,
        .register-header {
            margin-bottom: 15px;
        }

        .login-logo,
        .login-logoRegister {
            max-width: 120px;
        }

        .login-title {
            font-size: 22px;
            margin: 15px 0 3px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group input {
            padding: 11px 38px 11px 12px;
            font-size: 16px;
            border-radius: 6px;
        }

        .input-icon i {
            right: 12px;
            font-size: 15px;
        }

        .form-checkbox {
            margin-bottom: 15px;
        }

        .form-checkbox label {
            font-size: 12px;
        }

        .btn-login,
        .btn-register {
            padding: 10px;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .divider-social {
            margin: 12px 0;
            gap: 8px;
        }

        .divider-social span {
            font-size: 12px;
        }

        .google-container {
            margin: 12px 0;
        }

        .btn-google {
            padding: 11px 14px;
            font-size: 14px;
            gap: 8px;
        }

        .google-logo {
            width: 18px;
            height: 18px;
        }

        .forgot-password {
            font-size: 12px;
            margin-top: 10px;
        }
    }

    @media (max-width: 600px) {
        .login-container {
            padding: 10px;
        }

        .login-wrapper {
            height: 620px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .login-panel,
        .register-panel {
            padding: 25px 18px;
            min-height: auto;
        }

        .login-header,
        .register-header {
            margin-bottom: 12px;
        }

        .login-logo,
        .login-logoRegister {
            max-width: 100px;
            margin: 0;
        }

        .login-title {
            font-size: 20px;
            margin: 12px 0 0;
        }

        .form-group {
            margin-bottom: 9px;
        }

        .form-group input {
            padding: 10px 36px 10px 11px;
            font-size: 15px;
        }

        .input-icon i {
            right: 10px;
            font-size: 14px;
        }

        .form-checkbox {
            margin-bottom: 12px;
        }

        .form-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }

        .form-checkbox label {
            font-size: 11px;
        }

        .btn-login,
        .btn-register,
        .btn-recovery {
            padding: 9px;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .divider-social {
            margin: 10px 0;
            gap: 6px;
        }

        .divider-social span {
            font-size: 11px;
        }

        .google-container {
            margin: 10px 0;
        }

        .btn-google {
            padding: 10px 12px;
            font-size: 13px;
            gap: 8px;
        }

        .google-logo {
            width: 16px;
            height: 16px;
        }

        .btn-google span {
            font-size: 13px;
        }

        .forgot-password {
            font-size: 11px;
            margin-top: 8px;
        }

        .toggle-btn {
            right: 40px;
            font-size: 14px;
        }

        .form-error {
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 8px;
        }

        .login-wrapper {
            height: 600px;
            border-radius: 10px;
        }

        .login-panel,
        .register-panel,
        .recovery-panel {
            padding: 20px 16px;
        }

        .login-logo,
        .login-logoRegister {
            max-width: 90px;
        }

        .login-title {
            font-size: 18px;
            margin: 11px 2px 31px;
        }

        .form-group input {
            padding: 9px 34px 9px 10px;
            font-size: 14px;
        }

        .btn-login,
        .btn-register,
        .btn-recovery {
            padding: 8px;
            font-size: 12px;
        }

        .btn-google {
            padding: 9px 10px;
            font-size: 12px;
            gap: 6px;
        }

        .google-logo {
            width: 14px;
            height: 14px;
        }

        .btn-google span {
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const authContainer = document.getElementById('authContainer');
    const loginPanel = document.getElementById('loginPanel');
    const registerPanel = document.getElementById('registerPanel');
    const recoveryPanel = document.getElementById('recoveryPanel');
    const loginWrapper = document.getElementById('authWrapper');
    const switchRegisterBtn = document.getElementById('switchRegister');
    const switchLoginBtn = document.getElementById('switchLogin');
    const switchRegisterResponsiveBtn = document.getElementById('switchRegisterResponsive');
    const switchLoginResponsiveBtn = document.getElementById('switchLoginResponsive');
    const switchRecoveryBtn = document.getElementById('switchRecoveryBtn');
    const switchLoginFromRecoveryResponsiveBtn = document.getElementById('switchLoginFromRecoveryResponsive');
    const overlayRegisterBtn = document.getElementById('overlayRegisterBtn');
    const overlayLoginBtn = document.getElementById('overlayLoginBtn');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    const isResponsive = () => window.innerWidth <= 900;

    // Función para cambiar a registro
    const switchToRegister = () => {
        // Remover clase de recuperación primero
        authContainer.classList.remove('show-recovery');
        
        if (isResponsive()) {
            loginWrapper.classList.add('transitioning');
            loginPanel.classList.add('slide-out-left');
            loginPanel.classList.remove('slide-in-right');
            
            setTimeout(() => {
                authContainer.classList.add('show-register');
                registerPanel.classList.add('slide-in-left');
                registerPanel.classList.remove('slide-out-right');
                loginPanel.classList.remove('slide-out-left');
            }, 400);
            
            setTimeout(() => {
                loginWrapper.classList.remove('transitioning');
            }, 800);
        } else {
            authContainer.classList.add('show-register');
        }
        window.scrollTo(0, 0);
    };

    // Función para cambiar a login
    const switchToLogin = () => {
        if (isResponsive()) {
            loginWrapper.classList.add('transitioning');
            registerPanel.classList.add('slide-out-right');
            registerPanel.classList.remove('slide-in-left');
            recoveryPanel.classList.add('slide-out-right');
            recoveryPanel.classList.remove('slide-in-left');
            
            setTimeout(() => {
                authContainer.classList.remove('show-register');
                authContainer.classList.remove('show-recovery');
                loginPanel.classList.add('slide-in-right');
                loginPanel.classList.remove('slide-out-left');
                registerPanel.classList.remove('slide-out-right');
                recoveryPanel.classList.remove('slide-out-right');
            }, 400);
            
            setTimeout(() => {
                loginWrapper.classList.remove('transitioning');
            }, 800);
        } else {
            authContainer.classList.remove('show-register');
            authContainer.classList.remove('show-recovery');
        }
        window.scrollTo(0, 0);
    };

    // Función para cambiar a recuperación
    const switchToRecovery = () => {
        // Remover clase de registro primero
        authContainer.classList.remove('show-register');
        
        if (isResponsive()) {
            loginWrapper.classList.add('transitioning');
            loginPanel.classList.add('slide-out-left');
            loginPanel.classList.remove('slide-in-right');
            
            setTimeout(() => {
                authContainer.classList.add('show-recovery');
                recoveryPanel.classList.add('slide-in-left');
                recoveryPanel.classList.remove('slide-out-right');
                loginPanel.classList.remove('slide-out-left');
            }, 400);
            
            setTimeout(() => {
                loginWrapper.classList.remove('transitioning');
            }, 800);
        } else {
            authContainer.classList.add('show-recovery');
        }
        window.scrollTo(0, 0);
    };

    // Mostrar registro si viene de la ruta /register o hay error
    const shouldShowRegister = {{ (old('is_register') || (isset($auth_mode) && $auth_mode === 'register')) ? 'true' : 'false' }};
    if (shouldShowRegister) {
        authContainer.classList.add('show-register');
    }

    // Switch a registro desde botón en panel login (desktop)
    if (switchRegisterBtn) {
        switchRegisterBtn.addEventListener('click', switchToRegister);
    }

    // Switch a registro desde botón en responsivo
    if (switchRegisterResponsiveBtn) {
        switchRegisterResponsiveBtn.addEventListener('click', switchToRegister);
    }

    // Switch a registro desde botón en overlay
    if (overlayRegisterBtn) {
        overlayRegisterBtn.addEventListener('click', switchToRegister);
    }

    // Switch a login desde botón en panel registro (desktop)
    if (switchLoginBtn) {
        switchLoginBtn.addEventListener('click', switchToLogin);
    }

    // Switch a login desde botón en responsivo
    if (switchLoginResponsiveBtn) {
        switchLoginResponsiveBtn.addEventListener('click', switchToLogin);
    }

    // Switch a login desde botón en overlay
    if (overlayLoginBtn) {
        overlayLoginBtn.addEventListener('click', switchToLogin);
    }

    // Switch a login desde botón en overlay de recuperación
    const overlayLoginFromRecoveryBtn = document.getElementById('overlayLoginFromRecoveryBtn');
    if (overlayLoginFromRecoveryBtn) {
        overlayLoginFromRecoveryBtn.addEventListener('click', switchToLogin);
    }

    // Switch a recuperación de contraseña desde botón en panel login
    if (switchRecoveryBtn) {
        switchRecoveryBtn.addEventListener('click', (e) => {
            e.preventDefault();
            switchToRecovery();
        });
    }

    // Switch a login desde panel de recuperación en responsivo
    if (switchLoginFromRecoveryResponsiveBtn) {
        switchLoginFromRecoveryResponsiveBtn.addEventListener('click', switchToLogin);
    }

    // Toggle contraseña
    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = togglePasswordBtn.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }

    // Ocultar alerta de recuperación de contraseña (éxito)
    const recoveryStatusAlert = document.getElementById('recoveryStatusAlert');
    if (recoveryStatusAlert) {
        setTimeout(() => {
            recoveryStatusAlert.style.transition = 'opacity 0.5s ease';
            recoveryStatusAlert.style.opacity = '0';
            setTimeout(() => {
                recoveryStatusAlert.style.display = 'none';
            }, 500);
        }, 7000);
    }

    // Ocultar alerta de recuperación de contraseña (error)
    recoveryErrorAlert = document.getElementById('recoveryErrorAlert');
    if (recoveryErrorAlert) {
        setTimeout(() => {
            recoveryErrorAlert.style.transition = 'opacity 0.5s ease';
            recoveryErrorAlert.style.opacity = '0';
            setTimeout(() => {
                recoveryErrorAlert.style.display = 'none';
            }, 500);
        }, 7000);
    }

    // ===== MOSTRAR ALERTA DE SESIÓN EXPIRADA =====
    const sessionExpiredAlerts = document.querySelectorAll('.custom-alert-danger');
    
    sessionExpiredAlerts.forEach(alert => {
        // Si hay una alerta de sesión expirada, animarla
        if (alert.textContent.includes('sesión')) {
            alert.style.animation = 'slideDown 0.4s ease-out';
        }
    });
});

// ===== ANIMACIÓN DE SLIDE DOWN PARA ALERTA =====
const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush
