<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Plaza Gastronómica - La Comarca</title>
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vue 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
        }

        :root {
            --primary:       #D4773A;
            --primary-light: #F0956040;
            --primary-glow:  #D4773A30;
            --bg:            #0E0C0A;
            --surface:       #181410;
            --card:          #1E1A15;
            --card-hover:    #252018;
            --border:        #2E2820;
            --text:          #F5F0E8;
            --muted:         #8A8070;
            --radius:        16px;
            --radius-sm:     10px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        img {
            display: block;
            max-width: 100%;
            height: auto;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ========== CONTAINER ========== */
        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* ========== HEADER STICKY ========== */
        .plaza-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 200;
            background: rgba(14,12,10,0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
        }

        .logo-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary);
        }

        .header-auth {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-auth {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-login {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-login:hover {
            background: var(--primary-light);
        }

        .btn-logout {
            background: var(--primary);
            color: #fff;
        }

        .btn-logout:hover {
            background: #c06830;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 12px;
            font-size: 0.85rem;
            color: var(--muted);
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .user-info:hover {
            color: var(--primary);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Dropdown Menu ========== */
        .user-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--surface-dark);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            min-width: 200px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
            display: none;
            flex-direction: column;
            z-index: 1000;
            margin-top: 8px;
            overflow: hidden;
        }

        .user-menu-dropdown.active {
            display: flex;
        }

        .user-menu-dropdown a,
        .user-menu-dropdown form {
            padding: 12px 16px;
            color: var(--text);
            text-decoration: none;
            font-size: 0.85rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-menu-dropdown a:last-child,
        .user-menu-dropdown form:last-child {
            border-bottom: none;
        }

        .user-menu-dropdown a:hover,
        .user-menu-dropdown form:hover {
            background: var(--surface);
        }

        .user-menu-dropdown button {
            background: none;
            border: none;
            color: var(--text);
            padding: 12px 16px;
            text-align: left;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }

        .user-menu-dropdown button:hover {
            background: var(--surface);
        }

        .user-menu-dropdown button.logout-btn {
            color: #ff6b6b;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--card);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--primary);
        }

        /* ========== HERO SECTION ========== */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 0 60px;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.35;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--bg) 0%, rgba(14,12,10,.8) 50%, rgba(14,12,10,.4) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            border: 1px solid var(--primary-glow);
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 999px;
            margin-bottom: 16px;
            width: fit-content;
        }

        .hero-badge i {
            font-size: 0.85rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 900;
            line-height: 1.2;
            color: var(--text);
            margin-bottom: 12px;
            word-break: break-word;
        }

        .hero-title em {
            font-style: italic;
            color: var(--primary);
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 24px;
            max-width: 520px;
        }

        /* ========== SEARCH ========== */
        .search-wrap {
            position: relative;
            margin-bottom: 32px;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
            font-size: 1rem;
        }

        .search-input {
            width: 100%;
            height: 48px;
            background: rgba(30,26,21,0.95);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0 16px 0 44px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .search-input::placeholder {
            color: var(--muted);
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        /* ========== STATS ========== */
        .hero-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            background: var(--primary-light);
            color: var(--primary);
            flex-shrink: 0;
            font-size: 1rem;
        }

        .stat strong {
            display: block;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .stat small {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 2px;
            display: block;
        }

        /* ========== CATEGORY BAR ========== */
        .category-bar {
            background: rgba(14,12,10,0.95);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(16px);
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 12px 0;
        }

        .categories-scroll {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            -ms-overflow-style: none;
            scrollbar-width: none;
            padding: 8px 0;
        }

        .categories-scroll::-webkit-scrollbar {
            display: none;
        }

        .cat-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--card);
            color: var(--muted);
            flex-shrink: 0;
        }

        .cat-btn:hover {
            background: var(--card-hover);
            color: var(--text);
        }

        .cat-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 16px var(--primary-glow);
        }

        /* ========== SECTIONS ========== */
        .section {
            padding: 48px 0;
            border-bottom: 1px solid var(--border);
        }

        .section:nth-child(even) {
            background: rgba(30,26,21,0.4);
        }

        .section-header {
            margin-bottom: 32px;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 999px;
            margin-bottom: 10px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .section-sub {
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* ========== GRID ========== */
        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        }

        .grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        @media (min-width: 768px) {
            .grid {
                gap: 20px;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            .grid-2 {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* ========== PRODUCT CARD ========== */
        .product-card {
            position: relative;
            background: var(--card);
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.4);
        }

        .product-img {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            background: rgba(0,0,0,0.2);
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .product-card:hover .product-img img {
            transform: scale(1.08);
        }

        .product-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--primary);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 999px;
        }

        .product-body {
            padding: 12px;
        }

        .product-local {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .product-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 8px;
        }

        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .product-price {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
        }

        .product-popular {
            font-size: 0.6rem;
            font-weight: 600;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 6px;
            border-radius: 999px;
        }

        /* ========== LOCAL CARD ========== */
        .local-card {
            background: var(--card);
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .local-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.4);
        }

        .local-img-wrap {
            position: relative;
            aspect-ratio: 16/9;
            overflow: hidden;
        }

        .local-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .local-card:hover .local-img {
            transform: scale(1.06);
        }

        .local-status {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(61,168,122,0.9);
            color: #fff;
            backdrop-filter: blur(8px);
        }

        .local-logo-wrap {
            position: absolute;
            bottom: -16px;
            left: 12px;
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 3px solid var(--card);
            background: var(--card);
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }

        .local-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .local-body {
            padding: 28px 12px 12px;
        }

        .local-name {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .local-desc {
            font-size: 0.75rem;
            color: var(--muted);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .local-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.75rem;
            color: var(--muted);
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .meta-item i {
            color: var(--primary);
            font-size: 0.9rem;
        }

        .btn-ver-menu {
            width: 100%;
            padding: 10px 0;
            background: var(--primary);
            color: #fff;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-ver-menu:hover {
            background: #c06830;
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .btn-ver-menu:active {
            transform: scale(0.98);
        }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 2.5rem;
            opacity: 0.3;
            margin-bottom: 12px;
        }

        /* ========== FOOTER ========== */
        .footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 40px 0 20px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--muted);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 480px) {
            .plaza-header {
                padding: 10px 0;
            }

            .header-logo {
                font-size: 0.95rem;
            }

            .btn-auth {
                padding: 6px 10px;
                font-size: 0.75rem;
            }

            .user-info {
                font-size: 0.8rem;
            }

            .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .hero-subtitle {
                font-size: 0.9rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div id="plaza-app" class="plaza-app">
        <!-- HEADER STICKY -->
        <header class="plaza-header">
            <div class="container">
                <div class="header-inner">
                    <div class="header-logo">
                        <span class="logo-dot"></span>
                        Plaza Gastronómica
                    </div>
                    <div class="header-auth">
                        @auth
                            <!-- User Menu cliente-->
                            <div class="user-menu-top" style="position: relative;">
                                <button class="user-menu-btn" style="background: none; border: 2px solid var(--primary); cursor: pointer; padding: 8px 12px; color: var(--primary); border-radius: 8px; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500;">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset(auth()->user()->avatar) }}" alt="Avatar" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <i class="fas fa-user-circle" style="font-size: 24px; color: var(--primary);"></i>
                                    @endif
                                    <span class="user-role-label" style="font-size: 13px; color: var(--text); font-weight: 600;">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                                </button>
                                <div class="user-menu-dropdown" style="position: absolute; top: 100%; right: 0; margin-top: 8px; background: var(--card); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); min-width: 220px; z-index: 1000; display: none;">
                                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); font-size: 13px;">
                                        <div style="font-weight: 600; color: var(--text);">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                                        <div style="color: var(--muted); font-size: 12px; margin-top: 4px;">{{ auth()->user()->email }}</div>
                                    </div>

                                    <a href="{{ route('client.profile.edit') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: var(--text); text-decoration: none; border-bottom: 1px solid var(--border-color);">
                                        <i class="fas fa-user-edit" style="color: var(--muted); font-size: 14px;"></i>
                                        <span>Editar perfil</span>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" style="width: 100%; display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: #ff6b6b; text-decoration: none; border: none; background: none; cursor: pointer; font-size: 13px;">
                                            <i class="fas fa-sign-out-alt" style="font-size: 14px;"></i>
                                            <span>Cerrar sesión</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-auth btn-login">
                                <i class="fas fa-sign-in-alt"></i>
                                Iniciar Sesión
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- HERO -->
        <section class="hero">
            <div class="hero-bg">
                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=1920&h=1080&fit=crop" alt="Plaza Gastronómica">
            </div>
            <div class="hero-overlay"></div>
            
            <div class="container hero-content">
                <div class="hero-badge">
                    La Comarca Gastro Parck
                </div>
                
                <h1 class="hero-title">
                    Descubre los Mejores<br>
                    <em>Sabores</em>
                </h1>
                
                <p class="hero-subtitle">
                    Explora {{ $stats['total_locales'] }} locales con los platillos más deliciosos.<br>
                    Desde hamburguesas hasta sushi, encuentra tu antojo perfecto.
                </p>

                <form class="search-wrap" @submit.prevent="performSearch" method="GET">
                    <i class="fas fa-search search-icon"></i>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Buscar local o platillo..."
                        class="search-input"
                    >
                </form>

                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-icon">
                            <i class="fas fa-store"></i>
                        </span>
                        <div>
                            <strong>{{ $stats['total_locales'] }}</strong>
                            <small>Locales</small>
                        </div>
                    </div>
                    <div class="stat">
                        <span class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div>
                            <strong>{{ $stats['horario_apertura'] }} – {{ $stats['horario_cierre'] }}</strong>
                            <small>Horario</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CATEGORIES BAR -->
        <div class="category-bar">
            <div class="container">
                <div class="categories-scroll">
                    <a href="{{ route('plaza.index') }}" 
                       class="cat-btn {{ $categoria_actual === 'todos' ? 'active' : '' }}">
                        <i class="fas fa-th"></i>
                        Todos
                    </a>
                    @foreach($categorias as $cat)
                    <a href="{{ route('plaza.index', ['categoria' => $cat['slug']]) }}"
                       class="cat-btn {{ $categoria_actual === $cat['slug'] ? 'active' : '' }}">
                        <i class="fas {{ $cat['icono'] }}"></i>
                        {{ $cat['nombre'] }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- PRODUCTOS ALEATORIOS -->
        @if($productos->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <div class="section-badge">
                        <i class="fas fa-fire"></i>
                        Lo Más Buscado
                    </div>
                    <h2 class="section-title">Platillos Destacados</h2>
                    <p class="section-sub">Los favoritos de nuestros clientes de todos nuestros locales</p>
                </div>

                <div class="grid">
                    @foreach($productos as $producto)
                    <div class="product-card">
                        <div class="product-img">
                            <img src="{{ $producto->photo_url ?? asset('images/product-placeholder.jpg') }}" 
                                 alt="{{ $producto->name }}">
                            <span class="product-badge">Popular</span>
                        </div>
                        <div class="product-body">
                            <div class="product-local">
                                {{ $producto->locals->first()?->name ?? 'Local' }}
                            </div>
                            <h3 class="product-name" title="{{ $producto->name }}">
                                {{ $producto->name }}
                            </h3>
                            <div class="product-footer">
                                <span class="product-price">${{ number_format($producto->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- NUESTROS LOCALES -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Nuestros Locales</h2>
                    <p class="section-sub">Explora los mejores restaurantes de la plaza</p>
                </div>

                @if($locales->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No se encontraron locales disponibles</p>
                </div>
                @else
                <div class="grid grid-2">
                    @foreach($locales as $local)
                    <article class="local-card">
                        <div class="local-img-wrap">
                            <img src="{{ $local['imagen'] }}" alt="{{ $local['nombre'] }}" class="local-img">
                            <span class="local-status">
                                {{ $local['estado'] === 'abierto' ? '🟢 Abierto' : '🔴 Cerrado' }}
                            </span>
                        </div>
                        <div class="local-body">
                            <h3 class="local-name">{{ $local['nombre'] }}</h3>
                            <p class="local-desc">{{ $local['descripcion'] }}</p>
                            <div class="local-meta">
                                <span class="meta-item">
                                    <i class="fas fa-star"></i>
                                    {{ $local['calificacion'] }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $local['tiempo_entrega'] }}
                                </span>
                            </div>
                            <a href="{{ route('plaza.show', $local['id']) }}" class="btn-ver-menu">
                                Ver Menú
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="footer">
            <div class="container">
                <p>&copy; {{ date('Y') }} La Comarca Gastro Park. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>

    <script>
        // Toggle User Menu
        function toggleUserMenu(event) {
            event.stopPropagation();
            const btn = event.target.closest('.user-menu-btn');
            const dropdown = btn?.nextElementSibling;
            
            if (dropdown) {
                const isOpen = dropdown.style.display === 'block';
                dropdown.style.display = isOpen ? 'none' : 'block';
            }
        }

        // Cerrar menú al hacer click fuera
        document.addEventListener('click', function(event) {
            const menuTop = event.target.closest('.user-menu-top');
            if (!menuTop) {
                document.querySelectorAll('.user-menu-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });

        // Inicializar el menú de usuario al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuBtn = document.querySelector('.user-menu-btn');
            if (userMenuBtn) {
                userMenuBtn.addEventListener('click', toggleUserMenu);
            }
        });

        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    searchQuery: ''
                };
            },
            methods: {
                performSearch() {
                    if (this.searchQuery.trim()) {
                        // Por ahora: redirigir a búsqueda futura
                        console.log('Búsqueda:', this.searchQuery);
                    }
                }
            }
        }).mount('#plaza-app');
    </script>
</body>
</html>
