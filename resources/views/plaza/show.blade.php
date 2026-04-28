<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $local->name }} - La Comarca Gastro Park</title>

    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">

    <!-- Google Fonts with preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.reviews.css') }}">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    @vite(['resources/js/app.js'])

</head>
<body>
<div id="plaza-app" v-cloak data-local-id="{{ $local->local_id }}">

    <!-- ── HEADER ── -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <a href="{{ route('plaza.index') }}" class="btn-back">
                    <i class="fas fa-chevron-left"></i> Atrás
                </a>
                <span class="header-label">Menú</span>
                <div class="flex-row">
                    <!-- Calendar Button (visible to all) -->
                    <button @click="openEventsDrawer" class="cart-btn" :style="{ borderColor: showEventsDrawer ? 'var(--primary)' : 'var(--border-light)' }">
                        <i class="fas fa-calendar"></i>
                    </button>
                    
                    @auth
                        <button @click="openCartDrawer" class="cart-btn" :style="{ borderColor: showCartDrawer ? 'var(--primary)' : 'var(--border-light)' }">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count">@{{ totalDrawerQty }}</span>
                        </button>
                    @endauth
                    <div>
                        @auth
                            <div class="user-menu-top">
                                <button class="user-menu-btn" id="userMenuBtn">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset(auth()->user()->avatar) }}" alt="" class="avatar-img">
                                    @else
                                        <i class="fas fa-user-circle icon-md"></i>
                                    @endif
                                    <span class="text-label">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                                </button>
                                <div class="user-menu-dropdown" id="userMenuDropdown">
                                    <div class="dropdown-header">
                                        <div class="dropdown-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                                        <div class="dropdown-email">{{ auth()->user()->email }}</div>
                                    </div>
                                    <a href="{{ route('client.profile.edit') }}" class="dropdown-item">
                                        <i class="fas fa-user-edit text-muted"></i> Editar perfil
                                    </a>
                                    <a href="{{ route('client.orders.history') }}" class="dropdown-item">
                                        <i class="fas fa-history text-muted"></i> Ver mis pedidos
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
                            <a href="{{ route('login') }}" class="cart-btn">
                                <i class="fas fa-sign-in-alt"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── HERO ── -->
    <section class="local-hero">
        <div class="hero-bg">
            <img :src="localActual.logo_url || 'https://via.placeholder.com/1200x600/111009/D4773A?text=' + encodeURIComponent(localActual.name)" :alt="localActual.name">
        </div>
        <div class="hero-gradient"></div>
        <div class="hero-body container">
            <div class="hero-logo-ring" v-if="localActual.logo_url">
                <img :src="localActual.logo_url" :alt="localActual.name">
            </div>
            <div class="hero-text">
                
                <div class="flex-row-space">
                    <div>
                        <h1 class="hero-name">@{{ localActual.name }}</h1>
                        <p class="hero-desc" v-if="localActual.description">@{{ localActual.description }}</p>
                    </div>
                    <div class="user-info-row" v-if="horarioActual.status" :data-schedule-day="diaActual">
                        <div class="time-display">
                            <div class="time-label">@{{ diaActual }}</div>
                            <div class="time-value" v-if="horarioActual.opening_time || horarioActual.closing_time">
                                <span data-opening-time>@{{ horarioActual.opening_time || 'N/A' }}</span> - <span data-closing-time>@{{ horarioActual.closing_time || 'N/A' }}</span>
                            </div>
                            <div class="status-label">
                                <span class="status-text-open" v-if="estaAbierto" data-schedule-status="open">
                                    <i class="fas fa-circle status-open"></i> Abierto
                                </span>
                                <span class="status-text-closed" v-else data-schedule-status="closed">
                                    <i class="fas fa-circle status-closed"></i> Cerrado
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="user-info-row" v-else>
                        <div class="error-text">Cerrado hoy</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── LOCAL SWITCHER + CATEGORY STRIP ── -->
    @if($localesDisponibles->isNotEmpty() || $categorias->isNotEmpty())
    <div class="cat-strip">
        <div class="container">
            <!-- LOCAL SELECTOR (FUERA DEL SCROLL) -->
            @if($localesDisponibles->isNotEmpty())
            <div class="local-selector-wrapper">
                <div class="custom-dropdown" :class="{ open: showLocalDropdown }">
                    <button 
                        class="custom-dropdown-btn" 
                        @click="showLocalDropdown = !showLocalDropdown"
                        :disabled="isLoadingLocal"
                    >
                        <span class="dropdown-text">
                            @{{ currentLocalName || 'Selecciona Local' }}
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="custom-dropdown-menu" v-if="showLocalDropdown">
                        <button 
                            v-for="loc in localesDisponibles" 
                            :key="loc.local_id"
                            class="dropdown-item"
                            :class="{ active: currentLocalId === loc.local_id }"
                            @click="selectLocal(loc.local_id, loc.name)"
                        >
                            <span>@{{ loc.name }}</span>
                            <i v-if="currentLocalId === loc.local_id" class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <div class="cat-scroll">
                <!-- TODOS BUTTON -->
                <button class="cat-pill" :class="{ active: activeCategory === null }" @click="activeCategory = null">
                    <i class="fas fa-border-all"></i> Todos
                </button>

                <!-- CATEGORIES (REACTIVAS) -->
                <button
                    v-for="cat in categoriasActuales"
                    :key="cat.slug"
                    class="cat-pill"
                    :class="{ active: activeCategory === cat.slug }"
                    @click="activeCategory = cat.slug">
                    <i :class="'fas ' + cat.icono"></i>
                    @{{ cat.nombre }}
                </button>

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
                    <h2 class="section-heading">Lo Mejor de <em>@{{ localActual.name }}</em></h2>
                </div>
                <span class="item-count">@{{ productCount }} Productos</span>
            </div>

            @if($productos->isEmpty())
                <div class="empty-wrap">
                    <div class="empty-icon"><i class="fas fa-bowl-food"></i></div>
                    <p class="empty-msg">No hay productos disponibles por el momento</p>
                </div>
            @else
                <div class="products-grid">
                    @foreach($productos as $i => $producto)
                    <div class="p-card {{ $i === 0 ? 'featured' : '' }}"
                         v-show="activeCategory === null || '{{ Str::slug($producto->category) }}' === activeCategory"
                         @click="navigateToProduct('{{ route('plaza.product.detail', [$local->local_id, $producto->product_id]) }}')"
                         style="cursor: pointer;">

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
                            <div class="p-card-stars">
                                @php
                                    $rating = round($producto->average_rating ?? 0);
                                    for ($j = 1; $j <= 5; $j++):
                                @endphp
                                    <i class="fas fa-star text-xs" style="color: {{ $j <= $rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }};"></i>
                                @php
                                    endfor;
                                @endphp
                            </div>
                            <div class="p-card-footer">
                                <span class="p-card-price">
                                    <sup>₡</sup>{{ number_format($producto->price, 2) }}
                                </span>
                                <button 
                                    class="btn-add-cart"
                                    @click.stop="openAddToCartModal({
                                        product_id: {{ $producto->product_id }},
                                        local_id: {{ $local->local_id }},
                                        name: '{{ addslashes($producto->name) }}',
                                        description: '{{ addslashes($producto->description ?? '') }}',
                                        photo_url: '{{ $producto->photo_url ?? asset("images/product-placeholder.png") }}',
                                        price: {{ $producto->price }}
                                    })">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    <!-- ═══ RESEÑAS DEL LOCAL ═══ -->
    @include('plaza.reviews')

    <!-- ═══ MODAL: AGREGAR AL CARRITO ═══ -->
    @include('plaza.carrito._add_to_cart_modal')

    <!-- ═══ DRAWER: EVENTOS (PANEL LATERAL) ═══ -->
    @include('plaza.evento.drawer')

    <!-- ═══ DRAWER: CARRITO (PANEL LATERAL) ═══ -->
    @include('plaza.carrito._cart_drawer')
    @include('plaza.carrito._my_orders_drawer')

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

</div>

<!-- ═══ TOAST NOTIFICATIONS (OUTSIDE TEMPLATE) ═══ -->
@include('plaza.carrito._toast-notifications')

<script>
    // Validar si el usuario está autenticado
    window.isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

    // Datos de productos con galería
    window.productsData = {!! json_encode($productos->map(function($p) {
        return [
            'product_id' => $p->product_id,
            'local_id' => $p->local_id,
            'name' => $p->name,
            'description' => $p->description,
            'category' => $p->category,
            'photo_url' => $p->photo_url,
            'price' => $p->price,
            'average_rating' => $p->average_rating,
            'gallery' => $p->gallery ?: []
        ];
    })->keyBy('product_id')) !!};

    // Inyectar datos del usuario autenticado si existe
    @auth
    window.authData = {
        name: '{{ auth()->user()->name ?? explode("@", auth()->user()->email)[0] }}',
        email: '{{ auth()->user()->email }}',
        phone: '{{ auth()->user()->phone ?? "" }}'
    };
    @endauth

    // Función helper para mostrar toasts personalizados
    const showToast = (config) => { if (window.showNotification) { window.showNotification(config); } };

    // Inicializar SweetAlert Toast
    const initSwToast = () => {
        if (typeof Swal !== 'undefined' && !window.swToast) {
            const SwToastClass = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 7000,
                timerProgressBar: true,
                background: '#161310',
                color: '#F5F0E8',
                customClass: {
                    container: 'custom-toast-container',
                    popup: 'custom-toast-popup',
                    title: 'custom-toast-title',
                    icon: 'custom-toast-icon'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            window.swToast = SwToastClass;
        }
    };

    // Inicializar swToast cuando esté listo
    if (typeof Swal !== 'undefined') {
        initSwToast();
    }

    // User menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar swToast si no está ya
        initSwToast();
        
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
    });

    // Vue app
    const { createApp } = Vue;
    createApp({
        data() {
            return {
                isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
                activeCategory: null,
                currentLocalId: {{ $local->local_id }},
                currentLocalName: '{{ $local->name }}',
                showLocalDropdown: false,
                localesDisponibles: {!! json_encode($localesDisponibles->map(function($l) { return ['local_id' => $l->local_id, 'name' => $l->name]; })->toArray()) !!},
                // Datos del local actual (reactivos para cambios dinámicos)
                localActual: {
                    local_id: {{ $local->local_id }},
                    name: '{{ $local->name }}',
                    description: '{{ $local->description ?? '' }}',
                    logo_url: '{{ $local->logo_url ?? '' }}',
                },
                horarioActual: {
                    opening_time: '{{ $horarioHoy?->opening_time?->format("H:i") ?? "" }}',
                    closing_time: '{{ $horarioHoy?->closing_time?->format("H:i") ?? "" }}',
                    status: {{ $horarioHoy?->status ? 'true' : 'false' }},
                },
                diaActual: '{{ $diaActual ?? "" }}',
                estaAbierto: {{ $estaAbierto ? 'true' : 'false' }},
                categoriasActuales: {!! json_encode($categorias) !!},
                productosActuales: {!! json_encode($productos->map(function($p) { return ['product_id' => $p->product_id, 'name' => $p->name, 'category' => $p->category, 'description' => $p->description, 'photo_url' => $p->photo_url ? asset($p->photo_url) : null, 'price' => $p->price, 'average_rating' => $p->average_rating]; })) !!},
                productCount: {{ $productos->count() }},
                showAddToCartModal: false,
                showCartDrawer: false,
                showEventsDrawer: false,
                showEventoDetail: false,
                // Eventos
                eventosTab: 'hoy',
                eventosHoy: {!! json_encode($eventosHoy) !!},
                eventosProximos: {!! json_encode($eventosProximos) !!},
                currentEvento: {},
                showConfirmOrder: false,
                showConfirmClear: false,
                showConfirmRemove: false,
                itemToRemoveIndex: null,
                drawerCart: [],
                isCheckingOut: false,
                isLoadingLocal: false,
                currentProduct: {
                    name: '',
                    description: '',
                    photo_url: '',
                    price: 0,
                    product_id: 0,
                    local_id: 0
                },
                quantity: 1,
                customization: '',
                isAddingToCart: false,
                // Datos del cliente
                customerName: '',
                customerEmail: '',
                customerPhone: '',
                additionalNotes: '',

                // Órdenes del cliente
                myOrders: [],
                showMyOrdersDrawer: false,
                isCancellingOrder: false,
                selectedOrderToCancel: null,
                ordersTab: 'current',
                orderHistory: [],
                isLoadingHistory: false,
                historyLoaded: false,
                cancelReason: '',

                // Product Detail Modal (propiedades para openProductDetailModal)
                selectedProduct: {},
                selectedProductGallery: [],
                selectedGalleryIndex: 0,
                detailQuantity: 1,
                detailCustomization: '',
                showProductDetailModal: false
            }
        },
        methods: {
            openAddToCartModal(product) {
                // Validar que el usuario esté autenticado
                if (!this.isAuthenticated) {
                    this.showAuthNotification();
                    return;
                }

                //  VALIDACIÓN: Verificar si el local está abierto
                if (!this.estaAbierto) {
                    showToast({
                        icon: 'warning',
                        title: 'Local Cerrado',
                        message: `${this.localActual.name} está cerrado. No puedes agregar items en este momento.`,
                        timer: 5500
                    });
                    return;
                }

                this.currentProduct = product;
                this.quantity = 1;
                this.customization = '';
                // Rellenar datos del cliente si está autenticado
                const authData = window.authData || null;
                if (authData) {
                    this.customerName = authData.name || '';
                    this.customerEmail = authData.email || '';
                    this.customerPhone = authData.phone || '';
                }
                this.showAddToCartModal = true;
                document.body.classList.add('modal-open');
            },
            closeAddToCartModal() {
                document.body.classList.remove('modal-open');
                this.showAddToCartModal = false;
                setTimeout(() => {
                    this.currentProduct = { name: '', description: '', photo_url: '', price: 0, product_id: 0, local_id: 0 };
                    this.quantity = 1;
                    this.customization = '';
                    this.customerName = '';
                    this.customerEmail = '';
                    this.customerPhone = '';
                    this.additionalNotes = '';
                }, 300);
            },
            showAuthNotification() {
                // Crear contenedor si no existe
                let container = document.getElementById('auth-notification-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'auth-notification-container';
                    document.body.appendChild(container);
                }

                // Crear notificación
                const notification = document.createElement('div');
                notification.className = 'auth-notification';
                notification.innerHTML = `
                    <div class="login-warning">
                        <i class="fas fa-lock lock-icon"></i>
                        <span class="login-warning-text">Debes iniciar sesión para agregar al carrito</span>
                        <a href="{{ route('login') }}" class="auth-notify-btn">Login</a>
                    </div>
                `;
                
                container.appendChild(notification);

                // Remover después de 5 segundos
                setTimeout(() => {
                    notification.classList.add('fade-out');
                    setTimeout(() => notification.remove(), 400);
                }, 5000);
            },
            increaseQuantity() {
                this.quantity++;
            },
            decreaseQuantity() {
                if (this.quantity > 1) {
                    this.quantity--;
                }
            },
            validateQuantity() {
                if (this.quantity < 1) {
                    this.quantity = 1;
                } else if (!Number.isInteger(this.quantity)) {
                    this.quantity = Math.floor(this.quantity);
                }
            },
            validateCustomization() {
                // Solo limitar a 500 caracteres (el textarea ya lo hace)
                if (this.customization.length > 500) {
                    this.customization = this.customization.substring(0, 500);
                }
                // Los espacios se limpiarán al enviar (en proceedAddToCart)
            },
            async proceedAddToCart() {
                if (this.isAddingToCart) return;

                // Sincronizar notas: ambos campos lleven lo mismo
                this.additionalNotes = this.customization;

                // Rellenar datos del cliente desde usuario autenticado
                if (window.authData) {
                    this.customerName = window.authData.name || this.customerName;
                    this.customerEmail = window.authData.email || this.customerEmail;
                    this.customerPhone = window.authData.phone || this.customerPhone;
                }

                // Validar que tenemos datos requeridos
                if (!this.customerName || !this.customerEmail) {
                    console.error('Datos incompletos:', {name: this.customerName, email: this.customerEmail});
                    alert('Error: Nombre y email son requeridos. Por favor, recarga la página.');
                    this.isAddingToCart = false;
                    return;
                }

                this.isAddingToCart = true;

                try {
                    const response = await fetch('{{ route("plaza.add.cart") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: this.currentProduct.product_id,
                            local_id: this.currentProduct.local_id,
                            quantity: this.quantity,
                            customization: this.customization.trim(),
                            customer_name: this.customerName,
                            customer_email: this.customerEmail,
                            customer_phone: this.customerPhone,
                            additional_notes: this.additionalNotes.trim()
                        })
                    });

                    const data = await response.json();
                    console.log('Add to cart response:', data);

                    if (response.ok && data.success) {
                        // Mostrar toast de �xito
                        showToast({
                            icon: 'success',
                            title: '¡Producto agregado!',
                            message: this.currentProduct.name + ' se agregó al carrito correctamente',
                            timer: 5500
                        });

                        // Cargar carrito actualizado para que Vue reaccione
                        this.loadCartDrawer();

                        // Cerrar modal
                        this.closeAddToCartModal();
                    } else {
                        showToast({
                            icon: 'error',
                            title: 'Oops, algo salió mal',
                            message: data.message || 'No pudimos agregar el producto al carrito',
                            timer: 5500
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast({
                        icon: 'error',
                        title: 'Error al agregar al carrito'
                    });
                } finally {
                    this.isAddingToCart = false;
                }
            },
            // ── DRAWER METHODS ──
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
                // Cargar carrito desde sesión
                fetch('{{ route("plaza.cart.get") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Convertir precios y cantidades a números
                    this.drawerCart = (data.cart || []).map(item => ({
                        ...item,
                        price: parseFloat(item.price),
                        quantity: parseInt(item.quantity)
                    }));
                    console.log('Cart loaded:', {
                        items: this.drawerCart.length,
                        total: this.totalDrawerQty,
                        cart: this.drawerCart
                    });
                })
                .catch(error => {
                    console.error('Error loading cart:', error);
                });
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
            clearDrawerCart() {
                if (confirm('¿estás seguro de que deseas vaciar el carrito?')) {
                    this.drawerCart = [];
                }
            },
            truncateText(text, length) {
                if (text.length > length) {
                    return text.substring(0, length) + '...';
                }
                return text;
            },
            goToCheckout() {
                if (this.drawerCart.length === 0) {
                    showToast({ icon: 'warning', title: 'El carrito está vacío' });
                    return;
                }
                this.showConfirmOrder = true;
            },
            cancelConfirmOrder() {
                this.showConfirmOrder = false;
            },
            async processCheckout() {
                this.isCheckingOut = true;

                try {
                    // PASO 1: Solicitar QR key
                    const qrKey = await this.solicitarQRKey();
                    if (!qrKey) {
                        this.isCheckingOut = false;
                        return; // Usuario canceló
                    }

                    // PASO 2: Solicitar permisos GPS
                    const coords = await this.obtenerUbicacion();
                    if (!coords) {
                        this.isCheckingOut = false;
                        return; // Usuario denegó o hubo error
                    }

                    // PASO 3: Enviar orden con QR y GPS
                    const response = await fetch('{{ route("plaza.order.confirm") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ 
                            qr_key: qrKey,
                            latitude: coords.latitude,
                            longitude: coords.longitude
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast({ icon: 'success', title: '¡Órdenes confirmadas!', message: data.message, timer: 6000 });
                        this.drawerCart = [];
                        this.showConfirmOrder = false;
                        this.closeCartDrawer();
                        if (data.orders && data.orders.length > 0) {
                            const tokensMsg = data.orders.map(o => `${o.order_number}: ${o.token}`).join('\n');
                            console.log('Tokens de verificación:\n' + tokensMsg);
                        }
                        // Cargar órdenes después de un tiempo
                        setTimeout(() => {
                            this.loadMyOrders();
                        }, 1000);
                    } else {
                        showToast({ icon: 'error', title: 'No se pudo procesar', message: data.message || 'Hubo un problema', timer: 5500 });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast({ icon: 'error', title: 'Error', message: error.message || 'Hubo un problema de conexión', timer: 5500 });
                } finally {
                    this.isCheckingOut = false;
                }
            },

            // Solicitar QR key al usuario (escanear o ingresar manualmente)
            solicitarQRKey() {
                return new Promise((resolve) => {
                    let stream = null;
                    let scanInterval = null;
                    let resolved = false;

                    function stopCamera() {
                        if (scanInterval) { clearInterval(scanInterval); scanInterval = null; }
                        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
                    }

                    function doResolve(value) {
                        if (!resolved) {
                            resolved = true;
                            stopCamera();
                            Swal.close();
                            resolve(value);
                        }
                    }

                    const html = `
                        <div style="text-align:center;">
                            <p style="color:#555;margin-bottom:14px;font-size:14px;">¿Cómo deseas ingresar el código de tu mesa?</p>
                            <div id="qr-options" style="display:flex;gap:10px;margin-bottom:4px;">
                                <button id="btn-scan-qr" type="button"
                                    style="flex:1;padding:14px 8px;background:#10b981;color:white;border:none;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;">
                                    <div><i class="fas fa-qrcode" style="font-size:24px;margin-bottom:5px;"></i></div>
                                    Escanear QR
                                </button>
                                <button id="btn-manual-code" type="button"
                                    style="flex:1;padding:14px 8px;background:#3b82f6;color:white;border:none;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;">
                                    <div><i class="fas fa-keyboard" style="font-size:24px;margin-bottom:5px;"></i></div>
                                    Ingresar código
                                </button>
                            </div>

                            <div id="qr-scanner-section" style="display:none;">
                                <div style="position:relative;display:inline-block;width:100%;max-width:280px;">
                                    <video id="qr-video" style="width:100%;border-radius:12px;background:#000;display:block;" autoplay playsinline muted></video>
                                    <canvas id="qr-canvas" style="display:none;"></canvas>
                                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:170px;height:170px;border:3px solid #10b981;border-radius:10px;pointer-events:none;box-shadow:0 0 0 9999px rgba(0,0,0,0.45);"></div>
                                </div>
                                <p id="scan-status" style="color:#10b981;font-size:13px;margin:8px 0 2px;font-weight:500;">Iniciando cámara...</p>
                                <button id="btn-back-scan" type="button" style="background:none;border:none;color:#888;font-size:13px;cursor:pointer;padding:4px 8px;">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </button>
                            </div>

                            <div id="qr-manual-section" style="display:none;">
                                <input id="qr-code-input" type="text"
                                    placeholder="Ingresa el código QR"
                                    style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:8px;font-size:16px;box-sizing:border-box;text-align:center;letter-spacing:1px;margin-bottom:6px;"
                                    autocomplete="off" autocorrect="off" autocapitalize="characters" spellcheck="false">
                                <p style="color:#888;font-size:12px;margin:0 0 6px;">Ingresa el código que aparece en el QR de tu mesa</p>
                                <button id="btn-back-manual" type="button" style="background:none;border:none;color:#888;font-size:13px;cursor:pointer;padding:4px 8px;">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </button>
                            </div>
                        </div>
                    `;

                    Swal.fire({
                        title: '<i class="fas fa-qrcode" style="margin-right:8px;"></i>Verificación de Mesa',
                        html: html,
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                        confirmButtonText: '<i class="fas fa-check"></i> Continuar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            const optionsDiv  = document.getElementById('qr-options');
                            const scanSection = document.getElementById('qr-scanner-section');
                            const manualSection = document.getElementById('qr-manual-section');

                            function showOptions() {
                                stopCamera();
                                optionsDiv.style.display   = 'flex';
                                scanSection.style.display  = 'none';
                                manualSection.style.display = 'none';
                                Swal.update({ showConfirmButton: false });
                            }

                            document.getElementById('btn-back-scan')?.addEventListener('click', showOptions);
                            document.getElementById('btn-back-manual')?.addEventListener('click', showOptions);

                            // --- MODO ESCANEAR ---
                            document.getElementById('btn-scan-qr').addEventListener('click', async () => {
                                optionsDiv.style.display  = 'none';
                                scanSection.style.display = 'block';
                                const statusEl = document.getElementById('scan-status');

                                if (!window.isSecureContext) {
                                    statusEl.innerHTML = `
                                        <span style="color:#ef4444;font-weight:600;">Se requiere HTTPS para usar la cámara.</span><br>
                                        <small style="color:#888;">Estás en HTTP. Usa "Ingresar código" o accede al sitio por HTTPS.</small>`;
                                    return;
                                }

                                if (!navigator.mediaDevices?.getUserMedia) {
                                    statusEl.innerHTML = `
                                        <span style="color:#ef4444;font-weight:600;">Cámara no disponible en este navegador.</span><br>
                                        <small style="color:#888;">Usa "Ingresar código" para continuar.</small>`;
                                    return;
                                }

                                statusEl.textContent = 'Solicitando permiso de cámara...';

                                try {
                                    stream = await navigator.mediaDevices.getUserMedia({
                                        video: { facingMode: { ideal: 'environment' } }
                                    });
                                    const video = document.getElementById('qr-video');
                                    video.srcObject = stream;
                                    await video.play();
                                    statusEl.textContent = 'Apunta al código QR de tu mesa...';

                                    const canvas = document.getElementById('qr-canvas');
                                    const ctx = canvas.getContext('2d');

                                    scanInterval = setInterval(() => {
                                        if (video.readyState === video.HAVE_ENOUGH_DATA) {
                                            canvas.width  = video.videoWidth;
                                            canvas.height = video.videoHeight;
                                            ctx.drawImage(video, 0, 0);
                                            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                            const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });
                                            if (code?.data) {
                                                // El QR puede contener una URL con ?key=... — extraer solo el key
                                                let qrValue = code.data;
                                                try {
                                                    const url = new URL(qrValue);
                                                    const keyParam = url.searchParams.get('key');
                                                    if (keyParam) qrValue = keyParam;
                                                } catch (e) { /* no es URL, usar tal cual */ }
                                                statusEl.textContent = '✓ QR detectado!';
                                                doResolve(qrValue);
                                            }
                                        }
                                    }, 200);
                                } catch (err) {
                                    let msg = 'No se pudo acceder a la cámara.';
                                    let hint = 'Usa "Ingresar código" para continuar.';
                                    if (err.name === 'NotAllowedError') {
                                        msg = 'Permiso de cámara denegado.';
                                        hint = 'Haz clic en el ícono de cámara en la barra de dirección de Chrome y permite el acceso.';
                                    } else if (err.name === 'NotFoundError') {
                                        msg = 'No se encontró cámara en este dispositivo.';
                                    }
                                    statusEl.innerHTML = `<span style="color:#ef4444;font-weight:600;">${msg}</span><br><small style="color:#888;">${hint}</small>`;
                                }
                            });

                            // --- MODO MANUAL ---
                            document.getElementById('btn-manual-code').addEventListener('click', () => {
                                optionsDiv.style.display    = 'none';
                                manualSection.style.display = 'block';
                                Swal.update({ showConfirmButton: true });
                                document.getElementById('qr-code-input')?.focus();
                            });
                        },
                        preConfirm: () => {
                            const value = document.getElementById('qr-code-input')?.value?.trim();
                            if (!value) {
                                Swal.showValidationMessage('Debes ingresar el código QR');
                                return false;
                            }
                            return value;
                        },
                        willClose: () => stopCamera()
                    }).then((result) => {
                        if (!resolved) {
                            resolved = true;
                            if (result.isConfirmed && result.value) {
                                resolve(result.value);
                            } else {
                                resolve(null);
                            }
                        }
                    });
                });
            },

            // Obtener ubicación GPS
            obtenerUbicacion() {
                return new Promise((resolve) => {
                    if (!navigator.geolocation) {
                        Swal.fire('Error', 'Tu navegador no soporta geolocalización', 'error');
                        resolve(null);
                        return;
                    }

                    Swal.fire({
                        title: 'Obteniendo ubicación...',
                        text: 'Verificando que estés en Gastropark',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // getCurrentPosition fuera del didOpen para que Swal.close() funcione limpio
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            Swal.close();
                            resolve({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude
                            });
                        },
                        (error) => {
                            let msg = 'Hubo un error al obtener tu ubicación.';
                            if (error.code === 1) {
                                msg = 'Has denegado los permisos de ubicación. Necesitamos saber que estás en Gastropark.';
                            }
                            Swal.fire('Ubicación no disponible', msg, 'error');
                            resolve(null);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                });
            },

            // ── ÓRDENES METHODS ──
            async loadMyOrders() {
                try {
                    const response = await fetch('{{ route("plaza.my.orders") }}', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.myOrders = data.orders;
                        this.showMyOrdersDrawer = true;
                    } else {
                        showToast({ icon: 'error', title: 'Error', message: data.message, timer: 4000 });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast({ icon: 'error', title: 'Error', message: 'No se pudieron cargar las órdenes', timer: 4000 });
                }
            },

            async loadOrderHistory() {
                if (this.historyLoaded || this.isLoadingHistory) return;
                this.isLoadingHistory = true;
                try {
                    const response = await fetch('{{ route("client.orders.history") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.orderHistory = data.orders;
                        this.historyLoaded = true;
                    }
                } catch (error) {
                    console.error('Error cargando historial:', error);
                } finally {
                    this.isLoadingHistory = false;
                }
            },

            switchOrdersTab(tab) {
                this.ordersTab = tab;
                if (tab === 'history' && !this.historyLoaded) {
                    this.loadOrderHistory();
                }
            },

            closeMyOrdersDrawer() {
                this.showMyOrdersDrawer = false;
                this.selectedOrderToCancel = null;
                this.cancelReason = '';
                this.ordersTab = 'current';
                this.orderHistory = [];
                this.historyLoaded = false;
            },

            seleccionarParaCancelar(order) {
                this.selectedOrderToCancel = order;
            },

            cancelarSeleccion() {
                this.selectedOrderToCancel = null;
                this.cancelReason = '';
            },

            async confirmarCancelacion() {
                if (!this.selectedOrderToCancel) return;

                this.isCancellingOrder = true;

                try {
                    const response = await fetch(`{{ url('/plaza/carrito/api/cancelar') }}/${this.selectedOrderToCancel.order_id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ reason: this.cancelReason })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast({ icon: 'success', title: '¡Orden Cancelada!', message: data.message, timer: 5000 });
                        
                        // Remover de la lista
                        this.myOrders = this.myOrders.filter(o => o.order_id !== this.selectedOrderToCancel.order_id);
                        
                        // Limpiar selección
                        this.selectedOrderToCancel = null;
                        this.cancelReason = '';
                        
                        // Cerrar drawer si no quedan órdenes
                        if (this.myOrders.length === 0) {
                            this.closeMyOrdersDrawer();
                        }
                    } else {
                        showToast({ icon: 'error', title: 'No se pudo cancelar', message: data.message, timer: 5000 });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast({ icon: 'error', title: 'Error', message: error.message || 'Hubo un problema de conexión', timer: 5000 });
                } finally {
                    this.isCancellingOrder = false;
                }
            },

            // ── CUSTOM DROPDOWN SELECT ──
            selectLocal(localId, localName) {
                this.currentLocalId = localId;
                this.currentLocalName = localName;
                this.showLocalDropdown = false;
                this.cambiarLocal();
            },

            // ── LOCAL SWITCHER METHOD ──
            cambiarLocal() {
                if (!this.currentLocalId) return;

                this.isLoadingLocal = true;
                
                // Hacer solicitud AJAX para obtener datos del nuevo local
                fetch(`/plaza/${this.currentLocalId}/data`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar la URL sin recargar la página
                        window.history.pushState({ 
                            localId: this.currentLocalId 
                        }, '', `/plaza/${this.currentLocalId}`);
                        
                        // ▼ ACTUALIZAR DATOS REACTIVOS DE VUE (NO MANIPULAR DOM)
                        
                        // Actualizar local actual
                        this.localActual = {
                            local_id: data.local.local_id,
                            name: data.local.name,
                            description: data.local.description,
                            logo_url: data.local.logo_url,
                        };
                        
                        // Actualizar nombre del local en el dropdown
                        this.currentLocalName = data.local.name;
                        
                        // Actualizar horario
                        this.horarioActual = {
                            opening_time: data.horarioHoy?.opening_time || '',
                            closing_time: data.horarioHoy?.closing_time || '',
                            status: data.horarioHoy?.status ? true : false,
                        };
                        this.diaActual = data.diaActual;
                        this.estaAbierto = data.estaAbierto;
                        
                        // Actualizar categorías reactivas
                        this.categoriasActuales = data.categorias;
                        
                        // Actualizar productos reactivos
                        this.productosActuales = data.productos;
                        
                        // Actualizar conteo de productos
                        this.productCount = data.productos.length;
                        
                        // Limpiar categoría activa
                        this.activeCategory = null;
                        
                        // Actualizar meta title
                        document.title = `${data.local.name} - La Comarca Gastro Park`;
                        
                        // Actualizar productos globales
                        window.productsData = {};
                        data.productos.forEach(p => {
                            window.productsData[p.product_id] = {
                                product_id: p.product_id,
                                local_id: this.currentLocalId,
                                name: p.name,
                                description: p.description,
                                category: p.category,
                                photo_url: p.photo_url,
                                price: p.price,
                                average_rating: p.average_rating,
                                gallery: p.gallery || []
                            };
                        });
                        
                        // Recrear productos en grid (recargar productos)
                        this.recargarProductosLocal(data.productos);
                        
                        // Mostrar toast de éxito
                        showToast({
                            icon: 'success',
                            title: 'Local cambiado',
                            message: `Ahora viendo: ${data.local.name}`,
                            timer: 3000
                        });
                    } else {
                        showToast({
                            icon: 'error',
                            title: 'Error',
                            message: data.message || 'No se pudo cargar el local',
                            timer: 5500
                        });
                        // Revertir el cambio en el combobox
                        this.currentLocalId = {{ $local->local_id }};
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast({
                        icon: 'error',
                        title: 'Error de conexión',
                        message: 'No se pudo cambiar el local',
                        timer: 5500
                    });
                    // Revertir el cambio
                    this.currentLocalId = {{ $local->local_id }};
                })
                .finally(() => {
                    this.isLoadingLocal = false;
                });
            },

            recargarProductosLocal(productos) {
                // Buscar el contenedor de productos
                const productsContainer = document.querySelector('.menu-section .container');
                if (!productsContainer) return;
                
                // Buscar o crear el grid
                let grid = productsContainer.querySelector('.products-grid');
                let emptyWrap = productsContainer.querySelector('.empty-wrap');
                
                if (productos.length === 0) {
                    // Si no hay productos, mostrar mensaje vacío
                    if (grid) grid.remove();
                    if (!emptyWrap) {
                        emptyWrap = document.createElement('div');
                        emptyWrap.className = 'empty-wrap';
                        emptyWrap.innerHTML = `
                            <div class="empty-icon"><i class="fas fa-bowl-food"></i></div>
                            <p class="empty-msg">No hay productos disponibles por el momento</p>
                        `;
                        productsContainer.appendChild(emptyWrap);
                    }
                    return;
                }
                
                // Si hay productos, asegurarse de que el grid exista
                if (!grid) {
                    if (emptyWrap) emptyWrap.remove();
                    grid = document.createElement('div');
                    grid.className = 'products-grid';
                    // Remover event listener anterior si existe
                    grid.onclick = null;
                    productsContainer.appendChild(grid);
                }
                
                // Usar DocumentFragment para mejor rendimiento
                const fragment = document.createDocumentFragment();
                let gridHtml = '';
                
                // Construir HTML en un string (más rápido que appendChild repetido)
                productos.forEach((producto, i) => {
                    // Calcular estrellas
                    let starsHtml = '';
                    const rating = Math.round(producto.average_rating || 0);
                    for (let j = 1; j <= 5; j++) {
                        const color = j <= rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)';
                        starsHtml += `<i class="fas fa-star text-xs" style="color: ${color};"></i>`;
                    }
                    
                    const descHtml = i === 0 && producto.description ? `<p class="featured-desc">${producto.description}</p>` : '';
                    const featured = i === 0 ? 'featured' : '';
                    const featuredLabel = i === 0 ? '<p class="featured-label"><i class="fas fa-crown"></i> &nbsp;Destacado</p>' : '';
                    const categoryTag = producto.category ? `<span class="p-card-cat">${producto.category}</span>` : '';
                    
                    gridHtml += `
                        <div class="p-card ${featured}" data-product-id="${producto.product_id}" data-local-id="${this.currentLocalId}" style="cursor: pointer;">
                            <div class="p-card-img">
                                <img src="${producto.photo_url || '/images/product-placeholder.png'}" alt="${producto.name}" loading="${i < 4 ? 'eager' : 'lazy'}">
                                <div class="p-card-img-fade"></div>
                                ${categoryTag}
                            </div>
                            <div class="p-card-body">
                                ${featuredLabel}
                                <h3 class="p-card-name">${producto.name}</h3>
                                ${descHtml}
                                <div class="p-card-stars">
                                    ${starsHtml}
                                </div>
                                <div class="p-card-footer">
                                    <span class="p-card-price">
                                        <sup>₡</sup>${parseFloat(producto.price).toFixed(2)}
                                    </span>
                                    <button class="btn-add-cart" data-product-id="${producto.product_id}">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                // Agregar todo el HTML de una vez
                grid.innerHTML = gridHtml;
                
                // Usar delegación de eventos (más eficiente que event listeners en cada card)
                grid.onclick = (e) => {
                    const card = e.target.closest('.p-card');
                    if (!card) return;
                    
                    const button = e.target.closest('.btn-add-cart');
                    const productId = parseInt(card.dataset.productId);
                    const localId = parseInt(card.dataset.localId);
                    
                    if (button) {
                        e.stopPropagation();
                        // Encontrar producto en array
                        const producto = productos.find(p => p.product_id === productId);
                        if (producto) {
                            this.openAddToCartModal({
                                product_id: productId,
                                local_id: localId,
                                name: producto.name,
                                description: producto.description,
                                photo_url: producto.photo_url,
                                price: producto.price
                            });
                        }
                    } else {
                        const url = `/plaza/${localId}/producto/${productId}`;
                        this.navigateToProduct(url);
                    }
                };
                
                // Aplicar filtro de categoría después de recargar productos
                this.aplicarFiltroCategoria();
            },

            aplicarFiltroCategoria() {
                const grid = document.querySelector('.products-grid');
                if (!grid) return;
                
                const cards = grid.querySelectorAll('.p-card');
                let visibleCount = 0;
                
                cards.forEach(card => {
                    // Obtener categoría del producto desde el card
                    const categoryTag = card.querySelector('.p-card-cat');
                    const categorySlug = categoryTag ? categoryTag.textContent.toLowerCase().replace(/\s+/g, '-') : '';
                    
                    // Mostrar/ocultar según filtro
                    const showCard = this.activeCategory === null || categorySlug === this.activeCategory;
                    card.style.display = showCard ? '' : 'none';
                    
                    if (showCard) visibleCount++;
                });
                
                // Mostrar mensaje de vacío si no hay productos visibles
                if (visibleCount === 0) {
                    let emptyWrap = grid.parentElement.querySelector('.empty-wrap');
                    if (!emptyWrap) {
                        emptyWrap = document.createElement('div');
                        emptyWrap.className = 'empty-wrap';
                        emptyWrap.innerHTML = `
                            <div class="empty-icon"><i class="fas fa-bowl-food"></i></div>
                            <p class="empty-msg">No hay productos disponibles en esta categoría</p>
                        `;
                        grid.parentElement.appendChild(emptyWrap);
                    }
                    emptyWrap.style.display = '';
                } else {
                    const emptyWrap = grid.parentElement.querySelector('.empty-wrap');
                    if (emptyWrap) emptyWrap.style.display = 'none';
                }
            },

            // ── PRODUCT DETAIL MODAL METHODS ──
            navigateToProduct(url) {
                window.location.href = url;
            },

            openProductDetailModal(productId) {
                if (!this.isAuthenticated) {
                    this.showAuthNotification();
                    return;
                }

                // Obtener datos del producto desde la variable global
                const product = window.productsData[productId];
                if (!product) {
                    console.error('Producto no encontrado:', productId);
                    return;
                }

                this.selectedProduct = product;
                this.selectedProductGallery = (product.gallery || []).map(item => ({
                    image_url: item.image_url || item.url
                }));
                
                // Si no hay galería, agregar la foto principal
                if (this.selectedProductGallery.length === 0 && product.photo_url) {
                    this.selectedProductGallery.push({ image_url: product.photo_url });
                }

                this.selectedGalleryIndex = 0;
                this.detailQuantity = 1;
                this.detailCustomization = '';
                this.showProductDetailModal = true;
                document.body.classList.add('modal-open');
            },
            // ── EVENTS DRAWER METHODS ──
            openEventsDrawer() {
                this.showEventsDrawer = true;
                document.body.classList.add('events-drawer-open');
            },
            closeEventsDrawer() {
                this.showEventsDrawer = false;
                document.body.classList.remove('events-drawer-open');
            },
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
            },

            // Recalcular automáticamente si el local está abierto o cerrado basado en la hora actual
            recalculateEstadoLocal() {
                if (!this.horarioActual.opening_time || !this.horarioActual.closing_time) {
                    this.estaAbierto = false;
                    return;
                }

                const isOpen = Boolean(this.horarioActual.status);
                if (!isOpen) {
                    this.estaAbierto = false;
                    return;
                }

                const now = new Date();
                const current = now.getHours() * 60 + now.getMinutes();
                
                try {
                    const [oh, om] = this.horarioActual.opening_time.split(':').map(Number);
                    const [ch, cm] = this.horarioActual.closing_time.split(':').map(Number);
                    const openMinutes = oh * 60 + om;
                    const closeMinutes = ch * 60 + cm;
                    
                    const wasOpen = this.estaAbierto;
                    this.estaAbierto = current >= openMinutes && current < closeMinutes;
                    
                    // Mostrar notificación cuando cambia de estado
                    if (wasOpen && !this.estaAbierto) {
                        console.log('🔴 El local acaba de CERRAR automáticamente');
                        if (window.swToast) {
                            window.swToast.fire({
                                icon: 'warning',
                                title: 'El local ha cerrado',
                                timer: 3000
                            });
                        }
                    } else if (!wasOpen && this.estaAbierto) {
                        console.log('🟢 El local acaba de ABRIR automáticamente');
                        if (window.swToast) {
                            window.swToast.fire({
                                icon: 'success',
                                title: 'El local ha abierto',
                                timer: 3000
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error al recalcular estado:', error);
                    this.estaAbierto = false;
                }
            }
        },

        mounted() {
            // Cargar carrito al iniciar la aplicación
            this.loadCartDrawer();

            // Cerrar dropdown de locales cuando se hace click fuera
            document.addEventListener('click', (e) => {
                const dropdown = document.querySelector('.custom-dropdown');
                if (dropdown && !dropdown.contains(e.target)) {
                    this.showLocalDropdown = false;
                }
            });

            // CA1 & CA2 — Escuchar actualizaciones de horario en tiempo real (desde Echo via CustomEvent)
            document.addEventListener('schedule-updated', (event) => {
                const { schedules } = event.detail;
                console.log('📢 Vue recibió evento schedule-updated:', schedules);
                
                const todaySchedule = schedules.find(s => s.day_of_week === this.diaActual);
                console.log(`🔍 Buscando horario para día: ${this.diaActual}`);
                console.log(`✓ Horario encontrado:`, todaySchedule);
                
                if (!todaySchedule) {
                    console.warn(`⚠ No se encontró horario para ${this.diaActual}`);
                    return;
                }

                // Asegurar que status es boolean
                const isOpen = Boolean(todaySchedule.status);
                
                this.horarioActual = {
                    opening_time: todaySchedule.opening_time,
                    closing_time: todaySchedule.closing_time,
                    status: isOpen,
                };

                console.log(`📊 horarioActual actualizado:`, this.horarioActual);

                // Recalcular el estado del local basado en la nueva información
                this.recalculateEstadoLocal();
            });

            // Recalcular el estado del local cada 10 segundos (10000 ms)
            // Esto permite que el estado cambie automáticamente de "Abierto" a "Cerrado" cuando pasa la hora de cierre
            setInterval(() => {
                this.recalculateEstadoLocal();
            }, 10000); // 10 segundos para testing
        },

        watch: {
            activeCategory() {
                // Refiltar productos cuando cambia la categoría activa
                this.aplicarFiltroCategoria();
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

    // Inicializar listener de horario en tiempo real para la vista show
    (function() {
        const localId = {{ $local->local_id }};

        if (window.Echo && window.initScheduleListener) {
            window.initScheduleListener(localId);
            return;
        }

        let attempts = 0;
        const retry = setInterval(() => {
            attempts++;
            if (window.Echo && window.initScheduleListener) {
                window.initScheduleListener(localId);
                clearInterval(retry);
            } else if (attempts >= 10) {
                clearInterval(retry);
            }
        }, 500);
    })();
</script>

<script>
// ═══ CAROUSEL REVIEWS INITIALIZATION ═══
(function () {
    const track   = document.getElementById('lrcTrack');
    const dotsEl  = document.getElementById('lrcDots');
    const btnPrev = document.getElementById('lrcPrev');
    const btnNext = document.getElementById('lrcNext');

    if (!track) return;

    let current  = 0;
    let autoPlayInterval = null;
    let isAnimating = false;
    const ANIMATION_SPEED = 450;
    const AUTO_PLAY_DELAY = 5000;

    function getSlides() {
        return track.querySelectorAll('.lrc-slide'); // siempre fresco
    }

    function getSlideWidth() {
        const slides = getSlides();
        return slides.length > 0 ? slides[0].offsetWidth + 20 : 0;
    }

    function buildDots() {
        const total = getSlides().length;
        dotsEl.innerHTML = '';
        for (let i = 0; i < total; i++) {
            const dot = document.createElement('button');
            dot.className = 'lrc-dot' + (i === current ? ' lrc-dot--active' : '');
            dot.addEventListener('click', () => { goToSlide(i); resetAutoPlay(); });
            dotsEl.appendChild(dot);
        }
    }

    function updateDots() {
        const dots = dotsEl.querySelectorAll('.lrc-dot');
        dots.forEach((dot, i) => {
            dot.classList.toggle('lrc-dot--active', i === current);
        });
    }

    function goToSlide(slideIndex) {
        if (isAnimating) return;
        const total = getSlides().length;
        current = ((slideIndex % total) + total) % total;
        const offset = -current * getSlideWidth();
        isAnimating = true;
        track.style.transition = `transform ${ANIMATION_SPEED}ms cubic-bezier(0.25, 0.46, 0.45, 0.94)`;
        track.style.transform = `translateX(${offset}px)`;
        setTimeout(() => { isAnimating = false; updateDots(); }, ANIMATION_SPEED);
    }

    function nextSlide() { goToSlide(current + 1); }
    function prevSlide()  { goToSlide(current - 1); }

    function startAutoPlay() {
        stopAutoPlay();
        autoPlayInterval = setInterval(nextSlide, AUTO_PLAY_DELAY);
    }

    function stopAutoPlay() {
        if (autoPlayInterval) { clearInterval(autoPlayInterval); autoPlayInterval = null; }
    }

    function resetAutoPlay() { startAutoPlay(); }

    btnPrev.addEventListener('click', () => { prevSlide(); resetAutoPlay(); });
    btnNext.addEventListener('click', () => { nextSlide(); resetAutoPlay(); });
    track.parentElement.addEventListener('mouseenter', stopAutoPlay);
    track.parentElement.addEventListener('mouseleave', startAutoPlay);

    window.addEventListener('resize', () => {
        track.style.transition = 'none';
        track.style.transform = `translateX(${-current * getSlideWidth()}px)`;
        buildDots();
        updateDots();
    });

    // Exponer para actualización dinámica tras agregar nueva reseña
    window.reiniciarCarrusel = function() {
        current = 0;
        track.style.transition = 'none';
        track.style.transform = 'translateX(0)';
        buildDots();
        updateDots();
        startAutoPlay();
    };

    // Inicialización
    if (getSlides().length > 0) {
        buildDots();
        updateDots();
        startAutoPlay();
    }
})();
</script>
</body>
</html>
