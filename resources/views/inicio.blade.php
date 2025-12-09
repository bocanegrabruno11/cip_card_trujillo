<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CARD CD La Libertad')</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* === ESTILOS GLOBALES === */
        body, html { 
            margin: 0; 
            padding: 0; 
            width: 100%; 
            height: 100%; 
        }

        /* === ANIMACIÓN DE CARGA (LOADER DIAGONAL) === */
        .container-loader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
            background-color: white; z-index: 9999; pointer-events: none;
        }

        .left-side, .right-side {
            position: absolute; 
            top: 0; 
            width: 100%; /* CAMBIO: Deben ocupar el ancho completo para la diagonal */
            height: 100%;
            transition: clip-path 1.0s ease-in-out; /* Animamos el recorte, no la posición */
            will-change: clip-path; 
            z-index: 10000;
        }

        /* Triángulo Superior Izquierdo (Rojo Claro) */
        .left-side { 
            left: 0; 
            background-color: #B02E2D; 
            /* Coordenadas: Arriba-Izq, Arriba-Der, Abajo-Izq */
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        /* Triángulo Inferior Derecho (Rojo Oscuro) */
        .right-side { 
            right: 0; 
            background-color: #C13835; 
            /* Coordenadas: Arriba-Der, Abajo-Der, Abajo-Izq */
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
        }
        
        /* === ESTADO ACTIVO (ABRIENDO) === */
        
        /* El lado izquierdo se contrae hacia la esquina superior izquierda */
        .container-loader.active .left-side { 
            clip-path: polygon(0 0, 0 0, 0 0); 
        }

        /* El lado derecho se contrae hacia la esquina inferior derecha */
        .container-loader.active .right-side { 
            clip-path: polygon(100% 100%, 100% 100%, 100% 100%); 
        }
        
        /* Logo Central */
        .image-container {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            z-index: 10001; text-align: center; transition: opacity 0.5s ease;
        }
        .image-container img { max-width: 200px; height: auto; }
        .image-container p { color: white; font-weight: bold; margin-top: 15px; font-family: Arial, sans-serif; }
        
        .container-loader.active .image-container { opacity: 0; }

        /* === ESTRUCTURA PRINCIPAL === */
        .main-wrapper {
            opacity: 0; 
            transition: opacity 0.8s ease-in-out;
            display: flex; 
            flex-direction: column; 
            min-height: 100vh;
            position: relative;
        }
        .main-wrapper.visible { opacity: 1; }

        .contenido-dinamico {
            flex: 1; 
            width: 100%;
            margin-top: 110px;
            position: relative;
            z-index: 1;
        }
    </style>
    
    @yield('styles')
</head>
<body>

    <div class="container-loader" id="loader">
        <div class="left-side"></div>
        <div class="right-side"></div>
        <div class="image-container">
            <img src="{{ asset('img/logo.png') }}" alt="Logo CIP">
            <p>CONSEJO DEPARTAMENTAL LA LIBERTAD</p>
        </div>
    </div>

    <div class="main-wrapper" id="mainWrapper">
        
        @include('plantilla.navegacion')

        @if(request()->routeIs('welcome'))
        @if($popupData)
            @include('plantilla.pop-up')
        @endif
        @endif

        <div class="contenido-dinamico">
            @yield('content')
        </div>

        @include('plantilla.footer')

    </div>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const loader = document.getElementById('loader');
            const mainWrapper = document.getElementById('mainWrapper');
            
            // Verificamos si estamos en la página de inicio
            // window.location.pathname devuelve "/" en el home
            const isHome = window.location.pathname === '/' || window.location.pathname === '/public/'; // Ajuste para entornos locales

            if (isHome) {
                // === ANIMACIÓN COMPLETA SOLO EN HOME ===
                setTimeout(() => {
                    loader.classList.add('active'); 
                    
                    setTimeout(() => {
                        mainWrapper.classList.add('visible'); 
                        setTimeout(() => { loader.style.display = 'none'; }, 1000);
                    }, 300);
                }, 500); 
            } else {
                // === OTRAS PÁGINAS: MOSTRAR DIRECTO ===
                loader.style.display = 'none';
                mainWrapper.classList.add('visible');
                mainWrapper.style.transition = 'none'; // Quitar efecto fade para que sea instantáneo
                mainWrapper.style.opacity = '1';
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>