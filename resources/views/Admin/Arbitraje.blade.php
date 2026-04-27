@extends('Admin.app')

@section('title', 'Gestión de Arbitrajes')
@section('page-title', 'Administración de Arbitrajes')

@section('content')

<div class="container-fluid">
    
    <!-- Header con filtros -->
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

    <!-- Estadísticas -->
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

    <!-- Spinner de carga -->
    <div id="loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando arbitrajes...</p>
    </div>

    <!-- Mensaje sin resultados -->
    <div id="noResults" class="alert alert-info text-center" style="display: none;">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>No se encontraron arbitrajes</h5>
        <p class="mb-0">No hay arbitrajes registrados o no coinciden con tu búsqueda.</p>
    </div>

    <!-- Tabla de Arbitrajes -->
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
                            <th width="80">ID</th>
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
                            <td colspan="9" class="text-center py-5">
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

<!-- Modal para subir documentos -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Subir Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="upload_arbitraje_id" name="id_arbitraje">
                    <input type="hidden" id="upload_proceso_id" name="proceso_id">
                    
                    <div class="alert alert-info mb-3">
                        <small>
                            <strong>Arbitraje ID:</strong> <span id="info_arbitraje_id"></span><br>
                            <strong>Proceso ID:</strong> <span id="info_proceso_id"></span>
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                            <option value="">Seleccione...</option>
                            <option value="archivo">📄 Subir Archivo (PDF, JPG, PNG)</option>
                            <option value="link">🔗 Enlace (Google Drive, Dropbox, etc.)</option>
                        </select>
                    </div>
                    
                    <div id="campo_archivo" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Archivo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Formatos: PDF, JPG, JPEG, PNG (Máx. 20MB)</small>
                        </div>
                    </div>
                    
                    <div id="campo_link" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Enlace <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" name="link" placeholder="https://drive.google.com/...">
                            <small class="text-muted">Enlace a documento en Drive, Dropbox, etc.</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre del Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre_documento" placeholder="Ej: Contrato firmado" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Observaciones (opcional)</label>
                        <textarea class="form-control" name="observaciones" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-upload me-2"></i>Subir Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de mensajes -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="messageModalBody"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Script principal - Inicio
console.log('=== ADMIN ARBITRAJES SCRIPT INICIADO ===');
console.log('URL actual:', window.location.href);

// Función para redirigir al detalle del arbitraje
function irADetalle(id) {
    const url = `/arbitrajes/${id}/detalle`;
    console.log('Redirigiendo a:', url);
    window.location.href = url;
}

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado completamente');
    cargarArbitrajes();
    
    const btnBuscar = document.getElementById('btnBuscar');
    const searchDni = document.getElementById('searchDni');
    
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function() {
            const dni = searchDni ? searchDni.value.trim() : '';
            console.log('Buscando por DNI:', dni);
            cargarArbitrajes(dni);
        });
    }
    
    if (searchDni) {
        searchDni.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const dni = this.value.trim();
                console.log('Enter presionado, buscando:', dni);
                cargarArbitrajes(dni);
            }
        });
    }
    
    // Configurar el cambio de tipo de documento
    const tipoDoc = document.getElementById('tipo_documento');
    if (tipoDoc) {
        tipoDoc.addEventListener('change', function() {
            const tipo = this.value;
            const campoArchivo = document.getElementById('campo_archivo');
            const campoLink = document.getElementById('campo_link');
            const archivoInput = document.querySelector('input[name="archivo"]');
            const linkInput = document.querySelector('input[name="link"]');
            
            if (tipo === 'archivo') {
                if (campoArchivo) campoArchivo.style.display = 'block';
                if (campoLink) campoLink.style.display = 'none';
                if (archivoInput) archivoInput.required = true;
                if (linkInput) linkInput.required = false;
            } else if (tipo === 'link') {
                if (campoArchivo) campoArchivo.style.display = 'none';
                if (campoLink) campoLink.style.display = 'block';
                if (archivoInput) archivoInput.required = false;
                if (linkInput) linkInput.required = true;
            } else {
                if (campoArchivo) campoArchivo.style.display = 'none';
                if (campoLink) campoLink.style.display = 'none';
                if (archivoInput) archivoInput.required = false;
                if (linkInput) linkInput.required = false;
            }
        });
    }
    
    // Configurar el formulario de subida
    const uploadForm = document.getElementById('uploadDocumentForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            subirDocumentoHandler(e);
        });
    }
});



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
        'finalizado': 'bg-secondary'
    };
    const badgeClass = badges[estado?.toLowerCase()] || 'bg-secondary';
    return `<span class="badge ${badgeClass}">${(estado || 'iniciado').toUpperCase()}</span>`;
}

function showMessage(title, message, isError = false) {
    const modalElement = document.getElementById('messageModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        document.getElementById('messageModalTitle').textContent = title;
        document.getElementById('messageModalTitle').className = `modal-title text-${isError ? 'danger' : 'success'}`;
        document.getElementById('messageModalBody').textContent = message;
        modal.show();
    } else {
        alert(message);
    }
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

function subirDocumento(arbitrajeId, procesoId) {
    document.getElementById('upload_arbitraje_id').value = arbitrajeId;
    document.getElementById('upload_proceso_id').value = procesoId;
    document.getElementById('info_arbitraje_id').textContent = arbitrajeId;
    document.getElementById('info_proceso_id').textContent = procesoId;
    
    document.getElementById('uploadDocumentForm').reset();
    document.getElementById('campo_archivo').style.display = 'none';
    document.getElementById('campo_link').style.display = 'none';
    document.getElementById('tipo_documento').value = '';
    
    new bootstrap.Modal(document.getElementById('uploadDocumentModal')).show();
}

function subirDocumentoHandler(e) {
    const tipoDocumento = document.getElementById('tipo_documento').value;
    const nombreDocumento = document.querySelector('input[name="nombre_documento"]').value.trim();
    
    if (!tipoDocumento) {
        showMessage('Error', 'Seleccione el tipo de documento', true);
        return;
    }
    if (!nombreDocumento) {
        showMessage('Error', 'Ingrese el nombre del documento', true);
        return;
    }
    
    if (tipoDocumento === 'archivo') {
        const archivo = document.querySelector('input[name="archivo"]').files[0];
        if (!archivo) {
            showMessage('Error', 'Seleccione un archivo', true);
            return;
        }
        if (archivo.size > 20 * 1024 * 1024) {
            showMessage('Error', 'El archivo no debe superar los 20MB', true);
            return;
        }
    }
    
    if (tipoDocumento === 'link') {
        const link = document.querySelector('input[name="link"]').value.trim();
        if (!link) {
            showMessage('Error', 'Ingrese el enlace del documento', true);
            return;
        }
        if (!link.startsWith('http://') && !link.startsWith('https://')) {
            showMessage('Error', 'El enlace debe comenzar con http:// o https://', true);
            return;
        }
    }
    
    const formData = new FormData();
    const arbitrajeId = document.getElementById('upload_arbitraje_id').value;
    const procesoId = document.getElementById('upload_proceso_id').value;
    
    formData.append('id_arbitraje', arbitrajeId);
    formData.append('proceso_id', procesoId);
    formData.append('tipo_documento', tipoDocumento);
    formData.append('nombre_documento', nombreDocumento);
    formData.append('observaciones', document.querySelector('textarea[name="observaciones"]').value);
    
    if (tipoDocumento === 'archivo') {
        formData.append('archivo', document.querySelector('input[name="archivo"]').files[0]);
    } else {
        formData.append('link', document.querySelector('input[name="link"]').value.trim());
    }
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subiendo...';
    submitBtn.disabled = true;
    
    fetch(`/arbitraje/${arbitrajeId}/documentos`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(async response => {
        const text = await response.text();
        try {
            const json = JSON.parse(text);
            return { response, json };
        } catch (e) {
            throw new Error(text.substring(0, 200));
        }
    })
    .then(({ response, json }) => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (response.ok && json.success) {
            showMessage('Éxito', 'Documento subido correctamente');
            bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal')).hide();
            e.target.reset();
            cargarArbitrajes();
        } else {
            showMessage('Error', json.message || 'Error al subir el documento', true);
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        showMessage('Error', 'Error de conexión: ' + error.message, true);
    });
}

// Función para obtener el badge de tipo de arbitraje
function getTipoBadge(arbitraje) {
    // Verificar diferentes formas en que podría venir el tipo
    const tipo = arbitraje.tipo_arbitraje || arbitraje.tipo || 'normal';
    
    if (tipo === 'emergencia') {
        return '<span class="badge bg-danger"><i class="fas fa-bolt me-1"></i>EMERGENCIA</span>';
    }
    return '<span class="badge bg-secondary"><i class="fas fa-gavel me-1"></i>NORMAL</span>';
}

// Función para ordenar arbitrajes (emergencia primero, luego normales)
function ordenarArbitrajes(arbitrajes) {
    return [...arbitrajes].sort((a, b) => {
        // Obtener tipos con valores por defecto
        const tipoA = a.tipo_arbitraje || a.tipo || 'normal';
        const tipoB = b.tipo_arbitraje || b.tipo || 'normal';
        
        // Primero: todos los de emergencia van arriba
        if (tipoA === 'emergencia' && tipoB !== 'emergencia') return -1;
        if (tipoA !== 'emergencia' && tipoB === 'emergencia') return 1;
        
        // Si son del mismo tipo, ordenar por fecha de inicio (más recientes primero)
        const fechaA = a.fecha_inicio ? new Date(a.fecha_inicio) : new Date(0);
        const fechaB = b.fecha_inicio ? new Date(b.fecha_inicio) : new Date(0);
        return fechaB - fechaA;
    });
}

function renderArbitrajes(data) {
    const tbody = document.getElementById('arbitrajesTableBody');
    
    if (!data || data.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay arbitrajes registrados</td></tr>';
        actualizarEstadisticas([]);
        return;
    }
    
    document.getElementById('noResults').style.display = 'none';
    actualizarEstadisticas(data);
    
    // Depuración: Ver qué datos están llegando
    console.log('Primer arbitraje:', data[0]);
    console.log('Tipos de arbitraje:', data.map(a => ({ id: a.id_arbitraje, tipo: a.tipo_arbitraje })));
    
    // Ordenar los arbitrajes: emergencia primero, luego normales
    const arbitrajesOrdenados = ordenarArbitrajes(data);
    
    tbody.innerHTML = arbitrajesOrdenados.map(arb => {
        // Determinar si es emergencia para aplicar clase especial
        const tipo = arb.tipo_arbitraje || arb.tipo || 'normal';
        const isEmergencia = tipo === 'emergencia';
        const rowClass = isEmergencia ? 'emergencia-row' : '';
        
        return `
        <tr style="cursor: pointer;" class="${rowClass}">
            <td><strong>#${arb.id_arbitraje}</strong></td>
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
                <button class="btn btn-sm btn-danger" onclick="irADetalle(${arb.id_arbitraje})" title="Ver detalles">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
    
    // Agregar evento de clic en la fila para redirigir también
    document.querySelectorAll('#arbitrajesTableBody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            // Evitar que se dispare si se hizo clic en el botón
            if (e.target.closest('.btn')) return;
            const id = this.querySelector('td:first-child').textContent.replace('#', '');
            irADetalle(id);
        });
    });
}

function cargarArbitrajes(dni = '') {
    console.log('=== Cargando arbitrajes ===');
    console.log('DNI:', dni);
    
    const loading = document.getElementById('loading');
    const noResults = document.getElementById('noResults');
    const tbody = document.getElementById('arbitrajesTableBody');
    
    if (loading) loading.style.display = 'block';
    if (noResults) noResults.style.display = 'none';
    
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-5"><div class="spinner-border text-danger" role="status"><span class="visually-hidden">Cargando...</span></div><p class="mt-2 mb-0">Cargando arbitrajes...</p></td></tr>';
    }
    
    let url = '/arbitrajes/obtener';
    if (dni) url += `?dni=${encodeURIComponent(dni)}`;
    
    console.log('Fetch URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (loading) loading.style.display = 'none';
            
            if (data.success && data.arbitrajes && data.arbitrajes.length > 0) {
                console.log('Renderizando', data.arbitrajes.length, 'arbitrajes');
                renderArbitrajes(data.arbitrajes);
            } else {
                console.log('No hay arbitrajes');
                if (noResults) noResults.style.display = 'block';
                if (tbody) tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay arbitrajes registrados</td></tr>';
                actualizarEstadisticas([]);
            }
        })
        .catch(error => {
            console.error('Error en fetch:', error);
            if (loading) loading.style.display = 'none';
            if (noResults) noResults.style.display = 'block';
            if (tbody) tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger py-4">Error al cargar los datos: ${error.message}</td></tr>`;
            actualizarEstadisticas([]);
        });
}
</script>
@endsection

@push('styles')
<style>
.table-hover tbody tr:hover { background-color: #f8f9fa; cursor: pointer; }
.card { border: none; transition: all 0.3s ease; }
.card:hover { transform: translateY(-2px); }
.badge { font-size: 0.75rem; font-weight: 600; padding: 0.5em 0.75em; }
.gap-2 { gap: 0.5rem; }

/* Estilo para filas de arbitrajes de emergencia - rojo tenue */
.emergencia-row {
    background-color: #fff5f5 !important;
    border-left: 4px solid #dc3545;
}

.emergencia-row:hover {
    background-color: #ffe8e8 !important;
}

/* Opcional: animación suave para las filas */
#arbitrajesTableBody tr {
    transition: all 0.2s ease-in-out;
}
</style>
@endpush