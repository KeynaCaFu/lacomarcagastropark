@extends('layouts.welcome')

@section('title', 'La Comarca - Iniciar Sesión')

@section('content')
<div class="login-container" id="authContainer">
    <div class="login-wrapper" id="authWrapper">
        {{-- PANEL LOGIN --}}
        <div class="login-panel" id="loginPanel">
            <div class="login-header">
                <img src="{{ asset('images/iconoblanco.png') }}" alt="Logo La Comarca" class="login-logo">
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

                 @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">¿Olvidaste tu contraseña?</a>
                @endif

                <div class="divider-social">
                    <span>O continúa con</span>
                </div>

                <div class="google-container">
                    <a href="{{ route('auth.google') }}" class="btn-google" title="Iniciar sesión con Google">
                        <i class="fa-brands fa-google"></i>
                    </a>
                </div>

               
            </form>

            {{-- <div class="panel-footer">
                ¿No tienes cuenta? 
                <button type="button" class="switch-btn" id="switchRegister">Registrate aquí</button>
            </div> --}}
        </div>

        {{-- PANEL REGISTRO --}}
        <div class="register-panel" id="registerPanel">
            <div class="register-header">
                <img src="{{ asset('images/iconoblanco.png') }}" alt="Logo La Comarca" class="login-logoRegister">
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
            </form>

            {{-- <div class="panel-footer">
                ¿Ya tienes cuenta? 
                <button type="button" class="switch-btn" id="switchLogin">Inicia sesión</button>
            </div> --}}
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
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===== CONTENEDOR PRINCIPAL ===== */
    .login-container {
        width: 100%;
        max-width: 960px;
        margin: 0 auto;
        padding: 20px;
    }

    .login-wrapper {
        position: relative;
        width: 100%;
        height: 600px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        display: flex;
    }

    /* ===== PANELES ===== */
    .login-panel,
    .register-panel {
        position: absolute;
        width: 50%;
        height: 100%;
        padding: 50px 40px;
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

    /* Panel visible */
    .login-container.show-register .login-panel {
        transform: translateX(100%);
    }

    .login-container.show-register .register-panel {
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

    .login-container.show-register .overlay-login {
        display: none;
    }

    .login-container.show-register .overlay-register {
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

    /* ===== HEADERS ===== */
    .login-header,
    .register-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-logo {
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
    .login-title {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 20px 0 5px;
    }

    .login-panel .login-title {
        color: #fff;
        margin-top: -19px;
    }

    .register-panel .login-title {
        color: #fff;
        margin-top: -41px;
    }

    .register-subtitle {
        font-size: 14px;
        color: #666;
        margin: 5px 0 0;
    }

    /* ===== FORMULARIOS ===== */
    .form-group {
        margin-bottom: 12px;
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
        right: 44px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 16px;
        padding: 4px;
        z-index: 10;
    }

    .login-panel .toggle-btn {
        color: #b0b0b0;
    }

    .toggle-btn:hover {
        color: #e18018;
    }

    /* ===== CHECKBOX ===== */
    .form-checkbox {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
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
    .btn-register {
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

    /* ===== DIVIDER SOCIAL ===== */
    .divider-social {
        display: flex;
        align-items: center;
        margin: 16px 0;
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
        margin: 16px 0;
    }

    .btn-google {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        background: #ffffff24;
        border: 2px solid #b96317;
        border-radius: 8px;
        color: #a45f22;
        font-size: 24px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-google:hover {
        background: #ffffff24;
        border-color: #a45f22;
        box-shadow: 0 4px 12px rgba(244, 191, 66, 0.2);
        transform: translateY(-2px);
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

    /* ===== RESPONSIVE ===== */
    @media (max-width: 900px) {
        .login-wrapper {
            height: auto;
            min-height: 600px;
        }

        .login-panel,
        .register-panel {
            width: 100%;
            position: relative;
            padding: 40px 30px;
        }

        .auth-overlay {
            display: none;
        }

        .login-panel {
            transform: translateX(0);
            right: auto;
        }

        .register-panel {
            transform: translateX(-100%);
            left: auto;
        }

        .login-container.show-register .login-panel {
            transform: translateX(100%);
        }

        .login-container.show-register .register-panel {
            transform: translateX(0);
        }
    }

    @media (max-width: 600px) {
        .login-container {
            padding: 10px;
        }

        .login-wrapper {
            min-height: 500px;
            border-radius: 15px;
        }

        .login-panel,
        .register-panel {
            padding: 30px 20px;
        }

        .login-title {
            font-size: 24px;
        }

        .login-logo {
            max-width: 100px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const authContainer = document.getElementById('authContainer');
    const switchRegisterBtn = document.getElementById('switchRegister');
    const switchLoginBtn = document.getElementById('switchLogin');
    const overlayRegisterBtn = document.getElementById('overlayRegisterBtn');
    const overlayLoginBtn = document.getElementById('overlayLoginBtn');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Mostrar registro si viene de la ruta /register o hay error
    const shouldShowRegister = {{ (old('is_register') || (isset($auth_mode) && $auth_mode === 'register')) ? 'true' : 'false' }};
    if (shouldShowRegister) {
        authContainer.classList.add('show-register');
    }

    // Switch a registro desde botón en panel login
    if (switchRegisterBtn) {
        switchRegisterBtn.addEventListener('click', () => {
            authContainer.classList.add('show-register');
        });
    }

    // Switch a registro desde botón en overlay
    if (overlayRegisterBtn) {
        overlayRegisterBtn.addEventListener('click', () => {
            authContainer.classList.add('show-register');
        });
    }

    // Switch a login desde botón en panel registro
    if (switchLoginBtn) {
        switchLoginBtn.addEventListener('click', () => {
            authContainer.classList.remove('show-register');
        });
    }

    // Switch a login desde botón en overlay
    if (overlayLoginBtn) {
        overlayLoginBtn.addEventListener('click', () => {
            authContainer.classList.remove('show-register');
        });
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
});
</script>
@endpush
