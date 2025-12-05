<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contenidos - CIP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 270px;
            --cip-red: #AD2B2E;
            --cip-red-dark: #801a1d;
            --cip-accent: #FFD700;
            --bg-light: #f3f4f6;
            --text-dark: #333;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* === SIDEBAR === */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(160deg, var(--cip-red) 0%, #6d1215 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            box-shadow: 5px 0 25px rgba(0,0,0,0.15);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Header del Sidebar */
        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
            background: rgba(0,0,0,0.1);
        }
        .sidebar-header img { 
            width: 90px; 
            height: auto; 
            margin-bottom: 10px; 
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
            transition: transform 0.3s;
        }
        .sidebar-header img:hover { transform: scale(1.05); }

        /* Botón cerrar en móvil */
        .btn-close-sidebar {
            position: absolute; top: 15px; right: 15px;
            background: rgba(255,255,255,0.2); 
            border: none; color: white; 
            width: 35px; height: 35px; border-radius: 50%;
            display: none; align-items: center; justify-content: center;
            cursor: pointer; transition: background 0.3s;
        }
        .btn-close-sidebar:hover { background: rgba(255,255,255,0.4); }

        /* Botón "Ir al Sitio Web" */
        .sidebar-action { padding: 15px 20px; }
        .btn-visit-site {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        .btn-visit-site:hover {
            background-color: white;
            color: var(--cip-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Menú */
        .sidebar-menu { 
            flex: 1; 
            padding: 10px 15px; 
            list-style: none; 
            overflow-y: auto;
            /* Scrollbar personalizado fino */
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
        }
        
        .menu-item { margin-bottom: 5px; }

        .menu-link {
            display: flex; align-items: center; 
            padding: 12px 20px;
            color: rgba(255,255,255,0.85); 
            text-decoration: none; 
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-link i { 
            width: 30px; 
            font-size: 18px; 
            text-align: center; 
            transition: transform 0.3s; 
        }

        /* Animaciones Hover Sidebar */
        .menu-link:hover { 
            background-color: rgba(255,255,255,0.1); 
            color: white; 
            padding-left: 25px; /* Pequeño desplazamiento a la derecha */
        }
        .menu-link:hover i { 
            transform: scale(1.2); 
            color: var(--cip-accent);
        }

        .menu-link.active {
            background-color: white;
            color: var(--cip-red);
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .menu-link.active i { color: var(--cip-red); }

        /* Footer Usuario */
        .user-footer {
            padding: 20px; 
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
            backdrop-filter: blur(5px);
        }
        .user-info { margin-bottom: 15px; }
        .user-name { font-weight: 600; font-size: 14px; display: block; color: white; }
        .user-email { color: rgba(255,255,255,0.6); font-size: 12px; }
        
        .btn-logout {
            color: #ffb3b3; text-decoration: none; font-size: 13px;
            display: flex; align-items: center; gap: 8px;
            background: none; border: none; padding: 5px 0;
            cursor: pointer; transition: all 0.3s;
        }
        .btn-logout:hover { color: white; transform: translateX(5px); }

        /* === CONTENIDO PRINCIPAL === */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: margin-left 0.3s ease-in-out;
            display: flex; flex-direction: column;
        }

        .main-content { 
            padding: 30px; 
            flex: 1; 
            animation: fadeContent 0.6s ease-out; 
        }

        @keyframes fadeContent {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background-color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 1000;
        }

        /* Overlay */
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0;
            width: 100%; height: 100%; background: rgba(0,0,0,0.6);
            z-index: 1040; opacity: 0; transition: opacity 0.3s;
            backdrop-filter: blur(3px);
        }

        /* === RESPONSIVE === */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .btn-close-sidebar { display: flex; }
            .main-wrapper { margin-left: 0; width: 100%; }
            .mobile-header { display: flex; }
            .main-content { padding: 20px 15px; }
            .sidebar-overlay.show { display: block; opacity: 1; }
        }
        
        /* UI Tweaks */
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05) !important; overflow: hidden; }
        .card-header { background-color: white; border-bottom: 1px solid #f0f0f0; padding: 15px 20px; }
        .btn-primary { background-color: var(--cip-red); border-color: var(--cip-red); }
        .btn-primary:hover { background-color: var(--cip-red-dark); border-color: var(--cip-red-dark); }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="btn-close-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
            <img src="{{ asset('img/logo.png') }}" alt="Logo CIP">
            <div class="small text-white-50 fw-bold mt-1">PANEL ADMINISTRATIVO</div>
        </div>

        <div class="sidebar-action">
            <a href="{{ route('welcome') }}" target="_blank" class="btn-visit-site">
                <i class="fas fa-external-link-alt"></i> Ir al Sitio Web
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="{{ route('gestion-contenido') }}" class="menu-link {{ request()->routeIs('gestion-contenido') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('publicaciones.index') }}" class="menu-link {{ request()->routeIs('publicaciones*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i> <span>Publicaciones</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('comunicados.index') }}" class="menu-link {{ request()->routeIs('comunicados*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i> <span>Comunicados</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('eventos.index') }}" class="menu-link {{ request()->routeIs('eventos*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> <span>Eventos</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('organizacion-gestion.index') }}" class="menu-link {{ request()->routeIs('organizacion-gestion*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap"></i> <span>Organización</span>
                </a>
            </li>
             <li class="menu-item">
                <a href="{{ route('documentos-gestion.index') }}" class="menu-link {{ request()->routeIs('documentos-gestion*') ? 'active' : '' }}">
                    <i class="fas fa-folder-open"></i> <span>Documentación</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('calculadoras-gestion.index') }}" class="menu-link {{ request()->routeIs('calculadoras*') ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i> <span>Calculadoras</span>
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name ?? 'Administrador' }}</span>
                <span class="user-email">{{ Auth::user()->email ?? 'admin@cip.org.pe' }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </nav>

    <div class="main-wrapper">
        
        <header class="mobile-header">
            <button class="btn btn-light border shadow-sm" onclick="toggleSidebar()">
                <i class="fas fa-bars text-dark"></i>
            </button>
            <span class="fw-bold text-dark">Gestión de Contenidos</span>
            
            <a href="{{ route('welcome') }}" target="_blank" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script>
        // Lógica del Menú Móvil
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Inicializar Summernote (Configuración mejorada)
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
                    ],
                    callbacks: {
                        onImageUpload: function(files) {
                            // Aquí podrías agregar lógica para subir imágenes al servidor si fuera necesario
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>