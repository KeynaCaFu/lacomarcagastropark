<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $local->name }} - La Comarca Gastro Park</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary:       #D4773A;
            --primary-light: rgba(212,119,58,0.15);
            --primary-glow:  rgba(212,119,58,0.25);
            --bg:            #0A0908;
            --surface:       #111009;
            --card:          #161310;
            --card-hover:    #1D1914;
            --border:        #252018;
            --border-light:  #302820;
            --text:          #F5F0E8;
            --muted:         #7A7060;
            --radius:        14px;
            --radius-sm:     8px;
        }

        html, body { width: 100%; min-height: 100%; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        img { display: block; max-width: 100%; }
        a { text-decoration: none; color: inherit; }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ── HEADER ─────────────────────────────── */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10,9,8,0.96);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--card);
            border: 1px solid var(--border-light);
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-back:hover { background: var(--card-hover); color: var(--text); }
        .btn-back i { font-size: 0.7rem; }

        .header-label {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: var(--text);
        }

        /* auth user menu */
        .user-menu-top { position: relative; }
        .user-menu-btn {
            background: none;
            border: 1px solid var(--border-light);
            cursor: pointer;
            padding: 7px 12px;
            color: var(--primary);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
        }
        .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
            min-width: 190px;
            z-index: 1000;
            display: none;
            overflow: hidden;
        }
        .user-menu-dropdown.open { display: block; }
        .dropdown-header {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
        }
        .dropdown-name { font-weight: 600; font-size: 0.8rem; color: var(--text); }
        .dropdown-email { font-size: 0.7rem; color: var(--muted); margin-top: 2px; }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            font-size: 0.78rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
            cursor: pointer;
            background: none;
            border-left: none;
            border-right: none;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
        }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: var(--card-hover); }
        .dropdown-item.danger { color: #e05c5c; }

        /* ── HERO ────────────────────────────────── */
        .local-hero {
            position: relative;
            height: 35vh;
            min-height: 280px;
            max-height: 380px;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
        }
        .hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.55);
            transition: transform 8s ease;
        }
        .local-hero:hover .hero-bg img { transform: scale(1.04); }

        .hero-gradient {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to top,  var(--bg) 0%, rgba(10,9,8,0.7) 35%, transparent 70%),
                linear-gradient(to right, rgba(10,9,8,0.4) 0%, transparent 60%);
        }

        .hero-body {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 24px 20px;
            display: flex;
            align-items: flex-end;
            gap: 14px;
        }

        .hero-logo-ring {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid rgba(212,119,58,0.6);
            box-shadow: 0 6px 24px rgba(0,0,0,0.5), 0 0 0 3px rgba(212,119,58,0.1);
            flex-shrink: 0;
        }
        .hero-logo-ring img { width: 100%; height: 100%; object-fit: cover; }

        .hero-text { flex: 1; min-width: 0; }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--primary-light);
            border: 1px solid var(--primary-glow);
            color: var(--primary);
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 3px 8px;
            border-radius: 999px;
            margin-bottom: 6px;
        }

        .hero-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.05;
            color: var(--text);
            margin-bottom: 4px;
        }

        .hero-desc {
            font-size: 0.75rem;
            color: rgba(245,240,232,0.55);
            line-height: 1.4;
        }

        /* ── CATEGORY BAR ────────────────────────── */
        .cat-strip {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            position: sticky;
            top: 57px;
            z-index: 90;
        }

        .cat-scroll {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .cat-scroll::-webkit-scrollbar { display: none; }

        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 500;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
            flex-shrink: 0;
        }
        .cat-pill:hover { color: var(--text); border-color: var(--border-light); background: var(--card); }
        .cat-pill.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 4px 16px var(--primary-glow);
        }

        /* ── SECTION INTRO ───────────────────────── */
        .menu-section {
            padding: 52px 0 80px;
        }

        .menu-intro {
            margin-bottom: 40px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .menu-intro-left {}

        .section-eyebrow {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .section-heading {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.1;
        }

        .section-heading em { font-style: italic; color: var(--primary); }

        .item-count {
            font-size: 0.78rem;
            color: var(--muted);
            padding: 6px 12px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 999px;
        }

        /* ── PRODUCT GRID ────────────────────────── */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2px;
        }

        @media (min-width: 640px)  { .products-grid { grid-template-columns: repeat(2, 1fr); gap: 2px; } }
        @media (min-width: 900px)  { .products-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1100px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }

        /* ── PRODUCT CARD ────────────────────────── */
        .p-card {
            position: relative;
            background: var(--card);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.35s cubic-bezier(.22,.68,0,1.2), box-shadow 0.35s;
            border: 1px solid var(--border);
        }

        .p-card:hover {
            transform: translateY(-6px) scale(1.015);
            box-shadow: 0 20px 48px rgba(0,0,0,0.55), 0 0 0 1px var(--primary-glow);
            z-index: 2;
        }

        .p-card-img {
            position: relative;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #0d0b08;
        }

        .p-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(.22,.68,0,1.2), filter 0.4s;
            filter: brightness(0.9);
        }

        .p-card:hover .p-card-img img {
            transform: scale(1.1);
            filter: brightness(1);
        }

        /* shine overlay on hover */
        .p-card-img::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s;
        }
        .p-card:hover .p-card-img::after { opacity: 1; }

        /* bottom gradient on image */
        .p-card-img-fade {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(10,9,8,0.85) 0%, transparent 100%);
            pointer-events: none;
        }

        /* category badge top-left */
        .p-card-cat {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 4px 9px;
            border-radius: 999px;
            background: rgba(10,9,8,0.75);
            border: 1px solid rgba(212,119,58,0.4);
            color: var(--primary);
            backdrop-filter: blur(8px);
        }

        .p-card-body {
            padding: 16px 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
        }

        /* top rule */
        .p-card-body::before {
            content: '';
            position: absolute;
            top: 0; left: 16px; right: 16px;
            height: 1px;
            background: linear-gradient(to right, var(--primary-glow), transparent);
        }

        .p-card-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .p-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .p-card-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
        }

        .p-card-price sup {
            font-size: 0.7em;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            vertical-align: super;
            margin-right: 1px;
            opacity: 0.8;
        }

        .p-card-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--primary-light);
            border: 1px solid var(--primary-glow);
            color: var(--primary);
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .p-card:hover .p-card-icon {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        /* ── FEATURED CARD (first item, full width) ── */
        .p-card.featured {
            grid-column: 1 / 3;
            display: flex;
            flex-direction: row;
        }

        @media (max-width: 640px) {
            .p-card.featured { 
                grid-column: 1 / -1;
                flex-direction: column; 
            }
        }

        .p-card.featured .p-card-img {
            flex: 0 0 40%;
            aspect-ratio: unset;
            min-height: 200px;
        }

        .p-card.featured .p-card-body {
            flex: 1;
            justify-content: center;
            padding: 20px 20px;
        }

        .p-card.featured .p-card-name {
            font-size: 1.4rem;
            -webkit-line-clamp: 2;
        }

        .p-card.featured .p-card-price {
            font-size: 1.6rem;
        }

        .featured-label {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .featured-desc {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.6;
            margin-top: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ── EMPTY STATE ─────────────────────────── */
        .empty-wrap {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
        }

        .empty-icon {
            font-size: 3.5rem;
            opacity: 0.15;
            margin-bottom: 16px;
        }

        .empty-msg {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            opacity: 0.5;
        }

        /* ── FOOTER ──────────────────────────────── */
        .site-footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 56px 0 24px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 36px;
            margin-bottom: 36px;
        }

        .footer-col h4 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .footer-col h4 i { color: var(--primary); font-size: 0.95rem; }

        .footer-col p {
            font-size: 0.82rem;
            color: var(--muted);
            line-height: 1.9;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .footer-col p i { color: var(--primary); width: 14px; text-align: center; }

        .social-row {
            display: flex;
            gap: 12px;
            margin-top: 14px;
        }

        .social-btn {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            background: var(--primary-light);
            color: var(--primary);
            font-size: 1rem;
            transition: all 0.2s;
            border: 1px solid var(--primary-glow);
        }
        .social-btn:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px var(--primary-glow);
        }

        .footer-copy {
            border-top: 1px solid var(--border);
            padding-top: 20px;
            text-align: center;
            font-size: 0.75rem;
            color: var(--muted);
        }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 480px) {
            .hero-name { font-size: 1.8rem; }
            .section-heading { font-size: 1.6rem; }
            .p-card-name { font-size: 1rem; }
            .footer-grid { grid-template-columns: 1fr; gap: 24px; }
            .footer-col { text-align: center; }
            .footer-col h4 { justify-content: center; }
            .footer-col p { justify-content: center; }
            .social-row { justify-content: center; }
        }
    </style>
</head>
<body>
<div id="plaza-app">

    <!-- ── HEADER ── -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <a href="{{ route('plaza.index') }}" class="btn-back">
                    <i class="fas fa-chevron-left"></i> Atrás
                </a>
                <span class="header-label">Menú</span>
                <div>
                    @auth
                        <div class="user-menu-top">
                            <button class="user-menu-btn" id="userMenuBtn">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset(auth()->user()->avatar) }}" alt="" style="width:18px;height:18px;border-radius:50%;object-fit:cover;">
                                @else
                                    <i class="fas fa-user-circle" style="font-size:17px;"></i>
                                @endif
                                <span style="color:var(--text);font-size:0.72rem;">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                            </button>
                            <div class="user-menu-dropdown" id="userMenuDropdown">
                                <div class="dropdown-header">
                                    <div class="dropdown-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                                    <div class="dropdown-email">{{ auth()->user()->email }}</div>
                                </div>
                                <a href="{{ route('client.profile.edit') }}" class="dropdown-item">
                                    <i class="fas fa-user-edit" style="color:var(--muted);"></i> Editar perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="dropdown-item danger">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border:1px solid var(--border-light);border-radius:var(--radius-sm);font-size:0.78rem;color:var(--primary);">
                            <i class="fas fa-sign-in-alt"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- ── HERO ── -->
    <section class="local-hero">
        <div class="hero-bg">
            <img src="{{ $local->logo_url ?? 'https://via.placeholder.com/1200x600/111009/D4773A?text=' . urlencode($local->name) }}" alt="{{ $local->name }}">
        </div>
        <div class="hero-gradient"></div>
        <div class="hero-body container">
            @if($local->logo_url)
            <div class="hero-logo-ring">
                <img src="{{ $local->logo_url }}" alt="{{ $local->name }}">
            </div>
            @endif
            <div class="hero-text">
                <div class="hero-tag"><i class="fas fa-utensils"></i> La Comarca Gastro Park</div>
                <h1 class="hero-name">{{ $local->name }}</h1>
                @if($local->description)
                    <p class="hero-desc">{{ $local->description }}</p>
                @endif
            </div>
        </div>
    </section>

    <!-- ── CATEGORY STRIP ── -->
    @if($categorias->isNotEmpty())
    <div class="cat-strip">
        <div class="container">
            <div class="cat-scroll">
                <button class="cat-pill" :class="{ active: activeCategory === null }" @click="activeCategory = null">
                    <i class="fas fa-border-all"></i> Todos
                </button>
                @foreach($categorias as $cat)
                <button
                    class="cat-pill"
                    :class="{ active: activeCategory === '{{ $cat['slug'] }}' }"
                    @click="activeCategory = '{{ $cat['slug'] }}'">
                    <i class="fas {{ $cat['icono'] }}"></i>
                    {{ $cat['nombre'] }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ── PRODUCTS ── -->
    <main class="menu-section">
        <div class="container">
            <div class="menu-intro">
                <div class="menu-intro-left">
                    <p class="section-eyebrow">Nuestro Menú</p>
                    <h2 class="section-heading">Lo Mejor de <em>{{ $local->name }}</em></h2>
                </div>
                @if($productos->isNotEmpty())
                <span class="item-count">{{ $productos->count() }} platillos</span>
                @endif
            </div>

            @if($productos->isEmpty())
                <div class="empty-wrap">
                    <div class="empty-icon"><i class="fas fa-bowl-food"></i></div>
                    <p class="empty-msg">No hay platillos disponibles por el momento</p>
                </div>
            @else
                <div class="products-grid">
                    @foreach($productos as $i => $producto)
                    <div class="p-card {{ $i === 0 ? 'featured' : '' }}"
                         v-show="activeCategory === null || '{{ Str::slug($producto->category) }}' === activeCategory">

                        <div class="p-card-img">
                            <img src="{{ $producto->photo_url ?? asset('images/product-placeholder.png') }}"
                                 alt="{{ $producto->name }}"
                                 loading="{{ $i < 4 ? 'eager' : 'lazy' }}">
                            <div class="p-card-img-fade"></div>
                            @if($producto->category)
                            <span class="p-card-cat">{{ $producto->category }}</span>
                            @endif
                        </div>

                        <div class="p-card-body">
                            @if($i === 0)
                                <p class="featured-label"><i class="fas fa-crown"></i> &nbsp;Destacado</p>
                            @endif
                            <h3 class="p-card-name">{{ $producto->name }}</h3>
                            @if($i === 0 && $producto->description)
                                <p class="featured-desc">{{ $producto->description }}</p>
                            @endif
                            <div class="p-card-footer">
                                <span class="p-card-price">
                                    <sup>₡</sup>{{ number_format($producto->price, 2) }}
                                </span>
                                <span class="p-card-icon">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    <!-- ── FOOTER ── -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4><i class="fas fa-clock"></i> Horario</h4>
                    <p><strong>Lunes a Viernes</strong></p>
                    <p>12:00 PM – 12:00 AM</p>
                    <p style="margin-top:10px;"><strong>Sábado y Domingo</strong></p>
                    <p>11:00 AM – 2:00 AM</p>
                </div>
                <div class="footer-col">
                    <h4><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                    <p>
                        <i class="fas fa-location-dot"></i>
                        <a href="https://maps.app.goo.gl/UYkQZhrKbVnTKgWj8?g_st=aw" target="_blank" rel="noopener" style="color:var(--primary);text-decoration:underline;">La Comarca Gastro Park</a>
                    </p>
                    <p>
                        <i class="fas fa-map"></i>
                        <a href="https://maps.app.goo.gl/UYkQZhrKbVnTKgWj8?g_st=aw" target="_blank" rel="noopener" style="color:var(--muted);text-decoration:underline;">Guápiles, Limón, Costa Rica</a>
                    </p>
                    <p style="margin-top:10px;"><i class="fas fa-phone"></i> +506 8888 8888</p>
                    <p><i class="fas fa-envelope"></i> info@lacomarcagastropark.com</p>
                </div>
                <div class="footer-col">
                    <h4><i class="fas fa-share-alt"></i> Síguenos</h4>
                    <p style="margin-bottom:4px;">Conecta con nosotros</p>
                    <div class="social-row">
                        <a class="social-btn" href="https://www.facebook.com/share/1CYem5AGeo/" target="_blank" rel="noopener" aria-label="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a class="social-btn" href="https://www.instagram.com/la.comarcagastropark?igsh=bW43MHB0OG9yMG8y" target="_blank" rel="noopener" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="social-btn" href="https://www.tiktok.com/@la.comarcagastropark?_t=ZM-8z8TOSBnnGv&_r=1" target="_blank" rel="noopener" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer-copy">
                &copy; 2026 La Comarca Gastro Park. Todos los derechos reservados.
            </div>
        </div>
    </footer>

</div>

<script>
    // User menu toggle
    const menuBtn = document.getElementById('userMenuBtn');
    const menuDropdown = document.getElementById('userMenuDropdown');

    if (menuBtn && menuDropdown) {
        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            menuDropdown.classList.toggle('open');
        });
        document.addEventListener('click', () => {
            menuDropdown.classList.remove('open');
        });
    }

    // Vue app
    const { createApp } = Vue;
    createApp({
        data() {
            return { activeCategory: null };
        }
    }).mount('#plaza-app');
</script>
</body>
</html>