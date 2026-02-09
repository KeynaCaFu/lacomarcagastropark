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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        #sidebarToggleBtn {
            color: #ff9900 !important;
            transition: all 0.3s ease !important;
            background: #232c0c !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            width: 28px !important;
            height: 28px !important;
            position: absolute !important;
            right: -14px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            border-radius: 3px !important;
            z-index: 1041 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
            font-size: 0.9rem !important;
        }
        
        #sidebarToggleBtn:hover {
            color: #ffb84d !important;
            background: #2a3410 !important;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3) !important;
        }
        
        #sidebarToggleBtn:focus {
            outline: none !important;
            color: #ff9900 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
        }
        
        /* Estilos para Dashboard */
        .dash-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 20px; }
        .stats-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:16px; margin-bottom: 20px; }
        .stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; display:flex; align-items:center; gap:12px; }
        .stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
        .ic-total { background:#e0f2fe; color:#0369a1; }
        .ic-active { background:#dcfce7; color:#16a34a; }
        .ic-inactive { background:#fee2e2; color:#dc2626; }
        .ic-upcoming { background:#fef3c7; color:#b45309; }
        .stat-title { font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.02em; }
        .stat-number { font-size:28px; font-weight:800; color:#111827; }
        .quick-links { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:12px; margin: 8px 0 20px; }
        .quick-link { display:flex; align-items:center; gap:10px; border:1px solid #e5e7eb; background:#ffffff; border-radius:12px; padding:14px 16px; text-decoration:none; color:#111827; transition:transform .2s ease, box-shadow .2s ease; }
        .quick-link:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
        .quick-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
        .qi-users { background:#e0f2fe; color:#0369a1; }
        .qi-stores { background:#ede9fe; color:#6d28d9; }
        .qi-events { background:#ffe4e6; color:#be123c; }
        .qi-new { background:#dcfce7; color:#166534; }
        .quick-text { display:flex; flex-direction:column; }
        .quick-title { font-weight:700; }
        .quick-hint { font-size:12px; color:#6b7280; }
        .cards-grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
        @media (max-width: 992px){ .cards-grid{ grid-template-columns: 1fr; } }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; }
        .card-header { padding:12px 16px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
        .card-title { margin:0; font-weight:700; color:#111827; }
        .card-body { padding:12px 16px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { background:#f9fafb; font-weight:700; color:#374151; }
        .badge { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:700; }
        .bd-active { background:#dcfce7; color:#166534; }
        .bd-inactive { background:#fee2e2; color:#991b1b; }
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
        // Toggle del sidebar - Colapsable en desktop, overlay en móvil
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('appSidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle-btn');
            const body = document.body;

            const toggleSidebar = () => {
                if (window.innerWidth >= 992) {
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

            // Escuchar cambios de tamaño de ventana
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('open');
                    body.classList.remove('sidebar-open');
                } else {
                    sidebar.classList.remove('collapsed');
                    body.classList.remove('sidebar-collapsed');
                }
            });

            if (toggleBtn) {
                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Cerrar al hacer clic fuera en móviles
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 992 && !sidebar.classList.contains('open')) return;
                const clickInsideSidebar = e.target.closest('#appSidebar');
                const clickToggle = e.target.closest('.sidebar-toggle-btn');
                if (!clickInsideSidebar && !clickToggle && window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });
    </script>
    <style>
        /* Drawer/Sidebar styles */
        .drawer {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: #232c0c;
            color: #fff;
            transform: translateX(-100%);
            transition: transform 0.3s ease, width 0.3s ease;
            z-index: 1040;
            box-shadow: 2px 0 12px rgba(0,0,0,0.3);
            padding-bottom: 1rem;
            display: flex;
            flex-direction: column;
            overflow: visible !important;
        }

        .drawer.open {
            transform: translateX(0);
        }

        .drawer.collapsed {
            width: 100px;
        }

        .drawer:not(.collapsed) {
            width: 280px;
        }

        /* Sidebar Header */
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
            justify-content: center;
        }

        .brand-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .drawer.collapsed .brand-logo {
            width: 50px;
            height: 50px;
        }

        .drawer:not(.collapsed) .brand-logo {
            width: 50px;
            height: 105px;
            padding: -21px;
        }

        .brand-text {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            transition: opacity 0.3s ease;
            display: none;
        }

        .drawer.collapsed .brand-text {
            display: none;
        }

        .drawer:not(.collapsed) .brand-text {
            display: inline;
        }

        /* Sidebar Menu */
        .sidebar-menu {
            padding: 1rem;
            flex: 1;
            overflow-y: auto;
            transition: padding 0.3s ease;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 17px;
            transition: margin-bottom 0.3s ease;
        }

        .sidebar-menu a {
            transition: all 0.3s ease;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 10px 15px;
            color: #fff;
            text-decoration: none;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #ff9900;
        }

        .sidebar-menu i {
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        /* Estado colapsado */
        .drawer.collapsed .sidebar-menu a {
            padding: 15px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0;
            position: relative;
        }

        .drawer:not(.collapsed) .sidebar-menu a {
            display: flex;
            padding: 10px 20px;
            font-size: inherit;
        }

        .drawer.collapsed .sidebar-menu a i {
            margin-right: 0;
            font-size: 1.5rem;
        }

        /* Tooltips para estado colapsado */
        .drawer.collapsed .sidebar-menu a::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: #1f2937;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
            margin-left: 12px;
            z-index: 1050;
        }

        .drawer.collapsed .sidebar-menu a:hover::after {
            opacity: 1;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            position: relative;
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: auto;
            transition: padding 0.3s ease;
            overflow: visible !important;
        }

        .drawer.collapsed .sidebar-footer {
            padding: 1rem 0.5rem;
            overflow: visible !important;
        }

        .drawer:not(.collapsed) .sidebar-footer {
            padding: 1rem;
        }

        /* Admin Info */
        .admin-info {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .admin-info:hover {
            background-color: rgba(72, 90, 26, 0.3);
            color: rgba(255, 255, 255, 1);
        }

        .admin-info i {
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.3s ease;
            font-size: 1.5rem;
        }

        .admin-info:hover i {
            color: #ff9900;
        }

        .admin-info span {
            font-size: 0.95rem;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .drawer.collapsed .admin-info {
            display: flex;
            justify-content: center;
            padding: 0.5rem;
            flex: 1;
        }

        .admin-info-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
        }

        .admin-info-container .admin-info {
            flex: 1;
        }

        .admin-menu-toggle {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ff9900;
            border-radius: 6px;
            padding: 0.5rem;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .admin-menu-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffb84d;
        }

        .admin-menu-toggle i {
            transition: transform 0.3s ease;
            font-size: 0.85rem;
        }

        .admin-menu-toggle[aria-expanded="true"] i {
            transform: rotate(180deg);
        }

        .drawer.collapsed .admin-info span {
            display: none;
        }

        .drawer:not(.collapsed) .admin-info span {
            display: inline;
        }

        /* Admin Hover Wrapper */
        .admin-hover-wrapper {
            position: relative;
            display: block;
        }

        .admin-hover-menu {
            position: absolute;
            bottom: 100%;
            left: 0;
            background: #ffffff;
            color: #1f2937;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 10px 24px rgba(0,0,0,0.18);
            border-radius: 10px;
            padding: 0.75rem 0.5rem;
            min-width: 180px;
            display: none !important;
            z-index: 1051;
            margin-bottom: 10px;
        }

        .drawer.collapsed .admin-hover-menu {
            position: absolute !important;
            left: auto !important;
            right: -220px !important;
            bottom: 0 !important;
            top: auto !important;
            transform: none !important;
            margin-bottom: 0 !important;
            min-width: 200px !important;
        }

        .admin-hover-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .35rem .5rem;
            border-radius: 6px;
            color: #1f2937;
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: #f3f4f6;
            outline: none;
        }

        /* Main Content */
        #mainContent {
            width: 100%;
            position: relative;
            z-index: 1;
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        /* Layout adjustments */
        @media (min-width: 992px) {
            #mainContent {
                margin-left: 280px;
            }
            
            body.sidebar-collapsed #mainContent {
                margin-left: 100px;
            }
        }

        @media (max-width: 991.98px) {
            #mainContent {
                margin-left: 0;
            }
        }

        /* Header */
        .header {
            position: relative;
            z-index: 1050;
        }

        /* Backdrop cuando drawer está abierto */
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