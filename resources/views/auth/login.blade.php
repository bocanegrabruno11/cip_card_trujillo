<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa de Partes Virtual - Acceso</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { height: 100vh; width: 100%; overflow: hidden; }

        .login-container { display: flex; width: 100%; height: 100%; position: relative; }

        .bg-layer {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("{{ asset('img/appmovil.jpg') }}");
            background-size: cover; background-position: center; z-index: -1;
        }

        .overlay-layer {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(20, 20, 20, 0.85); z-index: 0;
        }

        .left-branding {
            flex: 1; display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            z-index: 1; color: white; padding: 40px; text-align: center;
        }

        .brand-logo-large { margin-bottom: 20px; }
        .brand-logo-large i {
            font-size: 80px; color: #D4AF37;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .brand-title {
            font-family: 'Times New Roman', serif; font-size: 60px;
            font-weight: bold; letter-spacing: 5px; color: #D4AF37;
            margin-bottom: 10px;
        }

        .brand-subtitle {
            font-size: 18px; text-transform: uppercase; line-height: 1.4;
            max-width: 400px; color: #f0f0f0;
        }

        .brand-footer-logo {
            position: absolute; bottom: 30px; left: 40px;
            display: flex; align-items: center; gap: 15px;
        }

        .brand-footer-logo img { height: 50px; }
        .brand-footer-text { font-size: 12px; color: #ccc; text-align: left; }

        .right-form {
            flex: 1; display: flex; justify-content: center;
            align-items: center; padding: 20px; z-index: 1;
        }

        .login-card {
            background-color: white; width: 100%; max-width: 450px;
            padding: 50px; border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .input-group { margin-bottom: 25px; }
        .input-group label {
            display: block; font-size: 14px; color: #666;
            margin-bottom: 8px; font-weight: bold;
        }

        .input-field {
            width: 100%; padding: 12px 0; border: none;
            border-bottom: 1px solid #ddd; font-size: 16px;
            outline: none; transition: border-color 0.3s;
        }

        .input-field:focus { border-bottom-color: #E31E24; }

        .password-wrapper { position: relative; }
        .toggle-password {
            position: absolute; right: 0; top: 12px;
            color: #999; cursor: pointer;
        }

        .btn-login {
            width: 100%; background-color: #E31E24;
            color: white; padding: 15px; border-radius: 5px;
            border: none; font-size: 16px; font-weight: bold;
            cursor: pointer; margin-bottom: 25px;
            transition: background 0.3s;
        }

        .btn-login:hover { background-color: #c2181e; }

        .forgot-link, .register-link {
            text-decoration: none; font-size: 14px; color: #555;
        }

        .forgot-link:hover, .register-link:hover { color: #E31E24; }

        @media (max-width: 900px) {
            .login-container { flex-direction: column; }
            .left-branding { display: none; }
            .right-form { width: 100%; flex: 1; }
        }
    </style>
</head>
<body>

    <div class="bg-layer"></div>
    <div class="overlay-layer"></div>

    <div class="login-container">

        <!-- BRANDING -->
        <div class="left-branding">
            <div class="brand-logo-large">
                <i class="fas fa-scale-balanced"></i>
            </div>

            <div class="brand-title">CARD</div>

            <div class="brand-subtitle">
                CENTRO DE ARBITRAJE Y RESOLUCIÓN DE DISPUTAS DEL CIP TRUJILLO
            </div>

            <div class="brand-footer-logo">
                <img src="{{ asset('img/logo.png') }}">
                <div class="brand-footer-text">
                    CONSEJO DEPARTAMENTAL DE LA LIBERTAD<br>
                    CENTRO DE ARBITRAJE
                </div>
            </div>
        </div>

        <!-- FORMULARIO REAL DE BREEZE -->
        <div class="right-form">
            <div class="login-card">

                <!-- SESSION STATUS -->
                @if (session('status'))
                    <div style="color: red; margin-bottom:10px;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="input-group">
                        <label>Usuario o Email <span>*</span></label>
                        <input type="email" name="email" class="input-field" value="{{ old('email') }}" required>

                        @error('email')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <label>Contraseña <span>*</span></label>

                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="input-field" required>
                            <i class="fas fa-eye-slash toggle-password" onclick="togglePassword()"></i>
                        </div>

                        @error('password')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>

                    <button type="submit" class="btn-login">Iniciar Sesión</button>

                    <a href="{{ route('register') }}" class="register-link">
                        ¿No tienes una cuenta? Regístrate ahora
                    </a>

                </form>

            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }
    </script>

</body>
</html>
