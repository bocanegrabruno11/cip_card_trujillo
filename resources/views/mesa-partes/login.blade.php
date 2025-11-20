<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa de Partes Virtual - Acceso</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* === RESET Y BASE === */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        
        body {
            height: 100vh;
            width: 100%;
            overflow: hidden; /* Evita scroll en pantalla completa */
        }

        /* === CONTENEDOR PRINCIPAL DIVIDIDO === */
        .login-container {
            display: flex;
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* === FONDO GENERAL (Imagen de fondo que cubre todo) === */
        .bg-layer {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("{{ asset('img/appmovil.jpg') }}");
            background-size: cover;
            background-position: center;
            z-index: -1;
        }

        /* Overlay oscuro general */
        .overlay-layer {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(20, 20, 20, 0.85); /* Fondo oscuro muy elegante */
            z-index: 0;
        }

        /* === ZONA IZQUIERDA (BRANDING) === */
        .left-branding {
            flex: 1; /* Ocupa el 50% */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1;
            color: white;
            padding: 40px;
            text-align: center;
        }

        /* Logo grande simulado (Estilo CARD) */
        .brand-logo-large {
            margin-bottom: 20px;
        }
        
        .brand-logo-large i {
            font-size: 80px;
            color: #D4AF37; /* Dorado */
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .brand-title {
            font-family: 'Times New Roman', serif;
            font-size: 60px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #D4AF37; /* Dorado */
            margin-bottom: 10px;
        }

        .brand-subtitle {
            font-size: 18px;
            text-transform: uppercase;
            line-height: 1.4;
            max-width: 400px;
            color: #f0f0f0;
        }

        .brand-footer-logo {
            position: absolute;
            bottom: 30px;
            left: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .brand-footer-logo img {
            height: 50px;
        }
        
        .brand-footer-text {
            text-align: left;
            font-size: 12px;
            color: #ccc;
        }

        /* === ZONA DERECHA (FORMULARIO) === */
        .right-form {
            flex: 1; /* Ocupa el 50% */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
            padding: 20px;
        }

        .login-card {
            background-color: white;
            width: 100%;
            max-width: 450px;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        /* Inputs */
        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .input-group label span { color: #E31E24; }

        .input-field {
            width: 100%;
            padding: 12px 0;
            border: none;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }

        .input-field:focus {
            border-bottom-color: #E31E24;
        }

        .password-wrapper {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 0;
            top: 12px;
            color: #999;
            cursor: pointer;
        }

        /* Links y Botones */
        .forgot-link {
            display: block;
            color: #888;
            font-size: 14px;
            text-decoration: none;
            margin-bottom: 30px;
        }
        .forgot-link:hover { color: #E31E24; }

        .btn-login {
            width: 100%;
            background-color: #E31E24; /* Rojo Intenso */
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 25px;
        }
        
        .btn-login:hover { background-color: #c2181e; }

        .register-link {
            text-align: center;
            font-size: 14px;
            color: #555;
            display: block;
            text-decoration: none;
        }
        
        .register-link:hover { text-decoration: underline; }

        /* BOTÓN VOLVER A INICIO (FLOTANTE) */
        .btn-back-home {
            position: absolute;
            top: 30px;
            right: 30px;
            z-index: 10;
            color: white; /* Blanco para que se vea sobre el fondo oscuro o rojo si está en blanco */
            background-color: rgba(0,0,0,0.5);
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .btn-back-home:hover {
            background-color: white;
            color: #E31E24;
        }

        /* === RESPONSIVIDAD === */
        @media (max-width: 900px) {
            .login-container { flex-direction: column; }
            
            /* En móvil, ocultamos el branding grande y dejamos solo el formulario centrado */
            .left-branding { display: none; }
            
            .right-form { width: 100%; flex: 1; }
            
            /* Botón volver un poco más pequeño */
            .btn-back-home { top: 20px; right: 20px; padding: 8px 15px; }
        }
    </style>
</head>
<body>

    <div class="bg-layer"></div>
    <div class="overlay-layer"></div>

    <a href="{{ route('welcome') }}" class="btn-back-home">
        <i class="fas fa-arrow-left"></i> Volver al Inicio
    </a>

    <div class="login-container">
        
        <div class="left-branding">
            <div class="brand-logo-large">
                <i class="fas fa-scale-balanced"></i>
            </div>
            <div class="brand-title">CARD</div>
            <div class="brand-subtitle">
                CENTRO DE ARBITRAJE Y RESOLUCIÓN DE DISPUTAS DEL CIP TRUJILLO
            </div>

            <div class="brand-footer-logo">
                <img src="{{ asset('img/logo.png') }}" alt="Logo CIP">
                <div class="brand-footer-text">
                    CONSEJO DEPARTAMENTAL DE LA LIBERTAD<br>
                    CENTRO DE ARBITRAJE
                </div>
            </div>
        </div>

        <div class="right-form">
            <div class="login-card">
                
                <form action="#" method="POST">
                    @csrf
                    
                    <div class="input-group">
                        <label>Usuario o Email <span>*</span></label>
                        <input type="text" name="email" class="input-field" required autocomplete="off">
                    </div>

                    <div class="input-group">
                        <label>Contraseña <span>*</span></label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="input-field" required>
                            <i class="fas fa-eye-slash toggle-password" onclick="togglePassword()"></i>
                        </div>
                    </div>

                    <a href="#" class="forgot-link">¿Perdiste tu contraseña?</a>

                    <button type="submit" class="btn-login">Iniciar Sesión</button>

                    <a href="{{ route('register') }}" class="register-link">¿No tienes una cuenta? Regístrate ahora</a>
                </form>

            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>
</html>