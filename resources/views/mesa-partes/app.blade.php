<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mesa de Partes - CIP')</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

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
            overflow-y: auto;
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
            cursor: pointer;
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
        
        /* Estilos para el menú desplegable */
        .menu-item-dropdown {
            position: relative;
        }
        
        .menu-link-dropdown {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: 0.3s;
            cursor: pointer;
        }
        
        .menu-link-dropdown:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        
        .menu-link-dropdown.active {
            background: rgba(255,255,255,0.15);
            border-left-color: white;
            color: #fff;
        }
        
        .menu-link-dropdown .fa-chevron-down {
            transition: transform 0.3s;
            font-size: 12px;
        }
        
        .menu-link-dropdown.expanded .fa-chevron-down {
            transform: rotate(180deg);
        }
        
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: rgba(0,0,0,0.2);
        }
        
        .submenu.show {
            max-height: 500px;
        }
        
        .submenu-link {
            display: flex;
            align-items: center;
            padding: 12px 25px 12px 55px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
        }
        
        .submenu-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            padding-left: 60px;
        }
        
        .submenu-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-weight: 600;
        }
        
        .submenu-link i {
            width: 20px;
            margin-right: 10px;
            font-size: 12px;
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
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }
        
        .sidebar-overlay.show {
            display: block;
        }

        @media(max-width: 991px) {
            .sidebar { 
                transform: translateX(-100%); 
                z-index: 1050;
            }
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
        
        /* Estilos adicionales útiles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .btn-primary {
            background-color: var(--cip-red);
            border-color: var(--cip-red);
        }
        
        .btn-primary:hover {
            background-color: var(--cip-red-dark);
            border-color: var(--cip-red-dark);
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
    
    @stack('styles')
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="btn-close-sidebar" onclick="toggleSidebar()">
                <i class="fa fa-times"></i>
            </button>
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <div class="text-white small mt-2">Mesa de Partes</div>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </li>
            <li>
                <a href="{{ route('persona.actualizar') }}" class="menu-link {{ request()->routeIs('persona.actualizar') ? 'active' : '' }}">
                    <i class="fas fa-user-edit"></i> Actualizar Información
                </a>
            </li>

            <li>
                <a href="{{ route('casilla.index') }}" class="menu-link {{ request()->routeIs('casilla.index') ? 'active' : '' }}">
                    <span class="position-relative">
                        <i class="fas fa-envelope"></i>
                        
                        @if(isset($notificaciones_sin_leer) && $notificaciones_sin_leer > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                style="font-size: 0.6rem; padding: 0.25em 0.5em; margin-left: -5px; border: 2px solid var(--cip-red);">
                                {{ $notificaciones_sin_leer > 9 ? '9+' : $notificaciones_sin_leer }}
                            </span>
                        @endif
                    </span>
                    <span class="ms-2">Casilla electrónica</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('mesa.virtual') }}" class="menu-link {{ request()->routeIs('mesa.virtual') ? 'active' : '' }}">
                    <i class="fas fa-laptop"></i> Mesa de Partes Virtual
                </a>
            </li>
            <!-- Menú Desplegable de Arbitraje -->
            <li class="menu-item-dropdown">
                <div class="menu-link-dropdown {{ request()->routeIs('arbitraje') || request()->routeIs('RegistrosArbitraje') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                    <span>
                        <i class="fas fa-scale-balanced"></i> Arbitraje
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <ul class="submenu {{ request()->routeIs('arbitraje') || request()->routeIs('RegistrosArbitraje') ? 'show' : '' }}">
                    <li>
                        <a href="{{ route('arbitraje') }}" class="submenu-link {{ request()->routeIs('arbitraje') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle"></i> Nuevo Arbitraje
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('RegistrosArbitraje') }}" class="submenu-link {{ request()->routeIs('RegistrosArbitraje') ? 'active' : '' }}">
                            <i class="fas fa-list"></i> Registros de Arbitraje
                        </a>
                    </li>
                </ul>
            </li>
            
  <!-- Menú Desplegable de JRD -->
<li class="menu-item-dropdown">
    <div class="menu-link-dropdown {{ request()->routeIs('jrd') || request()->routeIs('registros.jrd') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
        <span>
            <i class="fas fa-gavel"></i> JPRD
        </span>
        <i class="fas fa-chevron-down"></i>
    </div>
    <ul class="submenu {{ request()->routeIs('jrd') || request()->routeIs('registros.jrd') ? 'show' : '' }}">
        <li>
            <a href="{{ route('jrd') }}" class="submenu-link {{ request()->routeIs('jrd') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i> Nuevo JPRD
            </a>
        </li>
        <li>
            <a href="{{ route('registros.jrd') }}" class="submenu-link {{ request()->routeIs('registros.jrd') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Registros de JPRD
            </a>
        </li>
    </ul>
</li>
            <!-- Agrega más items según necesites -->
            @yield('sidebar-menu')
        </ul>

        <div class="user-footer">
            <strong>{{ Auth::user()->name ?? 'Usuario' }}</strong><br>
            <span>{{ Auth::user()->email ?? 'usuario@cip.org.pe' }}</span>

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
            <span class="fw-bold text-dark">@yield('page-title', 'Mesa de Partes')</span>
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
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" id="logoutCancelBtn">Cancelar</button>
                
                <form action="{{ route('logout') }}" method="POST" id="logoutFormAction">
                    @csrf
                    <button type="submit" class="btn btn-danger px-4 fw-bold" id="logoutSubmitBtn">
                        <span id="logoutBtnText">Salir</span>
                        <i id="logoutBtnSpinner" class="fas fa-circle-notch fa-spin d-none"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Scripts globales -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
    // Escuchamos el evento submit del formulario de salida
    document.getElementById('logoutFormAction').addEventListener('submit', function() {
        const btnSubmit = document.getElementById('logoutSubmitBtn');
        const btnCancel = document.getElementById('logoutCancelBtn');
        const text = document.getElementById('logoutBtnText');
        const spinner = document.getElementById('logoutBtnSpinner');

        // Desactivamos botones inmediatamente
        btnSubmit.disabled = true;
        btnCancel.disabled = true;

        // Feedback visual
        text.textContent = "Saliendo...";
        spinner.classList.remove('d-none');
    });
</script>
    <script>
        // Función para mostrar/ocultar el sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Función para toggle del submenu
        function toggleSubmenu(element) {
            const submenu = element.nextElementSibling;
            element.classList.toggle('expanded');
            submenu.classList.toggle('show');
        }

        // Cerrar sidebar al hacer clic en el overlay
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            toggleSidebar();
        });

        // Expandir automáticamente el submenu si hay una ruta activa
        document.addEventListener('DOMContentLoaded', function() {
            const activeSubmenu = document.querySelector('.submenu.show');
            if (activeSubmenu) {
                const parentDropdown = activeSubmenu.previousElementSibling;
                if (parentDropdown) {
                    parentDropdown.classList.add('expanded');
                }
            }
        });

        // Inicializar Summernote si existe
        $(document).ready(function() {
            if ($('#summernote').length > 0) {
                $('#summernote').summernote({
                    placeholder: 'Escribe el contenido aquí...',
                    tabsize: 2,
                    height: 300,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>