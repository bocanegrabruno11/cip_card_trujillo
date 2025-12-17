<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrativo')</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --cip-red: #AD2B2E;
            --cip-red-dark: #801a1d;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--cip-red) 0%, var(--cip-red-dark) 100%);
            color: white;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: transform .3s ease-in-out;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }
        .sidebar-header img { width: 80px; }
        .btn-close-sidebar {
            position: absolute;
            top: 10px; right: 10px;
            display: none;
            background: none; border: none;
            color: white;
            font-size: 20px;
        }
        .sidebar-menu {
            list-style: none;
            padding-left: 0;
            flex: 1;
            padding-top: 20px;
        }
        .menu-link {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: 0.3s;
        }
        .menu-link i { width: 30px; }
        .menu-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .menu-link.active {
            background: rgba(255,255,255,0.15);
            border-left-color: white;
            color: #fff;
        }
        .user-footer {
            padding: 20px;
            font-size: 14px;
            background: rgba(0,0,0,0.1);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .btn-logout {
            color: #ffcccc;
            text-decoration: none;
            background: none;
            border: none;
            padding: 5px 0;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: color 0.3s;
        }
        .btn-logout:hover {
            color: white;
        }
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }
        .mobile-header {
            display: none;
        }
        .main-content { 
            padding: 30px; 
            flex: 1;
        }

        @media(max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .btn-close-sidebar { display: block; }
            .main-wrapper { margin-left: 0; }
            .mobile-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px 20px;
                background: #fff;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .main-content {
                padding: 20px;
            }
        }
        
        /* Estilos adicionales que pueden ser útiles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .btn-primary {
            background-color: var(--cip-red);
            border-color: var(--cip-red);
        }
        
        .btn-primary:hover {
            background-color: var(--cip-red-dark);
            border-color: var(--cip-red-dark);
        }
    </style>
    
    @stack('styles')
</head>

<body>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="btn-close-sidebar" onclick="toggleSidebar()">
                <i class="fa fa-times"></i>
            </button>
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
        </div>

        <ul class="sidebar-menu">
            <!-- Menú completo -->
            <li>
                <a href="{{ route('Admin.dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('Admin.Arbitraje') }}" class="menu-link {{ request()->routeIs('Arbitraje') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Ver Arbitrajes
                </a>
            </li>
            <li>
                <a href="{{ route('admin.solicitudes.index') }}" class="menu-link {{ request()->routeIs('admin.solicitudes.index') ? 'active' : '' }}">
                    <i class="fas fa-file "></i> Permisos de Repositorio
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <strong>{{ Auth::user()->name ?? 'Administrador' }}</strong><br>
            <span>{{ Auth::user()->email ?? 'admin@cip.org.pe' }}</span>

            <button type="button" class="btn-logout mt-2 d-block" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </div>
    </nav>

    <div class="main-wrapper">
        <header class="mobile-header">
            <button class="btn btn-light border shadow-sm" onclick="toggleSidebar()">
                <i class="fas fa-bars text-dark"></i>
            </button>
            <span class="fw-bold text-dark">@yield('page-title', 'Panel Administrativo')</span>
            <a href="{{ route('welcome') }}" target="_blank" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </header>

        <main class="main-content">
            <!-- Aquí se inyectará el contenido de cada página -->
            @yield('content')
        </main>
    </div>

    <!-- Modal de Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold">Cerrar Sesión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <i class="fas fa-sign-out-alt fa-3x text-secondary mb-3"></i>
                    <h5 class="fw-bold mb-2">¿Deseas salir del sistema?</h5>
                    <p class="text-muted mb-0">Selecciona "Salir" si estás listo para finalizar tu sesión actual.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Salir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts globales -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para mostrar/ocultar el sidebar en móviles
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Cerrar sidebar al hacer clic fuera en móviles
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const isMobile = window.innerWidth <= 991;
            
            if (isMobile && sidebar.classList.contains('show')) {
                if (!sidebar.contains(event.target) && !event.target.closest('.mobile-header')) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>