<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $local->name }} -  La Comarca Gastro Parck</title>
    
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

        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* ========== HEADER CON BACK ========== */
        .header-top {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(14,12,10,0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
        }

        .header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(30,26,21,0.85);
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 0.85rem;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-back:hover {
            background: var(--card-hover);
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

        .header-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
        }

        /* User Menu Dropdown */
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 8px;
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
            width: 28px;
            height: 28px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .user-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--surface-dark);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            min-width: 180px;
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
            padding: 10px 14px;
            color: var(--text);
            text-decoration: none;
            font-size: 0.8rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 8px;
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
            padding: 10px 14px;
            text-align: left;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 8px;
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

        /* ========== LOCAL HERO ========== */
        .local-hero {
            position: relative;
            height: 50vh;
            min-height: 280px;
            overflow: hidden;
            margin-bottom: 0;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
        }

        .hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, var(--bg) 0%, rgba(14,12,10,0.6) 50%, rgba(14,12,10,0.2) 100%);
        }

        .hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px 16px;
            z-index: 10;
            display: flex;
            align-items: flex-end;
            gap: 16px;
        }

        .hero-logo {
            width: 70px;
            height: 70px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 3px solid var(--card);
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        }

        .hero-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-info {
            flex: 1;
            min-width: 0;
        }

        .local-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--text);
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .local-desc {
            font-size: 0.8rem;
            color: var(--muted);
        }

        /* ========== CONTENT SECTION ========== */
        .content-section {
            padding: 32px 0;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 20px;
        }

        /* ========== CATEGORY BAR ========== */
        .category-bar {
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .categories-scroll {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .categories-scroll::-webkit-scrollbar {
            display: none;
        }

        .cat-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 500;
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
        }

        /* ========== PRODUCT GRID ========== */
        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, 1fr);
        }

        @media (min-width: 768px) {
            .grid {
                gap: 20px;
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* ========== PRODUCT CARD ========== */
        .product-card {
            background: var(--card);
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.3s, box-shadow 0.3s;
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

        .product-body {
            padding: 12px;
        }

        .product-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .product-price {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 3rem;
            opacity: 0.3;
            margin-bottom: 16px;
        }

        /* ========== FOOTER ========== */
        .footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 48px 0 20px;
            color: var(--text);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 32px;
            margin-bottom: 32px;
        }

        .footer-section {
            text-align: left;
        }

        .footer-section h3 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-section h3 i {
            color: var(--primary);
            font-size: 1.1rem;
        }

        .footer-section p {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.8;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-section p i {
            color: var(--primary);
            width: 16px;
            text-align: center;
        }

        .footer-section p:first-of-type {
            margin-top: 0;
        }

        .social-icons {
            display: flex;
            gap: 16px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            background: rgba(212, 119, 58, 0.1);
            color: var(--primary);
            transition: all 0.2s;
            font-size: 1.1rem;
        }

        .social-icons a:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
        }

        .copyright {
            text-align: center;
            border-top: 1px solid var(--border);
            padding-top: 20px;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .copyright p {
            margin: 0;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 480px) {
            .hero-logo {
                width: 60px;
                height: 60px;
            }

            .local-name {
                font-size: 1.3rem;
            }

            .section-title {
                font-size: 1.2rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .footer-section {
                text-align: center;
            }

            .footer-section h3 {
                justify-content: center;
            }

            .footer-section p {
                justify-content: center;
            }

            .social-icons {
                justify-content: center;
            }

            .copyright {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div id="plaza-app">
        <!-- HEADER CON BACK -->
        <header class="header-top">
            <div class="container">
                <div class="header-flex">
                    <a href="{{ route('plaza.index') }}" class="btn-back">
                        <i class="fas fa-chevron-left"></i>
                        Atrás
                    </a>
                    <h1 class="header-title">Menú</h1>
                    <div class="header-auth" style="width: auto;">
                        @auth
                            <!-- User Menu (igual al admin, compacto para mobile) -->
                            <div class="user-menu-top" style="position: relative;">
                                <button class="user-menu-btn" style="background: none; border: 2px solid var(--primary); cursor: pointer; padding: 6px 10px; color: var(--primary); border-radius: 6px; display: flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 500;">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset(auth()->user()->avatar) }}" alt="Avatar" style="width: 18px; height: 18px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <i class="fas fa-user-circle" style="font-size: 18px; color: var(--primary);"></i>
                                    @endif
                                    <span class="user-role-label" style="font-size: 0.7rem; color: var(--text); font-weight: 600;">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                                </button>
                                <div class="user-menu-dropdown" style="position: absolute; top: 100%; right: 0; margin-top: 6px; background: var(--card); border: 1px solid var(--border-color); border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); min-width: 180px; z-index: 1000; display: none;">
                                    <div style="padding: 10px 12px; border-bottom: 1px solid var(--border-color); font-size: 12px;">
                                        <div style="font-weight: 600; color: var(--text); font-size: 0.8rem;">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                                        <div style="color: var(--muted); font-size: 0.7rem; margin-top: 2px;">{{ auth()->user()->email }}</div>
                                    </div>

                                    <a href="{{ route('client.profile.edit') }}" style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; color: var(--text); text-decoration: none; border-bottom: 1px solid var(--border-color); font-size: 0.75rem;">
                                        <i class="fas fa-user-edit" style="color: var(--muted); font-size: 12px;"></i>
                                        <span>Editar perfil</span>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" style="width: 100%; display: flex; align-items: center; gap: 8px; padding: 10px 12px; color: #ff6b6b; text-decoration: none; border: none; background: none; cursor: pointer; font-size: 0.75rem;">
                                            <i class="fas fa-sign-out-alt" style="font-size: 12px;"></i>
                                            <span>Cerrar sesión</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn-auth btn-login" style="font-size: 0.75rem; padding: 6px 10px;">
                                <i class="fas fa-sign-in-alt"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- HERO LOCAL -->
        <section class="local-hero">
            <div class="hero-bg">
                
            </div>
            <div class="hero-overlay"></div>
            <div class="hero-content">
                @if($local->image_logo)
                <div class="hero-logo">
                    <img src="{{ $local->image_logo }}" alt="">
                </div>
                @endif
                <div class="hero-info">
                    <h1 class="local-name">{{ $local->name }}</h1>
                    <p class="local-desc">{{ $local->description }}</p>
                </div>
            </div>
        </section>

        <!-- CONTENIDO -->
        <div class="container">
            <!-- CATEGORÍAS -->
            @if($categorias->isNotEmpty())
            <div class="content-section">
                <div class="category-bar">
                    <div class="categories-scroll">
                        <button class="cat-btn active" @click="activeCategory = null" v-if="activeCategory !== null">
                            <i class="fas fa-th"></i>
                            Todos
                        </button>
                        @foreach($categorias as $cat)
                        <button class="cat-btn" @click="activeCategory = '{{ $cat['slug'] }}'">
                            <i class="fas {{ $cat['icono'] }}"></i>
                            {{ $cat['nombre'] }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- PRODUCTOS -->
            <div class="content-section">
                @if($productos->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Este local no tiene productos disponibles</p>
                </div>
                @else
                <div class="grid">
                    @foreach($productos as $producto)
                    <div class="product-card" 
                         v-show="activeCategory === null || '{{ Str::slug($producto->category) }}' === activeCategory"
                         data-category="{{ Str::slug($producto->category) }}">
                        <div class="product-img">
                            <img src="{{ $producto->photo_url ?? asset('images/product-placeholder.jpg') }}" 
                                 alt="{{ $producto->name }}">
                        </div>
                        <div class="product-body">
                            <h3 class="product-name" title="{{ $producto->name }}">
                                {{ $producto->name }}
                            </h3>
                            <div class="product-info">
                                <span class="product-price">${{ number_format($producto->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- FOOTER -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-section">
                        <h3><i class="fas fa-clock"></i> Horario</h3>
                        <p><strong>Lunes a Viernes</strong></p>
                        <p>12:00 PM - 12:00 AM</p>
                        <p style="margin-top: 12px;"><strong>Sábado a Domingo</strong></p>
                        <p>11:00 AM - 2:00 AM</p>
                    </div>
                    <div class="footer-section">
                        <h3><i class="fas fa-map-marker-alt"></i> Ubicación</h3>
                        <p><i class="fas fa-location-dot"></i> <a href="https://maps.app.goo.gl/UYkQZhrKbVnTKgWj8?g_st=aw" target="_blank" rel="noopener noreferrer" style="color: var(--primary); text-decoration: underline; transition: color 0.2s;" onmouseover="this.style.color='#c06830'" onmouseout="this.style.color='var(--primary)'">La Comarca Gastro Park</a></p>
                        <p><i class="fas fa-map"></i> <a href="https://maps.app.goo.gl/UYkQZhrKbVnTKgWj8?g_st=aw" target="_blank" rel="noopener noreferrer" style="color: var(--muted); text-decoration: underline; transition: color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--muted)'">Guápiles, Limón, Costa Rica</a></p>
                        <p style="margin-top: 12px;"><i class="fas fa-phone"></i> +506 8888 8888</p>
                        <p><i class="fas fa-envelope"></i> info@lacomarcagastropark.com</p>
                    </div>
                    <div class="footer-section">
                        <h3><i class="fas fa-share-alt"></i> Síguenos</h3>
                        <p style="color: var(--text); margin-bottom: 12px;">Conecta con nosotros en redes sociales</p>
                        <div class="social-icons">
                            <a href="https://www.facebook.com/share/1CYem5AGeo/" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            title="Facebook"
                            aria-label="Síguenos en Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="https://www.instagram.com/la.comarcagastropark?igsh=bW43MHB0OG9yMG8y" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            title="Instagram"
                            aria-label="Síguenos en Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.tiktok.com/@la.comarcagastropark?_t=ZM-8z8TOSBnnGv&_r=1" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            title="TikTok"
                            aria-label="Síguenos en TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="copyright">
                    <p>&copy; 2026 La Comarca Gastro Park. Todos los derechos reservados.</p>
                </div>
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
                    activeCategory: null
                };
            }
        }).mount('#plaza-app');
    </script>
</body>
</html>
