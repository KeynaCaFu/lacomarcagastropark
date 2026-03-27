<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $local->name }} - Plaza Gastronómica</title>
    
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

        .header-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
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
            padding: 40px 0 20px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--muted);
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
                    <div style="width: 50px;"></div>
                </div>
            </div>
        </header>

        <!-- HERO LOCAL -->
        <section class="local-hero">
            <div class="hero-bg">
                <img src="{{ $local->gallery->first()?->image_url ?? asset('images/local-placeholder.jpg') }}" 
                     alt="{{ $local->name }}">
            </div>
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-logo">
                    <img src="{{ $local->image_logo ?? asset('images/logo-placeholder.png') }}" 
                         alt="">
                </div>
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
                         v-show="activeCategory === null || '{{ str_slug($producto->category) }}' === activeCategory"
                         data-category="{{ str_slug($producto->category) }}">
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
                <p>&copy; {{ date('Y') }} La Comarca Gastro Park. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>

    <script>
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
