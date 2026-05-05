@extends('eventoscipcdll.layout')

@section('title', 'Dashboard - Listado de Asistentes')

@section('content')

<style>
*{box-sizing:border-box;margin:0;padding:0}
.dash-wrap{padding:1.5rem;font-family:inherit}
.dash-header{margin-bottom:1.5rem}
.dash-header h2{font-size:20px;font-weight:600;color:#1a1a1a;margin-bottom:5px}
.dash-header p{font-size:13px;color:#666}
.dash-stats{display:flex;gap:12px;margin-bottom:1.5rem;flex-wrap:wrap}
.stat-card{flex:1;min-width:150px;background:#fff;border-radius:10px;padding:15px;box-shadow:0 1px 3px rgba(0,0,0,0.1);border:1px solid #e8e7e0}
.stat-card.pend{border-top:3px solid #EF9F27}
.stat-card.ap{border-top:3px solid #97C459}
.stat-card.re{border-top:3px solid #F09595}
.stat-num{font-size:28px;font-weight:700;margin-bottom:5px}
.stat-card.pend .stat-num{color:#854F0B}
.stat-card.ap .stat-num{color:#3B6D11}
.stat-card.re .stat-num{color:#A32D2D}
.stat-label{font-size:12px;color:#888;text-transform:uppercase;letter-spacing:.5px}

.btn-filtro{padding:8px 20px;border-radius:30px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .2s;background:#f2f1ec;color:#666;margin-right:8px;margin-bottom:10px}
.btn-filtro:hover{background:#e3e2dc}
.btn-filtro.activo{background:#1a1a1a;color:#fff}
.btn-filtro.pend.activo{background:#EF9F27;color:#fff}
.btn-filtro.ap.activo{background:#97C459;color:#fff}
.btn-filtro.re.activo{background:#F09595;color:#fff}

.tbl-wrap{border:1px solid #d8d7d0;border-radius:10px;overflow:hidden;margin-top:1rem}
.tbl-wrap table{width:100%;border-collapse:collapse}
.tbl-wrap th{padding:12px 11px;font-size:11px;font-weight:700;color:#999;text-align:left;border-bottom:1px solid #e0dfd8;text-transform:uppercase;background:#f2f1ec}
.tbl-wrap td{padding:10px 11px;font-size:13px;color:#1a1a1a;border-bottom:1px solid #ebebeb}
.tbl-wrap tr:nth-child(even){background:#fafaf8}
.tbl-wrap tr:hover{background:#f5f5f0}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:600}
.badge.registrado{background:#FAEEDA;color:#854F0B}
.badge.aprobado{background:#EAF3DE;color:#3B6D11}
.badge.rechazado{background:#FCEBEB;color:#A32D2D}
.empty-state{text-align:center;padding:2rem;color:#bbb}
.paginacion{display:flex;align-items:center;justify-content:center;gap:10px;margin-top:1rem}
.btn-pag{background:#fff;border:1px solid #d0cfca;border-radius:6px;padding:6px 14px;font-size:12px;cursor:pointer}
.btn-pag:hover:not(:disabled){background:#f5f5f3}
.btn-pag:disabled{opacity:.4;cursor:not-allowed}
.pag-info{font-size:12px;color:#888}
</style>

<div class="dash-wrap">
    <div class="dash-header">
        <h2>Bienvenido, {{ session('usuario') }}</h2>
        <p>Panel de control - Listado de asistentes registrados</p>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="dash-stats">
        <div class="stat-card pend">
            <div class="stat-num" id="total-pend">0</div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="stat-card ap">
            <div class="stat-num" id="total-ap">0</div>
            <div class="stat-label">Aprobados</div>
        </div>
        <div class="stat-card re">
            <div class="stat-num" id="total-re">0</div>
            <div class="stat-label">Rechazados</div>
        </div>
    </div>

    <!-- Botones de filtro -->
    <div>
        <button class="btn-filtro pend activo" id="filtro-pend" onclick="cambiarFiltro('registrado')">📋 Pendientes</button>
        <button class="btn-filtro ap" id="filtro-ap" onclick="cambiarFiltro('aprobado')">✅ Aprobados</button>
        <button class="btn-filtro re" id="filtro-re" onclick="cambiarFiltro('rechazado')">❌ Rechazados</button>
    </div>

    <!-- Tabla de asistentes -->
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>CIP</th>
                    <th>DNI</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Capítulo</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="tabla-body">
                <tr><td colspan="9" class="empty-state">Cargando...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="paginacion">
        <button class="btn-pag" id="btn-ant" disabled onclick="paginaAnterior()">← Anterior</button>
        <span class="pag-info">Página <b id="pag-actual">1</b> de <b id="pag-total">1</b></span>
        <button class="btn-pag" id="btn-sig" onclick="paginaSiguiente()">Siguiente →</button>
    </div>
</div>

<script>
let estadoActual = 'registrado';
let pagina = 1;
let ultimaPagina = 1;

function cambiarFiltro(estado) {
    estadoActual = estado;
    pagina = 1;
    
    // Actualizar clases de los botones
    document.querySelectorAll('.btn-filtro').forEach(btn => btn.classList.remove('activo'));
    if (estado === 'registrado') {
        document.getElementById('filtro-pend').classList.add('activo');
    } else if (estado === 'aprobado') {
        document.getElementById('filtro-ap').classList.add('activo');
    } else {
        document.getElementById('filtro-re').classList.add('activo');
    }
    
    cargarDatos();
}

function cargarDatos() {
    const tbody = document.getElementById('tabla-body');
    tbody.innerHTML = '<tr><td colspan="9" class="empty-state">Cargando...</td></tr>';
    
    fetch(`/asistentes/${estadoActual}?page=${pagina}`)
        .then(response => {
            if (response.status === 401) {
                window.location.href = '/login-eventos';
                throw new Error('Sesión expirada');
            }
            if (!response.ok) {
                throw new Error('Error al cargar los datos');
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            
            // Actualizar información de paginación
            ultimaPagina = data.last_page || 1;
            document.getElementById('pag-actual').textContent = data.current_page || 1;
            document.getElementById('pag-total').textContent = ultimaPagina;
            
            // Habilitar/Deshabilitar botones de paginación
            document.getElementById('btn-ant').disabled = (data.current_page <= 1);
            document.getElementById('btn-sig').disabled = (data.current_page >= ultimaPagina);
            
            const asistentes = data.data || [];
            
            if (asistentes.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="empty-state">No hay asistentes ${estadoActual === 'registrado' ? 'pendientes' : estadoActual === 'aprobado' ? 'aprobados' : 'rechazados'}</td></tr>`;
                return;
            }
            
            tbody.innerHTML = asistentes.map((asistente, index) => `
                <tr>
                    <td>${((pagina-1)*10) + index + 1}</td>
                    <td>${asistente.cip || '-'}</td>
                    <td>${asistente.dni || '-'}</td>
                    <td>${asistente.nombres || '-'}</td>
                    <td>${asistente.apellidos || '-'}</td>
                    <td>${asistente.capitulo || '-'}</td>
                    <td>${asistente.celular || '-'}</td>
                    <td>${asistente.correo || '-'}</td>
                    <td><span class="badge ${asistente.estado}">${asistente.estado === 'registrado' ? 'Pendiente' : (asistente.estado === 'aprobado' ? 'Aprobado' : 'Rechazado')}</span></td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `<td><td colspan="9" class="empty-state" style="color:#A32D2D">Error al cargar los datos. Verifique su conexión.</td></tr>`;
        });
}

function cargarEstadisticas() {
    fetch('/asistentes')
        .then(response => response.json())
        .then(data => {
            if (data.resumen) {
                document.getElementById('total-pend').textContent = data.resumen.registrados || 0;
                document.getElementById('total-ap').textContent = data.resumen.aprobados || 0;
                document.getElementById('total-re').textContent = data.resumen.rechazados || 0;
            }
        })
        .catch(error => console.error('Error al cargar estadísticas:', error));
}

function paginaSiguiente() {
    if (pagina < ultimaPagina) {
        pagina++;
        cargarDatos();
    }
}

function paginaAnterior() {
    if (pagina > 1) {
        pagina--;
        cargarDatos();
    }
}

// Cargar datos iniciales
cargarEstadisticas();
cargarDatos();

// Recargar estadísticas cada 30 segundos
setInterval(cargarEstadisticas, 30000);
</script>

@endsection