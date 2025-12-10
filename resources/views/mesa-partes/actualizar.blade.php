<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $persona ? 'Actualizar' : 'Registrar' }} Información Personal - CIP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        .menu-link.active {
            background: rgba(255,255,255,0.15);
            border-left-color: white;
            color: #fff;
        }
        .user-footer {
            padding: 20px;
            font-size: 14px;
            background: rgba(0,0,0,0.1);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .btn-logout {
            color: #ffcccc !important;
            text-decoration: none;
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
            display: flex;
            align-items: center;
            justify-content: center;
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

        /* Estilos del formulario */
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            width: 100%;
            padding: 40px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h1 {
            color: var(--cip-red);
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        .info-badge {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--cip-red) 0%, var(--cip-red-dark) 100%);
            color: white;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .alert-icon {
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group label .required {
            color: var(--cip-red);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--cip-red);
            box-shadow: 0 0 0 3px rgba(173, 43, 46, 0.1);
        }

        .form-control.error {
            border-color: #dc3545;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .btn-submit {
            width: 100%;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, var(--cip-red) 0%, var(--cip-red-dark) 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(173, 43, 46, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
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
            .form-container {
                padding: 25px;
            }
        }

        @media(max-width: 576px) {
            .form-header h1 {
                font-size: 24px;
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
            <div class="text-white small mt-2">Mesa de Partes</div>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="fas fa-home"></i> Inicio
                </a>
            </li>
            <li>
                <a href="{{ route('persona.actualizar') }}" class="menu-link active">
                    <i class="fas fa-user-edit"></i> Actualizar Información
                </a>
            </li>
            <li>
                <a href="{{ route('arbitraje') }}" class="menu-link">
                    <i class="fas fa-scale-balanced"></i> Arbitraje
                </a>
            </li>
            <li>
                <a href="{{ route('jrd') }}" class="menu-link">
                    <i class="fas fa-gavel"></i> JRD
                </a>
            </li>
        </ul>

        <div class="user-footer">
            <strong>{{ Auth::user()->name ?? 'Usuario' }}</strong><br>
            <span>{{ Auth::user()->email ?? 'usuario@cip.org.pe' }}</span>

            <a href="{{ route('welcome') }}" class="btn-logout mt-2 d-block">
                <i class="fas fa-sign-out-alt"></i> Regresar
            </a>
        </div>
    </nav>

    <div class="main-wrapper">
        <header class="mobile-header">
            <button class="btn btn-light border shadow-sm" onclick="toggleSidebar()">
                <i class="fas fa-bars text-dark"></i>
            </button>
            <span class="fw-bold text-dark">Actualizar Información</span>
            <a href="{{ route('welcome') }}" target="_blank" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </header>

        <main class="main-content">
            <div class="form-container">
                <div class="form-header">
                    <h1>{{ $persona ? 'Actualizar' : 'Registrar' }} Información Personal</h1>
                    <p>{{ $persona ? 'Modifica tus datos personales' : 'Completa tu perfil con tus datos' }}</p>
                </div>

                @if($persona)
                    <span class="info-badge">✓ Registro encontrado</span>
                @else
                    <span class="info-badge">📝 Nuevo registro</span>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        <span class="alert-icon">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <span class="alert-icon">✕</span>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ $persona ? route('persona.update') : route('persona.store') }}" method="POST" id="personaForm">
                    @csrf

                    <div class="form-group">
                        <label for="dni">DNI <span class="required">*</span></label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="dni" 
                            name="dni" 
                            value="{{ old('dni', $persona->dni ?? '') }}"
                            maxlength="8"
                            required
                            placeholder="Ingrese su DNI de 8 dígitos"
                        >
                        <span class="error-message" id="dni-error">El DNI debe tener 8 dígitos numéricos</span>
                    </div>

                    <div class="form-group">
                        <label for="correo_contacto">Correo de Contacto <span class="required">*</span></label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="correo_contacto" 
                            name="correo_contacto" 
                            value="{{ old('correo_contacto', $persona->correo_contacto ?? '') }}"
                            required
                            placeholder="ejemplo@correo.com"
                        >
                        <span class="error-message" id="correo-error">Ingrese un correo válido</span>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="direccion" 
                            name="direccion" 
                            value="{{ old('direccion', $persona->direccion ?? '') }}"
                            maxlength="200"
                            placeholder="Ingrese su dirección"
                        >
                    </div>

                    <div class="form-group">
                        <label for="celular">Celular</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="celular" 
                            name="celular" 
                            value="{{ old('celular', $persona->celular ?? '') }}"
                            maxlength="9"
                            placeholder="Ingrese su número de celular (9 dígitos)"
                        >
                        <span class="error-message" id="celular-error">El celular debe tener 9 dígitos</span>
                    </div>

                    <button type="submit" class="btn-submit">
                        {{ $persona ? '🔄 Actualizar Información' : '💾 Guardar Información' }}
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Función para mostrar/ocultar el sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Cerrar sidebar al hacer clic en el overlay
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            toggleSidebar();
        });

        // Validación del formulario
        document.getElementById('personaForm').addEventListener('submit', function(e) {
            let isValid = true;

            // Validar DNI
            const dni = document.getElementById('dni');
            const dniError = document.getElementById('dni-error');
            if (!/^\d{8}$/.test(dni.value)) {
                dni.classList.add('error');
                dniError.classList.add('show');
                isValid = false;
            } else {
                dni.classList.remove('error');
                dniError.classList.remove('show');
            }

            // Validar Correo
            const correo = document.getElementById('correo_contacto');
            const correoError = document.getElementById('correo-error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(correo.value)) {
                correo.classList.add('error');
                correoError.classList.add('show');
                isValid = false;
            } else {
                correo.classList.remove('error');
                correoError.classList.remove('show');
            }

            // Validar Celular (solo si tiene valor)
            const celular = document.getElementById('celular');
            const celularError = document.getElementById('celular-error');
            if (celular.value && !/^\d{9}$/.test(celular.value)) {
                celular.classList.add('error');
                celularError.classList.add('show');
                isValid = false;
            } else {
                celular.classList.remove('error');
                celularError.classList.remove('show');
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Permitir solo números en DNI
        document.getElementById('dni').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        // Permitir solo números en Celular
        document.getElementById('celular').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
    </script>

</body>
</html>