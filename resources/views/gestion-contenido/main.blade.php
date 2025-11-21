<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contenidos - CIP</title>
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
            overflow-x: hidden; /* Evita scroll horizontal indeseado */
        }

        /* === SIDEBAR === */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--cip-red) 0%, var(--cip-red-dark) 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1050; /* Mayor que el contenido */
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease-in-out;
        }

        /* Header del Sidebar */
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }
        .sidebar-header img { width: 80px; height: auto; margin-bottom: 10px; }

        /* Botón cerrar en móvil (dentro del sidebar) */
        .btn-close-sidebar {
            position: absolute; top: 10px; right: 10px;
            background: none; border: none; color: white; font-size: 20px;
            display: none; /* Oculto en desktop */
            cursor: pointer;
        }

        /* Menú */
        .sidebar-menu { flex: 1; padding-top: 20px; list-style: none; padding-left: 0; overflow-y: auto; }
        .menu-link {
            display: flex; align-items: center; padding: 15px 25px;
            color: rgba(255,255,255,0.8); text-decoration: none; font-size: 15px;
            transition: all 0.2s; border-left: 4px solid transparent;
        }
        .menu-link:hover { background-color: rgba(255,255,255,0.1); color: white; }
        .menu-link.active {
            background-color: rgba(255,255,255,0.15);
            color: #FFD700; border-left-color: #FFD700; font-weight: bold;
        }
        .menu-link i { width: 30px; font-size: 18px; text-align: center; }

        /* Footer Usuario */
        .user-footer {
            padding: 20px; border-top: 1px solid rgba(255,255,255,0.1);
            background-color: rgba(0,0,0,0.1);
        }
        .user-name { font-weight: bold; font-size: 14px; display: block; }
        .user-email { color: rgba(255,255,255,0.7); font-size: 12px; }
        .btn-logout {
            color: #ffcccc; text-decoration: none; font-size: 13px;
            display: flex; align-items: center; gap: 8px;
            background: none; border: none; padding: 0; margin-top: 10px;
            cursor: pointer; transition: color 0.3s;
        }
        .btn-logout:hover { color: white; }

        /* === CONTENIDO PRINCIPAL === */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: margin-left 0.3s ease-in-out;
            display: flex; flex-direction: column;
        }

        /* Barra Superior Móvil (Solo visible en pantallas pequeñas) */
        .mobile-header {
            display: none; /* Oculto en desktop */
            background-color: white;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 1000;
        }

        .main-content { padding: 30px; flex: 1; }

        /* Overlay para móvil */
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0;
            width: 100%; height: 100%; background: rgba(0,0,0,0.5);
            z-index: 1040; opacity: 0; transition: opacity 0.3s;
        }

        /* === RESPONSIVE (Tablets y Móviles) === */
        @media (max-width: 991.98px) {
            /* Sidebar oculto por defecto */
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            
            /* Mostrar botón cerrar en sidebar */
            .btn-close-sidebar { display: block; }

            /* Contenido ocupa todo el ancho */
            .main-wrapper { margin-left: 0; width: 100%; }

            /* Mostrar Header Móvil */
            .mobile-header { display: flex; }
            
            /* Ajustar padding contenido */
            .main-content { padding: 20px 15px; }
            
            /* Mostrar overlay cuando sidebar está activo */
            .sidebar-overlay.show { display: block; opacity: 1; }
        }
        
        /* Estilos de Formulario */
        .form-label { font-weight: 600; font-size: 14px; color: #444; }
        .form-control, .form-select { font-size: 14px; padding: 10px; border-radius: 6px; }
        .card { border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.04) !important; }
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
        </div>

        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-home"></i> <span>Principal</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('publicaciones.index') }}" class="menu-link">
                    <i class="fas fa-edit"></i> <span>Publicación</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-file"></i> <span>Comunicados</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-calendar"></i> <span>Eventos</span>
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name ?? 'Administrador' }}</span>
                <span class="user-email">{{ Auth::user()->email ?? 'admin@cip.org.pe' }}</span>
            </div>
            <!-- <form action="" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form> -->
            <a type="button" class="btn-logout" href="{{ route('welcome') }}">
               <i class="fas fa-sign-out-alt"></i>Regresar
            </a>
        </div>
    </nav>

    <div class="main-wrapper">
        
        <header class="mobile-header">
            <button class="btn btn-light border" onclick="toggleSidebar()">
                <i class="fas fa-bars text-secondary"></i>
            </button>
            <span class="fw-bold text-secondary">Gestión de Contenidos</span>
            <div style="width: 40px;"></div> 
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

        // Inicializar Summernote
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'Escribe la descripción aquí...',
                tabsize: 2,
                height: 250,
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
        });
    </script>
</body>
</html>