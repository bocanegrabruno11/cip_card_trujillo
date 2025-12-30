<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Mesa de Partes Virtual</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* === RESET Y FUENTES === */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        
        body {
            background-color: #F9F9F9; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* === HEADER SUPERIOR (NEGRO) === */
        .top-header {
            background-color: #000;
            padding: 20px 0;
            text-align: center;
            width: 100%;
            position: relative;
        }

        .header-logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .logo-icon-gold {
            font-size: 30px;
            color: #D4AF37; 
            margin-bottom: 5px;
        }
        
        .logo-text-gold {
            color: #D4AF37;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            letter-spacing: 2px;
            font-size: 18px;
        }
        
        .logo-subtext {
            color: #ccc;
            font-size: 8px;
            text-transform: uppercase;
        }

        /* === BOTÓN VOLVER (Estilo integrado en el header) === */
        .btn-back-home {
            position: absolute;
            top: 50%;
            left: 30px;
            transform: translateY(-50%);
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #333;
            padding: 8px 15px;
            border-radius: 30px;
            transition: all 0.3s;
        }

        .btn-back-home:hover {
            border-color: #D4AF37;
            color: #D4AF37;
        }

        /* === CONTENIDO PRINCIPAL === */
        .main-content {
            flex: 1; 
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        .page-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            text-transform: uppercase;
            color: #000;
        }

        /* TARJETA DE REGISTRO */
        .register-card {
            background-color: white;
            width: 100%;
            max-width: 1000px; 
            padding: 50px 60px;
            border-radius: 8px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; 
            column-gap: 50px; 
            row-gap: 30px; 
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        .input-group label {
            font-weight: bold;
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
            text-align: center; 
        }
        
        .input-group label span { color: #E31E24; }

        .input-field {
            width: 100%;
            padding: 12px 25px;
            border-radius: 50px; 
            border: 1px solid #ddd;
            font-size: 14px;
            outline: none;
            background-color: white;
            transition: border-color 0.3s;
        }

        .input-field:focus {
            border-color: #ccc;
        }
        
        .password-wrapper { position: relative; }
        
        .toggle-password {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            cursor: pointer;
        }

        /* === ZONA DE ACCIONES (BOTÓN + LINK) === */
        .actions-container {
            grid-column: 1 / -1; 
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            /* Alineación a la izquierda o centro? La imagen sugiere izquierda o flow */
            justify-content: flex-start; 
        }

        .btn-register {
            background-color: #000;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px; 
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
            transition: transform 0.2s;
        }
        
        .btn-register:hover {
            transform: scale(1.05);
            background-color: #222;
        }

        .login-redirect-link {
            font-size: 14px;
            color: #555;
            text-decoration: none;
        }

        .login-redirect-link strong {
            color: #E31E24; /* Rojo para destacar el "Inicia sesión" */
            cursor: pointer;
        }
        
        .login-redirect-link:hover strong {
            text-decoration: underline;
        }

        /* === FOOTER ROJO === */
        .red-footer {
            background-color: #E31E24; 
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .footer-logo-composite {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .footer-text-block {
            color: white;
            text-align: left;
        }
        
        .footer-title {
            font-family: 'Times New Roman', serif;
            font-size: 32px;
            color: #D4AF37;
            letter-spacing: 3px;
            margin-bottom: 5px;
        }
        
        .footer-subtitle {
            font-size: 10px;
            line-height: 1.4;
            text-transform: uppercase;
        }

        /* === RESPONSIVIDAD === */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr; 
                gap: 20px;
            }
            
            .register-card { padding: 30px 20px; }
            
            /* En móvil, las acciones se apilan */
            .actions-container {
                flex-direction: column;
                justify-content: center;
                text-align: center;
            }
            
            .btn-back-home {
                position: static; /* Deja de ser absoluto en móvil para no tapar el logo */
                margin-bottom: 15px;
                display: inline-flex;
                transform: none;
            }
            
            .footer-logo-composite {
                flex-direction: column;
                text-align: center;
            }
            
            .footer-text-block { text-align: center; }
        }
    </style>
</head>
<body>

    <header class="top-header">
        <a href="{{ route('welcome') }}" class="btn-back-home">
            <i class="fas fa-arrow-left"></i> Volver al Inicio
        </a>

        <div class="header-logo-container">
            <i class="fas fa-scale-balanced logo-icon-gold"></i>
            <div class="logo-text-gold">CARD</div>
            <div class="logo-subtext">
                CENTRO DE ARBITRAJE Y RESOLUCIÓN DE DISPUTAS<br>DEL CIP CDLL
            </div>
        </div>
    </header>

    <main class="main-content">
        
        <h1 class="page-title">REGISTRO</h1>

        <div class="register-card">
            <form action="#" method="POST">
                @csrf
                
                <div class="form-grid">
                    
                    <div class="input-group">
                        <label>Nombres y Apellidos <span>*</span></label>
                        <input type="text" name="fullname" class="input-field" required>
                    </div>

                    <div class="input-group">
                        <label>Email <span>*</span></label>
                        <input type="email" name="email" class="input-field" required>
                    </div>

                    <div class="input-group">
                        <label>Usuario <span>*</span></label>
                        <input type="text" name="username" class="input-field" placeholder="No debe contener espacios" required>
                    </div>

                    <div class="input-group">
                        <label>Contraseña <span>*</span></label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="input-field" required>
                            <i class="fas fa-eye-slash toggle-password" onclick="togglePassword()"></i>
                        </div>
                    </div>

                    <div class="actions-container">
                        <button type="submit" class="btn-register">REGISTRARME</button>
                        
                        <a href="{{ route('login') }}" class="login-redirect-link">
                            ¿Ya tienes una cuenta? <strong>Inicia sesión</strong>
                        </a>
                    </div>

                </div>
            </form>
        </div>

    </main>

    <footer class="red-footer">
        <div class="footer-logo-composite">
            <div style="position: relative; width: 80px; height: 80px; display: flex; justify-content: center; align-items: center;">
                <i class="fas fa-cog" style="font-size: 80px; color: #D4AF37;"></i>
                <i class="fas fa-scale-balanced" style="font-size: 40px; color: white; position: absolute;"></i>
            </div>
            
            <div class="footer-text-block">
                <div class="footer-title">CARD</div>
                <div class="footer-subtitle">
                    CENTRO DE ARBITRAJE<br>
                    Y RESOLUCIÓN DE DISPUTAS<br>
                    DEL CIP CDLL
                </div>
            </div>
        </div>
    </footer>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>
</html>