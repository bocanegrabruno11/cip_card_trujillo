@extends('Admin.app')

@section('title', 'Gestión de JRD')
@section('page-title', 'Administración de JRD')

@section('content')

<div class="container-fluid">
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="mb-0">Gestión de JPRD</h3>
            <p class="text-muted">Administra y visualiza todos los procesos de JPRD del sistema</p>
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
                                <input type="text" class="form-control" id="searchDni" placeholder="Buscar por DNI...">
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
                        <div><h6 class="mb-0">Total</h6><h3 class="mb-0" id="totalJrd">0</h3></div>
                        <i class="fas fa-gavel fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-0">En Proceso</h6><h3 class="mb-0" id="totalProceso">0</h3></div>
                        <i class="fas fa-spinner fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-0">Observados</h6><h3 class="mb-0" id="totalObservados">0</h3></div>
                        <i class="fas fa-eye fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="mb-0">Terminados</h6><h3 class="mb-0" id="totalTerminados">0</h3></div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-danger" role="status"></div>
        <p class="mt-3 text-muted">Cargando JPRD...</p>
    </div>

    <div id="noResults" class="alert alert-info text-center" style="display: none;">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>No se encontraron JPRD</h5>
        <p class="mb-0">No hay JPRD registrados o no coinciden con tu búsqueda.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Listado de JPRD</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaJrd">
                    <thead class="table-light">
                        <tr>
                            <th>N° Expediente</th>
                            <th>Materia</th>
                            <th>Creador</th>
                            <th>DNI Creador</th>
                            <th>Personas</th>
                            <th width="150">Fecha Inicio</th>
                            <th width="120">Estado</th>
                            <th width="100" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="jrdTableBody">
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="spinner-border text-danger" role="status"></div>
                                <p class="mt-2 mb-0">Cargando JPRD...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<style>
.table-hover tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
.badge { font-size: 0.75rem; font-weight: 600; padding: 0.5em 0.75em; }
</style>

<script>
console.log('=== ADMIN JRD SCRIPT INICIADO ===');

function irADetalle(id) {
    window.location.href = `/jrd/${id}`;
}

function formatFecha(fecha) {
    if (!fecha) return 'No especificada';
    return new Date(fecha).toLocaleDateString('es-PE', { 
        year: 'numeric', month: 'short', day: 'numeric'
    });
}

function getEstadoBadge(estado) {
    const badges = {
        'en proceso': 'bg-warning text-dark',
        'terminado': 'bg-success',
        'observado': 'bg-info',
        'archivado': 'bg-secondary'
    };
    const badgeClass = badges[estado?.toLowerCase()] || 'bg-secondary';
    return `<span class="badge ${badgeClass}">${(estado || 'iniciado').toUpperCase()}</span>`;
}

function actualizarEstadisticas(data) {
    document.getElementById('totalJrd').textContent       = data.length;
    document.getElementById('totalProceso').textContent   = data.filter(j => j.estado?.toLowerCase() === 'en proceso').length;
    document.getElementById('totalObservados').textContent = data.filter(j => j.estado?.toLowerCase() === 'observado').length;
    document.getElementById('totalTerminados').textContent = data.filter(j => j.estado?.toLowerCase() === 'terminado').length;
}

function renderJrd(data) {
    const tbody = document.getElementById('jrdTableBody');
    
    if (!data || data.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay JPRD registrados</td></tr>';
        actualizarEstadisticas([]);
        return;
    }
    
    document.getElementById('noResults').style.display = 'none';
    actualizarEstadisticas(data);
    
    tbody.innerHTML = data.map(jrd => {
        const personasList = jrd.personas && Array.isArray(jrd.personas) ? jrd.personas : [];
        const dnis = personasList.length > 0 
            ? personasList.map(p => `${p.dni || 'N/A'} (${p.tipo || 'N/A'})`).join('<br>')
            : '<span class="text-muted">Sin personas</span>';
        
        const numeroExpediente = jrd.numero_expediente 
            ? `<span class="badge bg-dark">${jrd.numero_expediente}</span>`
            : '<span class="text-muted">Sin expediente</span>';
        
        return `
            <tr style="cursor: pointer;" onclick="irADetalle(${jrd.id_jrd})">
                <td>${numeroExpediente}</td>
                <td>
                    <strong>${jrd.nombre_materia || 'Sin materia'}</strong><br>
                    <small class="text-muted">${(jrd.pretenciones || '').substring(0, 50)}${(jrd.pretenciones || '').length > 50 ? '...' : ''}</small>
                </td>
                <td>${jrd.creador_nombre || 'N/A'}</td>
                <td><span class="badge bg-secondary">${jrd.creador_dni || 'N/A'}</span></td>
                <td><small>${dnis}</small></td>
                <td><small>${formatFecha(jrd.fecha_inicio)}</small></td>
                <td>${getEstadoBadge(jrd.estado)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger" 
                            onclick="event.stopPropagation(); irADetalle(${jrd.id_jrd})" 
                            title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function cargarJrd(dni = '') {
    const loading = document.getElementById('loading');
    const noResults = document.getElementById('noResults');
    const tbody = document.getElementById('jrdTableBody');
    
    if (loading) loading.style.display = 'block';
    if (noResults) noResults.style.display = 'none';
    if (tbody) tbody.innerHTML = '<tr><td colspan="9" class="text-center py-5"><div class="spinner-border text-danger" role="status"></div><p class="mt-2 mb-0">Cargando JPRD...</p></td></tr>';
    
    let url = '{{ route("admin.jrd.obtener") }}';
    if (dni) url += `?dni=${encodeURIComponent(dni)}`;
    
    console.log('Fetch URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Status:', response.status);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (loading) loading.style.display = 'none';
            
            if (data.success && data.jrd && data.jrd.length > 0) {
                renderJrd(data.jrd);
            } else {
                if (noResults) noResults.style.display = 'block';
                if (tbody) tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay JPRD registrados</td></tr>';
                actualizarEstadisticas([]);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loading) loading.style.display = 'none';
            if (tbody) tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger py-4">Error: ${error.message}</td></tr>`;
            actualizarEstadisticas([]);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM listo - cargando JRD');
    cargarJrd();
    
    document.getElementById('btnBuscar')?.addEventListener('click', function() {
        cargarJrd(document.getElementById('searchDni')?.value.trim() || '');
    });
    
    document.getElementById('searchDni')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') cargarJrd(this.value.trim());
    });
});
</script>

@endsection