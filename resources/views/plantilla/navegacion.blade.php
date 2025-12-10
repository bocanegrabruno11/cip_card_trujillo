<style>
    /* =========================================
       1. RESET Y ESTILOS BASE
       ========================================= */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
    }
    
    /* Contenedor que envuelve todo el header para dejarlo fijo */
    .header-container {
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1100;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    /* =========================================
       2. TOP BAR (Barra Superior Roja Oscura)
       ========================================= */
    .top-bar {
        background-color: #8B0000; /* Rojo oscuro */
        color: white;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        height: 40px;
        padding: 0 20px;
        font-size: 13px;
    }

    .top-info {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .info-item img {
        width: 14px; height: 14px; 
        filter: invert(1) brightness(2); 
    }

    .virtual-desk-btn {
        background-color: #111;
        color: white;
        text-decoration: none;
        font-weight: bold;
        padding: 0 20px;
        height: 40px;
        display: flex;
        align-items: center;
        margin-left: 20px;
        transition: background 0.3s;
    }
    .virtual-desk-btn:hover { background-color: #333; }
    
    /* =========================================
       3. MAIN NAV (Barra Principal Roja)
       ========================================= */
    .main-nav-container {
        background-color: #AD2B2E; /* Rojo institucional */
        width: 100%;
        height: 70px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        position: relative;
    }
    
    .logo-container {
        height: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1102;
    }
    
    .logo-container img { height: 55px; width: auto; }
    
    .logo-text {
        color: white;
        font-weight: 700;
        font-size: 14px;
        line-height: 1.2;
        max-width: 250px;
    }

    /* =========================================
       4. MENÚ DE NAVEGACIÓN (DESKTOP)
       ========================================= */
    .nav-menu {
        display: flex;
        list-style: none;
        height: 100%;
        margin: 0;
        padding: 0;
        align-items: center;
    }
    
    .nav-menu > li { height: 100%; position: relative; list-style: none; }
    
    .nav-menu > li > a {
        color: white;
        text-decoration: none !important; /* Sin subrayado en menú principal */
        font-weight: bold;
        font-size: 15px;
        padding: 0 15px;
        height: 100%;
        display: flex;
        align-items: center;
        transition: background-color 0.3s;
        text-transform: uppercase;
    }
    
    .nav-menu > li > a:hover { background-color: #8B0000; }

    /* === ESTILOS DEL DROPDOWN (ESTILO DORADO) === */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #D7B56D; /* COLOR DORADO DE LA IMAGEN */
        min-width: 260px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        display: none;
        flex-direction: column;
        z-index: 1105;
        list-style: none;
        padding: 0;
    }
    
    .nav-menu li:hover > .dropdown-menu { display: flex; } 

    .dropdown-menu li { 
        width: 100%; 
        border-bottom: 1px solid rgba(255,255,255,0.3); /* Línea separadora blanca sutil */
        list-style: none;
    }
    .dropdown-menu li:last-child { border-bottom: none; }

    .dropdown-menu li a {
        color: white; /* TEXTO BLANCO */
        height: auto;
        padding: 12px 20px;
        font-size: 14px;
        text-transform: none;
        text-decoration: none !important; /* IMPORTANTE: QUITA EL SUBRAYADO */
        display: block;
        transition: background-color 0.2s;
    }
    
    /* Hover: Un poco más oscuro o claro para feedback */
    .dropdown-menu li a:hover {
        background-color: rgba(0,0,0,0.1); /* Oscurece levemente el dorado */
        color: white;
    }

    /* HAMBURGUESA (Oculta en Desktop) */
    .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        gap: 5px;
        padding: 5px;
        z-index: 1103;
    }
    .hamburger span {
        width: 30px; height: 3px; background-color: white; transition: 0.3s;
    }
    /* Animación al activar */
    .hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
    .hamburger.active span:nth-child(2) { opacity: 0; }
    .hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(7px, -6px); }


    /* FONDO OSCURO (Overlay para móvil) */
    .nav-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1090;
        display: none;
    }
    .nav-overlay.active { display: block; }

    /* === AJUSTE PARA PANTALLAS MEDIANAS (TABLET HORIZONTAL / LAPTOP PEQUEÑA) === */
    /* Cuando el ancho es menor a 1250px pero mayor a 1024px (que es cuando entra el móvil) */
    @media (max-width: 1350px) and (min-width: 1025px) {
        .logo-text {
            font-size: 11px; /* Reduce el texto del logo */
            max-width: 180px;
        }
        
        .nav-menu > li > a {
            font-size: 11px; /* Reduce fuente del menú */
            padding: 0 8px;  /* Reduce espacio entre items */
            text-align: center; /* Centra el texto si se parte en dos líneas */
            line-height: 1.2;
        }
    }

    /* =========================================
       5. RESPONSIVIDAD (MEDIA QUERIES)
       ========================================= */
    
    @media (max-width: 1024px) {
        .top-bar { display: none; }
        .hamburger { display: flex; }
        .logo-text { font-size: 11px; max-width: 200px; }

        /* ESTILOS DEL MENÚ LATERAL (MÓVIL) */
        .nav-menu {
            position: fixed;
            top: 70px; 
            right: -100%; 
            width: 80%; 
            max-width: 350px;
            height: calc(100vh - 70px); 
            background-color: white; 
            flex-direction: column; 
            align-items: flex-start; 
            transition: right 0.4s ease-in-out; 
            overflow-y: auto; 
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            z-index: 1100;
        }

        .nav-menu.active {
            right: 0; 
        }

        /* Items del menú en móvil */
        .nav-menu > li {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #eee; 
        }

        .nav-menu > li > a {
            color: #333; 
            height: 55px;
            width: 100%;
            justify-content: space-between; 
            text-decoration: none !important;
        }
        
        .nav-menu > li > a:hover {
            background-color: #f9f9f9;
            color: #AD2B2E;
        }
        
        /* Flecha automática */
        .nav-menu > li.has-dropdown > a::after {
            content: '▼'; 
            font-size: 12px;
            color: #AD2B2E;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }
        
        .nav-menu > li.has-dropdown.open > a::after {
            transform: rotate(180deg);
        }

        /* Dropdowns estáticos en móvil */
        /* En móvil mantenemos fondo claro para legibilidad, pero sin subrayado */
        .dropdown-menu {
            position: static; 
            box-shadow: none;
            width: 100%;
            background-color: #f9f9f9; 
            display: none;
            padding: 0;
        }
        
        .dropdown-menu li {
            border-bottom: none;
        }
        
        .dropdown-menu li a {
            color: #555; /* Texto gris oscuro en móvil */
            padding-left: 30px; 
            text-decoration: none !important;
        }
        
        .dropdown-menu li a:hover {
            background-color: #eee;
            color: #AD2B2E;
        }

        .nav-menu li.open > .dropdown-menu {
            display: flex;
        }
    }

    @media (max-width: 480px) {
        .logo-text { display: none; }
        .logo-container img { height: 45px; }
    }

    /* =========================================
       6. BURBUJAS SOCIALES
       ========================================= */
    .social-bubbles {
        position: fixed; bottom: 20px; right: 20px; z-index: 999;
        display: flex; flex-direction: column; gap: 10px;
    }
    .social-bubble {
        width: 45px; height: 45px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; position: relative; overflow: hidden;
        transition: width 0.3s ease; background: rgba(255,255,255,0.1);
        border: 2px solid rgba(0,0,0,0.1); backdrop-filter: blur(2px);
    }
    .social-bubble:hover { width: 140px; border-radius: 25px; justify-content: flex-start; padding-left: 10px; background: white; }
    .social-bubble img { width: 25px; height: 25px; }
    .social-text { 
        position: absolute; left: 45px; white-space: nowrap; opacity: 0; 
        font-size: 14px; font-weight: bold; color: #333; transition: 0.3s;
    }
    .social-bubble:hover .social-text { opacity: 1; }

</style>
    @yield('styles')

    <div class="header-container">
      

        <div class="main-nav-container">
            <div class="logo-container">
                <img src="{{ asset('img/logo.png') }}" alt="Logo CIP">
                <div class="logo-text">
                    CENTRO DE ARBITRAJE Y RESOLUCIÓN DE DISPUTAS DEL CIP CD LA LIBERTAD
                </div>
            </div>
            
            <div class="hamburger" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="nav-overlay" onclick="closeMenu()"></div>
            
            <ul class="nav-menu" id="navMenu">
    
                {{-- 1. INICIO (Enlace simple) --}}
                <li><a href="{{ route('welcome') }}" onclick="handleMenuClick(event, this)">INICIO</a></li>    
                
                {{-- 2. EL CARD --}}
                <li class="has-dropdown {{ request()->routeIs('card.*') ? 'current-menu-item' : '' }}" onclick="toggleMobileDropdown(this)">
                    <a href="javascript:void(0)">CARD</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('presentacion') }}" onclick="handleMenuClick(event, this)">Presentación</a></li>
                        <li><a href="{{ route('organizacion-card') }}" onclick="handleMenuClick(event, this)">Organización del CARD</a></li>
                        <li><a href="{{ route('mision-vision') }}" onclick="handleMenuClick(event, this)">Visión y Misión</a></li>
                        <li><a href="{{ route('organigrama') }}" onclick="handleMenuClick(event, this)">Organigrama</a></li>
                        <li><a href="{{ route('nuestro-equipo') }}" onclick="handleMenuClick(event, this)">Nuestro equipo</a></li>
                        <li><a href="{{ route('certificaciones') }}" onclick="handleMenuClick(event, this)">Certificaciones</a></li>
                        <li><a href="{{ route('politicas') }}" onclick="handleMenuClick(event, this)">Políticas</a></li>
                        <li><a href="{{ route('licencias') }}" onclick="handleMenuClick(event, this)">Licencia de funcionamiento</a></li>
                    </ul>
                </li>
                
                {{-- 3. MODELOS DE CLÁUSULAS --}}
                <li class="has-dropdown {{ request()->routeIs('clausulas.*') ? 'current-menu-item' : '' }}" onclick="toggleMobileDropdown(this)">
                    <a href="javascript:void(0)">MODELOS DE CLÁUSULAS</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('arbitral') }}" onclick="handleMenuClick(event, this)">Modelo de Cláusula Arbitral</a></li>
                        <li><a href="{{ route('junta-res-disputas') }}" onclick="handleMenuClick(event, this)">Modelo de cláusula Junta de Resolución de Disputas</a></li>
                    </ul>
                </li>
                
                {{-- 4. SERVICIOS --}}
                <li class="has-dropdown {{ request()->routeIs('servicios.*') ? 'current-menu-item' : '' }}" onclick="toggleMobileDropdown(this)">
                    <a href="javascript:void(0)">SERVICIOS</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('institucion-arbitral') }}" onclick="handleMenuClick(event, this)">Institución Arbitral</a></li>
                        <li><a href="{{ route('junta-prevencion') }}" onclick="handleMenuClick(event, this)">Junta de Prevención y Resolución de Disputas (CAJPRD)</a></li>
                    </ul>
                </li>
                
                {{-- 5. COMUNICADOS --}}
                <li><a href="{{ route('comunicados') }}" onclick="handleMenuClick(event, this)">COMUNICADOS</a></li>
                
                {{-- 6. EVENTOS --}}
                <li><a href="{{ route('eventos') }}" onclick="handleMenuClick(event, this)">EVENTOS</a></li>
                
                {{-- 7. CONTACTOS --}}
                <li><a href="{{ route('contactos') }}" onclick="handleMenuClick(event, this)">CONTACTOS</a></li>
                {{-- 8. MESA DE PARTES VIRTUAL (Dinámico) --}}
                <li>
                    @guest
                        {{-- Si NO está logueado -> Login --}}
                        <a href="{{ route('login') }}" onclick="handleMenuClick(event, this)">MESA DE PARTES - VIRTUAL</a>
                    @endguest

                    @auth
                        {{-- Si SÍ está logueado -> Redirigir según Rol --}}
                        @if(Auth::user()->hasRole('admin'))
                            <a href="{{ route('Admin.dashboard') }}" onclick="handleMenuClick(event, this)">MESA DE PARTES - VIRTUAL</a>
                        
                        @elseif(Auth::user()->hasRole('gestor_contenido'))
                            <a href="{{ route('gestion-contenido') }}" onclick="handleMenuClick(event, this)">MESA DE PARTES - VIRTUAL</a>
                        
                        @elseif(Auth::user()->hasRole('mesa_partes'))
                            <a href="{{ route('dashboard') }}" onclick="handleMenuClick(event, this)">MESA DE PARTES - VIRTUAL</a>
                        
                        @else
                            {{-- Default si tiene otro rol (o ninguno) --}}
                            <a href="{{ url('/') }}" onclick="handleMenuClick(event, this)">MESA DE PARTES - VIRTUAL</a>
                        @endif
                    @endauth
                </li>
                
            </ul>
        </div>
    </div>

    
    <div class="social-bubbles">
       
    
        <div class="social-bubble facebook" onclick="window.open('https://www.facebook.com/CIPLaLibertad?locale=es_LA', '_blank')">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
            <span class="social-text">Facebook</span>
        </div>
       
    </div>

    <script>
        // Variables para control del scroll
        let lastScrollPosition = 0;
        const mainNavbar = document.querySelector('.main-nav-container');
        const headerContainer = document.querySelector('.header-container');
        // Altura inicial segura
        let totalHeaderHeight = 110; 
        
        function adjustMargin() {
            if(headerContainer) {
                totalHeaderHeight = headerContainer.offsetHeight;
                const main = document.querySelector('main') || document.querySelector('.contenido-dinamico');
                if(main) main.style.marginTop = totalHeaderHeight + 'px';
            }
            if (window.innerWidth > 1024) {
                 closeMenu();
            }
        }

        window.addEventListener('load', adjustMargin);
        window.addEventListener('resize', adjustMargin);
        
        // Función para manejar el scroll (Opcional si quieres ocultar al bajar)
        /*
        function handleScroll() {
            const currentScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            if (window.innerWidth > 1024) { 
                if (currentScrollPosition > lastScrollPosition && currentScrollPosition > totalHeaderHeight) {
                    headerContainer.style.top = '-40px'; // Esconder top-bar
                } else {
                    headerContainer.style.top = '0';
                }
            } else {
                headerContainer.style.top = '0';
            }
            lastScrollPosition = currentScrollPosition;
        }
        window.addEventListener('scroll', handleScroll);
        */
        
        // Lógica de Menú Móvil (Hamburguesa)
        function toggleMenu() {
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');
            const overlay = document.querySelector('.nav-overlay');
            
            if(hamburger) hamburger.classList.toggle('active');
            if(navMenu) navMenu.classList.toggle('active');
            if(overlay) overlay.classList.toggle('active');
            
            if (navMenu && navMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }
        
        function closeMenu() {
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');
            const overlay = document.querySelector('.nav-overlay');
            
            if(hamburger) hamburger.classList.remove('active');
            if(navMenu) navMenu.classList.remove('active');
            if(overlay) overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
            closeAllDropdowns();
        }
        
        function toggleMobileDropdown(element) {
            // Solo actuar en pantallas móviles
            if (window.innerWidth <= 1024) { 
                // Cierra otros abiertos
                const allItems = document.querySelectorAll('.nav-menu > li');
                allItems.forEach(item => {
                    if (item !== element) item.classList.remove('open');
                });
                
                // Alterna el actual
                element.classList.toggle('open');
            }
        }
       
        function handleMenuClick(event, element) {
            // Si es enlace directo, no detener propagación excesivamente, pero cerrar menú
            if (window.innerWidth <= 1024) {
                setTimeout(() => {
                    closeMenu();
                }, 150);
            }
        }
        
        function closeAllDropdowns() {
            const allItems = document.querySelectorAll('.nav-menu > li');
            allItems.forEach(li => li.classList.remove('open'));
        }
        
        // Prevenir cierre al hacer clic dentro del menú
        document.querySelector('.nav-menu').addEventListener('click', function(event) {
             event.stopPropagation();
        });
    </script>
    @yield('scripts')