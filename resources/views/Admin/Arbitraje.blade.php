@extends('Admin.app')

@section('title', 'Gestión de Arbitrajes')
@section('page-title', 'Administración de Arbitrajes')

@section('content')

<div class="container-fluid">
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="mb-0">Gestión de Arbitrajes</h3>
            <p class="text-muted">Administra y visualiza todos los procesos de arbitraje del sistema</p>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body py-2">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="searchDni" 
                                       placeholder="Buscar por DNI...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-danger btn-sm w-100" id="btnBuscar">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total</h6>
                            <h3 class="mb-0" id="totalArbitrajes">0</h3>
                        </div>
                        <i class="fas fa-balance-scale fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Validando</h6>
                            <h3 class="mb-0" id="totalValidando">0</h3>
                        </div>
                        <i class="fas fa-hourglass-half fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">En Proceso</h6>
                            <h3 class="mb-0" id="totalProceso">0</h3>
                        </div>
                        <i class="fas fa-spinner fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Terminados</h6>
                            <h3 class="mb-0" id="totalTerminados">0</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando arbitrajes...</p>
    </div>

    <div id="noResults" class="alert alert-info text-center" style="display: none;">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>No se encontraron arbitrajes</h5>
        <p class="mb-0">No hay arbitrajes registrados o no coinciden con tu búsqueda.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Listado de Arbitrajes
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaArbitrajes">
                    <thead class="table-light">
                        <tr>
                            <th>N° Expediente</th>
                            <th>Materia</th>
                            <th>Creador</th>
                            <th>DNI Creador</th>
                            <th>Personas</th>
                            <th width="150">Fecha Inicio</th>
                            <th width="120">Estado</th>
                            <th width="120">Tipo</th>
                            <th width="120" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="arbitrajesTableBody">
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="spinner-border text-danger" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando arbitrajes...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
console.log('=== ADMIN ARBITRAJES SCRIPT INICIADO ===');

function irADetalle(id) {
    window.location.href = `/arbitrajes/${id}/detalle`;
}

function formatFecha(fecha) {
    if (!fecha) return 'No especificada';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-PE', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getEstadoBadge(estado) {
    const badges = {
        'validando': 'bg-warning text-dark',
        'iniciado': 'bg-info',
        'en proceso': 'bg-primary',
        'terminado': 'bg-success',
        'rechazado': 'bg-danger',
        'observado': 'bg-danger',
        'finalizado': 'bg-secondary'
    };
    const badgeClass = badges[estado?.toLowerCase()] || 'bg-secondary';
    return `<span class="badge ${badgeClass}">${(estado || 'iniciado').toUpperCase()}</span>`;
}

function getTipoBadge(arbitraje) {
    const tipo = arbitraje.tipo_arbitraje || 'normal';
    if (tipo === 'emergencia') {
        return '<span class="badge bg-danger"><i class="fas fa-bolt me-1"></i>EMERGENCIA</span>';
    }
    return '<span class="badge bg-secondary"><i class="fas fa-gavel me-1"></i>NORMAL</span>';
}

function ordenarArbitrajes(arbitrajes) {
    return [...arbitrajes].sort((a, b) => {
        const tipoA = a.tipo_arbitraje || 'normal';
        const tipoB = b.tipo_arbitraje || 'normal';
        if (tipoA === 'emergencia' && tipoB !== 'emergencia') return -1;
        if (tipoA !== 'emergencia' && tipoB === 'emergencia') return 1;
        const fechaA = a.fecha_inicio ? new Date(a.fecha_inicio) : new Date(0);
        const fechaB = b.fecha_inicio ? new Date(b.fecha_inicio) : new Date(0);
        return fechaB - fechaA;
    });
}

function actualizarEstadisticas(data) {
    const total = data.length;
    const validando = data.filter(a => a.estado?.toLowerCase() === 'validando').length;
    const proceso = data.filter(a => a.estado?.toLowerCase() === 'en proceso' || a.estado?.toLowerCase() === 'iniciado').length;
    const terminados = data.filter(a => a.estado?.toLowerCase() === 'terminado').length;
    
    const totalEl = document.getElementById('totalArbitrajes');
    const validandoEl = document.getElementById('totalValidando');
    const procesoEl = document.getElementById('totalProceso');
    const terminadosEl = document.getElementById('totalTerminados');
    
    if (totalEl) totalEl.textContent = total;
    if (validandoEl) validandoEl.textContent = validando;
    if (procesoEl) procesoEl.textContent = proceso;
    if (terminadosEl) terminadosEl.textContent = terminados;
}

function renderArbitrajes(data) {
    const tbody = document.getElementById('arbitrajesTableBody');
    
    if (!data || data.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No hay arbitrajes registrados</td></tr>';
        actualizarEstadisticas([]);
        return;
    }
    
    document.getElementById('noResults').style.display = 'none';
    actualizarEstadisticas(data);
    
    const arbitrajesOrdenados = ordenarArbitrajes(data);
    
    tbody.innerHTML = arbitrajesOrdenados.map(arb => {
        const tipo = arb.tipo_arbitraje || 'normal';
        const isEmergencia = tipo === 'emergencia';
        const rowClass = isEmergencia ? 'emergencia-row' : '';
        
        const numeroExpediente = arb.numero_expediente 
            ? `<span class="badge bg-dark">${arb.numero_expediente}</span>`
            : '<span class="text-muted">Sin expediente</span>';
        
        return `
            <tr style="cursor: pointer;" class="${rowClass}" onclick="irADetalle(${arb.id_arbitraje})">
                <td>${numeroExpediente}</td>
                <td>
                    <strong>${arb.nombre_materia || 'Sin materia'}</strong><br>
                    <small class="text-muted">${(arb.pretenciones || '').substring(0, 60)}${(arb.pretenciones || '').length > 60 ? '...' : ''}</small>
                </td>
                <td>${arb.creador_nombre || 'No especificado'}</td>
                <td><span class="badge bg-secondary">${arb.creador_dni || 'N/A'}</span></td>
                <td><small>${arb.personas?.map(p => `${p.dni} (${p.tipo})`).join('<br>') || 'Sin personas'}</small></td>
                <td><small>${formatFecha(arb.fecha_inicio)}</small></td>
                <td>${getEstadoBadge(arb.estado)}</td>
                <td>${getTipoBadge(arb)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); irADetalle(${arb.id_arbitraje})" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function cargarArbitrajes(dni = '') {
    console.log('=== Cargando arbitrajes ===');
    const loading = document.getElementById('loading');
    const noResults = document.getElementById('noResults');
    const tbody = document.getElementById('arbitrajesTableBody');
    
    if (loading) loading.style.display = 'block';
    if (noResults) noResults.style.display = 'none';
    
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-5"><div class="spinner-border text-danger" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2 mb-0">Cargando arbitrajes...</p></td></tr>';
    }
    
    let url = '/arbitrajes/obtener';
    if (dni) url += `?dni=${encodeURIComponent(dni)}`;
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (loading) loading.style.display = 'none';
            if (data.success && data.arbitrajes && data.arbitrajes.length > 0) {
                renderArbitrajes(data.arbitrajes);
            } else {
                if (noResults) noResults.style.display = 'block';
                if (tbody) tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No hay arbitrajes registrados</td></td>';
                actualizarEstadisticas([]);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loading) loading.style.display = 'none';
            if (noResults) noResults.style.display = 'block';
            if (tbody) tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger py-4">Error al cargar los datos: ${error.message}</td></tr>`;
            actualizarEstadisticas([]);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    cargarArbitrajes();
    
    const btnBuscar = document.getElementById('btnBuscar');
    const searchDni = document.getElementById('searchDni');
    
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function() {
            cargarArbitrajes(searchDni ? searchDni.value.trim() : '');
        });
    }
    
    if (searchDni) {
        searchDni.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') cargarArbitrajes(this.value.trim());
        });
    }
});
</script>
@endsection

@push('styles')
<style>
.table-hover tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
.card { border: none; transition: all 0.3s ease; }
.card:hover { transform: translateY(-2px); }
.badge { font-size: 0.75rem; font-weight: 600; padding: 0.5em 0.75em; }
.emergencia-row { background-color: #fff5f5 !important; border-left: 4px solid #dc3545; }
.emergencia-row:hover { background-color: #ffe8e8 !important; }
#arbitrajesTableBody tr { transition: all 0.2s ease-in-out; }
</style>
@endpush