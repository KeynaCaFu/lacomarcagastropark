@extends('layouts.welcome')

@section('title', 'La Comarca - Cambiar Contraseña Temporal')

@section('content')
<div class="change-temp-password-container">
    <div class="change-temp-password-card">
        <div class="change-temp-password-header">
            <div class="change-temp-password-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="change-temp-password-title">Cambiar Contraseña Temporal</h1>
            <p class="change-temp-password-subtitle">Has iniciado sesión con una contraseña temporal. Debes cambiarla ahora por una contraseña permanente.</p>
        </div>

        <form method="POST" action="{{ route('client.password.update-temporary') }}" class="change-temp-password-form">
            @csrf
            @method('PUT')

            <!-- Contraseña Temporal -->
            <div class="form-group">
                <label for="temporary_password">Contraseña Temporal</label>
                <div class="input-wrapper">
                    <input 
                        type="text" 
                        id="temporary_password" 
                        name="temporary_password" 
                        placeholder="Pega la contraseña que recibiste por correo" 
                        required 
                        class="@error('temporary_password', 'temporaryPassword') is-invalid @enderror"
                    >
                    <i class="fa-solid fa-key"></i>
                </div>
                @error('temporary_password', 'temporaryPassword')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nueva Contraseña -->
            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ingresa tu nueva contraseña" 
                        required 
                        class="@error('password', 'temporaryPassword') is-invalid @enderror"
                    >
                    <button type="button" class="password-toggle" onclick="togglePasswordField('password')">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
                @error('password', 'temporaryPassword')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Confirma tu nueva contraseña" 
                        required 
                        class="@error('password_confirmation', 'temporaryPassword') is-invalid @enderror"
                    >
                    <button type="button" class="password-toggle" onclick="togglePasswordField('password_confirmation')">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
                @error('password_confirmation', 'temporaryPassword')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Indicador de coincidencia -->
            <div id="passwordMatchFeedback" class="password-match-feedback">
                <i class="fas fa-check-circle"></i>
                <span id="matchText">Las contraseñas coinciden</span>
            </div>

            <!-- Botón Submit -->
            <button type="submit" class="btn-submit">Cambiar Contraseña</button>
        </form>

        <div class="info-box">
            <i class="fa-solid fa-info-circle"></i>
            <p>Tu contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números.</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --primary: #D47744;
        --primary-glow: rgba(212, 119, 58, 0.2);
        --bg-dark: #161310;
        --bg-light: #2D2623;
        --text: #F5F0E8;
        --muted: #A89968;
        --border-light: #3D3430;
    }

    .change-temp-password-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: var(--bg-dark);
        padding: 20px;
    }

    .change-temp-password-card {
        background: var(--bg-light);
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
        max-width: 550px;
        width: 100%;
        padding: 60px;
        border: 1px solid var(--border-light);
    }

    .change-temp-password-header {
        text-align: center;
        margin-bottom: 45px;
    }

    .change-temp-password-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary), #a5573a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: #fff;
        margin: 0 auto 20px;
    }

    .change-temp-password-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 15px;
    }

    .change-temp-password-subtitle {
        font-size: 16px;
        color: var(--muted);
        margin: 0;
        line-height: 1.5;
    }

    .change-temp-password-form {
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .form-group input {
        width: 100%;
        padding: 16px 50px 16px 16px;
        border: 2px solid var(--border-light);
        border-radius: 10px;
        font-size: 15px;
        font-family: inherit;
        background: var(--bg-dark);
        color: var(--text);
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-group input::placeholder {
        color: var(--muted);
        opacity: 0.6;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-glow);
    }

    .form-group input.is-invalid {
        border-color: #ff6b6b;
    }

    .form-group input.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.15);
    }

    .input-wrapper i {
        position: absolute;
        right: 16px;
        font-size: 18px;
        color: var(--muted);
        pointer-events: none;
    }

    .password-toggle {
        position: absolute;
        right: 14px;
        background: none;
        border: none;
        color: var(--muted);
        cursor: pointer;
        font-size: 18px;
        padding: 10px;
        transition: color 0.3s;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: var(--primary);
    }

    .form-error {
        display: block;
        color: #ff8888;
        font-size: 14px;
        margin-top: 8px;
    }

    .password-match-feedback {
        display: none;
        align-items: center;
        gap: 12px;
        padding: 16px 16px;
        background-color: rgba(212, 119, 58, 0.1);
        border: 2px solid var(--primary);
        border-radius: 10px;
        font-size: 15px;
        color: var(--primary);
        margin-bottom: 24px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .password-match-feedback.show {
        display: flex;
    }

    .password-match-feedback.mismatch {
        background-color: rgba(255, 107, 107, 0.1);
        border-color: #ff6b6b;
        color: #ff8888;
    }

    .password-match-feedback.mismatch i {
        color: #ff6b6b;
    }

    .password-match-feedback i {
        font-size: 20px;
        color: var(--primary);
        flex-shrink: 0;
    }

    .btn-submit {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, var(--primary), #a5573a);
        color: var(--text);
        border: none;
        border-radius: 10px;
        font-size: 17px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px var(--primary-glow);
    }

    .btn-submit:active {
        transform: translateY(-1px);
    }

    .info-box {
        background-color: rgba(212, 119, 58, 0.15);
        border-left: 4px solid var(--primary);
        padding: 16px 18px;
        border-radius: 10px;
        display: flex;
        gap: 14px;
        font-size: 15px;
        color: var(--text);
        line-height: 1.5;
    }

    .info-box i {
        flex-shrink: 0;
        color: var(--primary);
        font-size: 20px;
        margin-top: 2px;
    }

    .info-box p {
        margin: 0;
    }

    @media (max-width: 768px) {
        .change-temp-password-card {
            padding: 45px 35px;
            max-width: 500px;
        }

        .change-temp-password-title {
            font-size: 28px;
        }

        .change-temp-password-subtitle {
            font-size: 15px;
        }

        .form-group label {
            font-size: 14px;
        }

        .form-group input {
            padding: 14px 44px 14px 14px;
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .change-temp-password-card {
            padding: 30px 20px;
        }

        .change-temp-password-icon {
            width: 60px;
            height: 60px;
            font-size: 28px;
        }

        .change-temp-password-title {
            font-size: 20px;
        }

        .change-temp-password-subtitle {
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            font-size: 13px;
        }

        .form-group input {
            padding: 12px 44px 12px 12px;
            font-size: 14px;
            border-width: 1px;
        }

        .btn-submit {
            padding: 14px;
            font-size: 15px;
        }

        .info-box {
            padding: 12px 12px;
            font-size: 12px;
        }

        .info-box i {
            font-size: 16px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function validatePasswordMatch() {
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirmation');
        const feedback = document.getElementById('passwordMatchFeedback');
        const matchText = document.getElementById('matchText');

        if (!passwordField.value || !confirmField.value) {
            feedback.classList.remove('show');
            return;
        }

        feedback.classList.add('show');

        if (passwordField.value === confirmField.value) {
            feedback.classList.remove('mismatch');
            feedback.classList.add('match');
            matchText.textContent = 'Las contraseñas coinciden';
            feedback.querySelector('i').className = 'fas fa-check-circle';
        } else {
            feedback.classList.remove('match');
            feedback.classList.add('mismatch');
            matchText.textContent = 'Las contraseñas no coinciden';
            feedback.querySelector('i').className = 'fas fa-times-circle';
        }
    }

    function togglePasswordField(fieldId) {
        const field = document.getElementById(fieldId);
        const button = event.currentTarget;
        const icon = button.querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }

    // Validar en tiempo real cuando se escriba
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirmation');

        if (passwordField) {
            passwordField.addEventListener('input', validatePasswordMatch);
        }

        if (confirmField) {
            confirmField.addEventListener('input', validatePasswordMatch);
        }
    });
</script>
@endpush
