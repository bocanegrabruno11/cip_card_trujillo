<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>@yield('title', 'Eventos CIPCDLL')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
}

/* 🎨 COLOR BASE CIP */
:root {
    --rojo: #b30000;
    --rojo-oscuro: #990000;
    --rojo-hover: #cc0000;
}

/* NAVBAR SOLO MÓVIL */
.navbar {
    display: none;
    background: var(--rojo);
    color: white;
    padding: 10px;
    align-items: center;
    justify-content: space-between;
}

.navbar img {
    width: 35px;
}

/* SIDEBAR */
.sidebar {
    width: 240px;
    background: var(--rojo-oscuro);
    color: white;
    height: 100vh;
    position: fixed;
    display: flex;
    flex-direction: column;
}

/* HEADER SIDEBAR (LOGO + NOMBRE) */
.sidebar-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.sidebar-header img {
    width: 40px;
}

.sidebar-header span {
    font-weight: bold;
}

/* LINKS */
.sidebar a {
    padding: 12px 15px;
    text-decoration: none;
    color: white;
    display: block;
}

.sidebar a:hover {
    background: var(--rojo-hover);
}

/* USER ABAJO */
.sidebar-bottom {
    margin-top: auto;
    padding: 15px;
    border-top: 1px solid rgba(255,255,255,0.2);
}

.sidebar-bottom button {
    width: 100%;
    padding: 8px;
    background: white;
    color: var(--rojo);
    border: none;
    cursor: pointer;
}

/* CONTENIDO */
.content {
    margin-left: 240px;
    padding: 20px;
}

/* HAMBURGUESA */
.menu-toggle {
    font-size: 24px;
    cursor: pointer;
}

/* RESPONSIVE */
@media (max-width: 768px) {

    .navbar {
        display: flex;
    }

    .sidebar {
        left: -240px;
        transition: 0.3s;
    }

    .sidebar.active {
        left: 0;
    }

    .content {
        margin-left: 0;
    }
}
</style>
</head>
<body>

<!-- NAVBAR SOLO MÓVIL -->
<div class="navbar">
    <img src="{{ asset('img/logo.png') }}">
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <!-- HEADER -->
    <div class="sidebar-header">
        <img src="{{ asset('img/logo.png') }}">
        <span>Gestor de Eventos</span>
    </div>

    <!-- MENÚ -->
    <a href="/dashboard-eventos">🏠 Registros</a>
    <a href="/validacion">✔️ Validación</a>
    <a href="/envio-tarjetas">📧 Envio de Tarjetas</a>
    <a href="/asistencia">📋 Asistencia</a>
    <div class="sidebar-bottom">
        <div><strong>{{ session('usuario') }}</strong></div>

        <form method="POST" action="{{ route('logout.eventos') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>
</div>

<!-- CONTENIDO -->
<div class="content">
    @yield('content')
</div>

<script>
function toggleMenu() {
    document.getElementById('sidebar').classList.toggle('active');
}
</script>

</body>
</html>