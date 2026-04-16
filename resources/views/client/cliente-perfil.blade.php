<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Perfil - La Comarca Gastro Park</title>
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <style>
        :root {
            --primary: #D4773A;
            --primary-light: rgba(212,119,58,0.15);
            --primary-glow: rgba(212,119,58,0.28);
            --bg: #0A0908;
            --surface: #0F0D0B;
            --card: #171410;
            --card-hover: #1E1A14;
            --border: #262018;
            --border-light: #302820;
            --text: #F5F0E8;
            --muted: #7A7060;
            --radius: 14px;
            --radius-sm: 8px;
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; min-height: 100%; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; -webkit-tap-highlight-color: transparent; overflow-x: hidden; display: flex; flex-direction: column; }
        img { display: block; max-width: 100%; }
        a { text-decoration: none; color: inherit; }
        .container { width: 100%; max-width: 1280px; margin: 0 auto; padding: 0 20px; }
        @media (max-width: 480px) { .container { padding: 0 16px; } }
        
        .plaza-header { position: fixed; top: 0; left: 0; right: 0; z-index: 200; background: rgba(10,9,8,0.92); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border); padding: 12px 0; height: 58px; transition: background 0.3s; }
        @media (max-width: 480px) { .plaza-header { padding: 10px 0; height: 54px; } }
        .header-inner { display: flex; align-items: center; justify-content: space-between; }
        .header-logo { display: flex; align-items: center; gap: 10px; }
        .header-logo-img { height: 34px; width: auto; object-fit: contain; }
        .header-logo-text { font-family: 'Cormorant Garamond', serif; font-size: 1.05rem; font-weight: 600; color: var(--text); letter-spacing: 0.02em; }
        .header-auth { display: flex; align-items: center; gap: 8px; }
        .back-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border: 1px solid var(--border-light); background: none; border-radius: var(--radius-sm); color: var(--text); cursor: pointer; font-size: 0.78rem; font-weight: 600; font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
        .back-btn:hover { border-color: var(--primary); color: var(--primary); }
        .user-menu-top { position: relative; }
        .user-menu-btn { background: none; border: 1px solid var(--border-light); cursor: pointer; padding: 7px 12px; color: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; gap: 8px; font-size: 0.78rem; font-weight: 600; font-family: 'DM Sans', sans-serif; transition: border-color 0.2s; }
        .user-menu-btn:hover { border-color: var(--primary); }
        .user-menu-dropdown { position: absolute; top: calc(100% + 8px); right: 0; background: var(--card); border: 1px solid var(--border-light); border-radius: var(--radius-sm); box-shadow: 0 12px 40px rgba(0,0,0,0.5); min-width: 210px; z-index: 1000; display: none; overflow: hidden; }
        .user-menu-dropdown.open { display: block; }
        .dropdown-header { padding: 12px 14px; border-bottom: 1px solid var(--border); }
        .dropdown-name { font-weight: 600; font-size: 0.82rem; color: var(--text); }
        .dropdown-email { font-size: 0.7rem; color: var(--muted); margin-top: 2px; }
        .dropdown-item { display: flex; align-items: center; gap: 9px; padding: 10px 14px; font-size: 0.78rem; color: var(--text); border-bottom: 1px solid var(--border); cursor: pointer; background: none; border-left: none; border-right: none; width: 100%; font-family: 'DM Sans', sans-serif; transition: background 0.15s; text-align: left; }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: var(--card-hover); }
        .dropdown-item.danger { color: #e05c5c; }
        
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; margin-top: 58px; }
        @media (max-width: 480px) { main { margin-top: 54px; padding: 30px 16px; } }
        .profile-container { max-width: 700px; width: 100%; }
        .profile-card { background: var(--card); border-radius: var(--radius); padding: 40px; border: 1px solid var(--border-light); box-shadow: 0 12px 40px rgba(0,0,0,0.5); animation: slideUp 0.6s ease; }
        @media (max-width: 768px) { .profile-card { padding: 30px 20px; } }
        @media (max-width: 480px) { .profile-card { padding: 20px 15px; border-radius: 10px; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        
        .profile-header { text-align: center; margin-bottom: 35px; padding-bottom: 25px; border-bottom: 1px solid var(--border); }
        .profile-avatar-container { width: 120px; height: 120px; border-radius: 50%; border: 3px solid var(--primary); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; background: var(--primary-light); overflow: hidden; }
        .profile-avatar-container img { width: 100%; height: 100%; object-fit: cover; }
        .profile-initials { font-size: 48px; font-weight: 700; color: var(--primary); font-family: 'DM Sans', sans-serif; letter-spacing: -2px; }
        .profile-title { font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 700; color: var(--text); margin: 0; letter-spacing: 0.01em; }
        .profile-subtitle { font-size: 0.82rem; color: var(--muted); margin: 8px 0 0; font-weight: 300; }
        
        .form-group-custom { margin-bottom: 25px; }
        .form-group-custom label { display: block; font-size: 0.82rem; font-weight: 600; color: var(--text); margin-bottom: 10px; letter-spacing: 0.01em; }
        .form-group-custom input { width: 100%; padding: 12px 15px; border: 1px solid var(--border-light); background: var(--surface); border-radius: var(--radius-sm); font-size: 0.88rem; color: var(--text); font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
        .form-group-custom input::placeholder { color: var(--muted); }
        .form-group-custom input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        .form-group-custom input:disabled { background: var(--border); color: var(--muted); cursor: not-allowed; }
        .form-help-text { font-size: 0.75rem; color: var(--muted); margin-top: 6px; font-weight: 300; }
        .form-help-text.error { color: #e05c5c; }
        
        .file-input-wrapper { position: relative; }
        .file-input-label { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: var(--surface); border: 1px solid var(--border-light); border-radius: var(--radius-sm); cursor: pointer; font-weight: 600; color: var(--primary); transition: all 0.2s; font-size: 0.78rem; font-family: 'DM Sans', sans-serif; }
        .file-input-label:hover { background: var(--card-hover); border-color: var(--primary); }
        .file-input-wrapper input[type="file"] { display: none; }
        .file-name { font-size: 0.75rem; color: var(--muted); margin-top: 8px; }
        .avatar-preview { width: 100%; max-width: 150px; border-radius: var(--radius-sm); margin-top: 15px; display: none; border: 2px solid var(--primary); }
        .avatar-preview.show { display: block; }
        
        .button-group { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 35px; }
        @media (max-width: 480px) { .button-group { grid-template-columns: 1fr; } }
        .btn-custom { padding: 12px 25px; border: none; border-radius: var(--radius-sm); font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; font-family: 'DM Sans', sans-serif; letter-spacing: 0.01em; }
        .btn-save { background: var(--primary); color: #F5F0E8; border: 1px solid var(--primary); }
        .btn-save:hover { background: transparent; border-color: var(--primary); color: var(--primary); transform: translateY(-2px); box-shadow: 0 8px 20px var(--primary-glow); }
        .btn-cancel { background: transparent; color: var(--text); border: 1px solid var(--border-light); }
        .btn-cancel:hover { border-color: var(--text); background: var(--card-hover); }
        
        .alert-custom { padding: 15px 20px; border-radius: var(--radius-sm); margin-bottom: 25px; border-left: 4px solid; display: flex; align-items: center; gap: 12px; font-size: 0.82rem; font-weight: 500; }
        .alert-success { background: rgba(39, 174, 96, 0.1); border-color: #27ae60; color: #27ae60; }
        .alert-error { background: rgba(224, 92, 92, 0.1); border-color: #e05c5c; color: #e05c5c; }
        
        .password-section { display: none; margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border); max-height: 0; opacity: 0; overflow: hidden; transition: max-height 0.4s ease, opacity 0.4s ease; }
        .password-section.visible { display: block; max-height: 1000px; opacity: 1; }
        .toggle-password-btn { width: 100%; padding: 12px 20px; background: transparent; border: 1px solid var(--border-light); border-radius: var(--radius-sm); color: var(--text); cursor: pointer; font-size: 0.82rem; font-weight: 600; font-family: 'DM Sans', sans-serif; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s; margin-bottom: 20px; margin-top: 21px; }
        .toggle-password-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--card-hover); }
        .toggle-password-btn i { transition: transform 0.3s ease; }
        .toggle-password-btn.active i { transform: rotate(180deg); }
        
        .password-tabs { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 1px solid var(--border); }
        .password-tab { padding: 12px 16px; background: none; border: none; border-bottom: 3px solid transparent; color: var(--muted); cursor: pointer; font-size: 0.82rem; font-weight: 600; font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
        .password-tab:hover { color: var(--text); }
        .password-tab.active { border-bottom-color: var(--primary); color: var(--primary); }
        
        .password-tab-content { display: none; }
        .password-tab-content.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .forget-password-link { display: inline-block; margin-top: 10px; font-size: 0.75rem; }
        .forget-password-link a { color: var(--primary); text-decoration: underline; cursor: pointer; }
        .forget-password-link a:hover { text-decoration: none; }
        
        .password-input-wrapper { position: relative; display: flex; align-items: center; }
        .password-input-wrapper input { width: 100%; padding: 12px 45px 12px 15px; border: 1px solid var(--border-light); background: var(--surface); border-radius: var(--radius-sm); font-size: 0.88rem; color: var(--text); font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
        .password-input-wrapper input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        .password-toggle-btn { position: absolute; right: 12px; background: none; border: none; color: var(--muted); cursor: pointer; padding: 8px; font-size: 0.9rem; transition: color 0.2s; }
        .password-toggle-btn:hover { color: var(--primary); }
        .password-match-feedback { display: none; font-size: 0.78rem; margin-top: 8px; padding: 8px 12px; border-radius: var(--radius-sm); align-items: center; gap: 8px; }
        .password-match-feedback.show { display: flex; }
        .password-match-feedback.match { background: rgba(39, 174, 96, 0.1); border: 1px solid #27ae60; color: #27ae60; }
        .password-match-feedback.mismatch { background: rgba(224, 92, 92, 0.1); border: 1px solid #e05c5c; color: #e05c5c; }
        .password-match-feedback i { font-size: 0.85rem; }
    </style>
</head>
<body>
    <header class="plaza-header">
        <div class="container">
            <div class="header-inner">
                <div class="header-logo">
                    <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca" class="header-logo-img">
                    <span class="header-logo-text">La Comarca Gastropark</span>
                </div>
                <div class="header-auth">
                    <button onclick="history.back()" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
                    <div class="user-menu-top">
                        <button class="user-menu-btn" id="userMenuBtn" onclick="toggleUserMenu(event)">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset(auth()->user()->avatar) }}" alt="" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
                            @else
                                <i class="fas fa-user-circle"></i>
                            @endif
                            <span>{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                        </button>
                        <div class="user-menu-dropdown" id="userMenuDropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                                <div class="dropdown-email">{{ auth()->user()->email }}</div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item danger">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="profile-container">
            @if (session('status'))
                <div class="alert-custom alert-success">
                    <i class="fas fa-check-circle"></i> 
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-custom alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Por favor revisa los errores en el formulario</span>
                </div>
            @endif

            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar-container" id="avatarContainer">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="Avatar" id="avatarDisplay">
                        @else
                            <span class="profile-initials" id="initialsDisplay">{{ strtoupper(substr($user->full_name, 0, 1)) }}{{ strtoupper(substr(strrchr($user->full_name, ' '), 1, 1)) }}</span>
                        @endif
                    </div>
                    <h1 class="profile-title">Editar Perfil</h1>
                    <p class="profile-subtitle">Actualiza tu información personal</p>
                </div>

                <!-- FORM 1: EDIT PROFILE -->
                <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="form-group-custom">
                        <label for="full_name">Nombre completo</label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required class="@error('full_name') is-invalid @enderror">
                        @error('full_name')
                            <div class="form-help-text error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group-custom">
                        <label>Foto de perfil</label>
                        <div class="file-input-wrapper">
                            <label for="avatar" class="file-input-label">
                                <i class="fas fa-camera"></i> Seleccionar foto
                            </label>
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="@error('avatar') is-invalid @enderror" onchange="previewAvatar(event)">
                            <div class="file-name" id="fileName"></div>
                            <img id="avatarPreview" class="avatar-preview" alt="Preview">
                        </div>
                        @error('avatar')
                            <div class="form-help-text error">{{ $message }}</div>
                        @enderror
                        <div class="form-help-text">Formatos permitidos: JPG, PNG, GIF (máx 2MB)</div>
                    </div>

                    <div class="form-group-custom">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" value="{{ $user->email }}" disabled>
                        <div class="form-help-text">Tu correo no puede ser modificado</div>
                    </div>

                    <div class="form-group-custom">
                        <label for="phone">Teléfono</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Ej: +506 8888 7777" class="@error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="form-help-text error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group-custom">
                        <label for="created_at">Miembro desde</label>
                        <input type="text" id="created_at" value="{{ $user->created_at->format('d \\d\\e F \\d\\e Y') }}" disabled>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn-custom btn-save">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                        <a href="{{ route('plaza.index') }}" class="btn-custom btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>

                <!-- PASSWORD SECTION -->
                @if(!$user->provider)
                    @if ($errors->any() && ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation') || $errors->has('temporary_password')))
                        <button type="button" class="toggle-password-btn active" onclick="togglePasswordSection()"><i class="fas fa-key"></i> Ocultar cambio de contraseña <i class="fas fa-chevron-down"></i></button>
                    @else
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordSection()"><i class="fas fa-key"></i> Cambiar contraseña <i class="fas fa-chevron-down"></i></button>
                    @endif

                    <div id="passwordSection" class="password-section @if ($errors->any() && ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation') || $errors->has('temporary_password'))) visible @endif">
                        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; font-weight: 600; color: var(--text); margin-bottom: 20px; letter-spacing: 0.01em;">Cambiar Contraseña</h2>

                        <!-- TABS -->
                        <div class="password-tabs">
                            <button type="button" class="password-tab active" onclick="switchPasswordTab('normal')">
                                <i class="fas fa-lock"></i> Cambiar Contraseña
                            </button>
                            <button type="button" class="password-tab" onclick="switchPasswordTab('temporary')">
                                <i class="fas fa-envelope"></i> Usar Temporal
                            </button>
                        </div>

                        <!-- TAB 1: CAMBIAR CONTRASEÑA NORMAL -->
                        <div id="normalTab" class="password-tab-content active">
                            <form method="POST" action="{{ route('client.password.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="form-group-custom">
                                    <label for="current_password">Contraseña Actual</label>
                                    <input type="password" id="current_password" name="current_password" required class="@error('current_password', 'updatePassword') is-invalid @enderror">
                                    @error('current_password', 'updatePassword')
                                        <div class="form-help-text error">{{ $message }}</div>
                                    @enderror
                                    <div class="forget-password-link">
                                        <a onclick="switchPasswordTab('temporary'); requestTemporaryPassword();">¿No recuerdas tu contraseña?</a>
                                    </div>
                                </div>

                                <div class="form-group-custom">
                                    <label for="password">Nueva Contraseña</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="password" name="password" required oninput="validatePasswordMatch()" class="@error('password', 'updatePassword') is-invalid @enderror">
                                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password')">
                                            <i class="fas fa-eye-slash" id="password-toggle-icon"></i>
                                        </button>
                                    </div>
                                    @error('password', 'updatePassword')
                                        <div class="form-help-text error">{{ $message }}</div>
                                    @enderror
                                    <div class="form-help-text">Mínimo 8 caracteres con mayúscula, minúscula y número</div>
                                </div>

                                <div class="form-group-custom">
                                    <label for="password_confirmation">Confirmar Contraseña</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="password_confirmation" name="password_confirmation" required oninput="validatePasswordMatch()" class="@error('password_confirmation', 'updatePassword') is-invalid @enderror">
                                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_confirmation')">
                                            <i class="fas fa-eye-slash" id="password_confirmation-toggle-icon"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation', 'updatePassword')
                                        <div class="form-help-text error">{{ $message }}</div>
                                    @enderror
                                    <div class="password-match-feedback" id="passwordMatchFeedback">
                                        <i class="fas fa-check-circle"></i>
                                        <span id="matchText">Las contraseñas coinciden</span>
                                    </div>
                                </div>

                                <button type="submit" class="btn-custom btn-save" style="width: 100%; justify-content: center; margin-top: 20px;">
                                    <i class="fas fa-key"></i> Cambiar Contraseña
                                </button>
                            </form>
                        </div>

                        <!-- TAB 2: USAR CONTRASEÑA TEMPORAL -->
                        <div id="temporaryTab" class="password-tab-content">
                            <!-- Request Temporary Password Form -->
                            <div id="requestTempForm">
                                <div style="background: var(--primary-light); border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 15px; margin-bottom: 20px; display: flex; gap: 12px; align-items: flex-start;">
                                    <i class="fas fa-info-circle" style="color: var(--primary); flex-shrink: 0; margin-top: 2px; font-size: 0.9rem;"></i>
                                    <div style="font-size: 0.78rem; color: var(--muted); line-height: 1.6;">
                                        <strong style="color: var(--text);">¿No recuerdas tu contraseña?</strong><br>
                                        Haz clic en el botón de abajo para recibir una contraseña temporal válida por 15 minutos en tu correo.
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('client.password.request-temporary') }}">
                                    @csrf
                                    <button type="submit" class="btn-custom btn-save" style="width: 100%; justify-content: center;">
                                        <i class="fas fa-envelope"></i> Enviar Contraseña Temporal
                                    </button>
                                </form>
                            </div>

                            <!-- Use Temporary Password Form (Hidden by default) -->
                            <div id="useTempForm" style="display: none;">
                                <form method="POST" action="{{ route('client.password.update-temporary') }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group-custom">
                                        <label for="temporary_password">Contraseña Temporal</label>
                                        <div class="password-input-wrapper">
                                            <input type="text" id="temporary_password" name="temporary_password" required placeholder="Pega aquí la contraseña del correo" class="@error('temporary_password', 'temporaryPassword') is-invalid @enderror">
                                            <button type="button" class="password-toggle-btn" onclick="copyFromClipboard('temporary_password')" style="right: 40px;">
                                                <i class="fas fa-paste"></i>
                                            </button>
                                        </div>
                                        @error('temporary_password', 'temporaryPassword')
                                            <div class="form-help-text error">{{ $message }}</div>
                                        @enderror
                                        <div class="form-help-text">Copia la contraseña que recibiste en tu correo</div>
                                    </div>

                                    <div class="form-group-custom">
                                        <label for="temp_password">Nueva Contraseña</label>
                                        <div class="password-input-wrapper">
                                            <input type="password" id="temp_password" name="password" required oninput="validatePasswordMatchTemp()" class="@error('password', 'temporaryPassword') is-invalid @enderror">
                                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('temp_password')">
                                                <i class="fas fa-eye-slash" id="temp_password-toggle-icon"></i>
                                            </button>
                                        </div>
                                        @error('password', 'temporaryPassword')
                                            <div class="form-help-text error">{{ $message }}</div>
                                        @enderror
                                        <div class="form-help-text">Mínimo 8 caracteres con mayúscula, minúscula y número</div>
                                    </div>

                                    <div class="form-group-custom">
                                        <label for="temp_password_confirmation">Confirmar Contraseña</label>
                                        <div class="password-input-wrapper">
                                            <input type="password" id="temp_password_confirmation" name="password_confirmation" required oninput="validatePasswordMatchTemp()" class="@error('password_confirmation', 'temporaryPassword') is-invalid @enderror">
                                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('temp_password_confirmation')">
                                                <i class="fas fa-eye-slash" id="temp_password_confirmation-toggle-icon"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation', 'temporaryPassword')
                                            <div class="form-help-text error">{{ $message }}</div>
                                        @enderror
                                        <div class="password-match-feedback" id="passwordMatchFeedbackTemp">
                                            <i class="fas fa-check-circle"></i>
                                            <span id="matchTextTemp">Las contraseñas coinciden</span>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn-custom btn-save" style="width: 100%; justify-content: center; margin-top: 20px;">
                                        <i class="fas fa-key"></i> Cambiar Contraseña
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border);">
                        <div style="padding: 16px; background: var(--primary-light); border: 1px solid var(--border-light); border-radius: var(--radius-sm); display: flex; gap: 12px; align-items: flex-start;">
                            <i class="fas fa-info-circle" style="color: var(--primary); flex-shrink: 0; margin-top: 2px;"></i>
                            <div>
                                <div style="font-weight: 600; color: var(--text); margin-bottom: 4px; font-size: 0.82rem;">Cuenta vinculada a {{ ucfirst($user->provider) }}</div>
                                <div style="font-size: 0.78rem; color: var(--muted); line-height: 1.5;">
                                    Tu cuenta está vinculada a {{ ucfirst($user->provider) }}. Para cambiar tu contraseña, debes hacerlo directamente en tu cuenta de {{ ucfirst($user->provider) }}.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <script>
        function toggleUserMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('userMenuDropdown');
            dropdown.classList.toggle('open');
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.user-menu-top')) {
                document.getElementById('userMenuDropdown').classList.remove('open');
            }
        });

        // Mostrar el formulario de uso temporal si ya se envió la solicitud exitosamente
        document.addEventListener('DOMContentLoaded', function() {
            const alertSuccess = document.querySelector('.alert-success');
            const alertMessage = alertSuccess?.textContent || '';
            
            if (alertMessage.includes('contraseña temporal') || alertMessage.includes('Contraseña temporal')) {
                const tempTab = document.querySelectorAll('.password-tab')[1];
                const useTempForm = document.getElementById('useTempForm');
                const requestTempForm = document.getElementById('requestTempForm');
                
                if (tempTab && useTempForm && requestTempForm) {
                    tempTab.classList.add('active');
                    document.querySelectorAll('.password-tab')[0].classList.remove('active');
                    requestTempForm.style.display = 'none';
                    useTempForm.style.display = 'block';
                }
            }
        });

        function previewAvatar(event) {
            const file = event.target.files[0];
            const fileNameElement = document.getElementById('fileName');
            const avatarContainer = document.getElementById('avatarContainer');

            if (file) {
                fileNameElement.textContent = `Archivo seleccionado: ${file.name}`;
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarContainer.innerHTML = `<img src="${e.target.result}" alt="Avatar Preview" id="avatarDisplay">`;
                };
                reader.readAsDataURL(file);
            } else {
                fileNameElement.textContent = '';
            }
        }

        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-toggle-icon');
            
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

        function togglePasswordSection() {
            const section = document.getElementById('passwordSection');
            const btn = document.querySelector('.toggle-password-btn');
            section.classList.toggle('visible');
            btn.classList.toggle('active');
            
            if (section.classList.contains('visible')) {
                btn.innerHTML = '<i class="fas fa-key"></i> Ocultar cambio de contraseña <i class="fas fa-chevron-down"></i>';
            } else {
                btn.innerHTML = '<i class="fas fa-key"></i> Cambiar contraseña <i class="fas fa-chevron-down"></i>';
            }
        }

        function switchPasswordTab(tab) {
            const tabs = document.querySelectorAll('.password-tab');
            const contents = document.querySelectorAll('.password-tab-content');
            
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            if (tab === 'normal') {
                tabs[0].classList.add('active');
                document.getElementById('normalTab').classList.add('active');
            } else {
                tabs[1].classList.add('active');
                document.getElementById('temporaryTab').classList.add('active');
            }
        }

        function validatePasswordMatchTemp() {
            const passwordField = document.getElementById('temp_password');
            const confirmField = document.getElementById('temp_password_confirmation');
            const feedback = document.getElementById('passwordMatchFeedbackTemp');
            const matchText = document.getElementById('matchTextTemp');

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

        function requestTemporaryPassword() {
            // Esta función se ejecuta cuando se solicita una contraseña temporal
            // El formulario se enviará automáticamente gracias al onclick en el botón
        }

        function copyFromClipboard(fieldId) {
            navigator.clipboard.read().then(items => {
                items.forEach(item => {
                    if (item.types.includes('text/plain')) {
                        item.getType('text/plain').then(blob => {
                            blob.text().then(text => {
                                document.getElementById(fieldId).value = text.trim();
                            });
                        });
                    }
                });
            }).catch(err => {
                alert('Por favor, pega manualmente la contraseña temporal');
            });
        }
    </script>
</body>
</html>
