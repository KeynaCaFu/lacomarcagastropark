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
                        <div class="top-help" id="topHelpContainer"></div>
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
