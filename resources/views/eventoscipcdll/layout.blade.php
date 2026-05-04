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

/* NAVBAR SOLO MÓVIL */
.navbar {
    display: none;
    background: #b30000;
    color: white;
    padding: 10px;
    align-items: center;
    justify-content: space-between;
}

.navbar img {
    width: 35px;
}

.menu-toggle {
    font-size: 24px;
    cursor: pointer;
}

/* SIDEBAR */
.sidebar {
    width: 230px;
    background: #8b0000;
    color: white;
    height: 100vh;
    position: fixed;
    display: flex;
    flex-direction: column;
}

.sidebar-top {
    padding: 15px;
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid #a52a2a;
}

.sidebar a {
    padding: 12px;
    text-decoration: none;
    color: white;
    display: block;
}

.sidebar a:hover {
    background: #cc0000;
}

/* USER ABAJO */
.sidebar-bottom {
    margin-top: auto;
    padding: 15px;
    border-top: 1px solid #a52a2a;
}

.sidebar-bottom form {
    margin-top: 10px;
}

.sidebar-bottom button {
    width: 100%;
    padding: 8px;
    background: white;
    color: #8b0000;
    border: none;
    cursor: pointer;
}

/* CONTENIDO */
.content {
    margin-left: 230px;
    padding: 20px;
}

/* RESPONSIVE */
@media (max-width: 768px) {

    .navbar {
        display: flex;
    }

    .sidebar {
        left: -230px;
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

<!-- NAVBAR SOLO EN MÓVIL -->
<div class="navbar">
    <img src="{{ asset('img/logo.png') }}">
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <!-- MENÚ -->
    <div class="sidebar-top">
        MENÚ
    </div>

    <a href="/dashboard-eventos">🏠 Dashboard</a>
    <a href="/validacion">✔️ Validación</a>
    <a href="/envio-tarjetas">📨 Envío de tarjetas</a>

    <!-- USUARIO ABAJO -->
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