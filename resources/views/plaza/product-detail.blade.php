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
            
<section class="reviews-section" style="margin-top:40px;">
    <h2 style="color:#e58a3a; font-size:1.4rem; font-weight:700; margin-bottom:20px; text-transform:uppercase;">
        Reseñas del producto
    </h2>

    @auth
        <div class="review-blocked" v-if="puedeResenar === false"
             style="margin-bottom:20px; padding:12px; border-radius:12px; background:rgba(255,255,255,0.05); border:1px solid rgba(229,138,58,0.3); color:#fff;">
            Solo puedes reseñar productos que hayas pedido y recibido.
        </div>

        <div class="review-actions" v-if="puedeResenar === true" style="margin-bottom:20px;">
            <button @click="openReviewModal"
                    style="background:#e58a3a; color:white; border:none; padding:12px 18px; border-radius:12px; font-weight:700; cursor:pointer;">
                <i class="fas fa-star"></i> Dejar reseña
            </button>
        </div>

        <div class="review-checking" v-if="puedeResenar === null"
             style="margin-bottom:20px; color:#fff;">
            Verificando...
        </div>
    @else
        <div class="review-login-prompt"
             style="margin-bottom:20px; padding:12px; border-radius:12px; background:rgba(255,255,255,0.05); border:1px solid rgba(229,138,58,0.3); color:#fff;">
            Debes <a href="{{ route('login') }}">iniciar sesión</a> para dejar una reseña.
        </div>
    @endauth

    <div class="reviews-list" v-if="reviews.length > 0" style="display:flex; flex-direction:column; gap:22px; margin-top:22px;">
        <div
            v-for="(r, i) in reviews"
            :key="i"
            style="
                background: linear-gradient(180deg, rgba(28,20,16,0.98) 0%, rgba(18,13,10,0.98) 100%);
                border: 1px solid rgba(229,138,58,0.18);
                border-radius: 22px;
                padding: 22px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.28);
                transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
                cursor: default;
            "
            onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 18px 40px rgba(0,0,0,0.38)'; this.style.borderColor='rgba(229,138,58,0.38)'"
            onmouseout="this.style.transform='translateY(0px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.28)'; this.style.borderColor='rgba(229,138,58,0.18)'"
        >
            <div style="display:flex; align-items:flex-start; gap:16px;">
                <div
                    style="
                        width:58px;
                        height:58px;
                        min-width:58px;
                        border-radius:50%;
                        background: linear-gradient(135deg, #d4773a, #a9541f);
                        color:#fff;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-weight:700;
                        font-size:1.15rem;
                        font-family:'DM Sans',sans-serif;
                        box-shadow: 0 8px 20px rgba(212,119,58,0.28);
                    "
                >
                    @{{ r.reviewer_name ? r.reviewer_name.substring(0,2).toUpperCase() : 'CL' }}
                </div>

                <div style="flex:1; min-width:0;">
                    <div>
                        <div style="color:#F5F0E8; font-weight:700; font-size:1.35rem; margin-bottom:4px;">
                            @{{ r.reviewer_name || 'Cliente' }}
                        </div>

                        <div style="color:rgba(245,240,232,0.58); font-size:0.95rem; margin-bottom:12px;">
                            @{{ formatDate(r.created_at) }}
                        </div>
                    </div>

                    <div style="display:flex; gap:5px; margin-bottom:16px;">
                        <i
                            v-for="s in 5"
                            :key="s"
                            class="fas fa-star"
                            :style="{ color: s <= r.rating ? '#e58a3a' : 'rgba(229,138,58,0.16)', fontSize: '16px' }"
                        ></i>
                    </div>

                    <p style="margin:0; color:#F5F0E8; font-size:1.08rem; line-height:1.9; font-style:italic;">
                        “@{{ r.comment }}”
                    </p>

                    <div
                        v-if="r.response"
                        style="
                            margin-top:18px;
                            padding:15px 18px;
                            border-left:4px solid #d4773a;
                            border-radius:0 14px 14px 0;
                            background: linear-gradient(90deg, rgba(212,119,58,0.13), rgba(212,119,58,0.05));
                        "
                    >
                        <div style="color:#e58a3a; font-weight:700; text-transform:uppercase; letter-spacing:1px; font-size:0.95rem; margin-bottom:8px;">
                            <i class="fas fa-reply" style="margin-right:8px;"></i>
                            Respuesta del local
                        </div>

                        <p style="margin:0; color:rgba(245,240,232,0.88); font-size:1rem; line-height:1.7;">
                            @{{ r.response }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="reviews-empty" v-else style="color:#aaa; margin-top:20px;">
        No hay reseñas todavía.
    </div>
</section>
<div v-if="showReviewModal" class="review-modal-overlay" @click="closeReviewModal"
     style="position:fixed; inset:0; background:rgba(0,0,0,0.68); z-index:9998;"></div>

<div v-if="showReviewModal"
     style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); width:min(92vw,520px); background:#14110f; border:1px solid rgba(229,138,58,0.22); border-radius:18px; z-index:9999; box-shadow:0 20px 50px rgba(0,0,0,0.35); overflow:hidden;">
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 18px; border-bottom:1px solid rgba(255,255,255,0.06); color:#fff;">
        <h3 style="margin:0;">Dejar reseña</h3>
        <button @click="closeReviewModal" style="background:transparent; border:none; color:#fff; cursor:pointer; font-size:18px;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div style="padding:18px;">
        <div style="margin-bottom:12px; color:#f5f0e8; font-weight:600;">Calificación</div>

        <div style="display:flex; gap:6px; margin-bottom:14px;">
            <i v-for="s in 5"
               :key="s"
               class="fas fa-star"
               :class="{ selected: s <= newReview.rating, hovered: s <= starHover }"
               @mouseenter="starHover = s"
               @mouseleave="starHover = 0"
               @click="newReview.rating = s"
               :style="{ color: (s <= newReview.rating || s <= starHover) ? '#e58a3a' : '#5a4636', fontSize:'24px', cursor:'pointer' }"></i>
        </div>

        <textarea
            v-model="newReview.comment"
            maxlength="500"
            rows="4"
            placeholder="Describe tu experiencia con este producto..."
            style="width:100%; box-sizing:border-box; background:#0f0d0b; color:#fff; border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px; resize:vertical;"></textarea>

        <button
            @click="submitProductReview"
            :disabled="isSendingReview || newReview.rating === 0 || newReview.comment.trim().length < 10"
            style="margin-top:14px; background:#e58a3a; color:#fff; border:none; border-radius:12px; padding:12px 18px; font-weight:700; cursor:pointer;">
            <span v-if="isSendingReview">Guardando...</span>
            <span v-else>Publicar reseña</span>
        </button>
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
                // Reseñas
                puedeResenar: null,
                showReviewModal: false,
                newReview: { rating: 0, comment: '' },
                starHover: 0,
                isSendingReview: false,
                starLabels: ['Muy malo', 'Malo', 'Regular', 'Bueno', '¡Excelente!'],
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
                reviews: {!! json_encode($reviews->toArray()) !!},
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
                eventosTab: 'hoy'
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
            },
            avgRating() {
                if (!this.reviews.length) return 0;
                const sum = this.reviews.reduce((acc, r) => acc + (r.rating || 0), 0);
                return Math.round((sum / this.reviews.length) * 10) / 10;
            },
        },
        methods: {
            assetUrl(path) {
                if (!path) return '{{ asset("images/product-placeholder.png") }}';
                return path.startsWith('http') ? path : '/images/' + path;
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

            // Solicitar QR key al usuario
            solicitarQRKey() {
                return new Promise((resolve) => {
                    Swal.fire({
                        title: 'Verificación de Plaza',
                        text: 'Ingresa el código QR de tu mesa o recinto',
                        icon: 'info',
                        input: 'text',
                        inputPlaceholder: 'Escanea o ingresa el QR',
                        showCancelButton: true,
                        confirmButtonText: 'Continuar',
                        cancelButtonText: 'Cancelar',
                        allowOutsideClick: false,
                        inputValidator: (value) => {
                            if (!value) return 'Debes ingresar el código QR'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resolve(result.value);
                        } else {
                            resolve(null);
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
                        didOpen: () => {
                            Swal.showLoading();
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
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
                        }
                    });
                });
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
            },

            // ── RESEÑAS METHODS ──
            openReviewModal() {
                if (this.puedeResenar !== true) {
                    showToast({
                        icon: 'warning',
                        title: 'No disponible',
                        message: 'Solo puedes reseñar productos que hayas pedido y recibido.'
                    });
                    return;
                }

                this.showReviewModal = true;
                document.body.classList.add('modal-open');
            },

            closeReviewModal() {
                this.showReviewModal = false;
                document.body.classList.remove('modal-open');
            },

            async checkPuedeResenar() {
                if (!this.isAuthenticated) {
                    this.puedeResenar = false;
                    return;
                }

                try {
                    const res = await fetch(`/plaza/producto/${this.product.product_id}/puede-resenar`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await res.json();
                    this.puedeResenar = data.puede ?? false;
                } catch (error) {
                    console.error('Error verificando si puede reseñar:', error);
                    this.puedeResenar = false;
                }
            },

            async submitProductReview() {
                if (this.isSendingReview) return;

                if (this.newReview.rating === 0) {
                    showToast({
                        icon: 'warning',
                        title: 'Selecciona una calificación'
                    });
                    return;
                }

                if (this.newReview.comment.trim().length < 10) {
                    showToast({
                        icon: 'warning',
                        title: 'Comentario inválido',
                        message: 'El comentario debe tener al menos 10 caracteres.'
                    });
                    return;
                }

                this.isSendingReview = true;

                try {
                    const res = await fetch(`/plaza/producto/${this.product.product_id}/resena`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            rating: this.newReview.rating,
                            comment: this.newReview.comment.trim()
                        })
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        this.reviews.unshift({
                            reviewer_name: data.review.nombre,
                            rating: data.review.rating,
                            comment: data.review.comment,
                            created_at: data.review.date,
                        });

                        this.newReview = { rating: 0, comment: '' };
                        this.starHover = 0;
                        this.closeReviewModal();

                        showToast({
                            icon: 'success',
                            title: '¡Reseña publicada!',
                            message: 'Tu reseña ya es visible para otros usuarios.'
                        });

                        return;
                    }

                    if (res.status === 403) {
                        this.puedeResenar = false;
                    }

                    showToast({
                        icon: 'error',
                        title: 'Error',
                        message: data.error || 'No se pudo guardar la reseña.'
                    });

                } catch (error) {
                    console.error('Error guardando reseña:', error);
                    showToast({
                        icon: 'error',
                        title: 'Error de conexión'
                    });
                } finally {
                    this.isSendingReview = false;
                }
            },
        },
        mounted() {
            this.checkPuedeResenar();
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