<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'La Comarca - Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS Personalizado La Comarca -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fixes.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        #sidebarToggleBtn {
            border: 1px solid #6c757d !important;
            transition: all 0.3s ease;
        }
        
        #sidebarToggleBtn:hover {
            background-color: #84878a;
            border-color: #495057 !important;
        }
        
        #sidebarToggleBtn:focus {
            border-color: #495057 !important;
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Container principal con diseño La Comarca -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (overlay/ocultable) -->
            <nav class="sidebar drawer" id="appSidebar">
                <div class="sidebar-header">
                    <a href="{{ route('dashboard') }}" class="brand text-decoration-none">
                        <span class="brand-text">La Comarca</span>
                    </a>
                    <button class="btn btn-sm btn-outline-light ms-auto d-md-none" id="sidebarCloseBtn" aria-label="Cerrar menú">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="sidebar-menu">
                    <ul>
                        @php $mode = auth()->user()->isAdminGlobal() ? 'global' : 'local'; @endphp

                        @if($mode === 'global')
                            <li>
                                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users*') ? 'active' : '' }}">
                                    <i class="fas fa-users"></i> Usuarios
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('eventos.index') }}" class="{{ request()->routeIs('eventos*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-days"></i> Eventos
                                </a>
                            </li>

                            <li class="mt-3">
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-danger text-decoration-none w-100 text-start">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products*') ? 'active' : '' }}">
                                    <i class="fas fa-box"></i> Productos
                                </a>
                            </li>
                            <li class="mt-3">
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-danger text-decoration-none w-100 text-start">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <!-- Usuario administrador al final del sidebar -->
                <div class="sidebar-footer">
                    <a href="{{ route('profile.edit') }}" class="admin-info text-decoration-none">
                        <i class="fas fa-user-circle fa-2x"></i>
                        <span>{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                    </a>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="main-content" id="mainContent">
                <!-- Header -->
                <div class="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary me-3" id="sidebarToggleBtn" aria-controls="appSidebar" aria-expanded="false">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h1 class="mb-0">@yield('title', 'Dashboard')</h1>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div id="topBackButtonContainer"></div>
                            <div class="top-help" id="topHelpContainer"></div>
                        </div>
                    </div>
                </div>

                <!-- Alertas de Bootstrap/Laravel -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

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

            // Notificación simple reutilizable (éxito/error/info)
            window.showNotification = function(type, message) {
                const iconMap = { success: 'success', error: 'error', info: 'info', warning: 'warning' };
                const icon = iconMap[type] || 'info';
                if (window.swAlert) return swAlert({ icon, title: '', text: message, timer: 1600, showConfirmButton: false });
                return Promise.resolve();
            };

            // Confirmación con opción de deshacer: presenta una notificación con botón "Deshacer" por unos segundos.
            // onConfirm se ejecuta al expirar el tiempo si no se deshace. onUndo cancela la acción.
            window.confirmWithUndo = function({ message, delayMs = 5000, onConfirm, onUndo }){
                // Render minimal toast-like panel
                const containerId = 'undo-toast-container';
                let container = document.getElementById(containerId);
                if (!container) {
                    container = document.createElement('div');
                    container.id = containerId;
                    container.style.position = 'fixed';
                    container.style.bottom = '20px';
                    container.style.right = '20px';
                    container.style.zIndex = '1060';
                    document.body.appendChild(container);
                }

                const panel = document.createElement('div');
                panel.style.background = '#111827';
                panel.style.color = '#fff';
                panel.style.borderRadius = '12px';
                panel.style.boxShadow = '0 10px 24px rgba(0,0,0,0.25)';
                panel.style.padding = '14px 16px';
                panel.style.marginTop = '8px';
                panel.style.display = 'flex';
                panel.style.alignItems = 'center';
                panel.style.gap = '12px';

                const text = document.createElement('div');
                text.textContent = message || 'Se eliminará el registro';

                const btnUndo = document.createElement('button');
                btnUndo.textContent = 'Deshacer';
                btnUndo.style.background = '#f59e0b';
                btnUndo.style.color = '#111827';
                btnUndo.style.border = 'none';
                btnUndo.style.borderRadius = '8px';
                btnUndo.style.padding = '8px 12px';
                btnUndo.style.cursor = 'pointer';

                const countdown = document.createElement('span');
                countdown.style.marginLeft = 'auto';
                countdown.style.fontSize = '12px';
                countdown.style.opacity = '0.8';

                panel.appendChild(text);
                panel.appendChild(btnUndo);
                panel.appendChild(countdown);
                container.appendChild(panel);

                let remaining = Math.floor(delayMs / 1000);
                countdown.textContent = remaining + 's';
                const interval = setInterval(() => {
                    remaining -= 1;
                    countdown.textContent = remaining + 's';
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
                }, delayMs);

                return Promise.resolve({ scheduled: true });
            };
        })();
    </script>
    <script>
        // Toggle del sidebar tipo drawer (overlay)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('appSidebar');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const closeBtn = document.getElementById('sidebarCloseBtn');
            const body = document.body;

            const openSidebar = () => {
                sidebar.classList.add('open');
                body.classList.add('sidebar-open');
                toggleBtn.setAttribute('aria-expanded', 'true');
            };
            const closeSidebar = () => {
                sidebar.classList.remove('open');
                body.classList.remove('sidebar-open');
                toggleBtn.setAttribute('aria-expanded', 'false');
            };

            if (toggleBtn) {
                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (sidebar.classList.contains('open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    closeSidebar();
                });
            }

            // Cerrar al hacer clic fuera en móviles/escritorio
            document.addEventListener('click', (e) => {
                if (!sidebar.classList.contains('open')) return;
                const clickInsideSidebar = e.target.closest('#appSidebar');
                const clickToggle = e.target.closest('#sidebarToggleBtn');
                if (!clickInsideSidebar && !clickToggle) {
                    closeSidebar();
                }
            });
        });
    </script>
    <style>
        /* Drawer overlay styles */
        .drawer {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: #232c0c;
            color: #fff;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1040; /* above main */
            box-shadow: 2px 0 12px rgba(0,0,0,0.3);
            padding-bottom: 1rem;
        }
        .drawer.open {
            transform: translateX(0);
        }

        /* Layout adjustments when sidebar open on larger screens */
        @media (min-width: 992px) {
            body.sidebar-open #mainContent {
                margin-left: 280px;
            }
        }

        /* Ensure main content spans full width when closed */
        #mainContent {
            width: 100%;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-menu {
            padding: 1rem;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* Backdrop when open on small screens */
        @media (max-width: 991.98px) {
            body.sidebar-open::after {
                content: '';
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.35);
                z-index: 1030;
            }
        }
    </style>
    @stack('scripts')
</body>
</html>
