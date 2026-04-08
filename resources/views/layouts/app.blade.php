<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>@yield('title', 'La Comarca - Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS Personalizado La Comarca -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fixes.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Top Navigation Bar Styles */
        .top-navbar {
            position: relative;
            margin: 0;
            padding: 12px 20px;
        }
        
        .main-content {
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            gap: 0;
        }
        
        .top-navbar .admin-menu-top,
        .top-navbar .user-menu-top {
            position: relative;
        }
        
        /* Search input focus */
        #topSearchInput:focus {
            border-color: #e18018 !important;
            background: #fff !important;
            box-shadow: 0 0 0 3px rgba(225, 128, 24, 0.1) !important;
        }
        
        #topSearchInput:hover {
            border-color: #e5e7eb !important;
        }
        
        /* Clear button hover */
        #clearSearchBtn:hover {
            color: #374151 !important;
        }
        
        /* Search button hover and active */
        #searchBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(225, 128, 24, 0.3);
        }
        
        #searchBtn:active {
            transform: translateY(0);
        }
        
        /* Menu dropdown animations */
        .admin-menu-dropdown,
        .user-menu-dropdown {
            animation: slideDown 0.2s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Hover effects for menu items */
        .admin-menu-dropdown a:hover,
        .user-menu-dropdown a:hover,
        .user-menu-dropdown button:hover {
            background-color: #f9fafb;
        }

        /* ===== Mobile Navigation Links (inside user menu) ===== */
        .mobile-nav-links {
            display: none;
        }

        @media (max-width: 575.98px) {
            .mobile-nav-links {
                display: block;
                border-bottom: 2px solid #f3f4f6;
                padding: 6px 0;
            }

            .mobile-nav-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 11px 16px;
                color: #374151;
                text-decoration: none;
                font-size: 14px;
                font-weight: 600;
                transition: background 0.15s ease;
            }

            .mobile-nav-item:hover {
                background: #fff7ed;
                color: #c9690f;
            }

            .mobile-nav-item.active {
                background: #fff7ed;
                color: #e18018;
                border-left: 3px solid #e18018;
            }

            .mobile-nav-item i {
                width: 20px;
                text-align: center;
                font-size: 15px;
                color: #9ca3af;
            }

            .mobile-nav-item.active i,
            .mobile-nav-item:hover i {
                color: #e18018;
            }
        }
        
        /* User menu button hover effect */
        .user-menu-btn:hover {
            background: #e18018 !important;
            color: white !important;
        }
        
        /* Responsive design */
        @media (max-width: 992px) {
            .top-navbar {
                flex-wrap: wrap;
            }
            
            .top-navbar > div:first-child {
                width: 100%;
                order: 2;
            }
            
            .top-navbar > div:last-child {
                width: 100%;
                order: 1;
                justify-content: space-between;
            }
        }
        
        @media (max-width: 575.98px) {
            .top-navbar {
                padding: 8px 12px !important;
                gap: 8px !important;
                margin-left: 0 !important;
            }

            .top-navbar-left {
                min-width: 0 !important;
                gap: 8px !important;
            }

            .top-search-bar {
                max-width: none !important;
                flex: 1 !important;
            }

            #topSearchInput {
                font-size: 14px !important;
                padding: 8px 28px 8px 32px !important;
            }

            #searchBtn span {
                display: none;
            }

            #searchBtn {
                padding: 8px 12px !important;
            }

            .top-navbar-right {
                gap: 8px !important;
            }

            .user-menu-btn span {
                font-size: 11px !important;
                color: #374151 !important;
            }

            .user-menu-btn {
                padding: 6px 10px !important;
            }

            .user-menu-btn i {
                font-size: 18px !important;
            }

            .admin-menu-dropdown,
            .user-menu-dropdown {
                right: 0 !important;
                left: auto !important;
                min-width: 200px !important;
            }

            /* Ocultar hamburguesa en phone para todos */
            .navbar-toggle {
                display: none !important;
            }

            /* Ayuda button: mantener visible con texto */
            .btn-help {
                padding: 7px 12px !important;
                font-size: 12px !important;
                border-width: 2px !important;
                border-color: #e18018 !important;
                color: #e18018 !important;
                background: #fff8f0 !important;
                font-weight: 700 !important;
                white-space: nowrap;
            }
        }

        @media (max-width: 480px) {
            .top-navbar {
                padding: 6px 8px !important;
                gap: 6px !important;
            }

            .top-search-bar {
                min-width: 0 !important;
            }

            #topSearchInput {
                font-size: 16px !important;
            }

            .top-navbar-right {
                gap: 6px !important;
            }

            .user-menu-btn span {
                font-size: 10px !important;
            }

            .user-menu-btn {
                padding: 5px 8px !important;
            }

            /* Ayuda: icon-only en pantallas muy pequeñas */
            .btn-help {
                padding: 7px 10px !important;
                font-size: 14px !important;
                background: #e18018 !important;
                color: #fff !important;
                border-color: #e18018 !important;
                border-radius: 8px !important;
            }

            .btn-help span,
            .btn-help .btn-help-text {
                display: none;
            }
        }

        /* Estilo personalizado para botón de confirmación SweetAlert2 */
        .swal2-confirm {
            background: linear-gradient(135deg, #e18018, #c9690f) !important;
            color: white !important;
            border: none !important;
        }
        
        .swal2-confirm:hover {
            background: linear-gradient(135deg, #d97c13, #b85f0d) !important;
        }
        
        .swal2-confirm:active {
            background: linear-gradient(135deg, #c9690f, #a84f0a) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="{{ (auth()->check() && !auth()->user()->isAdminGlobal()) ? 'gerente-mode' : '' }}">
    <!-- Container principal con diseño La Comarca -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (colapsable/ocultable) -->
            <nav class="sidebar drawer" id="appSidebar">
                <div class="sidebar-header">
                    <a href="{{ (auth()->check() && auth()->user()->isAdminGlobal()) ? route('admin.dashboard') : route('dashboard') }}" class="brand text-decoration-none" title="La Comarca">
                        <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca" class="brand-logo">
                    </a>
                    <button class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-controls="appSidebar" aria-expanded="false">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="sidebar-menu">
                    <ul>
                        @php $mode = auth()->user()->isAdminGlobal() ? 'global' : 'local'; @endphp

                        @if($mode === 'global')
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users*') ? 'active' : '' }}" data-tooltip="Usuarios">
                                    <i class="fas fa-users"></i> Usuarios
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('locales.index') }}" class="{{ request()->routeIs('locales*') ? 'active' : '' }}" data-tooltip="Locales">
                                    <i class="fas fa-store"></i> Locales
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('eventos.index') }}" class="{{ request()->routeIs('eventos*') ? 'active' : '' }}" data-tooltip="Eventos">
                                    <i class="fas fa-calendar-days"></i> Eventos
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
    
                            <li>
                                <a href="{{ route('local.index') }}" class="{{ request()->routeIs('local*') ? 'active' : '' }}" data-tooltip="Mi Local">
                                    <i class="fas fa-store"></i> Mi Local
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products*') ? 'active' : '' }}" data-tooltip="Productos">
                                    <i class="fas fa-box"></i> Productos
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers*') ? 'active' : '' }}" data-tooltip="Proveedores">
                                     <i class="fas fa-truck"></i> Proveedores
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders*') ? 'active' : '' }}" data-tooltip="Órdenes">
                                    <i class="fas fa-shopping-cart"></i> Órdenes
                                </a>
                            </li>

                            <li>
                                     <a href="{{ route('reviews.index') }}" class="{{ request()->routeIs('reviews*') ? 'active' : '' }}" data-tooltip="Reseñas">
                                      <i class="fas fa-star"></i> Reseñas
                                                    </a>
                                            </li>

                            <li>
                                <a href="{{ route('reports.orders') }}" class="{{ request()->routeIs('reports*') ? 'active' : '' }}" data-tooltip="Reportes">
                                    <i class="fas fa-chart-bar"></i> Reportes
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="main-content" id="mainContent">
                <!-- Top Navigation Bar -->
                <div class="top-navbar" style="background: #fff;  padding: 8px 20px; margin: 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-right: -14px; margin-left: -18px;">
                    <div class="top-navbar-left" style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                        <!-- Toggle Sidebar -->
                        <button class="navbar-toggle" id="navbarToggleBtn" style="background: none; border: none; cursor: pointer; padding: 8px; display: none;">
                            <i class="fas fa-bars" style="font-size: 20px; color: #374151;"></i>
                        </button>
                        
                        <!-- Search Bar -->
                        <div class="top-search-bar" style="display: flex; align-items: center; flex: 1; max-width: 400px;">
                            <div style="position: relative; width: 100%; display: flex; gap: 6px;">
                                <div style="position: relative; flex: 1;">
                                    <input type="text" id="topSearchInput" placeholder="Buscar..." style="width: 100%; padding: 8px 32px 8px 36px; border: 1px solid #e5e7eb; border-radius: 8px 0 0 8px; font-size: 13px; background: #f9fafb; transition: all 0.3s ease;">
                                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 13px;"></i>
                                    <button type="button" id="clearSearchBtn" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px 8px; display: none; font-size: 14px;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <button type="button" id="searchBtn" style="background: linear-gradient(135deg, #e18018, #c9690f); color: white; border: none; padding: 8px 16px; border-radius: 0 8px 8px 0; cursor: pointer; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px; transition: all 0.3s ease;">
                                    <i class="fas fa-search"></i>
                                    <span>Buscar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="top-navbar-right" style="display: flex; align-items: center; gap: 16px;">
                        <!-- Help Button Container (Usuarios) -->
                        <div id="topHelpContainer"></div>
                        
                        <!-- Help Button Container (Eventos) -->
                        <div id="topHelpEventContainer"></div>

                        <!-- Campana de órdenes pendientes -->
                        @if(auth()->check() && !auth()->user()->isAdminGlobal())
                        <div style="position: relative;">
                            <button id="notificationBellBtn" style="background: none; border: none; cursor: pointer; padding: 8px 12px; color: #e18018; font-size: 20px; display: flex; align-items: center; justify-content: center; position: relative;" title="Órdenes pendientes">
                                <i class="fas fa-bell"></i>
                                <span id="pendingCountBadge" style="position: absolute; top: -5px; right: 0; background: #ef4444; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; border: 2px solid white; display: none;">0</span>
                            </button>
                            
                            <!-- Dropdown de órdenes y reseñas pendientes -->
                            <div id="notificationDropdown" style="position: absolute; top: 100%; right: 0; margin-top: 8px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 360px; z-index: 1000; display: none;">
                                <!-- Tabs -->
                                <div style="display: flex; border-bottom: 2px solid #f3f4f6; padding: 0;">
                                    <button class="notif-tab" data-tab="orders" style="flex: 1; padding: 12px 16px; border: none; background: none; cursor: pointer; font-weight: 600; color: #111827; font-size: 14px; border-bottom: 3px solid #e18018; transition: all 0.2s;">
                                        Órdenes
                                    </button>
                                    <button class="notif-tab" data-tab="reviews" style="flex: 1; padding: 12px 16px; border: none; background: none; cursor: pointer; font-weight: 600; color: #9ca3af; font-size: 14px; border-bottom: 3px solid transparent; transition: all 0.2s;">
                                        Reseñas
                                    </button>
                                </div>
                                
                                <!-- Contenedor de órdenes -->
                                <div id="notificationList" class="notif-content" data-content="orders" style="max-height: 400px; overflow-y: auto;">
                                    <div style="padding: 20px 16px; text-align: center; color: #9ca3af; font-size: 13px;">
                                        Cargando...
                                    </div>
                                </div>
                                
                                <!-- Contenedor de reseñas -->
                                <div id="reviewsList" class="notif-content" data-content="reviews" style="max-height: 400px; overflow-y: auto; display: none;">
                                    <div style="padding: 40px 16px; text-align: center;">
                                        <div style="color: #9ca3af; font-size: 14px; margin-bottom: 8px;">
                                            <i class="fas fa-star" style="font-size: 32px; margin-bottom: 12px; display: block; color: #bfdbfe;"></i>
                                            Aún no hay notificaciones de reseñas
                                        </div>
                                        <p style="color: #d1d5db; font-size: 12px;">Esta funcionalidad estará disponible próximamente</p>
                                    </div>
                                </div>
                                
                                <!-- Footer (solo para órdenes) -->
                                <div id="notifFooter" style="padding: 12px 16px; border-top: 1px solid #f3f4f6; text-align: center;">
                                    <a href="{{ route('orders.index') }}" style="color: #e18018; text-decoration: none; font-weight: 600; font-size: 13px;">
                                        Ver todas las órdenes →
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- User Icon Menu -->
                        <div class="user-menu-top" style="position: relative;">
                            <button class="user-menu-btn" style="background: none; border: 2px solid #e18018; cursor: pointer; padding: 8px 12px; color: #e18018; border-radius: 8px; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500;">
                                <i class="fas fa-user-circle" style="font-size: 24px;"></i>
                                <span class="user-role-label" style="font-size: 13px; color: #374151; font-weight: 600;">
                                    @php
                                        $role = auth()->user()->role?->role_type ?? 'Usuario';
                                    @endphp
                                    {{ $role }}
                                </span>
                            </button>
                            <div class="user-menu-dropdown" style="position: absolute; top: 100%; right: 0; margin-top: 8px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 220px; z-index: 1000; display: none;">
                                <div style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6; font-size: 13px;">
                                    <div style="font-weight: 600; color: #111827;">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                                    <div style="color: #6b7280; font-size: 12px; margin-top: 4px;">{{ auth()->user()->email }}</div>
                                </div>

                                {{-- Navegación mobile (visible solo ≤991px) --}}
                                <div class="mobile-nav-links">
                                    @if($mode === 'global')
                                        <a href="{{ route('admin.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                            <i class="fas fa-home"></i> Dashboard
                                        </a>
                                        <a href="{{ route('users.index') }}" class="mobile-nav-item {{ request()->routeIs('users*') ? 'active' : '' }}">
                                            <i class="fas fa-users"></i> Usuarios
                                        </a>
                                        <a href="{{ route('locales.index') }}" class="mobile-nav-item {{ request()->routeIs('locales*') ? 'active' : '' }}">
                                            <i class="fas fa-store"></i> Locales
                                        </a>

                                        <a href="{{ route('eventos.index') }}" class="mobile-nav-item {{ request()->routeIs('eventos*') ? 'active' : '' }}">
                                            <i class="fas fa-calendar-days"></i> Eventos
                                        </a>
                                    @else
                                        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                            <i class="fas fa-home"></i> Dashboard
                                        </a>
                                        <a href="{{ route('local.index') }}" class="mobile-nav-item {{ request()->routeIs('local*') ? 'active' : '' }}">
                                            <i class="fas fa-store"></i> Mi Local   
                                        <a href="{{ route('products.index') }}" class="mobile-nav-item {{ request()->routeIs('products*') ? 'active' : '' }}">
                                            <i class="fas fa-box"></i> Productos
                                        </a>
                                        <a href="{{ route('suppliers.index') }}" class="mobile-nav-item {{ request()->routeIs('suppliers*') ? 'active' : '' }}">
                                            <i class="fas fa-truck"></i> Proveedores
                                        </a>
                                        <a href="{{ route('orders.index') }}" class="mobile-nav-item {{ request()->routeIs('orders*') ? 'active' : '' }}">
                                            <i class="fas fa-shopping-cart"></i> Órdenes
                                        </a>

                                        <a href="{{ route('reviews.index') }}" class="mobile-nav-item {{ request()->routeIs('reviews*') ? 'active' : '' }}">
                                            <i class="fas fa-star"></i> Reseñas
                                        </a>

                                        <a href="{{ route('reports.orders') }}" class="mobile-nav-item {{ request()->routeIs('reports*') ? 'active' : '' }}">
                                            <i class="fas fa-chart-bar"></i> Reportes
                                        </a>
                                        
                                    @endif
                                </div>

                                <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: #111827; text-decoration: none; border-bottom: 1px solid #f3f4f6;">
                                    <i class="fas fa-user-edit" style="color: #6b7280; font-size: 14px;"></i>
                                    <span>Editar perfil</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 100%; display: flex; align-items: center; gap: 10px; padding: 12px 16px; color: #dc2626; text-decoration: none; border: none; background: none; cursor: pointer; font-size: 13px;">
                                        <i class="fas fa-sign-out-alt" style="font-size: 14px;"></i>
                                        <span>Cerrar sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Contenido específico de cada página -->
                <div class="fade-in">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 global (carga perezosa si falta) y helpers de estilo -->
    <script>
        (function(){
            // Cargar SweetAlert si no existe
            if (typeof Swal === 'undefined') {
                const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
                if (!existing) {
                    const s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
                    document.head.appendChild(s);
                }
            }

            // Utilidades globales para estilo consistente
            window.SwalDefaults = {
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
            };

            window.swConfirm = function(options){
                const base = {
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: window.SwalDefaults.confirmButtonColor,
                    cancelButtonColor: window.SwalDefaults.cancelButtonColor,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar'
                };
                const exec = function(){ return Swal.fire(Object.assign(base, options || {})); };
                if (typeof Swal === 'undefined') {
                    const scriptEl = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
                    if (scriptEl) return new Promise(resolve => scriptEl.addEventListener('load', () => resolve(exec())));
                }
                return exec();
            };

            window.swAlert = function(options){
                const base = {
                    icon: 'success',
                    confirmButtonColor: window.SwalDefaults.confirmButtonColor,
                };
                const exec = function(){ return Swal.fire(Object.assign(base, options || {})); };
                if (typeof Swal === 'undefined') {
                    const scriptEl = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
                    if (scriptEl) return new Promise(resolve => scriptEl.addEventListener('load', () => resolve(exec())));
                }
                return exec();
            };

            // Toast para notificaciones pequeñas en la esquina superior derecha
            const initSwToast = () => {
                if (typeof Swal !== 'undefined' && !window.swToast) {
                    window.swToast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 7000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                }
            };

            initSwToast();

            if (typeof Swal === 'undefined') {
                const checkInterval = setInterval(() => {
                    if (typeof Swal !== 'undefined') {
                        initSwToast();
                        clearInterval(checkInterval);
                    }
                }, 100);

                setTimeout(() => clearInterval(checkInterval), 5000);
            }

            window.showNotification = function(type, message) {
                const iconMap = { success: 'success', error: 'error', info: 'info', warning: 'warning' };
                const icon = iconMap[type] || 'info';
                if (window.swAlert) return swAlert({ icon, title: '', text: message, timer: 1600, showConfirmButton: false });
                return Promise.resolve();
            };

            window.confirmWithUndo = function({ message, delayMs = 10000, onConfirm, onUndo }){
                const containerId = 'undo-toast-container';
                let container = document.getElementById(containerId);
                if (!container) {
                    container = document.createElement('div');
                    container.id = containerId;
                    container.style.position = 'fixed';
                    container.style.bottom = '20px';
                    container.style.left = '20px';
                    container.style.right = 'auto';
                    container.style.top = 'auto';
                    container.style.transform = 'none';
                    container.style.zIndex = '1060';
                    container.style.display = 'flex';
                    container.style.flexDirection = 'column';
                    container.style.alignItems = 'flex-start';
                    document.body.appendChild(container);
                }

                const panel = document.createElement('div');
                panel.style.background = 'linear-gradient(135deg, #e18018, #c9690f)';
                panel.style.color = '#ffffff';
                panel.style.border = '1px solid #d97c13';
                panel.style.borderRadius = '12px';
                panel.style.boxShadow = '0 10px 24px rgba(225, 128, 24, 0.15)';
                panel.style.padding = '14px 16px';
                panel.style.marginBottom = '8px';
                panel.style.maxWidth = '520px';
                panel.style.display = 'flex';
                panel.style.alignItems = 'center';
                panel.style.gap = '12px';

                const text = document.createElement('div');
                let remainingMs = typeof delayMs === 'number' ? delayMs : 10000;
                let remaining = Math.floor(remainingMs / 1000);
                const baseMessage = message || 'Se eliminará el registro';
                text.textContent = baseMessage;
                text.style.fontWeight = '600';

                const btnUndo = document.createElement('button');
                btnUndo.textContent = 'Deshacer';
                btnUndo.style.background = '#ffffff';
                btnUndo.style.color = '#e18018';
                btnUndo.style.border = 'none';
                btnUndo.style.borderRadius = '8px';
                btnUndo.style.padding = '8px 14px';
                btnUndo.style.cursor = 'pointer';
                btnUndo.style.fontWeight = '700';
                btnUndo.style.transition = 'all 0.2s ease';
                btnUndo.addEventListener('mouseover', () => {
                    btnUndo.style.background = '#fff8f0';
                    btnUndo.style.transform = 'translateY(-1px)';
                });
                btnUndo.addEventListener('mouseout', () => {
                    btnUndo.style.background = '#ffffff';
                    btnUndo.style.transform = 'translateY(0)';
                });

                const countdown = document.createElement('span');
                countdown.style.marginLeft = 'auto';
                countdown.style.fontSize = '12px';
                countdown.style.color = '#ffffff';
                countdown.style.opacity = '0.9';
                countdown.style.minWidth = '35px';
                countdown.style.textAlign = 'right';

                panel.appendChild(text);
                panel.appendChild(btnUndo);
                panel.appendChild(countdown);
                container.appendChild(panel);

                countdown.textContent = 'en ' + remaining + 's';
                const interval = setInterval(() => {
                    remaining -= 1;
                    countdown.textContent = 'en ' + Math.max(remaining, 0) + 's';
                }, 1000);

                const cleanup = () => {
                    clearInterval(interval);
                    panel.remove();
                };

                let undone = false;
                btnUndo.addEventListener('click', () => {
                    undone = true;
                    try { if (typeof onUndo === 'function') onUndo(); } catch(e){}
                    cleanup();
                    if (window.showNotification) window.showNotification('info', 'Acción cancelada');
                });

                setTimeout(() => {
                    if (!undone) {
                        try { if (typeof onConfirm === 'function') onConfirm(); } catch(e){}
                        if (window.showNotification) window.showNotification('success', 'Acción completada');
                    }
                    cleanup();
                }, remainingMs);

                return Promise.resolve({ scheduled: true });
            };
        })();
    </script>

    {{-- SweetAlert para mensajes de sesión --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let retries = 0;
            const showSuccess = () => {
                if (window.swToast) {
                    window.swToast.fire({
                        icon: 'success',
                        title: @json(session('success'))
                    });
                } else if (retries < 50) {
                    retries++;
                    setTimeout(showSuccess, 100);
                } else if (window.swAlert) {
                    window.swAlert({
                        icon: 'success',
                        title: 'Éxito',
                        text: @json(session('success'))
                    });
                }
            };
            showSuccess();
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let retries = 0;
            const showError = () => {
                if (window.swToast) {
                    window.swToast.fire({
                        icon: 'error',
                        title: @json(session('error'))
                    });
                } else if (retries < 50) {
                    retries++;
                    setTimeout(showError, 100);
                } else if (window.swAlert) {
                    window.swAlert({
                        icon: 'error',
                        title: 'Error',
                        text: @json(session('error'))
                    });
                }
            };
            showError();
        });
    </script>
    @endif

    @if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let retries = 0;
            const showWarning = () => {
                if (window.swToast) {
                    window.swToast.fire({
                        icon: 'warning',
                        title: @json(session('warning'))
                    });
                } else if (retries < 50) {
                    retries++;
                    setTimeout(showWarning, 100);
                } else if (window.swAlert) {
                    window.swAlert({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: @json(session('warning'))
                    });
                }
            };
            showWarning();
        });
    </script>
    @endif

    @if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let retries = 0;
            const showInfo = () => {
                if (window.swToast) {
                    window.swToast.fire({
                        icon: 'info',
                        title: @json(session('info'))
                    });
                } else if (retries < 50) {
                    retries++;
                    setTimeout(showInfo, 100);
                } else if (window.swAlert) {
                    window.swAlert({
                        icon: 'info',
                        title: 'Información',
                        text: @json(session('info'))
                    });
                }
            };
            showInfo();
        });
    </script>
    @endif

    <script>
        // Toggle del sidebar - Colapsable en desktop/tablet (>=576px), overlay en móvil (<576px)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('appSidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle-btn');
            const body = document.body;

            const toggleSidebar = () => {
                if (window.innerWidth >= 576) {
                    // Desktop/Tablet: collapsar/expandir
                    sidebar.classList.toggle('collapsed');
                    body.classList.toggle('sidebar-collapsed');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', sidebar.classList.contains('collapsed') ? 'true' : 'false');
                } else {
                    // Phone: abrir/cerrar drawer
                    sidebar.classList.toggle('open');
                    body.classList.toggle('sidebar-open');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', sidebar.classList.contains('open') ? 'true' : 'false');
                }
            };

            const closeSidebar = () => {
                sidebar.classList.remove('open');
                body.classList.remove('sidebar-open');
                if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
            };

            const applyLayoutByWidth = () => {
                if (window.innerWidth >= 576) {
                    // Modo desktop/tablet: sidebar fijo, sin overlay
                    sidebar.classList.remove('open');
                    body.classList.remove('sidebar-open');
                } else {
                    // Modo phone: sidebar como drawer cerrado por defecto
                    sidebar.classList.remove('collapsed');
                    body.classList.remove('sidebar-collapsed');
                }
            };

            applyLayoutByWidth();
            window.addEventListener('resize', applyLayoutByWidth);

            if (toggleBtn) {
                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            document.addEventListener('click', (e) => {
                if (window.innerWidth < 576 && !sidebar.classList.contains('open')) return;
                const clickInsideSidebar = e.target.closest('#appSidebar');
                const clickToggle = e.target.closest('.sidebar-toggle-btn');
                if (!clickInsideSidebar && !clickToggle && window.innerWidth < 576) {
                    closeSidebar();
                }
            });
        });
    </script>
    
    <!-- Top Navbar Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Admin Menu Dropdown
            const adminMenuBtn = document.querySelector('.admin-menu-btn');
            const adminMenuDropdown = document.querySelector('.admin-menu-dropdown');
            
            if (adminMenuBtn && adminMenuDropdown) {
                adminMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    adminMenuDropdown.style.display = adminMenuDropdown.style.display === 'none' ? 'block' : 'none';
                });
            }
            
            // User Menu Dropdown
            const userMenuBtn = document.querySelector('.user-menu-btn');
            const userMenuDropdown = document.querySelector('.user-menu-dropdown');
            
            if (userMenuBtn && userMenuDropdown) {
                userMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userMenuDropdown.style.display = userMenuDropdown.style.display === 'none' ? 'block' : 'none';
                });
            }
            
            // Cerrar menús al hacer clic afuera
            document.addEventListener('click', (e) => {
                if (adminMenuDropdown && !e.target.closest('.admin-menu-top')) {
                    adminMenuDropdown.style.display = 'none';
                }
                if (userMenuDropdown && !e.target.closest('.user-menu-top')) {
                    userMenuDropdown.style.display = 'none';
                }
            });
            
            // Buscador en la barra superior
            const searchInput = document.getElementById('topSearchInput');
            const clearBtn = document.getElementById('clearSearchBtn');
            const searchBtn = document.getElementById('searchBtn');
            
            if (searchInput) {
                // Mostrar/ocultar botón X según si hay texto
                searchInput.addEventListener('input', () => {
                    clearBtn.style.display = searchInput.value.trim() ? 'inline-block' : 'none';
                });
                
                // Limpiar búsqueda
                if (clearBtn) {
                    clearBtn.addEventListener('click', () => {
                        searchInput.value = '';
                        clearBtn.style.display = 'none';
                        searchInput.focus();
                        
                        // Limpiar filtro según la ruta actual
                        const currentRoute = window.location.pathname;
                        if (currentRoute.includes('proveedores')) {
                            loadSuppliersAjax('/proveedores');
                        } else if (currentRoute.includes('eventos')) {
                            // Recargar eventos sin filtro de búsqueda (AJAX)
                            loadEventsAjax('/eventos');
                        } else if (currentRoute.includes('usuarios')) {
                            // Recargar usuarios sin filtro
                            window.location.href = '/usuarios';
                        } else if (currentRoute.includes('productos')) {
                            // Recargar productos sin filtro
                            window.location.href = '/productos';
                        }
                    });
                }
                
                // Buscar al presionar Enter
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });
                
                // Buscar al hacer click en el botón
                if (searchBtn) {
                    searchBtn.addEventListener('click', performSearch);
                }
                
                // Función para realizar la búsqueda
                function performSearch() {
                    const query = searchInput.value.trim();
                    if (query) {
                        const currentRoute = window.location.pathname;
                        if (currentRoute.includes('proveedores')) {
                            // AJAX para proveedores sin refrescar la página
                            loadSuppliersAjax(`/proveedores?buscar=${encodeURIComponent(query)}`);
                        } else if (currentRoute.includes('eventos')) {
                            // AJAX para eventos sin refrescar la página
                            loadEventsAjax(`/eventos?q=${encodeURIComponent(query)}`);
                        } else if (currentRoute.includes('usuarios')) {
                            window.location.href = `/usuarios?q=${encodeURIComponent(query)}`;
                        } else if (currentRoute.includes('productos')) {
                            window.location.href = `/productos?q=${encodeURIComponent(query)}`;
                        }
                    }
                }

                // Función AJAX para cargar proveedores sin refrescar
                function loadSuppliersAjax(url) {
                    const tableWrapper = document.querySelector('.table-wrapper');
                    if (tableWrapper) {
                        tableWrapper.style.opacity = '0.6';
                        tableWrapper.style.pointerEvents = 'none';
                    }
                    
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'text/html',
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const temp = document.createElement('div');
                        temp.innerHTML = html;
                        
                        const newTable = temp.querySelector('.table-wrapper');
                        
                        if (newTable && tableWrapper) {
                            tableWrapper.innerHTML = newTable.innerHTML;
                            tableWrapper.style.opacity = '1';
                            tableWrapper.style.pointerEvents = 'auto';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableWrapper) {
                            tableWrapper.style.opacity = '1';
                            tableWrapper.style.pointerEvents = 'auto';
                        }
                        if (window.swAlert) {
                            swAlert({ icon: 'error', title: 'Error', text: 'Hubo un error al buscar proveedores' });
                        }
                    });
                }

                // Función AJAX para cargar eventos sin refrescar
                function loadEventsAjax(url) {
                    const eventsContainer = document.getElementById('eventsContainer');
                    if (eventsContainer) {
                        eventsContainer.style.opacity = '0.6';
                        eventsContainer.style.pointerEvents = 'none';
                    }
                    
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        if (eventsContainer) {
                            eventsContainer.innerHTML = html;
                            eventsContainer.style.opacity = '1';
                            eventsContainer.style.pointerEvents = 'auto';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (eventsContainer) {
                            eventsContainer.style.opacity = '1';
                            eventsContainer.style.pointerEvents = 'auto';
                        }
                        if (window.swAlert) {
                            swAlert({ icon: 'error', title: 'Error', text: 'Hubo un error al buscar eventos' });
                        }
                    });
                }
            }
        });
    </script>

    <!-- Campana de notificaciones - Órdenes Pendientes -->
    <script>
        // Función global para cambiar estado desde la campanita
        async function changeOrderStatusFromNotif(orderId, status) {
            // Mensajes de confirmación según el estado
            const confirmMessages = {
                'Preparing': '¿Cambiar estado a En Preparación?',
                'Cancelled': '¿Cancelar esta orden?'
            };
            
            const message = confirmMessages[status] || '¿Cambiar estado?';
            
            // Mostrar confirmación
            const result = await window.swConfirm({
                title: 'Confirmar cambio',
                text: message,
                icon: status === 'Cancelled' ? 'warning' : 'info'
            });
            
            // Si no confirmó, salir
            if (!result.isConfirmed) return;
            
            try {
                const response = await fetch(`{{ url('ordenes') }}/${orderId}/cambiar-estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status })
                });
                
                if (response.ok) {
                    // Recargar órdenes después de cambiar el estado
                    if (window.loadPendingOrdersNotif) {
                        window.loadPendingOrdersNotif();
                    }
                    const statusNames = {
                        'Preparing': 'En Preparación',
                        'Cancelled': 'Cancelada'
                    };
                    if (window.swToast) {
                        window.swToast.fire({
                            icon: 'success',
                            title: `Estado cambiado a ${statusNames[status]}`
                        });
                    }
                }
            } catch (error) {
                console.error('Error al cambiar estado:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const bellBtn = document.getElementById('notificationBellBtn');
            const dropdown = document.getElementById('notificationDropdown');
            const badge = document.getElementById('pendingCountBadge');
            const notificationList = document.getElementById('notificationList');

            if (!bellBtn) return; // No mostrar en rutas de admin global

            // Función para cargar órdenes pendientes
            async function loadPendingOrders() {
                try {
                    const response = await fetch('{{ route("orders.pending-count") }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    const count = data.count || 0;
                    const orders = data.orders || [];

                    // Actualizar badge
                    if (count > 0) {
                        badge.textContent = count <= 99 ? count : '99+';
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }

                    // Actualizar lista
                    if (orders.length > 0) {
                        notificationList.innerHTML = orders.map(order => `
                            <div style="padding: 14px 16px; border-bottom: 1px solid #f3f4f6; background: #fafafa; transition: background 0.2s ease;" 
                                 onmouseover="this.style.background='#f3f4f6'" 
                                 onmouseout="this.style.background='#fafafa'">
                                <div style="font-weight: 600; color: #111827; font-size: 13px; margin-bottom: 6px;">Orden #${order.order_number}</div>
                                <div style="color: #6b7280; font-size: 11px; margin-bottom: 8px;">Hace ${order.created_at}</div>
                                <div style="display: flex; gap: 12px; align-items: flex-start;">
                                    <div style="color: #6b7280; font-size: 12px; max-height: 50px; overflow-y: auto; flex: 1;">
                                        ${(order.items || []).map(item => `<div>• ${item.product_name} (${item.quantity}x)</div>`).join('')}
                                    </div>
                                    <select onchange="if(this.value) changeOrderStatusFromNotif(${order.order_id}, this.value); this.value='';" style="padding: 5px 8px; background: #fff7ed; color: #e18018; border: 1px solid #e18018; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer; flex-shrink: 0; white-space: nowrap; margin-top: -53px;">
                                        <option value=""> PENDIENTE </option>
                                        <option value="Preparing">Preparar</option>
                                        <option value="Cancelled">Cancelar</option>
                                    </select>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        notificationList.innerHTML = '<div style="padding: 20px 16px; text-align: center; color: #9ca3af; font-size: 13px;">Sin órdenes pendientes</div>';
                    }
                } catch (error) {
                    console.error('Error al cargar órdenes pendientes:', error);
                }
            }

            // Guardar la función en window para acceso global desde changeOrderStatusFromNotif
            window.loadPendingOrdersNotif = loadPendingOrders;

            // Cargar órdenes al abrir el dropdown
            bellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
                if (dropdown.style.display === 'block') {
                    loadPendingOrders();
                }
            });

            // Cerrar dropdown al hacer clic afuera
            document.addEventListener('click', function(e) {
                if (!bellBtn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // Manejar tabs de notificaciones
            const notificationTabs = document.querySelectorAll('.notif-tab');
            const notificationContents = document.querySelectorAll('.notif-content');
            const notifFooter = document.getElementById('notifFooter');

            notificationTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Actualizar tabs activos
                    notificationTabs.forEach(t => {
                        if (t.getAttribute('data-tab') === tabName) {
                            t.style.color = '#111827';
                            t.style.borderBottomColor = '#e18018';
                        } else {
                            t.style.color = '#9ca3af';
                            t.style.borderBottomColor = 'transparent';
                        }
                    });
                    
                    // Mostrar/ocultar contenido
                    notificationContents.forEach(content => {
                        if (content.getAttribute('data-content') === tabName) {
                            content.style.display = 'block';
                        } else {
                            content.style.display = 'none';
                        }
                    });
                    
                    // Mostrar/ocultar footer solo en tab de órdenes
                    if (tabName === 'orders') {
                        notifFooter.style.display = 'block';
                    } else {
                        notifFooter.style.display = 'none';
                    }
                });
            });

            // Cargar contador inicial
            loadPendingOrders();

            // Actualizar contador cada 30 segundos
            setInterval(loadPendingOrders, 30000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>