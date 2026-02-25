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
                
                <!-- Usuario administrador al final del sidebar -->
                <div class="sidebar-footer">
                    <div class="admin-hover-wrapper">
                        <div class="admin-info-container">
                            <a href="{{ route('profile.edit') }}" class="admin-info text-decoration-none" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle fa-2x"></i>
                                <span>{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                            </a>
                            <button class="admin-menu-toggle" id="adminMenuToggle" aria-haspopup="true" aria-expanded="false" type="button">
                                <i class="fas fa-chevron-up"></i>
                            </button>
                        </div>
                        <div class="admin-hover-menu" role="menu" aria-label="Acciones de perfil" id="adminMenu">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item" role="menuitem">
                                <i class="fas fa-user-edit"></i> Editar perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1" role="none">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger" role="menuitem">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="main-content" id="mainContent">
                <!-- Header -->
                <div class="header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <h1 class="mb-0">@yield('title', 'Dashboard')</h1>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div id="topBackButtonContainer"></div>
                            <div class="top-help" id="topHelpContainer"></div>
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
                text.textContent = `${baseMessage} en ${remaining}s`;

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

                countdown.textContent = remaining + 's';
                const interval = setInterval(() => {
                    remaining -= 1;
                    countdown.textContent = remaining + 's';
                    text.textContent = `${baseMessage} en ${Math.max(remaining,0)}s`;
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
        // Manejo del menú de admin cuando el sidebar está colapsado
        document.addEventListener('DOMContentLoaded', function() {
            const adminMenuToggle = document.getElementById('adminMenuToggle');
            const adminMenu = document.getElementById('adminMenu');
            const sidebar = document.getElementById('appSidebar');

            if (adminMenuToggle && adminMenu) {
                // Alternar menú al hacer click en flecha
                adminMenuToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    adminMenu.classList.toggle('show');
                    adminMenuToggle.setAttribute('aria-expanded', adminMenu.classList.contains('show') ? 'true' : 'false');
                });

                // Cerrar menú al hacer click afuera
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.admin-hover-wrapper') && !e.target.closest('#adminMenuToggle')) {
                        adminMenu.classList.remove('show');
                        adminMenuToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });
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
    @stack('scripts')
</body>
</html>