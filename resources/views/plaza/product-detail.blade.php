<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - {{ $local->name }} - La Comarca Gastro Park</title>

    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/product-detail-page.css') }}">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
<div id="product-detail-app" v-cloak>

    <!-- ── HEADER ── -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <a href="{{ route('plaza.index') }}" class="btn-back">
                    <i class="fas fa-chevron-left"></i> Atrás
                </a>
                <span class="header-label">Producto</span>
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

    <!-- Main Content -->
    <div class="product-detail-page">
        <div class="container">
            <!-- Gallery Section -->
            <section class="gallery-section">
                <div class="gallery-main">
                    <img :src="currentImage" :alt="product.name" loading="lazy">
                    <div class="gallery-counter" v-if="gallery.length > 1">
                        @{{ currentImageIndex + 1 }} / @{{ gallery.length }}
                    </div>
                    <button 
                        v-if="currentImageIndex > 0"
                        @click="currentImageIndex--" 
                        class="gallery-nav-btn gallery-nav-prev"
                        title="Foto anterior">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button 
                        v-if="currentImageIndex < gallery.length - 1"
                        @click="currentImageIndex++" 
                        class="gallery-nav-btn gallery-nav-next"
                        title="Siguiente foto">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Thumbnails -->
                <div class="gallery-thumbnails" v-if="gallery.length > 1" ref="thumbnailsContainer">
                    <button 
                        v-for="(image, idx) in gallery" 
                        :key="idx"
                        @click="scrollToThumbnail(idx)"
                        :class="['thumb', { active: currentImageIndex === idx }]"
                        :title="`Foto ${idx + 1}`">
                        <img :src="image.image_url" :alt="`{{ $product->name }} - ${idx + 1}`" loading="lazy">
                    </button>
                </div>
            </section>

            <!-- Product Info - Two Column Layout -->
            <section class="info-section">
                <!-- Left Column: Product Details -->
                <div class="info-left">
                    <!-- Meta Badges -->
                    <div class="product-meta">
                        <span class="meta-badge">
                            <i class="fas fa-store"></i> {{ $local->name }}
                        </span>
                        <span class="meta-badge" v-if="product.category">
                            <i class="fas fa-tag"></i> @{{ product.category }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="product-title">@{{ product.name }}</h1>

                    <!-- Rating -->
                    <div class="rating-section" v-if="product.average_rating > 0">
                        <div class="stars">
                            <i 
                                v-for="star in 5" 
                                :key="star"
                                class="fas fa-star" 
                                :class="{ active: star <= Math.round(product.average_rating) }">
                            </i>
                        </div>
                        <span class="rating-text">@{{ product.average_rating }}/5</span>
                    </div>

                    <!-- Price -->
                    <div class="price-section">
                        <span class="price-label">Precio</span>
                        <div class="price">
                            <sup>₡</sup>@{{ formatPrice(product.price) }}
                        </div>
                    </div>
                </div>

                <!-- Right Column: Description & Local Info -->
                <div class="info-right">
                    <!-- Description -->
                    <section class="description-section" v-if="product.description">
                        <h2 class="section-title">Descripción</h2>
                        <p class="description-text">@{{ product.description }}</p>
                    </section>

                    <!-- Local Info -->
                    <section class="local-section">
                        <div class="local-logo" v-if="local.image_logo">
                            <img :src="assetUrl(local.image_logo)" :alt="local.name" loading="lazy">
                        </div>
                        <div class="local-info">
                            <h3>@{{ local.name }}</h3>
                            <p v-if="local.description">@{{ local.description }}</p>
                            <p v-if="local.contact">
                                <i class="fas fa-phone"></i> @{{ local.contact }}
                            </p>
                        </div>
                    </section>
                </div>
            </section>

            <div v-else class="reviews-section reviews-empty">
                <i class="fas fa-comments"></i>
                <p>Sin reseñas aún. ¡Sé el primero en reseñar este producto!</p>
            </div>
        </div>
    </div>

    <!-- ═══ MODAL: AGREGAR AL CARRITO ═══ -->
    @include('plaza.carrito._add_to_cart_modal')

    <!-- ═══ DRAWER: EVENTOS (PANEL LATERAL) ═══ -->
    @include('plaza.evento.drawer')

    <!-- ═══ DRAWER: CARRITO (PANEL LATERAL) ═══ -->
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
                product: {!! json_encode([
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category,
                    'photo_url' => $product->photo_url,
                    'price' => $product->price,
                    'average_rating' => $product->average_rating,
                ]) !!},
                local: {!! json_encode([
                    'local_id' => $local->local_id,
                    'name' => $local->name,
                    'description' => $local->description,
                    'contact' => $local->contact,
                    'image_logo' => $local->image_logo,
                ]) !!},
                gallery: {!! json_encode($gallery->map(fn($g) => ['image_url' => $g->image_url])->toArray()) !!},
                reviews: {!! json_encode($reviews->map(fn($r) => [
                    'product_review_id' => $r->product_review_id,
                    'reviewer_name' => $r->reviewer_name,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at->toIso8601String(),
                ])->toArray()) !!},
                currentImageIndex: 0,
                // Propiedades del carrito
                isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
                showCartDrawer: false,
                showEventsDrawer: false,
                drawerCart: [],
                totalDrawerQty: 0,
                showAddToCartModal: false,
                showConfirmOrder: false,
                showConfirmClear: false,
                showConfirmRemove: false,
                itemToRemoveIndex: null,
                isCheckingOut: false,
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
                customerName: '',
                customerEmail: '',
                customerPhone: '',
                additionalNotes: '',
                // Eventos
                eventosHoy: {!! json_encode(isset($eventosHoy) ? $eventosHoy : []) !!},
                eventosProximos: {!! json_encode(isset($eventosProximos) ? $eventosProximos : []) !!},
                currentEvento: {},
                showEventoDetail: false,
                eventosTab: 'hoy',
            }
        },
        computed: {
            currentImage() {
                if (this.gallery.length === 0) {
                    return this.product.photo_url || '{{ asset("images/product-placeholder.png") }}';
                }
                return this.gallery[this.currentImageIndex]?.image_url;
            },
            totalDrawerQty() {
                return this.drawerCart.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
            }
        },
        methods: {
            assetUrl(path) {
                if (!path) return '{{ asset("images/product-placeholder.png") }}';
                return path.startsWith('http') ? path : '{{ asset("") }}' + path;
            },
            formatPrice(price) {
                return (price || 0).toLocaleString('es-CR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },
            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('es-CR', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },
            scrollToThumbnail(index) {
                this.currentImageIndex = index;
                this.$nextTick(() => {
                    const container = this.$refs.thumbnailsContainer;
                    const thumbs = container.querySelectorAll('.thumb');
                    const activeThumb = thumbs[index];
                    if (activeThumb) {
                        activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                });
            },
            // ── CARRITO METHODS ──
            openAddToCartModal(product) {
                if (!this.isAuthenticated) {
                    this.showAuthNotification();
                    return;
                }
                this.currentProduct = product || this.product;
                this.quantity = 1;
                this.customization = '';
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
                let container = document.getElementById('auth-notification-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'auth-notification-container';
                    document.body.appendChild(container);
                }
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
                setTimeout(() => {
                    notification.classList.add('fade-out');
                    setTimeout(() => notification.remove(), 400);
                }, 5000);
            },
            increaseQuantity() { this.quantity++; },
            decreaseQuantity() { if (this.quantity > 1) this.quantity--; },
            validateQuantity() {
                if (this.quantity < 1) this.quantity = 1;
                else if (!Number.isInteger(this.quantity)) this.quantity = Math.floor(this.quantity);
            },
            async proceedAddToCart() {
                if (this.isAddingToCart) return;
                this.additionalNotes = this.customization;
                if (window.authData) {
                    this.customerName = window.authData.name || this.customerName;
                    this.customerEmail = window.authData.email || this.customerEmail;
                    this.customerPhone = window.authData.phone || this.customerPhone;
                }
                if (!this.customerName || !this.customerEmail) {
                    alert('Error: Nombre y email son requeridos.');
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
                    if (response.ok && data.success) {
                        showToast({
                            icon: 'success',
                            title: '¡Producto agregado!',
                            message: this.currentProduct.name + ' se agregó al carrito correctamente',
                            timer: 5500
                        });
                        this.loadCartDrawer();
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
                    showToast({icon: 'error', title: 'Error al agregar al carrito'});
                } finally {
                    this.isAddingToCart = false;
                }
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
                    this.drawerCart[index].quantity = newQty;
                    fetch('{{ route("plaza.cart.update.qty") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({item_index: index, quantity: newQty})
                    }).catch(() => this.loadCartDrawer());
                }
            },
            removeFromCart(index) {
                this.itemToRemoveIndex = index;
                this.showConfirmRemove = true;
            },
            confirmRemoveItem() {
                if (this.itemToRemoveIndex === null) return;
                const index = this.itemToRemoveIndex;
                const itemKey = this.drawerCart[index].item_key;
                this.drawerCart.splice(index, 1);
                this.showConfirmRemove = false;
                this.itemToRemoveIndex = null;
                fetch('{{ route("plaza.cart.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({item_key: itemKey})
                }).then(r => r.json()).catch(() => this.loadCartDrawer());
            },
            goToClearCart() { this.showConfirmClear = true; },
            confirmClearCart() {
                this.drawerCart = [];
                this.showConfirmClear = false;
                fetch('{{ route("plaza.cart.clear") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).catch(() => this.loadCartDrawer());
            },
            goToCheckout() {
                if (this.drawerCart.length === 0) {
                    showToast({icon: 'warning', title: 'El carrito está vacío'});
                    return;
                }
                this.showConfirmOrder = true;
            },
            processCheckout() {
                this.isCheckingOut = true;
                fetch('{{ route("plaza.order.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({items: this.drawerCart})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.showConfirmOrder = false;
                        showToast({icon: 'success', title: '¡Orden confirmada!', message: 'Tu orden se ha procesado correctamente', timer: 6000});
                        this.drawerCart = [];
                        this.closeCartDrawer();
                    } else {
                        showToast({icon: 'error', title: 'No se pudo procesar', message: data.message || 'Hubo un problema', timer: 5500});
                    }
                })
                .catch(() => showToast({icon: 'error', title: 'Oops', message: 'Problema de conexión', timer: 5500}))
                .finally(() => {this.isCheckingOut = false;});
            },
            // ── EVENTOS METHODS ──
            openEventsDrawer() {
                this.showEventsDrawer = true;
            },
            closeEventsDrawer() {
                this.showEventsDrawer = false;
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
            }
        },
        mounted() {
            console.log('Producto cargado:', this.product.name);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft' && this.currentImageIndex > 0) {
                    this.currentImageIndex--;
                } else if (e.key === 'ArrowRight' && this.currentImageIndex < this.gallery.length - 1) {
                    this.currentImageIndex++;
                }
            });
        }
    }).mount('#product-detail-app');
</script>
</body>
</html>
