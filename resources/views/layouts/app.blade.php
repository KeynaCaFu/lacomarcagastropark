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
        
        @media (max-width: 768px) {
            .top-navbar {
                padding: 12px 16px !important;
            }
            
            .admin-menu-dropdown,
            .user-menu-dropdown {
                right: auto;
                left: 0;
            }
            
            #topSearchInput {
                font-size: 16px;
            }
            
            #searchBtn span {
                display: none;
            }
            
            #searchBtn {
                padding: 8px 12px !important;
            }
            
            .navbar-toggle {
                display: inline-block !important;
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
<body>
    <!-- Container principal con diseño La Comarca -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (colapsable/ocultable) -->
            <nav class="sidebar drawer" id="appSidebar">
                <div class="sidebar-header">
                    <a href="{{ (auth()->check() && auth()->user()->isAdminGlobal()) ? route('admin.dashboard') : route('dashboard') }}" class="brand text-decoration-none" title="La Comarca">
                        <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca" class="brand-logo">
                    </a>
                    <button class="sidebar-toggle-btn d-lg-none" id="sidebarToggleBtn" aria-controls="appSidebar" aria-expanded="false">
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
                                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products*') ? 'active' : '' }}" data-tooltip="Productos">
                                    <i class="fas fa-box"></i> Productos
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="main-content" id="mainContent">
                <!-- Top Navigation Bar -->
                <div class="top-navbar" style="background: #fff; border-bottom: 1px solid #e5e7eb; padding: 8px 20px; margin: 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-right: 0px; margin-left: -18px;">
                    <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 300px;">
                        <!-- Toggle Sidebar -->
                        <button class="navbar-toggle" id="navbarToggleBtn" style="background: none; border: none; cursor: pointer; padding: 8px; display: none;">
                            <i class="fas fa-bars" style="font-size: 20px; color: #374151;"></i>
                        </button>
                        
                        <!-- Search Bar -->
                        <div style="display: flex; align-items: center; flex: 1; max-width: 400px;">
                            <div style="position: relative; width: 100%; display: flex; gap: 6px;">
                                <div style="position: relative; flex: 1;">
                                    <input type="text" id="topSearchInput" placeholder="Buscar por nombre..." style="width: 100%; padding: 8px 32px 8px 36px; border: 1px solid #e5e7eb; border-radius: 8px 0 0 8px; font-size: 13px; background: #f9fafb; transition: all 0.3s ease;">
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

                    <div style="display: flex; align-items: center; gap: 16px;">
                        <!-- Help Button Container (Usuarios) -->
                        <div id="topHelpContainer"></div>
                        
                        <!-- Help Button Container (Eventos) -->
                        <div id="topHelpEventContainer"></div>

                        <!-- User Icon Menu -->
                        <div class="user-menu-top" style="position: relative;">
                            <button class="user-menu-btn" style="background: none; border: 2px solid #e18018; cursor: pointer; padding: 8px 12px; color: #e18018; border-radius: 8px; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500;">
                                <i class="fas fa-user-circle" style="font-size: 24px;"></i>
                                <span style="font-size: 12px; color: #6b7280;">
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

                <!-- Alertas de Bootstrap/Laravel - Comentadas para usar SweetAlert Toasts en su lugar -->
                {{-- Success messages are now handled by SweetAlert toasts in each view --}}
                {{-- @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}

                {{-- Error messages are handled by SweetAlert in each view --}}
                {{-- @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif --}}

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
            // Esperar a que Swal esté disponible antes de crear el mixin
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

            // Intentar inicializar inmediatamente
            initSwToast();

            // Si falla, intentar en intervalos hasta que Swal esté disponible
            if (typeof Swal === 'undefined') {
                const checkInterval = setInterval(() => {
                    if (typeof Swal !== 'undefined') {
                        initSwToast();
                        clearInterval(checkInterval);
                    }
                }, 100);

                // Limpiar después de 5 segundos si aún no está disponible
                setTimeout(() => clearInterval(checkInterval), 5000);
            }

            // Notificación simple reutilizable (éxito/error/info)
            window.showNotification = function(type, message) {
                const iconMap = { success: 'success', error: 'error', info: 'info', warning: 'warning' };
                const icon = iconMap[type] || 'info';
                if (window.swAlert) return swAlert({ icon, title: '', text: message, timer: 1600, showConfirmButton: false });
                return Promise.resolve();
            };

            // Confirmación con opción de deshacer
            window.confirmWithUndo = function({ message, delayMs = 10000, onConfirm, onUndo }){
                const containerId = 'undo-toast-container';
                let container = document.getElementById(containerId);
                if (!container) {
                    container = document.createElement('div');
                    container.id = containerId;
                    container.style.position = 'fixed';
                    container.style.top = '20px';
                    container.style.right = '20px';
                    container.style.left = 'auto';
                    container.style.transform = 'none';
                    container.style.zIndex = '1060';
                    container.style.display = 'flex';
                    container.style.flexDirection = 'column';
                    container.style.alignItems = 'flex-end';
                    document.body.appendChild(container);
                }

                const panel = document.createElement('div');
                panel.style.background = '#ffffff';
                panel.style.color = '#065f46';
                panel.style.border = '1px solid #10b981';
                panel.style.borderRadius = '12px';
                panel.style.boxShadow = '0 10px 24px rgba(0,0,0,0.08)';
                panel.style.padding = '14px 16px';
                panel.style.marginTop = '8px';
                panel.style.maxWidth = '520px';
                panel.style.display = 'flex';
                panel.style.alignItems = 'center';
                panel.style.gap = '12px';

                const text = document.createElement('div');
                let remainingMs = typeof delayMs === 'number' ? delayMs : 10000;
                let remaining = Math.floor(remainingMs / 1000);
                const baseMessage = message || 'Se eliminará el registro';
                text.textContent = baseMessage;

                const btnUndo = document.createElement('button');
                btnUndo.textContent = 'Deshacer';
                btnUndo.style.background = '#10b981';
                btnUndo.style.color = '#ffffff';
                btnUndo.style.border = 'none';
                btnUndo.style.borderRadius = '8px';
                btnUndo.style.padding = '8px 12px';
                btnUndo.style.cursor = 'pointer';

                const countdown = document.createElement('span');
                countdown.style.marginLeft = 'auto';
                countdown.style.fontSize = '12px';
                countdown.style.color = '#065f46';
                countdown.style.opacity = '0.8';

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
    <script>
        // Toggle del sidebar - Colapsable en desktop (>=1024px), overlay en móvil/tablet (<1024px)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('appSidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle-btn');
            const body = document.body;

            const toggleSidebar = () => {
                if (window.innerWidth >= 1024) {
                    // Desktop: collapsar/expandir
                    sidebar.classList.toggle('collapsed');
                    body.classList.toggle('sidebar-collapsed');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', sidebar.classList.contains('collapsed') ? 'true' : 'false');
                } else {
                    // Mobile: abrir/cerrar
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
                if (window.innerWidth >= 1024) {
                    // Modo desktop: sidebar fijo, sin overlay
                    sidebar.classList.remove('open');
                    body.classList.remove('sidebar-open');
                } else {
                    // Modo móvil/tablet: sidebar como drawer cerrado por defecto
                    sidebar.classList.remove('collapsed');
                    body.classList.remove('sidebar-collapsed');
                }
            };

            // Aplicar estado inicial según ancho actual
            applyLayoutByWidth();

            // Escuchar cambios de tamaño de ventana
            window.addEventListener('resize', applyLayoutByWidth);

            if (toggleBtn) {
                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Cerrar al hacer clic fuera en móviles
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 1024 && !sidebar.classList.contains('open')) return;
                const clickInsideSidebar = e.target.closest('#appSidebar');
                const clickToggle = e.target.closest('.sidebar-toggle-btn');
                if (!clickInsideSidebar && !clickToggle && window.innerWidth < 1024) {
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
                        if (currentRoute.includes('usuarios')) {
                            window.location.href = `/usuarios?q=${encodeURIComponent(query)}`;
                        } else if (currentRoute.includes('eventos')) {
                            window.location.href = `/eventos?q=${encodeURIComponent(query)}`;
                        } else if (currentRoute.includes('productos')) {
                            window.location.href = `/productos?q=${encodeURIComponent(query)}`;
                        }
                    }
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>