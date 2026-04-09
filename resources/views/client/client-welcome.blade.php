<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <title>Bienvenido - La Comarca Gastro Park</title>
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

        /* ===== DROPDOWN MENU ===== */
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

        .user-dropdown a:last-child {
            border-bottom: none;
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

        .welcome-container {
            max-width: 900px;
            width: 100%;
        }

        /* ===== BIENVENIDA ===== */
        .welcome-box {
            background: linear-gradient(135deg, #e18018, #915016);
            border-radius: 20px;
            padding: 50px;
            color: #fff;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 15px 40px rgba(225, 128, 24, 0.2);
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

        .welcome-icon {
            font-size: 60px;
            margin-bottom: 20px;
            animation: wave 2s ease-in-out infinite;
        }

        @keyframes wave {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(20deg);
            }
            75% {
                transform: rotate(-20deg);
            }
        }

        .welcome-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .welcome-subtitle {
            font-size: 18px;
            opacity: 0.95;
            margin: 0;
        }

        /* ===== CONTENT CARDS ===== */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            animation: fadeIn 0.8s ease 0.3s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .card-icon {
            font-size: 40px;
            color: #e18018;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(225, 128, 24, 0.1);
            border-radius: 12px;
        }

        .card-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .card-content {
            color: #666;
            line-height: 1.8;
        }

        /* ===== PERFIL CARD ===== */
        .perfil-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 25px;
        }

        .perfil-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e18018;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 2px;
            background: #f8f6f4;
            border-radius: 10px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 700;
            color: #e18018;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            word-break: break-all;
        }

        .edit-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #e18018, #915016);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .edit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(225, 128, 24, 0.3);
        }

        /* ===== PROXIMAMENTE CARD ===== */
        .coming-soon-message {
            background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 213, 115, 0.1));
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid #27ae60;
            margin-bottom: 25px;
        }

        .coming-soon-message p {
            font-size: 16px;
            color: #555;
            margin: 0;
            line-height: 1.6;
        }

        .features-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .feature {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 15px;
            background: #f8f6f4;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .feature:hover {
            background: rgba(225, 128, 24, 0.08);
        }

        .feature-icon {
            font-size: 24px;
            color: #915016;
            min-width: 30px;
        }

        .feature-text {
            flex: 1;
        }

        .feature-text strong {
            display: block;
            color: #333;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .feature-text span {
            font-size: 13px;
            color: #888;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }

            .welcome-box {
                padding: 35px 25px;
            }

            .welcome-title {
                font-size: 32px;
            }

            .welcome-subtitle {
                font-size: 16px;
            }

            .welcome-icon {
                font-size: 48px;
            }

            .content-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .card {
                padding: 30px 20px;
            }

            .perfil-info {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .header-container {
                padding: 0 15px;
            }

            .logo-section img {
                max-width: 120px;
            }

            .user-menu-btn {
                padding: 8px 15px;
                font-size: 13px;
            }

            .welcome-box {
                padding: 25px 20px;
                border-radius: 15px;
            }

            .welcome-icon {
                font-size: 40px;
                margin-bottom: 15px;
            }

            .welcome-title {
                font-size: 24px;
                margin-bottom: 10px;
            }

            .welcome-subtitle {
                font-size: 14px;
            }

            .card {
                padding: 20px 15px;
            }

            .card-icon {
                font-size: 32px;
                width: 50px;
                height: 50px;
            }

            .card-title {
                font-size: 20px;
            }

            .info-item {
                padding: 12px;
            }

            .info-label {
                font-size: 11px;
            }

            .info-value {
                font-size: 14px;
            }

            .feature {
                padding: 12px;
                gap: 10px;
            }

            .feature-icon {
                font-size: 20px;
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
            <div class="position-relative">
                <button class="user-menu-btn" onclick="toggleUserMenu(event)">
                    <i class="fas fa-user-circle"></i>
                    {{ $user->full_name }}
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <a href="{{ route('client.profile.edit') }}">
                        <i class="fas fa-edit"></i> Editar perfil
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="color: #d32f2f;">
                            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="client-main">
        <div class="welcome-container">
            <!-- Bienvenida -->
            <div class="welcome-box">
                <div class="welcome-icon">
                    <i class="fas fa-hand-wave"></i>
                </div>
                <h1 class="welcome-title">¡Bienvenido!</h1>
                <p class="welcome-subtitle">A La Comarca Gastro Park</p>
            </div>

            <!-- Contenido principal -->
            <div class="content-grid">
                <!-- Card de Perfil -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h2 class="card-title">Mi Perfil</h2>
                    </div>
                    <div class="card-content">
                        <p>Información de tu cuenta</p>
                        
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="Avatar" class="perfil-avatar">
                        @endif
                        
                        <div class="perfil-info">
                            <div class="info-item">
                                <div class="info-label">Nombre</div>
                                <div class="info-value">{{ $user->full_name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Correo</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>
                            @if($user->phone)
                                <div class="info-item">
                                    <div class="info-label">Teléfono</div>
                                    <div class="info-value">{{ $user->phone }}</div>
                                </div>
                            @endif
                            <div class="info-item">
                                <div class="info-label">Miembro desde</div>
                                <div class="info-value">{{ $user->created_at->format('d.m.Y') }}</div>
                            </div>
                        </div>
                        
                        <a href="{{ route('client.profile.edit') }}" class="edit-button">
                            <i class="fas fa-edit"></i> Editar perfil
                        </a>
                    </div>
                </div>

                <!-- Card Próximamente -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h2 class="card-title">Próximamente</h2>
                    </div>
                    <div class="card-content">
                        <div class="coming-soon-message">
                            <p>
                                ¡Muy pronto podrás explorar el menú completo de nuestros locales 
                                y realizar tus pedidos!
                            </p>
                        </div>

                        <div class="features-list">
                            <div class="feature">
                                <div class="feature-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Ver menú de nuestros locales</strong>
                                    <span>Explora todos nuestros productos</span>
                                </div>
                            </div>

                            <div class="feature">
                                <div class="feature-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Realizar pedidos</strong>
                                    <span>Pide desde la comodidad de tu hogar</span>
                                </div>
                            </div>

                            <div class="feature">
                                <div class="feature-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Eventos especiales</strong>
                                    <span>Participa en nuestros eventos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    </script>
</body>
</html>
