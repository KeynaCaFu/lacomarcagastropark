<x-guest-layout>
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #485a1a 0%, #0d5e2a 100%);
        }
        
        .login-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            padding: 40px;
            max-width: 420px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #485a1a, #0d5e2a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
        }

        .login-title {
            font-size: 28px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-family: inherit;
        }

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
            margin-bottom: 24px;
        }

        .remember-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #16a34a;
        }

        .remember-label {
            margin-left: 8px;
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
        }

        .login-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #485a1a, #0d5e2a);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
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

        .role-info {
            background: #f3f4f6;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            border-left: 4px solid #16a34a;
        }

        .role-info-title {
            font-weight: 700;
            color: #374151;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .role-info-content {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }

        .role-info-content strong {
            color: #374151;
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
        }

        .divider-text {
            background: white;
            padding: 0 10px;
            position: relative;
            display: inline-block;
            color: #9ca3af;
            font-size: 13px;
        }

        .forgot-link {
            text-align: center;
            margin-top: 16px;
        }

        .forgot-link a {
            color: #16a34a;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .forgot-link a:hover {
            color: #0d5e2a;
        }

        .status-message {
            padding: 12px 16px;
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            border-radius: 6px;
            color: #065f46;
            font-size: 13px;
            margin-bottom: 20px;
        }
    </style>

    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-leaf"></i>
                </div>
                <h1 class="login-title">La Comarca</h1>
                <p class="login-subtitle">Sistema de Administración</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="status-message">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Role Info -->
            <div class="role-info">
                <div class="role-info-title">Credenciales de Prueba</div>
                <div class="role-info-content">
                    <strong>Admin Global:</strong> admin@gmail.com<br>
                    <strong>Gerente:</strong> gerente.puntamona@gmail.com<br>
                    Contraseña: <strong>password</strong>
                </div>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="form-input"
                        required 
                        autofocus 
                        autocomplete="username"
                    />
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        class="form-input"
                        required 
                        autocomplete="current-password"
                    />
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="remember-group">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember" 
                        class="remember-checkbox"
                    />
                    <label for="remember_me" class="remember-label">Recuérdame</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-button">
                    Iniciar Sesión
                </button>

                <!-- Forgot Password Link -->
                <div class="forgot-link">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
