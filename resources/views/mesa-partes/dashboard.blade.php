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
        .user-footer {
            padding: 20px;
            font-size: 14px;
            background: rgba(0,0,0,0.1);
            border-top: 1px solid rgba(255,255,255,0.1);
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
        .main-content { padding: 30px; flex: 1; }

        @media(max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .btn-close-sidebar { display: block; }
            .main-wrapper { margin-left: 0; }
            .mobile-header {
                display: flex;
                justify-content: space-between;
                padding: 10px 20px;
                background: #fff;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="btn-close-sidebar" onclick="toggleSidebar()">
                <i class="fa fa-times"></i>
            </button>
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
        </div>

        <ul class="sidebar-menu">
             <li>
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="fa fa-sync"></i> Inicio
                </a>
            </li>
            <li>
                <a href="{{ route('persona.actualizar') }}" class="menu-link">
                    <i class="fa fa-sync"></i> Actualizar Información
                </a>
            </li>

            <li>
                <a href="{{ route('arbitraje') }}" class="menu-link">
                    <i class="fa fa-scale-balanced"></i> Arbitraje
                </a>
            </li>

            <li>
                <a href="{{ route('jrd') }}" class="menu-link">
                    <i class="fa fa-gavel"></i> JRD
                </a>
            </li>

        </ul>

        <div class="user-footer">
            <strong>{{ Auth::user()->name }}</strong><br>
            <span>{{ Auth::user()->email }}</span>

            <a href="{{ route('welcome') }}" class="btn-logout mt-2 d-block text-danger">
                <i class="fas fa-sign-out-alt"></i> Regresar
            </a>
        </div>
    </nav>

    <div class="main-wrapper">
        
        <header class="mobile-header">
            <button class="btn btn-light" onclick="toggleSidebar()">
                <i class="fa fa-bars"></i>
            </button>
            <strong>Gestión de Contenidos</strong>
            <span></span>
        </header>

        <main class="main-content">
            @yield('content')
        </main>

    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>

</body>
</html>
