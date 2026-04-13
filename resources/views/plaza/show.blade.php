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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/plaza.show.css') }}">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

</head>
<body>
<div id="plaza-app" v-cloak>

    <!-- ── HEADER ── -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <a href="{{ route('plaza.index') }}" class="btn-back">
                    <i class="fas fa-chevron-left"></i> Atrás
                </a>
                <span class="header-label">Menú</span>
                <div class="flex-row">
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
                    <div class="user-info-row" v-if="horarioActual.status">
                        <div class="time-display">
                            <div class="time-label">@{{ diaActual }}</div>
                            <div class="time-value" v-if="horarioActual.opening_time || horarioActual.closing_time">
                                @{{ horarioActual.opening_time || 'N/A' }} - @{{ horarioActual.closing_time || 'N/A' }}
                            </div>
                            <div class="status-label">
                                <span class="status-text-open" v-if="estaAbierto">
                                    <i class="fas fa-circle status-open"></i> Abierto
                                </span>
                                <span class="status-text-closed" v-else>
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
            <div class="cat-scroll">
                
                <!-- LOCAL SELECTOR (PRIMERO) -->
                <div class="local-selector-wrapper">
                    <select class="local-selector" v-model.number="currentLocalId" @change="cambiarLocal" :disabled="isLoadingLocal">
                        <option value="" disabled>Selecciona Local</option>
                        @foreach($localesDisponibles as $loc)
                        <option value="{{ $loc->local_id }}">
                            {{ $loc->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

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
                <span class="item-count">@{{ productCount }} platillos</span>
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
                                        photo_url: '{{ $producto->photo_url ?? asset('images/product-placeholder.png') }}',
                                        price: {{ $producto->price }},                                    })">
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

    <!-- ═══ MODAL: AGREGAR AL CARRITO ═══ -->
    @include('plaza.carrito._add_to_cart_modal')

    <!-- ═══ DRAWER: CARRITO (PANEL LATERAL) ═══ -->
    @include('plaza.carrito._cart_drawer')

    
   </div>{{-- cierra #plaza-app --}}

    @include('plaza.reviews')

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
                productCount: {{ $productos->count() }},
                showAddToCartModal: false,
                showCartDrawer: false,
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
                additionalNotes: ''
            }
        },
        methods: {
            openAddToCartModal(product) {
                // Validar que el usuario esté autenticado
                if (!this.isAuthenticated) {
                    this.showAuthNotification();
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
                if (!this.customerName || !this.customerEmail || !this.customerPhone) {
                    console.error('Datos incompletos:', {name: this.customerName, email: this.customerEmail, phone: this.customerPhone});
                    alert('Error: Datos del usuario incompletos. Por favor, recarga la página.');
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
            processCheckout() {
                this.isCheckingOut = true;
                fetch('{{ route("plaza.order.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ items: this.drawerCart })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showConfirmOrder = false;
                        showToast({ icon: 'success', title: '¡Orden confirmada!', message: 'Tu orden se ha procesado correctamente', timer: 6000 });
                        this.drawerCart = [];
                        this.closeCartDrawer();
                    } else {
                        showToast({ icon: 'error', title: 'No se pudo procesar', message: data.message || 'Hubo un problema', timer: 5500 });
                    }
                })
                .catch(error => {
                    showToast({ icon: 'error', title: 'Oops', message: 'Problema de conexión', timer: 5500 });
                })
                .finally(() => { this.isCheckingOut = false; });
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
                                gallery: []
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
                            <p class="empty-msg">No hay platillos disponibles por el momento</p>
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
                                <img src="${producto.photo_url}" alt="${producto.name}" loading="${i < 4 ? 'eager' : 'lazy'}">
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
            }
        },

        mounted() {
            // Cargar carrito al iniciar la aplicación
            this.loadCartDrawer();
        },

        computed: {
            totalDrawerQty() {
                return this.drawerCart.length;
            },
            totalDrawerPrice() {
                return this.drawerCart.reduce((sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0);
            }
        }
    }).mount('#plaza-app');
</script>
</body>
</html>
