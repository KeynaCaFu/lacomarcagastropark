<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi Historial de Pedidos - La Comarca Gastro Park</title>
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
                <h1 class="page-title">Mi Historial de Pedidos</h1>
                <p class="page-subtitle">Revisa todos tus pedidos anteriores</p>
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
                        <div class="order-card">
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
                                        <button class="reorder-btn" @click.prevent="reorderOrder({{ $order->order_id }})">
                                            <i class="fas fa-redo"></i> Reordenar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINATION -->
                @if($orders->hasPages())
                    <div class="pagination-wrapper">
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

    <!-- ═══ DRAWER: ÓRDENES PENDIENTES ═══ -->
    @include('plaza.carrito._my_orders_drawer')

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

<script>
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
                showEventsDrawer: false,
                showCartDrawer: false,
                filterAccordionOpen: false,
                isLoading: false,
                // Órdenes pendientes
                myOrders: [],
                showMyOrdersDrawer: false,
                isCancellingOrder: false,
                selectedOrderToCancel: null,
                cancelReason: ''
            };
        },
        methods: {
            openEventsDrawer() {
                this.showEventsDrawer = !this.showEventsDrawer;
            },
            openCartDrawer() {
                // Cart is disabled in order history page
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

            closeMyOrdersDrawer() {
                this.showMyOrdersDrawer = false;
                this.selectedOrderToCancel = null;
                this.cancelReason = '';
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
                        Swal.fire({
                            icon: 'success',
                            title: '¡Orden Cancelada!',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                        
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
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo cancelar',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema de conexión',
                        confirmButtonText: 'OK'
                    });
                } finally {
                    this.isCancellingOrder = false;
                }
            }
        },
        mounted() {
            // Cargar órdenes pendientes al iniciar
            this.loadMyOrders();
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