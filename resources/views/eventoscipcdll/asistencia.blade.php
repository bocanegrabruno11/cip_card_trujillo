@extends('eventoscipcdll.layout')

@section('title', 'Dashboard - Control de Asistencia')

@section('content')
<div class="dashboard-container">

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

    <div style="margin-bottom: 15px;">
        <button class="btn-qr" onclick="abrirEscaner()">
            📷 Escanear QR
        </button>
    </div>

    <div class="search-bar">
        <div class="search-icon">🔍</div>
        <input type="text" id="searchInput"
            placeholder="Buscar por CIP, DNI, nombres, apellidos o capítulo..."
            onkeyup="manejarEnter(event)">
        <button class="btn-buscar" onclick="ejecutarBusqueda()">Buscar</button>
        <button class="clear-search" onclick="limpiarBusqueda()" style="display:none;">✖</button>
    </div>

    <div id="avisoRechazado" style="display:none;" class="aviso-rechazado">
        ⚠️ Esta persona está <strong>RECHAZADA</strong> y no puede ingresar al evento.
    </div>

    <!-- TABLA CON ALTURA FIJA Y SCROLL -->
    <div class="table-wrapper">
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
        </table>
        <div class="table-body-scroll">
            <table class="asistentes-table asistentes-table-body">
                <tbody id="asistentesList">
                    <tr>
                        <td colspan="9" class="loading-text">Cargando asistentes...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL ESCÁNER QR -->
<div id="modalEscaner" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-escaner">
        <div class="modal-header-scanner">
            <h2>📷 Escanear QR</h2>
            <button class="modal-close" onclick="cerrarEscaner()">✖</button>
        </div>
        <div class="scanner-area">
            <video id="scannerVideo" autoplay playsinline muted></video>
            <div class="scanner-frame">
                <div class="scanner-line"></div>
            </div>
            <div id="scannerStatus" class="scanner-status">🔍 Iniciando cámara...</div>
        </div>

        <div id="qrDebugBox" class="qr-debug-box" style="display:none;">
            <span class="qr-debug-label">QR leído:</span>
            <span id="qrDebugValor"></span>
        </div>

        <!-- Input manual -->
        <div class="scanner-manual">
            <input type="text"
                   id="dniManualInput"
                   placeholder="O ingresa el DNI manualmente..."
                   maxlength="15"
                   onkeyup="if(event.key==='Enter') procesarManual()">
            <button onclick="procesarManual()">Buscar</button>
        </div>

        <p class="scanner-hint">Apunta la cámara al código QR del asistente</p>
    </div>
</div>

<!-- MODAL APROBADO -->
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
        <div id="yaAsistioAviso" class="ya-asistio-aviso" style="display:none;">
            ✅ Esta persona <strong>ya registró su asistencia</strong>
        </div>
        <div class="modal-acciones">
            <button id="btnMarcarAsistencia" class="btn-marcar" onclick="marcarAsistencia()">
                ✅ Marcar Asistencia
            </button>
            <button class="btn-cancelar-modal" onclick="cerrarModalAprobado()">Cerrar</button>
        </div>
    </div>
</div>

<!-- MODAL RECHAZADO -->
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

<!-- MODAL NO ENCONTRADO -->
<div id="modalNoEncontrado" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-noEncontrado">
        <button class="modal-close modal-close-dark" onclick="cerrarModalNoEncontrado()">✖</button>
        <div class="modal-icono-grande gris">❓</div>
        <h2 class="modal-titulo gris">NO ENCONTRADO</h2>
        <p id="msgNoEncontrado" style="text-align:center; color:#666; margin: 10px 0 20px;">
            El QR escaneado no está registrado en el sistema.
        </p>
        <div class="modal-acciones">
            <button class="btn-cancelar-modal" onclick="cerrarModalNoEncontrado()">Cerrar</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div id="toast" class="toast" style="display:none;"></div>

<style>
.dashboard-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
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

/* ── TABLA CON ALTURA FIJA + SCROLL ── */
.table-wrapper {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

/* Cabecera fija */
.table-wrapper > table.asistentes-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    table-layout: fixed;
}

/* Contenedor scrolleable del body */
.table-body-scroll {
    max-height: 460px;   /* ← ajusta este valor a tu gusto */
    overflow-y: auto;
    overflow-x: auto;
}

.table-body-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
.table-body-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
.table-body-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
.table-body-scroll::-webkit-scrollbar-thumb:hover { background: #aaa; }

/* Body tabla — mismos anchos de columna que el header */
.asistentes-table-body {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    table-layout: fixed;
}

/* Anchos de columna compartidos (header y body) */
.asistentes-table th:nth-child(1),
.asistentes-table-body td:nth-child(1) { width: 48px; }
.asistentes-table th:nth-child(2),
.asistentes-table-body td:nth-child(2) { width: 90px; }
.asistentes-table th:nth-child(3),
.asistentes-table-body td:nth-child(3) { width: 90px; }
.asistentes-table th:nth-child(4),
.asistentes-table-body td:nth-child(4) { width: 140px; }
.asistentes-table th:nth-child(5),
.asistentes-table-body td:nth-child(5) { width: 140px; }
.asistentes-table th:nth-child(6),
.asistentes-table-body td:nth-child(6) { width: 120px; }
.asistentes-table th:nth-child(7),
.asistentes-table-body td:nth-child(7) { width: 110px; }
.asistentes-table th:nth-child(8),
.asistentes-table-body td:nth-child(8) { width: 180px; }
.asistentes-table th:nth-child(9),
.asistentes-table-body td:nth-child(9) { width: 130px; }

.asistentes-table th {
    background: var(--rojo-oscuro);
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.asistentes-table-body td {
    padding: 10px 12px;
    border-bottom: 1px solid #e0e0e0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.asistentes-table-body tbody tr { transition: background-color 0.2s; }
.asistentes-table-body tbody tr:not(.fila-rechazada):hover { background-color: #f9f9f9; }
.fila-rechazada { background-color: #fff0f0 !important; }
.fila-rechazada td { color: #b30000 !important; font-weight: bold; }
.loading-text, .no-results { text-align: center; padding: 40px; color: #666; font-style: italic; }

.asistio-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    min-width: 100px;
}
.asistio-si        { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }
.asistio-no        { background:#ffebee; color:#c62828; border:1px solid #ef9a9a; }
.asistio-pendiente { background:#fff3e0; color:#e65100; border:1px solid #ffcc80; }
.badge-rechazado {
    display:inline-block; padding:5px 12px; border-radius:20px;
    font-size:12px; font-weight:bold; min-width:100px; text-align:center;
    background:#ffebee; color:#b30000; border:1px solid #b30000;
}

/* MODALES */
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

.ya-asistio-aviso {
    background: #e8f5e9;
    border: 1px solid #a5d6a7;
    border-radius: 8px;
    padding: 12px;
    color: #2e7d32;
    font-size: 14px;
    margin-bottom: 15px;
}

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
.btn-marcar:hover    { background: #1b5e20; }
.btn-marcar:disabled { background: #aaa; cursor: not-allowed; }
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

/* ESCÁNER */
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
.scanner-status {
    position: absolute;
    bottom: 8px;
    left: 0;
    right: 0;
    text-align: center;
    color: #fff;
    background: rgba(0,0,0,0.55);
    font-size: 13px;
    padding: 6px 0;
    border-radius: 0 0 12px 12px;
}
.qr-debug-box {
    margin: 10px 0 4px;
    background: #e8f5e9;
    border: 1px solid #a5d6a7;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    color: #2e7d32;
    word-break: break-all;
    text-align: left;
}
.qr-debug-label { font-weight: bold; margin-right: 6px; }
.scanner-manual {
    display: flex;
    gap: 8px;
    margin-top: 10px;
}
.scanner-manual input {
    flex: 1;
    padding: 9px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
}
.scanner-manual input:focus { outline: none; border-color: #1565c0; }
.scanner-manual button {
    padding: 9px 16px;
    background: #1565c0;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
}
.scanner-manual button:hover { background: #0d47a1; }
.scanner-hint {
    text-align: center;
    color: #666;
    font-size: 13px;
    margin-top: 12px;
}

/* TOAST */
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

@keyframes fadeIn  { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes scanLine { from { top: 0; } to { top: 100%; } }

@media (max-width: 768px) {
    .dashboard-container { padding: 15px; }
    .stats-cards { grid-template-columns: 1fr; gap: 15px; }
    .card { padding: 15px; }
    .card-icon { font-size: 36px; }
    .card-number { font-size: 28px; }
    .asistentes-table th,
    .asistentes-table-body td { font-size: 12px; padding: 8px; }
    .asistio-badge { min-width: 80px; font-size: 11px; padding: 4px 8px; }
    .search-bar input { font-size: 13px; padding: 10px 10px 10px 35px; }
    .btn-buscar { padding: 10px 14px; font-size: 13px; }
    .modal-box { padding: 20px; }
    .info-grid { grid-template-columns: 1fr; }
    .info-full { grid-column: span 1; }
    .table-body-scroll { max-height: 320px; }
}
</style>

<!-- jsQR con fallback -->
<script>
(function() {
    function cargarFallback() {
        var s = document.createElement('script');
        s.src = 'https://unpkg.com/jsqr@1.4.0/dist/jsQR.min.js';
        document.head.appendChild(s);
    }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
    s.onerror = cargarFallback;
    document.head.appendChild(s);
})();
</script>

<script>
let todosLosAprobados  = [];
let todosLosRechazados = [];
let streamActivo       = null;
let scannerInterval    = null;
let asistenteActualId  = null;
let escanerBloqueado   = false;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

document.addEventListener('DOMContentLoaded', function () {
    cargarDatos();
});

// ─── CARGA DE DATOS ───────────────────────────────────────────────────────────
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

// ─── TABLA ────────────────────────────────────────────────────────────────────
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

// ─── BÚSQUEDA LOCAL ───────────────────────────────────────────────────────────
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

// ─── TARJETAS ─────────────────────────────────────────────────────────────────
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
    document.querySelector('.table-wrapper').scrollIntoView({ behavior: 'smooth' });
}

function mostrarFaltan() {
    const faltan = todosLosAprobados.filter(a => a.asistio === null || a.asistio === 0);
    mostrarAsistentes(faltan, false);
    actualizarTarjetas(faltan);
    document.getElementById('searchInput').value            = '';
    document.getElementById('avisoRechazado').style.display = 'none';
    document.querySelector('.table-wrapper').scrollIntoView({ behavior: 'smooth' });
}

function mostrarError() {
    document.getElementById('asistentesList').innerHTML =
        '<tr><td colspan="9" class="loading-text">❌ Error al cargar los datos. Intente nuevamente.</td></tr>';
    document.getElementById('totalAsistieron').textContent = 'Error';
    document.getElementById('totalFaltan').textContent     = 'Error';
}

// ══════════════════════════════════════════════════════════════════════════════
// ESCÁNER QR
// ══════════════════════════════════════════════════════════════════════════════

function setStatusEscaner(msg) {
    const el = document.getElementById('scannerStatus');
    if (el) el.textContent = msg;
}

function abrirEscaner() {
    if (typeof jsQR === 'undefined') {
        mostrarToast('⚠️ Librería QR no cargó. Recarga la página.', 'error');
        return;
    }

    if (streamActivo) {
        streamActivo.getTracks().forEach(t => t.stop());
        streamActivo = null;
    }
    clearInterval(scannerInterval);

    document.getElementById('modalEscaner').style.display  = 'flex';
    document.getElementById('qrDebugBox').style.display    = 'none';
    document.getElementById('dniManualInput').value        = '';
    setStatusEscaner('🔍 Iniciando cámara...');
    escanerBloqueado = false;

    const constraints = {
        video: {
            facingMode: { ideal: 'environment' },
            width:  { ideal: 1280 },
            height: { ideal: 720 }
        }
    };

    navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => {
            streamActivo = stream;
            const video  = document.getElementById('scannerVideo');
            video.srcObject = stream;

            video.onloadedmetadata = function () {
                video.play();
                setStatusEscaner('🔍 Buscando código QR...');

                const canvas  = document.createElement('canvas');
                const context = canvas.getContext('2d', { willReadFrequently: true });

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

                    if (code && code.data && code.data.trim() !== '') {
                        escanerBloqueado = true;
                        const valorQr    = code.data.trim();

                        console.log('QR detectado:', valorQr);
                        document.getElementById('qrDebugValor').textContent = valorQr;
                        document.getElementById('qrDebugBox').style.display = 'block';
                        setStatusEscaner('✅ QR detectado — procesando...');

                        // Detener cámara y cerrar modal ANTES de procesar
                        detenerCamara();
                        document.getElementById('modalEscaner').style.display = 'none';

                        setTimeout(() => procesarQr(valorQr), 300);
                    }
                }, 300);
            };
        })
        .catch(err => {
            console.error('Error cámara:', err);
            setStatusEscaner('❌ Sin acceso a cámara — usa el ingreso manual');
            mostrarToast('⚠️ Sin acceso a cámara. Usa el ingreso manual.', 'error');
        });
}

function detenerCamara() {
    clearInterval(scannerInterval);
    scannerInterval = null;
    if (streamActivo) {
        streamActivo.getTracks().forEach(t => t.stop());
        streamActivo = null;
    }
    escanerBloqueado = false;
}

function cerrarEscaner() {
    detenerCamara();
    document.getElementById('modalEscaner').style.display = 'none';
}

// ── BÚSQUEDA MANUAL: NO cierra el escáner si no hay cámara activa ──────────
function procesarManual() {
    const input = document.getElementById('dniManualInput');
    const val   = input ? input.value.trim() : '';

    if (!val) {
        mostrarToast('⚠️ Ingresa un DNI o CIP', 'error');
        return;
    }

    // Cerrar escáner si estaba abierto
    cerrarEscaner();

    console.log('Búsqueda manual, valor:', val);
    procesarQr(val);
}

// ── ENVÍO AL SERVIDOR ────────────────────────────────────────────────────────
function procesarQr(dniRaw) {
    console.log('Enviando al servidor:', dniRaw);

    fetch('/buscar-por-dni', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept':       'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ dni: dniRaw })
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        console.log('Respuesta servidor:', data);

        if (!data.success) {
            abrirModalNoEncontrado(data.raw_qr || dniRaw, data.dni_buscado || dniRaw);
            return;
        }

        if (data.estado === 'aprobado') {
            abrirModalAprobado(data);
        } else if (data.estado === 'rechazado') {
            abrirModalRechazado(data);
        } else {
            abrirModalNoEncontrado(dniRaw, dniRaw);
        }
    })
    .catch(err => {
        console.error('Error fetch:', err);
        mostrarToast('❌ Error al conectar con el servidor', 'error');
    });
}

// ─── MODAL APROBADO ───────────────────────────────────────────────────────────
function abrirModalAprobado(data) {
    asistenteActualId = data.id;

    document.getElementById('qrCip').textContent       = data.cip       || '-';
    document.getElementById('qrDni').textContent       = data.dni       || '-';
    document.getElementById('qrNombres').textContent   = data.nombres   || '-';
    document.getElementById('qrApellidos').textContent = data.apellidos || '-';
    document.getElementById('qrCapitulo').textContent  = data.capitulo  || '-';

    const btnMarcar    = document.getElementById('btnMarcarAsistencia');
    const yaAsistioDiv = document.getElementById('yaAsistioAviso');

    if (data.asistio == 1) {
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

// ─── MODAL RECHAZADO ──────────────────────────────────────────────────────────
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

// ─── MODAL NO ENCONTRADO ──────────────────────────────────────────────────────
function abrirModalNoEncontrado(rawQr, dniBuscado) {
    const msg = document.getElementById('msgNoEncontrado');
    if (msg) {
        msg.innerHTML = `El dato ingresado no está registrado en el sistema.<br>
            <small style="color:#999; display:block; margin-top:8px;">
                Valor recibido: <strong>${rawQr}</strong><br>
                DNI buscado: <strong>${dniBuscado}</strong>
            </small>`;
    }
    document.getElementById('modalNoEncontrado').style.display = 'flex';
}

function cerrarModalNoEncontrado() {
    document.getElementById('modalNoEncontrado').style.display = 'none';
}

// ─── MARCAR ASISTENCIA ────────────────────────────────────────────────────────
function marcarAsistencia() {
    if (!asistenteActualId) return;

    const btnMarcar = document.getElementById('btnMarcarAsistencia');
    btnMarcar.disabled    = true;
    btnMarcar.textContent = 'Guardando...';

    fetch('/marcar-asistencia-qr', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept':       'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id: asistenteActualId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
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

// ─── TOAST ────────────────────────────────────────────────────────────────────
function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent   = mensaje;
    toast.className     = `toast toast-${tipo}`;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3500);
}
</script>
@endsection