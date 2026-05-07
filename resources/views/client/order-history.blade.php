<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mis Pedidos - La Comarca Gastro Park</title>
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plaza/order-history.css') }}">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        .orders-nav-tabs {
            display: flex;
            gap: 25px;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border-light, #e2e8f0);
            padding-bottom: 2px;
        }
        .nav-tab-item {
            padding: 12px 5px;
            cursor: pointer;
            font-weight: 600;
            color: var(--muted, #64748b);
            position: relative;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.05rem;
            user-select: none;
        }
        .nav-tab-item i {
            font-size: 1.15rem;
        }
        .nav-tab-item:hover {
            color: var(--primary, #d4773a);
        }
        .nav-tab-item.active {
            color: var(--primary, #d4773a);
        }
        .nav-tab-item.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary, #d4773a);
            border-radius: 10px 10px 0 0;
        }
        .order-card[v-cloak] {
            display: none;
        }
        .order-status.status-ready {
            background-color: #dcfce7;
            color: #166534;
        }
        .order-card-quick-action {
            padding: 0 16px 14px;
        }
        .cancel-order-btn-link {
            background: linear-gradient(135deg, #d4773a 0%, #c06830 100%);
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            text-decoration: none;
        }
        .cancel-order-btn-link:hover {
            background: linear-gradient(135deg, #c06830 0%, #a85a28 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 119, 58, 0.35);
        }
    </style>
</head>
<body>
<div id="order-history-app" v-cloak>
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
                    <button @click="openEventsDrawer" class="cart-btn" :style="{ borderColor: false ? 'var(--primary)' : 'var(--border-light)' }">
                        <i class="fas fa-calendar"></i>
                    </button>
                    
                    <!-- Pending Orders Button -->
                    <button @click="showMyOrdersDrawer = !showMyOrdersDrawer" class="cart-btn" :style="{ borderColor: showMyOrdersDrawer ? 'var(--primary)' : 'var(--border-light)' }" v-if="myOrders.length > 0">
                        <i class="fas fa-clock"></i>
                        <span style="background: var(--primary); color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold;">@{{ myOrders.length }}</span>
                    </button>
                    
                    @auth
                        <button @click="openCartDrawer" class="cart-btn" :style="{ borderColor: false ? 'var(--primary)' : 'var(--border-light)' }">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count">0</span>
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
                                <a href="{{ route('client.orders.history') }}" class="dropdown-item">
                                    <i class="fas fa-history text-muted"></i> Ver mis pedidos
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item danger">
                                        <i class="fas fa-sign-out-alt text-muted"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main>
        <div class="container">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title">Mis Pedidos</h1>
                <p class="page-subtitle">Revisa todos tus pedidos</p>
            </div>

            <!-- TABS NAVIGATION -->
            <div class="orders-nav-tabs">
                <div class="nav-tab-item" :class="{ active: activeTab === 'actuales' }" @click="activeTab = 'actuales'">
                    <i class="fas fa-utensils"></i>
                    <span>Pedidos Actuales</span>
                </div>
                <div class="nav-tab-item" :class="{ active: activeTab === 'historial' }" @click="activeTab = 'historial'">
                    <i class="fas fa-history"></i>
                    <span>Historial de Pedidos</span>
                </div>
            </div>

            <!-- FILTERS ACCORDION -->
            <div class="filters-accordion">
                <div class="accordion-header" @click="toggleFilterAccordion">
                    <div class="accordion-title">
                        <i class="fas" :class="filterAccordionOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        <span>Filtros</span>
                        @if($selectedLocalId)
                            <span class="filter-badge">{{ $selectedLocalId ? '1' : '' }}</span>
                        @endif
                    </div>
                </div>
                <div class="accordion-content" v-show="filterAccordionOpen">
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-map-marker-alt"></i> Filtrar por local
                        </label>
                        <select id="localFilter" class="filter-select" @change="filterByLocal">
                            <option value="">Todos los locales</option>
                            @foreach($locales as $local)
                                <option value="{{ $local->local_id }}" @if($selectedLocalId == $local->local_id) selected @endif>
                                    {{ $local->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($selectedLocalId)
                            <button @click="clearFilters" class="clear-filter-btn-inline">
                                <i class="fas fa-times"></i> Limpiar filtro
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ORDERS LIST -->
            @if($orders->count() > 0)
                <div class="orders-list">
                    @foreach($orders as $order)
                        <div class="order-card" v-show="shouldShowOrder('{{ $order->status }}')" data-status="{{ $order->status }}">
                            <!-- ACCORDION HEADER (ALWAYS VISIBLE) -->
                            <div class="order-card-header">
                                <div class="order-card-header-left">
                                    <div class="order-number">
                                        Pedido #{{ $order->order_number }}
                                    </div>
                                    <div class="order-status status-{{ strtolower(str_replace(' ', '_', $order->status)) }}">
                                        <i class="fas fa-{{ $order->status == 'Delivered' ? 'check-circle' : ($order->status == 'Cancelled' ? 'times-circle' : ($order->status == 'Ready' ? 'check' : 'clock')) }}"></i>
                                        {{ $order->status == 'Delivered' ? 'Entregado' : ($order->status == 'In Progress' ? 'En Preparación' : ($order->status == 'Ready' ? 'Listo' : ($order->status == 'Pending' ? 'Pendiente' : $order->status))) }}
                                    </div>
                                </div>
                                <div class="order-card-header-right">
                                    <div class="order-quick-total">₡{{ number_format($order->total_amount, 2, '.', ',') }}</div>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            <!-- CANCELAR - SIEMPRE VISIBLE (solo Pending) -->
                            @if($order->status === 'Pending')
                            <div class="order-card-quick-action">
                                <button class="cancel-order-btn-link" @click="confirmCancellationRequest({{ $order->order_id }}, '{{ $order->order_number }}')">
                                    <i class="fas fa-times-circle"></i> Cancelar y Editar
                                </button>
                            </div>
                            @endif

                            <!-- ACCORDION CONTENT (COLLAPSIBLE) -->
                            <div class="order-card-content" style="display: none;">
                                <!-- META INFO -->
                                <div class="order-meta">
                                    <div class="meta-item">
                                        <span class="meta-label">Fecha</span>
                                        <span class="meta-value">{{ $order->date ? \Carbon\Carbon::parse($order->date)->format('d \\d\\e M \\d\\e Y') : '-' }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Hora</span>
                                        <span class="meta-value">{{ $order->time ? \Carbon\Carbon::parse($order->time)->format('H:i') : '-' }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Local</span>
                                        <span class="meta-value">{{ $order->local->name ?? '-' }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Cantidad de Items</span>
                                        <span class="meta-value">{{ $order->items->count() }}</span>
                                    </div>
                                </div>

                                <!-- ITEMS -->
                                <div class="order-items">
                                    <span class="items-label">Productos</span>
                                    <div class="items-list">
                                        @forelse($order->items as $item)
                                            <div class="item-row">
                                                @if($item->product->photo_url)
                                                    <img src="{{ $item->product->photo_url }}" alt="{{ $item->product->name }}" class="item-thumbnail">
                                                @else
                                                    <div class="item-thumbnail-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                                <span class="item-name">
                                                    {{ $item->product->name ?? 'Producto no disponible' }}
                                                    @if($item->customization)
                                                        <br><small style="color: var(--muted);">{{ $item->customization }}</small>
                                                    @endif
                                                </span>
                                                <span class="item-qty">x{{ $item->quantity }}</span>
                                            </div>
                                        @empty
                                            <div class="item-row">
                                                <span class="item-name" style="color: var(--muted);">Sin items registrados</span>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- FOOTER -->
                                <div class="order-footer">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div>
                                            @if($order->additional_notes)
                                                <small style="color: var(--muted);">
                                                    <i class="fas fa-sticky-note"></i> {{ $order->additional_notes }}
                                                </small>
                                            @endif
                                        </div>
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end;">
                                        <button class="reorder-btn" @click.prevent="reorderOrder({{ $order->order_id }})">
                                            <i class="fas fa-redo"></i> Reordenar
                                        </button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Mensaje si no hay órdenes de este tipo en la página actual -->
                    <div class="empty-state" v-if="!hasVisibleOrders" style="padding: 40px 20px; background: transparent; border: none; box-shadow: none;">
                        <div class="empty-icon" style="font-size: 2.5rem; opacity: 0.3;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <p class="empty-text" style="color: var(--muted);">No hay pedidos @{{ activeTab === 'actuales' ? 'actuales' : 'finalizados' }} en esta página.</p>
                        <p v-if="activeTab === 'actuales'" style="font-size: 0.85rem; color: var(--muted); margin-top: 5px;">Revisa el Historial o las otras páginas de la lista.</p>
                    </div>
                </div>

                <!-- PAGINATION -->
                @if($orders->hasPages())
                    <div class="pagination-wrapper" v-if="activeTab === 'historial'">
                        {{-- Previous Page Link --}}
                        @if($orders->onFirstPage())
                            <span class="pagination-link disabled">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="pagination-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                            @if($page == $orders->currentPage())
                                <span class="pagination-link active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="pagination-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="pagination-link disabled">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                @endif
            @else
                <!-- EMPTY STATE -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3 class="empty-title">Aún no tienes pedidos{{ $selectedLocalId ? ' en este local' : '' }}</h3>
                    <p class="empty-text">
                        @if($selectedLocalId)
                            Parece que no has realizado pedidos en este local. ¡Haz tu primer pedido ahora!
                        @else
                            Parece que todavía no has realizado ningún pedido. ¡Comienza a explorar nuestros deliciosos platillos!
                        @endif
                    </p>
                    <a href="{{ route('plaza.index') }}" class="empty-action">
                        <i class="fas fa-shopping-bag"></i> Ir a la Plaza
                    </a>
                </div>
            @endif
        </div>
    </main>

    <!-- ══ EVENTS DRAWER ══ -->
    @include('plaza.evento.drawer')

    <!-- ══ CART DRAWER ══ -->
    @include('plaza.carrito._cart_drawer')

    <!-- ══ EDIT ITEM MODAL ══ -->
    @include('plaza.carrito._add_to_cart_modal')

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

@include('plaza.carrito._toast-notifications')

<script>
    @auth
    window.authData = {
        name: '{{ auth()->user()->name ?? explode("@", auth()->user()->email)[0] }}',
        email: '{{ auth()->user()->email }}',
        phone: '{{ auth()->user()->phone ?? "" }}'
    };
    @endauth

    const showToast = (config) => { if (window.showNotification) { window.showNotification(config); } };

    // User menu toggle
    document.addEventListener('DOMContentLoaded', function() {
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

    // Vue App for order history (solo para filtros y header)
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                // Tabs y filtros
                filterAccordionOpen: false,
                isLoading: false,
                activeTab: 'actuales',
                hasVisibleOrders: true,
                // Órdenes pendientes (header)
                myOrders: [],
                showMyOrdersDrawer: false,
                isCancellingOrder: false,
                // Eventos
                showEventsDrawer: false,
                eventosTab: 'hoy',
                eventosHoy: {!! json_encode($eventosHoy) !!},
                eventosProximos: {!! json_encode($eventosProximos) !!},
                showEventoDetail: false,
                currentEvento: {},
                // Carrito
                showCartDrawer: false,
                drawerCart: [],
                showConfirmOrder: false,
                showConfirmClear: false,
                showConfirmRemove: false,
                itemToRemoveIndex: null,
                isCheckingOut: false,
                // Editar item del carrito
                showAddToCartModal: false,
                editingCartItemKey: null,
                currentProduct: { name: '', description: '', photo_url: '', price: 0, product_id: 0, local_id: 0 },
                quantity: 1,
                customization: '',
                isAddingToCart: false,
                customerName: '',
                customerEmail: '',
                customerPhone: '',
                additionalNotes: '',
            };
        },
        computed: {
            totalDrawerQty() {
                return this.drawerCart.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
            },
            totalDrawerPrice() {
                return this.drawerCart.reduce((sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0);
            },
        },
        methods: {
            // ── EVENTOS ──
            openEventsDrawer() { this.showEventsDrawer = true; },
            closeEventsDrawer() { this.showEventsDrawer = false; },
            openEventoDetail(evento) { this.currentEvento = evento; this.showEventoDetail = true; },
            closeEventoDetail() { this.showEventoDetail = false; setTimeout(() => { this.currentEvento = {}; }, 300); },
            // ── CARRITO ──
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
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                })
                .then(r => r.json())
                .then(data => {
                    this.drawerCart = (data.cart || []).map(item => ({ ...item, price: parseFloat(item.price), quantity: parseInt(item.quantity) }));
                });
            },
            updateItemQty(index, newQty) {
                if (newQty < 1) newQty = 1;
                if (!this.drawerCart[index]) return;
                this.drawerCart[index].quantity = newQty;
                fetch('{{ route("plaza.cart.update.qty") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ item_index: index, quantity: newQty })
                }).then(r => r.json()).then(data => { if (!data.success) this.loadCartDrawer(); });
            },
            removeFromCart(index) { this.itemToRemoveIndex = index; this.showConfirmRemove = true; },
            cancelRemoveItem() { this.showConfirmRemove = false; this.itemToRemoveIndex = null; },
            confirmRemoveItem() {
                if (this.itemToRemoveIndex === null) return;
                const index = this.itemToRemoveIndex;
                const itemKey = this.drawerCart[index].item_key;
                this.drawerCart.splice(index, 1);
                this.showConfirmRemove = false;
                this.itemToRemoveIndex = null;
                fetch('{{ route("plaza.cart.remove") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ item_key: itemKey })
                }).then(r => r.json()).then(data => { if (!data.success) this.loadCartDrawer(); });
            },
            goToClearCart() { this.showConfirmClear = true; },
            cancelClearCart() { this.showConfirmClear = false; },
            confirmClearCart() {
                this.drawerCart = [];
                this.showConfirmClear = false;
                fetch('{{ route("plaza.cart.clear") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                }).then(r => r.json()).then(data => { if (!data.success) this.loadCartDrawer(); });
            },
            goToCheckout() { this.showConfirmOrder = true; },
            cancelConfirmOrder() { this.showConfirmOrder = false; },
            async processCheckout() {
                window.location.href = '{{ route("plaza.index") }}';
            },
            // ── EDITAR ITEM ──
            openEditCartItem(index) {
                const item = this.drawerCart[index];
                if (!item) return;
                this.editingCartItemKey = item.item_key;
                this.currentProduct = { name: item.name, description: item.description || '', photo_url: item.photo_url || '', price: parseFloat(item.price), product_id: item.product_id, local_id: item.local_id };
                this.quantity = item.quantity;
                this.customization = item.customization || '';
                if (window.authData) { this.customerName = window.authData.name || ''; this.customerEmail = window.authData.email || ''; this.customerPhone = window.authData.phone || ''; }
                this.showAddToCartModal = true;
                document.body.classList.add('modal-open');
            },
            closeAddToCartModal() {
                document.body.classList.remove('modal-open');
                this.showAddToCartModal = false;
                this.editingCartItemKey = null;
                setTimeout(() => { this.currentProduct = { name: '', description: '', photo_url: '', price: 0, product_id: 0, local_id: 0 }; this.quantity = 1; this.customization = ''; }, 300);
            },
            increaseQuantity() { this.quantity++; },
            decreaseQuantity() { if (this.quantity > 1) this.quantity--; },
            validateQuantity() { if (this.quantity < 1) this.quantity = 1; },
            validateCustomization() { if (this.customization.length > 500) this.customization = this.customization.substring(0, 500); },
            async proceedAddToCart() {
                if (this.isAddingToCart) return;
                const isEditing = !!this.editingCartItemKey;
                const editingKey = this.editingCartItemKey;
                if (window.authData) { this.customerName = window.authData.name || this.customerName; this.customerEmail = window.authData.email || this.customerEmail; this.customerPhone = window.authData.phone || this.customerPhone; }
                if (!this.customerName || !this.customerEmail) { alert('Error: Nombre y email son requeridos.'); return; }
                this.isAddingToCart = true;
                try {
                    if (isEditing) {
                        await fetch('{{ route("plaza.cart.remove") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify({ item_key: editingKey }) });
                    }
                    const response = await fetch('{{ route("plaza.add.cart") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify({ product_id: this.currentProduct.product_id, local_id: this.currentProduct.local_id, quantity: this.quantity, customization: this.customization.trim(), customer_name: this.customerName, customer_email: this.customerEmail, customer_phone: this.customerPhone, additional_notes: this.customization.trim() }) });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        showToast({ icon: 'success', title: isEditing ? '¡Item actualizado!' : '¡Agregado!', message: this.currentProduct.name + (isEditing ? ' se actualizó.' : ' se agregó al carrito.'), timer: 4000 });
                        this.loadCartDrawer();
                        this.closeAddToCartModal();
                    } else {
                        showToast({ icon: 'error', title: 'Error', message: data.message || 'No se pudo procesar.', timer: 4000 });
                    }
                } catch (e) {
                    showToast({ icon: 'error', title: 'Error de conexión' });
                } finally {
                    this.isAddingToCart = false;
                }
            },
            /**
             * Determina si una orden debe mostrarse según la pestaña activa y su estado
             */
            shouldShowOrder(status) {
                // Estados para "Pedidos Actuales"
                const actuales = ['Pending', 'Preparing', 'In Progress', 'Ready'];
                // Estados para "Historial"
                const historial = ['Delivered', 'Cancelled'];
                
                if (this.activeTab === 'actuales') {
                    return actuales.includes(status);
                }
                return historial.includes(status);
            },
            /**
             * Verifica si hay órdenes visibles en el DOM para la pestaña activa
             */
            updateVisibilityFlag() {
                const actuales = ['Pending', 'Preparing', 'In Progress', 'Ready'];
                const historial = ['Delivered', 'Cancelled'];
                
                // Escaneamos las tarjetas generadas por Blade en el DOM
                const cards = Array.from(document.querySelectorAll('.order-card'));
                this.hasVisibleOrders = cards.some(card => {
                    const status = card.getAttribute('data-status');
                    return this.activeTab === 'actuales' ? actuales.includes(status) : historial.includes(status);
                });
            },
            toggleFilterAccordion() {
                this.filterAccordionOpen = !this.filterAccordionOpen;
            },
            filterByLocal(event) {
                const localId = event.target.value;
                const url = new URL('{{ route("client.orders.history") }}', window.location.origin);
                if (localId) {
                    url.searchParams.append('local_id', localId);
                }
                window.location.href = url.toString();
            },
            clearFilters() {
                const selectElement = document.getElementById('localFilter');
                if (selectElement) {
                    selectElement.value = '';
                    window.location.href = '{{ route("client.orders.history") }}';
                }
            },
            /**
             * Reordenar un pedido anterior
             * Agrega todos los items del pedido al carrito
             */
            async reorderOrder(orderId) {
                try {
                    const response = await fetch('{{ route("plaza.cart.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            order_id: orderId
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Mostrar alerta según la respuesta
                        if (data.partial && data.unavailable_products && data.unavailable_products.length > 0) {
                            // Algunos productos no están disponibles
                            const unavailableNames = data.unavailable_products
                                .map(p => `${p.product_name} (x${p.quantity})`)
                                .join('\n');
                            
                            Swal.fire({
                                icon: 'warning',
                                title: 'Reorden parcial',
                                html: `
                                    <div style="text-align: left;">
                                        <p>${data.message}</p>
                                        <p style="margin-top: 10px;"><strong>Productos no disponibles:</strong></p>
                                        <pre style="background-color: #f5f5f5; padding: 10px; border-radius: 4px; text-align: left; font-size: 12px;">${unavailableNames}</pre>
                                        <p style="color: #666; font-size: 12px; margin-top: 10px;">Estos productos ya no están disponibles en este local o han sido descontinuados.</p>
                                    </div>
                                `,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d4423e'
                            });
                        } else {
                            // Todos los productos se agregaron correctamente
                            Swal.fire({
                                icon: 'success',
                                title: 'Reorden exitosa',
                                text: data.message,
                                confirmButtonText: 'Ver carrito',
                                confirmButtonColor: '#d4423e',
                                showCancelButton: true,
                                cancelButtonText: 'Continuar aquí'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Redirigir a la vista del carrito
                                    window.location.href = '{{ route("plaza.index") }}';
                                }
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en la reorden',
                            text: data.message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d4423e'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la reorden. Por favor intenta de nuevo.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d4423e'
                    });
                }
            },
            /**
             * Inicia el proceso de cancelación desde la lista de órdenes (Pestaña Actuales)
             */
            confirmCancellationRequest(orderId, orderNumber) {
                Swal.fire({
                    title: '¿Cancelar Pedido #' + orderNumber + '?',
                    text: 'La orden se cancelará y los productos volverán a tu carrito para que puedas editarlos o agregar más.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d4423e',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, cancelar y editar',
                    cancelButtonText: 'No, mantener pedido'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.processOrderCancellation(orderId);
                    }
                });
            },

            /**
             * Lógica de cancelación llamando al API
             */
            async processOrderCancellation(orderId) {
                this.isLoading = true;
                try {
                    const response = await fetch(`{{ url('/plaza/carrito/api/cancelar') }}/${orderId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ reason: 'Cancelado por el cliente para realizar cambios' })
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Orden Cancelada',
                            text: 'Los productos se han devuelto a tu carrito correctamente.',
                            confirmButtonText: 'Ir al carrito / Plaza',
                            confirmButtonColor: '#d4773a',
                            showCancelButton: true,
                            cancelButtonText: 'Cerrar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("plaza.index") }}';
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo cancelar la orden. Inténtalo de nuevo.', 'error');
                } finally {
                    this.isLoading = false;
                }
            },

            // ── ÓRDENES PENDIENTES METHODS ──
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
                        if (this.myOrders.length > 0) {
                            this.showMyOrdersDrawer = true;
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            },
        },
        watch: {
            // Actualizar la bandera de visibilidad cuando cambie la pestaña
            activeTab() {
                this.$nextTick(() => {
                    this.updateVisibilityFlag();
                });
            }
        },
        mounted() {
            // Cargar órdenes pendientes al iniciar
            this.loadMyOrders();
            // Verificar visibilidad inicial
            this.updateVisibilityFlag();
        }
    }).mount('#order-history-app');

    // Control de acordeones con JavaScript puro (independiente de Vue)
    function setupAccordions() {
        const headers = document.querySelectorAll('.order-card-header');
        
        headers.forEach(header => {
            // Remover event listeners anteriores para evitar duplicados
            header.removeEventListener('click', handleHeaderClick);
            // Agregar nuevo
            header.addEventListener('click', handleHeaderClick);
        });
    }
    
    function handleHeaderClick(e) {
        e.stopPropagation();
        
        const header = this;
        const card = header.closest('.order-card');
        const content = card.querySelector('.order-card-content');
        const chevron = header.querySelector('.fa-chevron-down, .fa-chevron-up');
        const isOpen = content.style.display === 'block';
        
        // Cerrar todos los acordeones
        document.querySelectorAll('.order-card-content').forEach(otherContent => {
            if (otherContent !== content) {
                otherContent.style.display = 'none';
                // Actualizar chevron del cerrado
                const otherHeader = otherContent.closest('.order-card').querySelector('.order-card-header');
                const otherChevron = otherHeader.querySelector('.fa-chevron-down, .fa-chevron-up');
                if (otherChevron) {
                    otherChevron.classList.remove('fa-chevron-up');
                    otherChevron.classList.add('fa-chevron-down');
                }
            }
        });
        
        // Abrir/cerrar el actual
        if (isOpen) {
            content.style.display = 'none';
            if (chevron) {
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            }
        } else {
            content.style.display = 'block';
            if (chevron) {
                chevron.classList.remove('fa-chevron-down');
                chevron.classList.add('fa-chevron-up');
            }
        }
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupAccordions);
    } else {
        setupAccordions();
    }
</script>

</body>
</html>