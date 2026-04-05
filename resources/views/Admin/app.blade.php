<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrativo')</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --sidebar-width: 260px;
    --cip-red: #AD2B2E;
    --cip-red-dark: #801a1d;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-color: #f0f2f5;
    font-family: 'Segoe UI', 'Arial', sans-serif;
    min-height: 100vh;
    display: flex;
}

/* ===== SIDEBAR ===== */
.sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    background: linear-gradient(180deg, var(--cip-red) 0%, var(--cip-red-dark) 100%);
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 25px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar-header img { 
    width: 80px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
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
    padding: 12px 25px;
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 14px;
    gap: 12px;
}

.menu-link i:first-child {
    width: 20px;
    font-size: 16px;
}

.menu-link:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
    padding-left: 28px;
}

.submenu {
    display: none;
    padding-left: 0;
    background: rgba(0,0,0,0.2);
}

.submenu .menu-link {
    padding-left: 52px;
    font-size: 13px;
}

.submenu .menu-link:hover {
    padding-left: 56px;
}

.menu-item.active .submenu {
    display: block;
}

.menu-item .fa-chevron-down {
    margin-left: auto;
    transition: transform 0.3s;
    font-size: 12px;
}

.menu-item.active .fa-chevron-down {
    transform: rotate(180deg);
}

/* ===== MAIN CONTENT ===== */
.main-wrapper {
    margin-left: var(--sidebar-width);
    width: 100%;
    min-height: 100vh;
}

.main-content {
    padding: 25px 30px;
}

/* ===== USER FOOTER ===== */
.user-footer {
    padding: 20px 25px;
    border-top: 1px solid rgba(255,255,255,0.1);
    font-size: 13px;
}

.user-footer strong {
    display: block;
    margin-bottom: 4px;
}

.user-footer span {
    font-size: 11px;
    opacity: 0.7;
}

.btn-logout {
    color: #ffcccc;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px 0;
    width: 100%;
    text-align: left;
    margin-top: 10px;
    transition: all 0.3s;
}

.btn-logout:hover { 
    color: white;
    background: none;
}

/* ===== CARDS Y CONTENIDO ===== */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.card-header {
    background-color: transparent;
    border-bottom: 1px solid #e9ecef;
    padding: 18px 20px;
    font-weight: 600;
}

.btn {
    border-radius: 8px;
    padding: 8px 18px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: var(--cip-red);
    border-color: var(--cip-red);
}

.btn-primary:hover {
    background-color: var(--cip-red-dark);
    border-color: var(--cip-red-dark);
    transform: translateY(-1px);
}

.table {
    margin-bottom: 0;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-top: none;
}

/* ===== MODAL ===== */
.modal-content {
    border-radius: 12px;
    border: none;
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    :root {
        --sidebar-width: 240px;
    }
    
    .main-content {
        padding: 20px 15px;
    }
    
    .menu-link {
        padding: 10px 18px;
        font-size: 13px;
    }
    
    .submenu .menu-link {
        padding-left: 42px;
    }
}

/* ===== UTILIDADES ===== */
.text-muted {
    color: #6c757d !important;
}

.badge {
    padding: 5px 10px;
    font-weight: 500;
    border-radius: 6px;
}
</style>
</head>

<body>

<nav class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('img/logo.png') }}" alt="Logo CIP">
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="#" class="menu-link toggle-menu">
                <i class="fas fa-gavel"></i> <span>Arbitrajes</span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('Admin.etapas.index') }}" class="menu-link">
                        <i class="fas fa-layer-group"></i> Crear Etapas Arbitrales
                    </a>
                </li>
                <li>
                    <a href="{{ route('Admin.Arbitraje') }}" class="menu-link">
                        <i class="fas fa-list-alt"></i> Ver Arbitrajes
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link toggle-menu">
                <i class="fas fa-users-cog"></i> <span>JPRD</span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('Admin.jrd.etapas.index') }}" class="menu-link">
                        <i class="fas fa-layer-group"></i> Crear Etapas JPRD
                    </a>
                </li>
                <li>
                    <a href="{{ route('Admin.Jrd') }}" class="menu-link">
                        <i class="fas fa-list-alt"></i> Ver JPRD
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <div class="user-footer">
        <strong><i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name ?? 'Administrador' }}</strong>
        <span>{{ Auth::user()->email ?? 'admin@cip.org.pe' }}</span>

        <!-- BOTÓN QUE ABRE MODAL -->
        <button type="button" class="btn-logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </button>
    </div>
</nav>

<div class="main-wrapper">
    <main class="main-content">
        @yield('content')
    </main>
</div>

<!-- ✅ MODAL LOGOUT -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center py-4">
                <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                <p class="mb-0">¿Seguro que deseas cerrar sesión?</p>
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancel">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>

                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                    @csrf
                    <button type="submit" class="btn btn-danger" id="btnLogout">
                        <span id="textLogout"><i class="fas fa-sign-out-alt me-1"></i> Salir</span>
                        <i id="spinnerLogout" class="fas fa-circle-notch fa-spin d-none"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// SUBMENÚS
document.querySelectorAll('.toggle-menu').forEach(menu => {
    menu.addEventListener('click', function(e) {
        e.preventDefault();
        const parent = this.closest('.menu-item');

        document.querySelectorAll('.menu-item').forEach(item => {
            if(item !== parent) item.classList.remove('active');
        });

        parent.classList.toggle('active');
    });
});

// LOGOUT LOADING
const logoutForm = document.getElementById('logoutForm');
if(logoutForm) {
    logoutForm.addEventListener('submit', function() {
        document.getElementById('btnLogout').disabled = true;
        document.getElementById('btnCancel').disabled = true;

        document.getElementById('textLogout').innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saliendo...';
        document.getElementById('spinnerLogout').classList.remove('d-none');
    });
}

// Abrir submenú activo si existe en localStorage
document.addEventListener('DOMContentLoaded', function() {
    // Opcional: mantener el submenú abierto según la página actual
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        const links = item.querySelectorAll('.submenu a');
        let isActive = false;
        links.forEach(link => {
            if(link.getAttribute('href') === currentPath) {
                isActive = true;
            }
        });
        if(isActive) {
            item.classList.add('active');
        }
    });
});
</script>

</body>
</html>