<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CIP - WEB')</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* === ESTILOS GLOBALES === */
        body, html { 
            margin: 0; 
            padding: 0; 
            width: 100%; 
            height: 100%; 
            /* Quitamos overflow-x: hidden global para evitar cortar contenidos sticky, 
               lo manejaremos en el wrapper */
        }

        /* === ANIMACIÓN DE CARGA (LOADER) === */
        .container-loader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
            background-color: white; z-index: 9999; pointer-events: none;
        }

        .left-side, .right-side {
            position: absolute; top: 0; width: 50%; height: 100%;
            transition: transform 1.5s ease-in-out; will-change: transform; z-index: 10000;
        }
        .left-side { left: 0; background-color: #B02E2D; transform-origin: left; }
        .right-side { right: 0; background-color: #C13835; transform-origin: right; }
        
        .container-loader.active .left-side { transform: translateX(-100%); }
        .container-loader.active .right-side { transform: translateX(100%); }
        
        .image-container {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            z-index: 10001; text-align: center; transition: opacity 0.5s ease;
        }
        .image-container img { max-width: 200px; height: auto; }
        .image-container p { color: white; font-weight: bold; margin-top: 15px; font-family: Arial, sans-serif; }
        .container-loader.active .image-container { opacity: 0; }

        /* === ESTRUCTURA PRINCIPAL (Sticky Footer) === */
        .main-wrapper {
            opacity: 0; 
            transition: opacity 1s ease-in-out;
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; /* Ocupa al menos toda la pantalla */
            position: relative;
        }
        .main-wrapper.visible { opacity: 1; }

        /* Contenido dinámico */
        .contenido-dinamico {
            flex: 1; /* Esto hace que empuje el footer hacia abajo si falta contenido */
            width: 100%;
            margin-top: 110px; /* Espacio para el navbar fijo */
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

        @include('plantilla.pop-up')

        <div class="contenido-dinamico">
            @yield('content')
        </div>

        @include('plantilla.footer')

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loader = document.getElementById('loader');
            const mainWrapper = document.getElementById('mainWrapper');
            
            setTimeout(() => {
                loader.classList.add('active'); 
                setTimeout(() => {
                    mainWrapper.classList.add('visible'); 
                    setTimeout(() => { loader.style.display = 'none'; }, 1500);
                }, 500);
            }, 2000); 
        });
    </script>
    
    @yield('scripts')
</body>
</html>