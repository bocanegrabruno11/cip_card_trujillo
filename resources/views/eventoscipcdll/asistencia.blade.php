@extends('eventoscipcdll.layout')

@section('title', 'Dashboard - Control de Asistencia')

@section('content')
<div class="dashboard-container">

    <!-- Tarjetas de resumen -->
    <div class="stats-cards">
        <div class="card card-asistieron" onclick="mostrarAsistieron()">
            <div class="card-icon">✅</div>
            <div class="card-info">
                <h3>Asistieron</h3>
                <div class="card-number" id="totalAsistieron">0</div>
                <span class="card-link">Ver detalles →</span>
            </div>
        </div>
        <div class="card card-faltan" onclick="mostrarFaltan()">
            <div class="card-icon">⏳</div>
            <div class="card-info">
                <h3>Faltan por llegar</h3>
                <div class="card-number" id="totalFaltan">0</div>
                <span class="card-link">Ver detalles →</span>
            </div>
        </div>
    </div>

    <!-- Botón QR -->
    <div style="margin-bottom: 15px;">
        <button class="btn-qr" onclick="abrirEscaner()">
            📷 Escanear QR
        </button>
    </div>

    <!-- Barra de búsqueda -->
    <div class="search-bar">
        <div class="search-icon">🔍</div>
        <input type="text" id="searchInput"
            placeholder="Buscar por CIP, DNI, nombres, apellidos o capítulo..."
            onkeyup="manejarEnter(event)">
        <button class="btn-buscar" onclick="ejecutarBusqueda()">Buscar</button>
        <button class="clear-search" onclick="limpiarBusqueda()" style="display:none;">✖</button>
    </div>

    <!-- Aviso rechazado búsqueda manual -->
    <div id="avisoRechazado" style="display:none;" class="aviso-rechazado">
        ⚠️ Esta persona está <strong>RECHAZADA</strong> y no puede ingresar al evento.
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="asistentes-table" id="asistentesTable">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>CIP</th>
                    <th>DNI</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Capítulo</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Estado Asistencia</th>
                </tr>
            </thead>
            <tbody id="asistentesList">
                <tr>
                    <td colspan="9" class="loading-text">Cargando asistentes...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL ESCÁNER QR
══════════════════════════════════════════ -->
<div id="modalEscaner" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-escaner">
        <div class="modal-header-scanner">
            <h2>📷 Escanear QR</h2>
            <button class="modal-close" onclick="cerrarEscaner()">✖</button>
        </div>
        <div class="scanner-area">
            <video id="scannerVideo" autoplay playsinline></video>
            <div class="scanner-frame">
                <div class="scanner-line"></div>
            </div>
        </div>
        <p class="scanner-hint">Apunta la cámara al código QR del asistente</p>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL APROBADO
══════════════════════════════════════════ -->
<div id="modalAprobado" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-aprobado">
        <button class="modal-close modal-close-dark" onclick="cerrarModalAprobado()">✖</button>
        <div class="modal-icono-grande verde">✅</div>
        <h2 class="modal-titulo verde">APROBADO</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">CIP</span>
                <span class="info-value" id="qrCip">-</span>
            </div>
            <div class="info-item">
                <span class="info-label">DNI</span>
                <span class="info-value" id="qrDni">-</span>
            </div>
            <div class="info-item info-full">
                <span class="info-label">Nombres</span>
                <span class="info-value" id="qrNombres">-</span>
            </div>
            <div class="info-item info-full">
                <span class="info-label">Apellidos</span>
                <span class="info-value" id="qrApellidos">-</span>
            </div>
            <div class="info-item info-full">
                <span class="info-label">Capítulo</span>
                <span class="info-value" id="qrCapitulo">-</span>
            </div>
        </div>
        <!-- Estado asistencia -->
        <div id="yaAsistioAviso" class="ya-asistio-aviso" style="display:none;">
            ✅ Esta persona <strong>ya registró su asistencia</strong>
        </div>
        <div class="modal-acciones">
            <button id="btnMarcarAsistencia" class="btn-marcar" onclick="marcarAsistencia()">
                ✅ Marcar Asistencia
            </button>
            <button class="btn-cancelar-modal" onclick="cerrarModalAprobado()">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL RECHAZADO
══════════════════════════════════════════ -->
<div id="modalRechazado" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-rechazado">
        <button class="modal-close modal-close-dark" onclick="cerrarModalRechazado()">✖</button>
        <div class="modal-icono-grande rojo">🚫</div>
        <h2 class="modal-titulo rojo">RECHAZADO</h2>
        <p class="modal-subtitulo-rojo">Esta persona no puede ingresar al evento</p>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">CIP</span>
                <span class="info-value" id="qrRCip">-</span>
            </div>
            <div class="info-item">
                <span class="info-label">DNI</span>
                <span class="info-value" id="qrRDni">-</span>
            </div>
            <div class="info-item info-full">
                <span class="info-label">Nombres</span>
                <span class="info-value" id="qrRNombres">-</span>
            </div>
            <div class="info-item info-full">
                <span class="info-label">Apellidos</span>
                <span class="info-value" id="qrRApellidos">-</span>
            </div>
            <div class="info-item info-full">
                <span class="info-label">Capítulo</span>
                <span class="info-value" id="qrRCapitulo">-</span>
            </div>
        </div>
        <div class="modal-acciones">
            <button class="btn-cancelar-modal" onclick="cerrarModalRechazado()">Cerrar</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     MODAL NO ENCONTRADO
══════════════════════════════════════════ -->
<div id="modalNoEncontrado" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-noEncontrado">
        <button class="modal-close modal-close-dark" onclick="cerrarModalNoEncontrado()">✖</button>
        <div class="modal-icono-grande gris">❓</div>
        <h2 class="modal-titulo gris">NO ENCONTRADO</h2>
        <p style="text-align:center; color:#666; margin: 10px 0 20px;">
            El DNI escaneado no está registrado en el sistema.
        </p>
        <div class="modal-acciones">
            <button class="btn-cancelar-modal" onclick="cerrarModalNoEncontrado()">Cerrar</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     TOAST NOTIFICACIÓN
══════════════════════════════════════════ -->
<div id="toast" class="toast" style="display:none;"></div>

<style>
/* ── GENERAL ── */
.dashboard-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* ── TARJETAS ── */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 5px solid;
}
.card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
.card-asistieron { border-left-color: #4caf50; }
.card-faltan     { border-left-color: #ff9800; }
.card-icon { font-size: 48px; }
.card-info { flex: 1; }
.card-info h3 { margin: 0 0 5px 0; font-size: 16px; color: #666; }
.card-number { font-size: 36px; font-weight: bold; margin: 10px 0; }
.card-asistieron .card-number { color: #4caf50; }
.card-faltan     .card-number { color: #ff9800; }
.card-link { font-size: 12px; color: #999; transition: color 0.3s; }
.card:hover .card-link { color: var(--rojo); }

/* ── BOTÓN QR ── */
.btn-qr {
    padding: 12px 24px;
    background: #1565c0;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}
.btn-qr:hover { background: #0d47a1; }

/* ── BARRA DE BÚSQUEDA ── */
.search-bar {
    position: relative;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.search-icon {
    position: absolute;
    left: 12px;
    font-size: 18px;
    color: #999;
    pointer-events: none;
}
.search-bar input {
    flex: 1;
    padding: 12px 12px 12px 40px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}
.search-bar input:focus {
    outline: none;
    border-color: var(--rojo);
    box-shadow: 0 0 0 3px rgba(179,0,0,0.1);
}
.btn-buscar {
    padding: 12px 20px;
    background: var(--rojo);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
    white-space: nowrap;
}
.btn-buscar:hover { background: var(--rojo-oscuro); }
.clear-search {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #999;
    padding: 0 5px;
}
.clear-search:hover { color: var(--rojo); }

/* ── AVISO RECHAZADO ── */
.aviso-rechazado {
    background: #fff0f0;
    border: 2px solid #b30000;
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 15px;
    color: #b30000;
    font-size: 15px;
    animation: fadeIn 0.4s ease;
}

/* ── TABLA ── */
.table-responsive { overflow-x: auto; border-radius: 8px; }
.asistentes-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.asistentes-table th {
    background: var(--rojo-oscuro);
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    position: sticky;
    top: 0;
    white-space: nowrap;
}
.asistentes-table td { padding: 10px 12px; border-bottom: 1px solid #e0e0e0; }
.asistentes-table tbody tr { transition: background-color 0.2s; }
.asistentes-table tbody tr:not(.fila-rechazada):hover { background-color: #f9f9f9; }
.fila-rechazada { background-color: #fff0f0 !important; }
.fila-rechazada td { color: #b30000 !important; font-weight: bold; }
.loading-text, .no-results { text-align: center; padding: 40px; color: #666; font-style: italic; }

/* ── BADGES ── */
.asistio-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    min-width: 100px;
}
.asistio-si       { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }
.asistio-no       { background:#ffebee; color:#c62828; border:1px solid #ef9a9a; }
.asistio-pendiente{ background:#fff3e0; color:#e65100; border:1px solid #ffcc80; }
.badge-rechazado  {
    display:inline-block; padding:5px 12px; border-radius:20px;
    font-size:12px; font-weight:bold; min-width:100px; text-align:center;
    background:#ffebee; color:#b30000; border:1px solid #b30000;
}

/* ══════════════════════════════════════════
   MODALES
══════════════════════════════════════════ */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.2s ease;
}
.modal-box {
    background: white;
    border-radius: 16px;
    padding: 30px;
    width: 90%;
    max-width: 420px;
    position: relative;
    animation: slideUp 0.3s ease;
    text-align: center;
}
.modal-close {
    position: absolute;
    top: 12px;
    right: 14px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #999;
}
.modal-close:hover { color: #333; }
.modal-icono-grande { font-size: 64px; margin-bottom: 10px; }
.modal-titulo {
    font-size: 26px;
    font-weight: bold;
    margin: 0 0 20px;
    letter-spacing: 1px;
}
.modal-titulo.verde { color: #2e7d32; }
.modal-titulo.rojo  { color: #b30000; }
.modal-titulo.gris  { color: #666; }
.modal-subtitulo-rojo { color: #b30000; margin: -15px 0 20px; font-size: 14px; }

/* Info grid dentro modal */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 20px;
    text-align: left;
}
.info-item {
    background: #f5f5f5;
    border-radius: 8px;
    padding: 10px 14px;
}
.info-full { grid-column: span 2; }
.info-label { display: block; font-size: 11px; color: #999; text-transform: uppercase; margin-bottom: 4px; }
.info-value { display: block; font-size: 15px; font-weight: bold; color: #333; }

/* Ya asistió aviso */
.ya-asistio-aviso {
    background: #e8f5e9;
    border: 1px solid #a5d6a7;
    border-radius: 8px;
    padding: 12px;
    color: #2e7d32;
    font-size: 14px;
    margin-bottom: 15px;
}

/* Botones modal */
.modal-acciones { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
.btn-marcar {
    padding: 12px 28px;
    background: #2e7d32;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}
.btn-marcar:hover { background: #1b5e20; }
.btn-marcar:disabled {
    background: #aaa;
    cursor: not-allowed;
}
.btn-cancelar-modal {
    padding: 12px 28px;
    background: #e0e0e0;
    color: #333;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.3s;
}
.btn-cancelar-modal:hover { background: #bdbdbd; }

/* ══════════════════════════════════════════
   ESCÁNER
══════════════════════════════════════════ */
.modal-escaner { max-width: 480px; padding: 20px; }
.modal-header-scanner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.modal-header-scanner h2 { margin: 0; font-size: 18px; }
.scanner-area {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    background: #000;
    border-radius: 12px;
    overflow: hidden;
}
#scannerVideo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.scanner-frame {
    position: absolute;
    inset: 0;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 12px;
    overflow: hidden;
}
.scanner-line {
    position: absolute;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, #1565c0, transparent);
    animation: scanLine 2s linear infinite;
}
.scanner-hint {
    text-align: center;
    color: #666;
    font-size: 13px;
    margin-top: 12px;
}

/* ══════════════════════════════════════════
   TOAST
══════════════════════════════════════════ */
.toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    padding: 14px 28px;
    border-radius: 30px;
    font-size: 15px;
    font-weight: bold;
    color: white;
    z-index: 99999;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    animation: fadeIn 0.3s ease;
}
.toast-success { background: #2e7d32; }
.toast-error   { background: #b30000; }

/* ══════════════════════════════════════════
   ANIMACIONES
══════════════════════════════════════════ */
@keyframes fadeIn  { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes scanLine{ from { top: 0; } to { top: 100%; } }
@keyframes parpadeo{ 0% { opacity:0; transform:translateY(-5px); } 100% { opacity:1; transform:translateY(0); } }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
    .dashboard-container { padding: 15px; }
    .stats-cards { grid-template-columns: 1fr; gap: 15px; }
    .card { padding: 15px; }
    .card-icon { font-size: 36px; }
    .card-number { font-size: 28px; }
    .asistentes-table { font-size: 12px; }
    .asistentes-table th, .asistentes-table td { padding: 8px; }
    .asistio-badge { min-width: 80px; font-size: 11px; padding: 4px 8px; }
    .search-bar input { font-size: 13px; padding: 10px 10px 10px 35px; }
    .btn-buscar { padding: 10px 14px; font-size: 13px; }
    .modal-box { padding: 20px; }
    .info-grid { grid-template-columns: 1fr; }
    .info-full { grid-column: span 1; }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>

<script>
let todosLosAprobados  = [];
let todosLosRechazados = [];
let streamActivo       = null;
let scannerInterval    = null;
let asistenteActualId  = null;
let escanerBloqueado   = false;

// ─── CSRF TOKEN ───────────────────────────────────────────────────────────────
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

document.addEventListener('DOMContentLoaded', function () {
    cargarDatos();
});

// ─── CARGA PARALELA ───────────────────────────────────────────────────────────
function cargarDatos() {
    Promise.all([
        fetch('/ver-aprobados',  { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json()),
        fetch('/ver-rechazados', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json())
    ])
    .then(([dataAprobados, dataRechazados]) => {
        todosLosAprobados  = dataAprobados.data  || [];
        todosLosRechazados = dataRechazados.data || [];
        mostrarAsistentes(todosLosAprobados, false);
        actualizarTarjetas(todosLosAprobados);
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError();
    });
}

// ─── RENDERIZAR FILAS ────────────────────────────────────────────────────────
function mostrarAsistentes(asistentes, incluirRechazados = false) {
    const tbody = document.getElementById('asistentesList');
    document.getElementById('avisoRechazado').style.display = 'none';

    if (!asistentes || asistentes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="no-results">No hay asistentes para mostrar</td></tr>';
        return;
    }

    tbody.innerHTML = asistentes.map((asistente, index) => {
        const esRechazado = asistente.estado === 'rechazado';
        const filaClase   = esRechazado ? 'fila-rechazada' : '';
        let asistioHtml   = '';

        if (esRechazado) {
            asistioHtml = '<span class="badge-rechazado">🚫 Rechazado</span>';
        } else if (asistente.asistio === 1) {
            asistioHtml = '<span class="asistio-badge asistio-si">✓ Asistió</span>';
        } else if (asistente.asistio === 0) {
            asistioHtml = '<span class="asistio-badge asistio-no">✗ No asistió</span>';
        } else {
            asistioHtml = '<span class="asistio-badge asistio-pendiente">⏳ Por llegar</span>';
        }

        return `
            <tr class="${filaClase}">
                <td>${index + 1}</td>
                <td>${asistente.cip       || '-'}</td>
                <td>${asistente.dni       || '-'}</td>
                <td>${asistente.nombres   || '-'}</td>
                <td>${asistente.apellidos || '-'}</td>
                <td>${asistente.capitulo  || '-'}</td>
                <td>${asistente.celular   || '-'}</td>
                <td>${asistente.correo    || '-'}</td>
                <td>${asistioHtml}</td>
            </tr>
        `;
    }).join('');
}

// ─── BÚSQUEDA MANUAL ─────────────────────────────────────────────────────────
function ejecutarBusqueda() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const clearBtn   = document.querySelector('.clear-search');
    const aviso      = document.getElementById('avisoRechazado');

    if (searchTerm === '') {
        clearBtn.style.display = 'none';
        aviso.style.display    = 'none';
        mostrarAsistentes(todosLosAprobados, false);
        actualizarTarjetas(todosLosAprobados);
        return;
    }

    clearBtn.style.display = 'block';

    const aprobadosFiltrados  = todosLosAprobados.filter(a  => buscarEnCampos(a, searchTerm));
    const rechazadosFiltrados = todosLosRechazados.filter(a => buscarEnCampos(a, searchTerm));
    const hayRechazados       = rechazadosFiltrados.length > 0;
    const combinados          = [...aprobadosFiltrados, ...rechazadosFiltrados];

    if (combinados.length === 0) {
        document.getElementById('asistentesList').innerHTML =
            '<tr><td colspan="9" class="no-results">No se encontraron resultados</td></tr>';
        aviso.style.display = 'none';
    } else {
        mostrarAsistentes(combinados, true);
        actualizarTarjetas(aprobadosFiltrados);
    }

    aviso.style.display = hayRechazados ? 'block' : 'none';
}

function buscarEnCampos(a, term) {
    return (
        (a.cip       && a.cip.toLowerCase().includes(term))       ||
        (a.dni       && a.dni.toLowerCase().includes(term))       ||
        (a.nombres   && a.nombres.toLowerCase().includes(term))   ||
        (a.apellidos && a.apellidos.toLowerCase().includes(term)) ||
        (a.capitulo  && a.capitulo.toLowerCase().includes(term))
    );
}

function manejarEnter(event) {
    if (event.key === 'Enter') ejecutarBusqueda();
}

function limpiarBusqueda() {
    document.getElementById('searchInput').value            = '';
    document.querySelector('.clear-search').style.display   = 'none';
    document.getElementById('avisoRechazado').style.display = 'none';
    mostrarAsistentes(todosLosAprobados, false);
    actualizarTarjetas(todosLosAprobados);
}

// ─── TARJETAS ────────────────────────────────────────────────────────────────
function actualizarTarjetas(asistentes) {
    const asistieron = asistentes.filter(a => a.asistio === 1).length;
    const faltan     = asistentes.filter(a => a.asistio === null || a.asistio === 0).length;
    document.getElementById('totalAsistieron').textContent = asistieron;
    document.getElementById('totalFaltan').textContent     = faltan;
}

function mostrarAsistieron() {
    const asistieron = todosLosAprobados.filter(a => a.asistio === 1);
    mostrarAsistentes(asistieron, false);
    actualizarTarjetas(asistieron);
    document.getElementById('searchInput').value            = '';
    document.getElementById('avisoRechazado').style.display = 'none';
    document.querySelector('.table-responsive').scrollIntoView({ behavior: 'smooth' });
}

function mostrarFaltan() {
    const faltan = todosLosAprobados.filter(a => a.asistio === null || a.asistio === 0);
    mostrarAsistentes(faltan, false);
    actualizarTarjetas(faltan);
    document.getElementById('searchInput').value            = '';
    document.getElementById('avisoRechazado').style.display = 'none';
    document.querySelector('.table-responsive').scrollIntoView({ behavior: 'smooth' });
}

// ─── ERROR ───────────────────────────────────────────────────────────────────
function mostrarError() {
    document.getElementById('asistentesList').innerHTML =
        '<tr><td colspan="9" class="loading-text">❌ Error al cargar los datos. Intente nuevamente.</td></tr>';
    document.getElementById('totalAsistieron').textContent = 'Error';
    document.getElementById('totalFaltan').textContent     = 'Error';
}

// ══════════════════════════════════════════════════════════════════════════════
// ESCÁNER QR
// ══════════════════════════════════════════════════════════════════════════════

function abrirEscaner() {
    document.getElementById('modalEscaner').style.display = 'flex';
    escanerBloqueado = false;

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(stream => {
            streamActivo = stream;
            const video  = document.getElementById('scannerVideo');
            video.srcObject = stream;
            video.play();

            // Canvas oculto para leer frames
            const canvas  = document.createElement('canvas');
            const context = canvas.getContext('2d');

            scannerInterval = setInterval(() => {
                if (escanerBloqueado) return;
                if (video.readyState !== video.HAVE_ENOUGH_DATA) return;

                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const code      = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert'
                });

                if (code && code.data) {
                    escanerBloqueado = true;
                    procesarQr(code.data.trim());
                }
            }, 300);
        })
        .catch(err => {
            console.error('Error cámara:', err);
            cerrarEscaner();
            mostrarToast('❌ No se pudo acceder a la cámara', 'error');
        });
}

function cerrarEscaner() {
    clearInterval(scannerInterval);
    if (streamActivo) {
        streamActivo.getTracks().forEach(t => t.stop());
        streamActivo = null;
    }
    document.getElementById('modalEscaner').style.display = 'none';
}

function procesarQr(dni) {
    cerrarEscaner();

    fetch('/buscar-por-dni', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ dni })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            abrirModalNoEncontrado();
            return;
        }

        if (data.estado === 'aprobado') {
            abrirModalAprobado(data);
        } else if (data.estado === 'rechazado') {
            abrirModalRechazado(data);
        } else {
            // Estado registrado u otro: tratar como no encontrado en este contexto
            abrirModalNoEncontrado();
        }
    })
    .catch(err => {
        console.error('Error al buscar DNI:', err);
        mostrarToast('❌ Error al consultar el servidor', 'error');
    });
}

// ── MODAL APROBADO ────────────────────────────────────────────────────────────
function abrirModalAprobado(data) {
    asistenteActualId = data.id;

    document.getElementById('qrCip').textContent       = data.cip       || '-';
    document.getElementById('qrDni').textContent       = data.dni       || '-';
    document.getElementById('qrNombres').textContent   = data.nombres   || '-';
    document.getElementById('qrApellidos').textContent = data.apellidos || '-';
    document.getElementById('qrCapitulo').textContent  = data.capitulo  || '-';

    const btnMarcar    = document.getElementById('btnMarcarAsistencia');
    const yaAsistioDiv = document.getElementById('yaAsistioAviso');

    if (data.asistio === 1) {
        // Ya asistió
        yaAsistioDiv.style.display = 'block';
        btnMarcar.style.display    = 'none';
    } else {
        yaAsistioDiv.style.display = 'none';
        btnMarcar.style.display    = 'inline-block';
        btnMarcar.disabled         = false;
        btnMarcar.textContent      = '✅ Marcar Asistencia';
    }

    document.getElementById('modalAprobado').style.display = 'flex';
}

function cerrarModalAprobado() {
    document.getElementById('modalAprobado').style.display = 'none';
    asistenteActualId = null;
}

// ── MODAL RECHAZADO ───────────────────────────────────────────────────────────
function abrirModalRechazado(data) {
    document.getElementById('qrRCip').textContent       = data.cip       || '-';
    document.getElementById('qrRDni').textContent       = data.dni       || '-';
    document.getElementById('qrRNombres').textContent   = data.nombres   || '-';
    document.getElementById('qrRApellidos').textContent = data.apellidos || '-';
    document.getElementById('qrRCapitulo').textContent  = data.capitulo  || '-';

    document.getElementById('modalRechazado').style.display = 'flex';
}

function cerrarModalRechazado() {
    document.getElementById('modalRechazado').style.display = 'none';
}

// ── MODAL NO ENCONTRADO ───────────────────────────────────────────────────────
function abrirModalNoEncontrado() {
    document.getElementById('modalNoEncontrado').style.display = 'flex';
}

function cerrarModalNoEncontrado() {
    document.getElementById('modalNoEncontrado').style.display = 'none';
}

// ── MARCAR ASISTENCIA ─────────────────────────────────────────────────────────
function marcarAsistencia() {
    if (!asistenteActualId) return;

    const btnMarcar = document.getElementById('btnMarcarAsistencia');
    btnMarcar.disabled    = true;
    btnMarcar.textContent = 'Guardando...';

    fetch('/marcar-asistencia-qr', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id: asistenteActualId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Actualizar en memoria
            const idx = todosLosAprobados.findIndex(a => a.id === asistenteActualId);
            if (idx !== -1) todosLosAprobados[idx].asistio = 1;

            mostrarAsistentes(todosLosAprobados, false);
            actualizarTarjetas(todosLosAprobados);

            cerrarModalAprobado();
            mostrarToast('✅ Asistencia registrada correctamente', 'success');
        } else {
            btnMarcar.disabled    = false;
            btnMarcar.textContent = '✅ Marcar Asistencia';
            mostrarToast('⚠️ ' + data.message, 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        btnMarcar.disabled    = false;
        btnMarcar.textContent = '✅ Marcar Asistencia';
        mostrarToast('❌ Error al conectar con el servidor', 'error');
    });
}

// ── TOAST ─────────────────────────────────────────────────────────────────────
function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent  = mensaje;
    toast.className    = `toast toast-${tipo}`;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3500);
}
</script>
@endsection