<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>Editar Perfil - La Comarca Gastro Park</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f6f4 0%, #faf8f6 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER SIMPLE ===== */
        .client-header {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 20px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo-section img {
            max-width: 150px;
            height: auto;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-btn {
            padding: 10px 15px;
            background: #f0f0f0;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: #e0e0e0;
            color: #e18018;
        }

        .user-menu-btn {
            background: linear-gradient(135deg, #e18018, #915016);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .user-menu-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(225, 128, 24, 0.3);
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            min-width: 200px;
            margin-top: 8px;
            display: none;
            z-index: 1000;
            overflow: hidden;
        }

        .user-dropdown.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

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

        .user-dropdown a,
        .user-dropdown form {
            display: block;
        }

        .user-dropdown a {
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .user-dropdown a:hover {
            background: #f8f6f4;
            color: #e18018;
        }

        .user-dropdown button {
            width: 100%;
            text-align: left;
            padding: 12px 20px;
            border: none;
            background: none;
            color: #d32f2f;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-dropdown button:hover {
            background: #f8f6f4;
        }

        /* ===== MAIN CONTENT ===== */
        .client-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .profile-container {
            max-width: 700px;
            width: 100%;
        }

        .profile-card {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-header {
            text-align: center;
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 2px solid #f0f0f0;
        }

        .profile-icon {
            font-size: 50px;
            color: #e18018;
            margin-bottom: 15px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e18018;
            margin: 0 auto 20px;
            display: block;
        }

        .profile-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .profile-subtitle {
            font-size: 14px;
            color: #888;
            margin: 8px 0 0;
        }

        /* ===== FILE INPUT ===== */
        .file-input-wrapper {
            position: relative;
        }

        .file-input-label {
            display: inline-block;
            padding: 10px 20px;
            background: #f0f0f0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #333;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .file-input-label:hover {
            background: #e0e0e0;
            color: #e18018;
        }

        .file-input-wrapper input[type="file"] {
            display: none;
        }

        .file-name {
            font-size: 12px;
            color: #888;
            margin-top: 8px;
        }

        .avatar-preview {
            width: 100%;
            max-width: 150px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
            border: 2px solid #e18018;
        }

        .avatar-preview.show {
            display: block;
        }

        /* ===== FORM ===== */
        .form-group-custom {
            margin-bottom: 25px;
        }

        .form-group-custom label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .form-group-custom input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group-custom input:focus {
            outline: none;
            border-color: #e18018;
            box-shadow: 0 0 0 3px rgba(225, 128, 24, 0.1);
        }

        .form-group-custom input:disabled {
            background: #f5f5f5;
            color: #999;
        }

        .form-help-text {
            font-size: 12px;
            color: #888;
            margin-top: 6px;
        }

        /* ===== BUTTONS ===== */
        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 35px;
        }

        .btn-custom {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-save {
            background: linear-gradient(135deg, #e18018, #915016);
            color: #fff;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(225, 128, 24, 0.3);
        }

        .btn-cancel {
            background: #f0f0f0;
            color: #333;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        /* ===== ALERTS ===== */
        .alert-custom {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            border-color: #27ae60;
            color: #27ae60;
        }

        .alert-error {
            background: rgba(211, 47, 47, 0.1);
            border-color: #d32f2f;
            color: #d32f2f;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .profile-card {
                padding: 30px 20px;
            }

            .button-group {
                grid-template-columns: 1fr;
            }

            .header-container {
                flex-direction: column;
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .header-container {
                padding: 0 15px;
            }

            .logo-section img {
                max-width: 120px;
            }

            .back-btn {
                padding: 8px 12px;
                font-size: 13px;
            }

            .user-menu-btn {
                padding: 8px 15px;
                font-size: 13px;
            }

            .profile-card {
                padding: 20px 15px;
            }

            .profile-icon {
                font-size: 40px;
            }

            .profile-title {
                font-size: 22px;
            }

            .profile-header {
                margin-bottom: 25px;
                padding-bottom: 20px;
            }

            .form-group-custom label {
                font-size: 13px;
            }

            .form-group-custom input {
                padding: 10px 12px;
                font-size: 14px;
            }

            .btn-custom {
                padding: 10px 20px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <!-- Header simple -->
    <header class="client-header">
        <div class="header-container">
            <div class="logo-section">
                <img src="{{ asset('images/logo_comarca.png') }}" alt="La Comarca">
            </div>
            <div class="header-actions">
                <a href="{{ route('client.welcome') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <div class="position-relative">
                    <button class="user-menu-btn" onclick="toggleUserMenu(event)">
                        <i class="fas fa-user-circle"></i>
                        {{ $user->full_name }}
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="color: #d32f2f;">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="client-main">
        <div class="profile-container">
            <!-- Alerts -->
            @if (session('status'))
                <div class="alert-custom alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-custom alert-error">
                    <i class="fas fa-exclamation-circle"></i> 
                    Por favor revisa los errores en el formulario
                </div>
            @endif

            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="Avatar" class="profile-avatar">
                    @endif
                    <h1 class="profile-title">Editar Perfil</h1>
                    <p class="profile-subtitle">Actualiza tu información personal</p>
                </div>

                <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <!-- Full Name -->
                    <div class="form-group-custom">
                        <label for="full_name">Nombre completo</label>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            value="{{ old('full_name', $user->full_name) }}"
                            required
                            class="@error('full_name') is-invalid @enderror"
                        >
                        @error('full_name')
                            <div class="form-help-text" style="color: #d32f2f;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Avatar Photo -->
                    <div class="form-group-custom">
                        <label>Foto de perfil</label>
                        <div class="file-input-wrapper">
                            <label for="avatar" class="file-input-label">
                                <i class="fas fa-camera"></i> Seleccionar foto
                            </label>
                            <input 
                                type="file" 
                                id="avatar" 
                                name="avatar"
                                accept="image/*"
                                class="@error('avatar') is-invalid @enderror"
                                onchange="previewAvatar(event)"
                            >
                            <div class="file-name" id="fileName"></div>
                            <img id="avatarPreview" class="avatar-preview" alt="Preview">
                        </div>
                        @error('avatar')
                            <div class="form-help-text" style="color: #d32f2f;">{{ $message }}</div>
                        @enderror
                        <div class="form-help-text">Formatos permitidos: JPG, PNG, GIF (máx 2MB)</div>
                    </div>

                    <!-- Email (read-only) -->
                    <div class="form-group-custom">
                        <label for="email">Correo electrónico</label>
                        <input 
                            type="email" 
                            id="email" 
                            value="{{ $user->email }}"
                            disabled
                        >
                        <div class="form-help-text">Tu correo no puede ser modificado</div>
                    </div>

                    <!-- Phone -->
                    <div class="form-group-custom">
                        <label for="phone">Teléfono</label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="Ej: +506 8888 7777"
                            class="@error('phone') is-invalid @enderror"
                        >
                        @error('phone')
                            <div class="form-help-text" style="color: #d32f2f;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Member Since (read-only) -->
                    <div class="form-group-custom">
                        <label for="created_at">Miembro desde</label>
                        <input 
                            type="text" 
                            id="created_at" 
                            value="{{ $user->created_at->format('d \\d\\e F \\d\\e Y') }}"
                            disabled
                        >
                    </div>

                    <!-- Buttons -->
                    <div class="button-group">
                        <button type="submit" class="btn-custom btn-save">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                        <a href="{{ route('client.welcome') }}" class="btn-custom btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function toggleUserMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            if (!event.target.closest('.position-relative')) {
                dropdown.classList.remove('show');
            }
        });

        function previewAvatar(event) {
            const file = event.target.files[0];
            const fileNameElement = document.getElementById('fileName');
            const previewElement = document.getElementById('avatarPreview');

            if (file) {
                // Mostrar nombre del archivo
                fileNameElement.textContent = `Archivo seleccionado: ${file.name}`;

                // Crear preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewElement.src = e.target.result;
                    previewElement.classList.add('show');
                };
                reader.readAsDataURL(file);
            } else {
                fileNameElement.textContent = '';
                previewElement.classList.remove('show');
            }
        }
    </script>
</body>
</html>
