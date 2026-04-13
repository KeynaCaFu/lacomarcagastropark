<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Plaza Gastronómica - La Comarca</title>

    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">

    <!-- Google Fonts with preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.index.css') }}">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
<div id="plaza-app">

    <!-- ══ HEADER ══ -->
    <header class="plaza-header">
        <div class="container">
            <div class="header-inner">
                <div class="header-logo">
                    <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca" class="header-logo-img">
                    <span class="header-logo-text">La Comarca Gastropark</span>
                </div>
                <div class="header-auth">
                    <!-- Calendar Button (visible to all) -->
                    <button @click="openEventsDrawer" class="cart-btn" :style="{ borderColor: showEventsDrawer ? 'var(--primary)' : 'var(--border-light)' }">
                        <i class="fas fa-calendar"></i>
                    </button>
                    
                    @auth
                        <button @click="openCartDrawer" class="cart-btn" :style="{ borderColor: showCartDrawer ? 'var(--primary)' : 'var(--border-light)' }">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count">@{{ totalDrawerQty }}</span>
                        </button>
                        <div class="user-menu-top">
                            <button class="user-menu-btn" id="userMenuBtn">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset(auth()->user()->avatar) }}" alt="" class="avatar-lg">
                                @else
                                    <i class="fas fa-user-circle icon-md"></i>
                                @endif
                                <span class="text-label">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down icon-sm"></i>
                            </button>
                            <div class="user-menu-dropdown" id="userMenuDropdown">
                                <div class="dropdown-header">
                                    <div class="dropdown-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                                    <div class="dropdown-email">{{ auth()->user()->email }}</div>
                                </div>
                                <a href="{{ route('client.profile.edit') }}" class="dropdown-item">
                                    <i class="fas fa-user-edit text-muted"></i> Editar perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item danger">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-auth btn-login">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- ══ ADMIN PREVIEW BANNER ══ -->
    @if(session('plaza_admin_preview'))
    <div class="preview-banner">
        <div class="container">
            <div class="flex-row-space">
                <div class="flex-row">
                    <i class="fas fa-eye icon-lg"></i>
                    <div>
                        <div class="preview-text-main">Vista Previa - Administrador</div>
                        <div class="preview-text-sub">Estás viendo la plaza en modo preview. Tu sesión administrativa se mantiene activa en otro tab.</div>
                    </div>
                </div>
                <button onclick="window.history.back()" class="preview-btn">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- ══ HERO ══ -->
    <section class="hero" id="hero-section">

        <div class="hero-bg">
            <img src="{{ asset('images/fotoindex.webp') }}" alt="La Comarca de noche" id="heroBgImg">
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-scanline"></div>
        <div class="hero-grain"></div>

        <!-- Orbs flotantes -->
        <div class="hero-orbs">
            <div class="orb orb-1" id="orb1">
                <img src="{{ asset('images/favicon.png') }}" alt="Comarca">
            </div>
            <div class="orb orb-2" id="orb2">
                <img src="{{ asset('images/iconoblanco.png') }}" alt="Comarca">
            </div>
            <div class="orb orb-3" id="orb3">
                <img src="{{ asset('images/favicon.png') }}" alt="Comarca">
            </div>
            <div class="orb-arc" id="orbArc"></div>
        </div>

        <!-- Partículas Vue -->
        <div class="hero-particles">
            <div
                v-for="p in particles" :key="p.id"
                class="particle"
                :style="{
                    left: p.x + '%', top: p.y + '%',
                    width: p.size + 'px', height: p.size + 'px',
                    '--dur': p.dur + 's',
                    '--delay': p.delay + 's',
                    '--dx': p.dx + 'px',
                    '--dy': p.dy + 'px',
                    '--op': p.op,
                    opacity: p.op,
                    animationDelay: p.delay + 's',
                    animationDuration: p.dur + 's',
                }">
            </div>
        </div>

        <!-- Contenido -->
        <div class="container hero-content">

            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                Guápiles, Limón &nbsp;·&nbsp; Costa Rica
            </div>

            <h1 class="hero-title">
                Descubre los<br>
                Mejores <em>Sabores</em><br>
                <strong>de La Comarca</strong>
            </h1>

            <p class="hero-subtitle">
                {{ $stats['total_locales'] }} Locales únicos te esperan. Platillos auténticos,
                ambiente inigualable y la magia de comer bajo las estrellas.
            </p>

            <div class="hero-divider">
                <span class="hero-divider-line"></span>
                <span class="hero-divider-icon"><i class="fas fa-utensils"></i></span>
                <span class="hero-divider-line r"></span>
            </div>

            <div class="search-wrap">
                <i class="fas fa-search search-icon"></i>
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Buscar local o platillo..."
                    class="search-input"
                >
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-icon"><i class="fas fa-store"></i></span>
                    <div>
                        <strong>{{ $stats['total_locales'] }}</strong>
                        <small>Locales</small>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                    <span class="stat-icon"><i class="fas fa-clock"></i></span>
                    <div>
                        <strong>{{ $stats['horario_apertura'] }}–{{ $stats['horario_cierre'] }}</strong>
                        <small>Horario</small>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                    <span class="stat-icon"><i class="fas fa-star"></i></span>
                    <div>
                        <strong>{{ $stats['calificacion'] }}</strong>
                        <small>Calificación</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══ CATEGORY BAR ══ -->
    <div class="category-bar" v-cloak>
        <div class="container">
            <div class="category-bar-inner">
                <button
                   @click="filtrarPorCategoria('todos')"
                   :class="['cat-pill', { active: categoriaSelect === 'todos' }]">
                    <i class="fas fa-border-all"></i> Todos
                </button>
                <button
                   v-for="cat in categorias"
                   :key="cat.slug"
                   @click="filtrarPorCategoria(cat.slug)"
                   :class="['cat-pill', { active: categoriaSelect === cat.slug }]">
                    <i :class="['fas', cat.icono]"></i>
                    @{{ cat.nombre }}
                </button>
            </div>
        </div>
    </div>

    <!-- ══ PRODUCTOS FILTRADOS ══ -->
    <section class="filtered-section" v-if="categoriaSelect !== 'todos'" v-cloak>
        <div class="container">
            <div class="filtered-header">
                <div>
                    <p class="filtered-label">
                        <i class="fas fa-filter"></i> Categoría Seleccionada
                    </p>
                    <h2 class="filtered-title">@{{ categoriaSelectNombre || 'Productos Filtrados' }}</h2>
                </div>
                <div class="filtered-count-badge" v-if="!cargandoProductos">
                    <strong>@{{ productosFiltrados.length }}</strong> resultados
                </div>
            </div>

            <!-- Loading skeletons -->
            <div v-if="cargandoProductos" class="skeleton-grid">
                <div class="skeleton-card" v-for="n in 8" :key="n">
                    <div class="skeleton-img"></div>
                    <div class="skeleton-body">
                        <div class="skeleton-line w-40"></div>
                        <div class="skeleton-line w-80"></div>
                        <div class="skeleton-line w-60"></div>
                    </div>
                </div>
            </div>

            <!-- No results -->
            <div v-if="!cargandoProductos && productosFiltrados.length === 0" class="empty-state">
                <i class="fas fa-bowl-food"></i>
                <p>No se encontraron productos en esta categoría</p>
            </div>

            <!-- Grid de productos filtrados -->
            <transition-group
                v-if="!cargandoProductos && productosFiltrados.length > 0"
                name="fade-slide"
                tag="div"
                class="grid-products-filtered">
                <div v-for="producto in productosFiltrados" :key="producto.id" class="product-card-v2">
                    <div class="product-img">
                        <img :src="producto.photo_url" :alt="producto.name" loading="lazy">
                        <div class="product-img-overlay"></div>
                        <span class="product-cat-chip">@{{ producto.category }}</span>
                    </div>
                    <div class="product-body">
                        <div class="product-local">@{{ producto.local }}</div>
                        <h3 class="product-name" :title="producto.name">@{{ producto.name }}</h3>
                        <div class="product-stars stars-container">
                            <i v-for="i in 5" :key="i" class="fas fa-star text-xs" :style="{color: i <= Math.round(producto.average_rating || 0) ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }"></i>
                        </div>
                        <div class="product-footer">
                            <span class="product-price">₡@{{ producto.price }}</span>
                            <a :href="'/plaza/' + producto.local_id" class="btn-link">
                                Ver <i class="fas fa-arrow-right arrow-ml"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </transition-group>
        </div>
    </section>



    <!-- ══ NUESTROS LOCALES ══ -->
    <section class="locales-section">
        <div class="container">
            <div class="locales-header-row">
                <div>
                    <p class="locales-eyebrow">Explora</p>
                    <h2 class="locales-title">Nuestros Locales</h2>
                    <p class="locales-sub">Los mejores restaurantes de la plaza, todos en un solo lugar</p>
                </div>
                <div class="locales-count" aria-hidden="true">
                    <span>{{ $stats['total_locales'] }}</span> locales
                </div>
            </div>

            @if($locales->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-store-slash"></i>
                    <p>No se encontraron locales disponibles</p>
                </div>
            @else
                <div class="grid-locals-v2">
                    @foreach($locales as $local)
                    <article class="local-card-v2">
                        <div class="local-img-wrap-v2">
                            <img src="{{ $local->image_logo ? asset($local->image_logo) : 'https://via.placeholder.com/400x225/171410/D4773A?text=' . urlencode($local->name) }}"
                                 alt="{{ $local->name }}" class="local-img-v2" loading="lazy">
                            <div class="local-img-gradient"></div>
                        </div>
                        <div class="local-body-v2">
                            <div class="flex-between mb-8">
                                <h3 class="local-name-v2">{{ $local->name }}</h3>
                                <span class="meta-chip nowrap">
                                    <span class="status-dot {{ $local->isOpenNow ? 'status-dot-open' : 'status-dot-closed' }}"></span>
                                    {{ $local->isOpenNow ? 'Abierto' : 'Cerrado' }}
                                </span>
                            </div>
                            <p class="local-desc-v2">{{ $local->description ?? 'Explora nuestro menú y descubre sabores únicos' }}</p>
                            <div class="local-stars stars-container-lg">
                                @php
                                    $rating = round($local->average_rating ?? 0);
                                    for ($j = 1; $j <= 5; $j++):
                                @endphp
                                    <i class="fas fa-star" style="color: {{ $j <= $rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }}; font-size: 0.75rem;"></i>
                                @php
                                    endfor;
                                @endphp
                            </div>
                            <a href="{{ route('plaza.show', $local->local_id) }}" class="btn-ver-menu-v2">
                                Ver Menú <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- ══ PRODUCTOS DESTACADOS ══ -->
    @if($productos->isNotEmpty())
    <section class="destacados-section">
        <div class="container">
            <div class="destacados-header">
                <div>
                    <div class="destacados-fire-badge">
                        <i class="fas fa-fire"></i> Lo Más Buscado
                    </div>
                    <h2 class="destacados-title">Productos Destacados</h2>
                    <p class="destacados-sub">Los favoritos de nuestros clientes de todos los locales</p>
                </div>
            </div>

            <div class="grid-destacados">
                @foreach($productos as $producto)
                <div class="destacado-card">
                    <div class="destacado-img">
                        <img src="{{ $producto->photo_url ?? asset('images/product-placeholder.png') }}"
                             alt="{{ $producto->name }}" loading="lazy">
                        <div class="destacado-img-overlay"></div>
                        <span class="destacado-popular-tag">Popular</span>
                    </div>
                    <div class="destacado-body">
                        <div class="destacado-local">
                            {{ $producto->locals->first()?->name ?? 'Local' }}
                        </div>
                        <h3 class="destacado-name" title="{{ $producto->name }}">
                            {{ $producto->name }}
                        </h3>
                        @php
                            $rating = round($producto->average_rating ?? 0);
                        @endphp
                        <div class="stars-container">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-xs" style="color: {{ $i <= $rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }};"></i>
                            @endfor
                        </div>
                        <div class="destacado-footer flex-between">
                            <span class="destacado-price">₡{{ number_format($producto->price, 2) }}</span>
                            <a href="{{ route('plaza.show', $producto->locals->first()?->local_id ?? '#') }}" class="btn-link">
                                Ver <i class="fas fa-arrow-right arrow-ml"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ══ FOOTER ══ -->
    <footer class="footer-v2">
        <!-- Mountains silhouette - separator -->
        <div class="footer-landscape">
            <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,40 C80,100 160,20 240,60 C280,80 300,30 340,50 C380,70 400,35 440,55
                         C480,75 510,25 560,65 C600,95 630,30 680,70 C720,105 750,40 800,75
                         C840,105 870,45 920,80 C960,110 990,50 1040,85 C1080,115 1110,55 1160,90
                         C1200,120 1240,65 1280,95 C1320,120 1360,70 1400,100 L1440,120 L1440,0 L0,0 Z"
                      fill="#0A0908" stroke="none"/>
            </svg>
        </div>

        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <!-- Brand column -->
                    <div>
                        <div class="footer-brand-logo">
                            <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca">
                            <span class="footer-brand-name">La Comarca Gastropark</span>
                        </div>
                        <p class="footer-brand-desc">
                            Un espacio gastronómico único en Guápiles, Limón. Sabores auténticos, ambiente inigualable y la magia de comer bajo las estrellas.
                        </p>
                        <div class="footer-horario-badge">
                            <span class="pulsedot"></span>
                            Mar–Dom &nbsp;·&nbsp; 12:00 MD – 10:00 PM
                        </div>
                    </div>

                    <!-- Contacto column -->
                    <div>
                        <h3 class="footer-col-title">Contáctanos</h3>
                        <div class="footer-contact">
                            <div class="footer-contact-item">
                                <i class="fas fa-location-dot"></i>
                                <span>
                                    <a href="https://maps.app.goo.gl/UYkQZhrKbVnTKgWj8?g_st=aw" target="_blank" rel="noopener">La Comarca Gastro Park</a><br>
                                    Guápiles, Limón, Costa Rica
                                </span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fas fa-phone"></i>
                                <span>+506 8888 8888</span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><a href="mailto:info@lacomarcagastropark.com">info@lacomarcagastropark.com</a></span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fas fa-clock"></i>
                                <span>Lunes: Cerrado<br>Mar–Dom: 12:00 MD – 10:00 PM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Síguenos column -->
                    <div>
                        <h3 class="footer-col-title">Síguenos</h3>
                        <p class="description-text">
                            Conecta con nosotros en redes sociales y mantente al día con nuestros eventos y promociones especiales.
                        </p>
                        <div class="footer-socials">
                            <a href="https://www.facebook.com/share/1CYem5AGeo/" target="_blank" rel="noopener"
                               class="footer-social-btn" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.instagram.com/la.comarcagastropark?igsh=bW43MHB0OG9yMG8y" target="_blank" rel="noopener"
                               class="footer-social-btn" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.tiktok.com/@la.comarcagastropark?_t=ZM-8z8TOSBnnGv&_r=1" target="_blank" rel="noopener"
                               class="footer-social-btn" aria-label="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <hr class="footer-divider">

                <div class="footer-bottom">
                    <p class="footer-copy">&copy; 2026 La Comarca Gastro Park. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- ══ EVENTOS DRAWER ══ -->
    <!-- ══ EVENTS DRAWER ══ -->
    @include('plaza.evento.drawer')

    <!-- ══ CART DRAWER ══ -->
    @include('plaza.carrito._cart_drawer')

    <!-- ═══ DRAWER: EVENTO DETAIL (PANEL LATERAL) ═══ -->
    <div v-if="showEventoDetail" class="evento-detail-overlay" @click="closeEventoDetail"></div>
    <div class="evento-detail-drawer" :class="{ 'active': showEventoDetail }">
        <!-- Header -->
        <div class="evento-detail-header">
            <h2>Detalles del Evento</h2>
            <button @click="closeEventoDetail" class="close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="evento-detail-content">
            <div v-if="currentEvento && currentEvento.event_id" class="evento-detail-body">
                <!-- Image -->
                <div class="evento-detail-image">
                    <img :src="currentEvento.image_url" :alt="currentEvento.title" loading="lazy">
                </div>

                <!-- Info -->
                <div class="evento-detail-info">
                    <h3 class="evento-title">@{{ currentEvento.title }}</h3>
                    
                    <div class="evento-meta-group">
                        <div class="evento-meta-item">
                            <i class="fas fa-clock"></i>
                            <span><strong>Hora:</strong> @{{ new Date(currentEvento.start_at).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}</span>
                        </div>
                        <div class="evento-meta-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Fecha:</strong> @{{ new Date(currentEvento.start_at).toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                        </div>
                        <div class="evento-meta-item">
                            <i class="fas fa-map-pin"></i>
                            <span><strong>Ubicación:</strong> @{{ currentEvento.location }}</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="evento-description">
                        <p>@{{ currentEvento.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ═══ TOAST NOTIFICATIONS (OUTSIDE TEMPLATE) ═══ -->
@include('plaza.carrito._toast-notifications')

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
    // Función helper para mostrar toasts personalizados
    const showToast = (config) => { if (window.showNotification) { window.showNotification(config); } };
    /* ── User menu ── */
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('userMenuBtn');
        const menuDrop = document.getElementById('userMenuDropdown');
        if (menuBtn && menuDrop) {
            menuBtn.addEventListener('click', e => {
                e.stopPropagation();
                menuDrop.classList.toggle('open');
            });
            document.addEventListener('click', () => menuDrop.classList.remove('open'));
        }
    });

    /* ── Vue App ── */
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                searchQuery: '',
                particles: [],
                categorias: {!! json_encode($categorias) !!},
                categoriaSelect: 'todos',
                categoriaSelectNombre: 'Todos',
                productosFiltrados: [],
                cargandoProductos: false,
                // Eventos
                eventosTab: 'hoy',
                eventosHoy: {!! json_encode($eventosHoy) !!},
                eventosProximos: {!! json_encode($eventosProximos) !!},
                showEventsDrawer: false,
                showEventoDetail: false,
                currentEvento: {},
                // Cart drawer data
                showCartDrawer: false,
                showConfirmOrder: false,
                showConfirmClear: false,
                showConfirmRemove: false,
                itemToRemoveIndex: null,
                drawerCart: [],
                isCheckingOut: false
            }
        },

        mounted() {
            this.buildParticles();
            document.addEventListener('mousemove', this.onMouseMove);
            window.addEventListener('scroll', this.onScroll, { passive: true });
            this.loadCartDrawer();
        },

        beforeUnmount() {
            document.removeEventListener('mousemove', this.onMouseMove);
            window.removeEventListener('scroll', this.onScroll);
        },

        methods: {
            buildParticles() {
                for (let i = 0; i < 20; i++) {
                    this.particles.push({
                        id: i,
                        x:     Math.random() * 100,
                        y:     Math.random() * 100,
                        size:  Math.random() * 3 + 1.5,
                        dur:   Math.random() * 9 + 6,
                        delay: Math.random() * 7,
                        dx:    (Math.random() - 0.5) * 70,
                        dy:    (Math.random() - 0.5) * 70,
                        op:    Math.random() * 0.35 + 0.08,
                    });
                }
            },

            onMouseMove(e) {
                const hero = document.getElementById('hero-section');
                if (!hero) return;
                const rect = hero.getBoundingClientRect();
                if (rect.bottom < 0) return;

                const cx = rect.width  / 2;
                const cy = rect.height / 2;
                const dx = (e.clientX - rect.left  - cx) / cx; // -1 → 1
                const dy = (e.clientY - rect.top   - cy) / cy;

                const o1 = document.getElementById('orb1');
                const o2 = document.getElementById('orb2');
                const o3 = document.getElementById('orb3');
                const arc = document.getElementById('orbArc');

                /* Profundidades distintas para sensación 3-D */
                if (o1)  o1.style.marginTop  = `${dy * -10}px`;
                if (o1)  o1.style.marginRight = `${dx * 10}px`;
                if (o2)  o2.style.transform  = `translate(${dx * -20}px, ${dy * -16}px)`;
                if (o3)  o3.style.transform  = `translate(${dx * -30}px, ${dy * -22}px)`;
                if (arc) arc.style.transform = `translate(${dx * -8}px, ${dy * -6}px)`;
            },

            onScroll() {
                const scrollY = window.scrollY;
                const bg = document.getElementById('heroBgImg');
                if (bg) bg.style.transform = `scale(1.06) translateY(${scrollY * 0.14}px)`;
            },

            filtrarPorCategoria(slug) {
                this.categoriaSelect = slug;

                // Actualizar el nombre de la categoría seleccionada
                if (slug === 'todos') {
                    this.categoriaSelectNombre = 'Todos';
                } else {
                    const cat = this.categorias.find(c => c.slug === slug);
                    this.categoriaSelectNombre = cat ? cat.nombre : 'Productos Filtrados';
                }

                // Obtener productos filtrados vía AJAX
                this.obtenerProductosFiltrados(slug);
            },

            async obtenerProductosFiltrados(categoria) {
                this.cargandoProductos = true;
                try {
                    const response = await fetch(`{{ route('plaza.get.productos') }}?categoria=${categoria}`);
                    const data = await response.json();

                    if (data.success) {
                        this.productosFiltrados = data.data;
                        // Scroll a la sección de productos filtrados
                        setTimeout(() => {
                            const section = document.querySelector('[v-if*="categoriaSelect"]');
                            if (section) {
                                section.scrollIntoView({ behavior: 'smooth' });
                            }
                        }, 100);
                    } else {
                        this.productosFiltrados = [];
                    }
                } catch (error) {
                    console.error('Error al obtener productos:', error);
                    this.productosFiltrados = [];
                } finally {
                    this.cargandoProductos = false;
                }
            },

            // ── DRAWER METHODS ──
            openEventsDrawer() {
                this.showEventsDrawer = true;
                document.body.classList.add('events-drawer-open');
            },
            closeEventsDrawer() {
                this.showEventsDrawer = false;
                document.body.classList.remove('events-drawer-open');
            },
            openCartDrawer() {
                this.showCartDrawer = true;
                document.body.classList.add('cart-drawer-open');
                this.loadCartDrawer();
            },
            closeCartDrawer() {
                this.showCartDrawer = false;
                document.body.classList.remove('cart-drawer-open');
            },
            loadCartDrawer() {
                fetch('{{ route("plaza.cart.get") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    this.drawerCart = (data.cart || []).map(item => ({
                        ...item,
                        price: parseFloat(item.price),
                        quantity: parseInt(item.quantity)
                    }));
                })
                .catch(error => console.error('Error loading cart:', error));
            },
            updateItemQty(index, newQty) {
                if (newQty < 1) newQty = 1;
                if (this.drawerCart[index]) {
                    // Actualizar localmente
                    this.drawerCart[index].quantity = newQty;
                    
                    // Guardar en servidor
                    fetch('{{ route("plaza.cart.update.qty") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            item_index: index,
                            quantity: newQty
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Error updating quantity:', data.message);
                            this.loadCartDrawer(); // Recargar si hay error
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.loadCartDrawer(); // Recargar si hay error
                    });
                }
            },
            removeFromCart(index) {
                // Mostrar diálogo de confirmación
                this.itemToRemoveIndex = index;
                this.showConfirmRemove = true;
            },
            cancelRemoveItem() {
                this.showConfirmRemove = false;
                this.itemToRemoveIndex = null;
            },
            confirmRemoveItem() {
                if (this.itemToRemoveIndex === null) return;
                
                const index = this.itemToRemoveIndex;
                const itemKey = this.drawerCart[index].item_key;
                
                // Remover localmente
                this.drawerCart.splice(index, 1);
                this.showConfirmRemove = false;
                this.itemToRemoveIndex = null;
                
                // Guardar en servidor usando item_key
                fetch('{{ route("plaza.cart.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        item_key: itemKey
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast({ icon: 'success', title: 'Item eliminado', message: 'El producto ha sido removido del carrito', timer: 5500 });
                    } else {
                        console.error('Error removing item:', data.message);
                        this.loadCartDrawer(); // Recargar si hay error
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loadCartDrawer(); // Recargar si hay error
                });
            },
            goToClearCart() {
                this.showConfirmClear = true;
            },
            cancelClearCart() {
                this.showConfirmClear = false;
            },
            confirmClearCart() {
                // Limpiar localmente
                this.drawerCart = [];
                this.showConfirmClear = false;
                
                // Guardar en servidor
                fetch('{{ route("plaza.cart.clear") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast({ icon: 'success', title: '¡Carrito vaciado!', message: 'Todos los items han sido eliminados', timer: 5500 });
                    } else {
                        console.error('Error clearing cart:', data.message);
                        this.loadCartDrawer();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loadCartDrawer();
                });
            },

            goToCheckout() {
                if (this.drawerCart.length === 0) {
                    showToast({ icon: 'warning', title: 'El carrito está vacío' });
                    return;
                }

                // Mostrar confirmación dentro del panel
                this.showConfirmOrder = true;
            },
            
            cancelConfirmOrder() {
                this.showConfirmOrder = false;
            },

            async processCheckout() {
                this.isCheckingOut = true;

                try {
                    const response = await fetch('{{ route("plaza.order.create") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ items: this.drawerCart })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast({ icon: 'success', title: '¡Orden confirmada!', message: 'Tu orden se ha procesado correctamente', timer: 6000 });
                        this.drawerCart = [];
                        this.showConfirmOrder = false;
                        this.showCartDrawer = false;
                    } else {
                        showToast({ icon: 'error', title: 'No se pudo procesar', message: data.message || 'Hubo un problema al confirmar tu orden', timer: 5500 });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast({ icon: 'error', title: 'Oops, algo salió mal', message: 'Hubo un problema de conexión. Intenta de nuevo', timer: 5500 });
                } finally {
                    this.isCheckingOut = false;
                }
            },

            // ══ Métodos de Eventos ══

            detalleEvento(evento) {
                this.currentEvento = evento;
                this.showEventoDetail = true;
                document.body.classList.add('evento-detail-open');
            },
            closeEventoDetail() {
                this.showEventoDetail = false;
                document.body.classList.remove('evento-detail-open');
                setTimeout(() => {
                    this.currentEvento = {};
                }, 300);
            }
        },

        computed: {
            totalDrawerQty() {
                return this.drawerCart.length;
            },
            totalDrawerPrice() {
                return this.drawerCart.reduce((sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0);
            },
            totalEventos() {
                return (this.eventosHoy?.length || 0) + (this.eventosProximos?.length || 0);
            }
        }
    }).mount('#plaza-app');
</script>

</body>
</html>
