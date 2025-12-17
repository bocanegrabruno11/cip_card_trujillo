@extends('mesa-partes.app')

@section('title', 'Control de JRD')
@section('page-title', 'Mis JRD')

@section('content')

<div class="container-fluid">
    
    <!-- Header con búsqueda -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Control de JRD</h3>
            <p class="text-muted">Gestiona y visualiza todos tus procesos de JRD</p>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       id="searchJrd" 
                       placeholder="Buscar por materia, descripción o ID...">
            </div>
        </div>
    </div>

    <!-- Spinner de carga -->
    <div id="loading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando JRD...</p>
    </div>

    <!-- Mensaje sin resultados -->
    <div id="noResults" class="alert alert-info text-center" style="display: none;">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>No se encontraron JRD</h5>
        <p class="mb-0">No tienes JRD registrados o no coinciden con tu búsqueda.</p>
    </div>

    <!-- Lista de JRD -->
    <div id="jrdList" class="accordion">
        <!-- Los JRD se cargarán dinámicamente aquí -->
    </div>

</div>

@endsection

@push('scripts')
<script>
let jrdList = [];

// Función para formatear fechas
function formatFecha(fecha) {
    if (!fecha) return 'No especificada';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-PE', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Función para obtener badge de estado
function getEstadoBadge(estado) {
    const badges = {
        'validando': 'bg-warning text-dark',
        'iniciado': 'bg-info',
        'en proceso': 'bg-primary',  // Con espacio como en la BD
        'terminado': 'bg-success',
        'rechazado': 'bg-danger'
    };
    return badges[estado] || 'bg-secondary';
}

// Función para verificar si una URL es válida
function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

// Función para renderizar JRD
function renderJrd(data) {
    const container = document.getElementById('jrdList');
    
    if (!data || data.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        container.innerHTML = '';
        return;
    }
    
    document.getElementById('noResults').style.display = 'none';
    
    container.innerHTML = data.map((jrd, index) => {
        // Badge según el rol del usuario en el JRD
        let rolBadge = '';
        if (jrd.es_creador) {
            rolBadge = '<span class="badge bg-info ms-2"><i class="fas fa-user-tie me-1"></i>Creador</span>';
        } else if (jrd.rol_usuario === 'Solicitante') {
            rolBadge = '<span class="badge bg-success ms-2"><i class="fas fa-user-check me-1"></i>Solicitante</span>';
        } else if (jrd.rol_usuario === 'Demandado' || jrd.rol_usuario === 'Contraparte' || jrd.rol_usuario === 'Tercero') {
            rolBadge = `<span class="badge bg-warning text-dark ms-2"><i class="fas fa-user-shield me-1"></i>${jrd.rol_usuario}</span>`;
        }
        
        return `
        <div class="card mb-3 shadow-sm jrd-card" data-materia="${jrd.nombre_materia.toLowerCase()}" data-id="${jrd.id_jrd}">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1">
                            <i class="fas fa-gavel text-danger me-2"></i>
                            ${jrd.nombre_materia}
                            ${rolBadge}
                        </h5>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Iniciado: ${formatFecha(jrd.fecha_inicio)}
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge ${getEstadoBadge(jrd.estado)} px-3 py-2">
                            ${jrd.estado.toUpperCase()}
                        </span>
                        <button class="btn btn-sm btn-outline-secondary ms-2" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse${jrd.id_jrd}">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="collapse${jrd.id_jrd}" class="collapse" data-bs-parent="#jrdList">
                <div class="card-body">
                    
                    <!-- Información General -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Información General
                            </h6>
                            <p><strong>Descripción:</strong> ${jrd.descripcion}</p>
                            <p><strong>ID JRD:</strong> #${jrd.id_jrd}</p>
                            <p><strong>Tu rol:</strong> <span class="badge ${jrd.es_creador ? 'bg-info' : (jrd.rol_usuario === 'Solicitante' ? 'bg-success' : 'bg-warning text-dark')}">${jrd.rol_usuario}</span></p>
                            ${jrd.fecha_finalizacion ? `<p><strong>Finalizado:</strong> ${formatFecha(jrd.fecha_finalizacion)}</p>` : ''}
                        </div>
                    </div>

                    <!-- Personas Involucradas -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-users me-2"></i>Personas Involucradas
                            </h6>
                            <div class="row">
                                ${jrd.personas && jrd.personas.length > 0 ? jrd.personas.map(persona => {
                                    let badgeClass = '';
                                    if (persona.tipo === 'Solicitante') badgeClass = 'bg-success';
                                    else if (persona.tipo === 'Demandado') badgeClass = 'bg-warning text-dark';
                                    else if (persona.tipo === 'Contraparte') badgeClass = 'bg-info';
                                    else badgeClass = 'bg-secondary';
                                    
                                    return `
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 bg-light rounded">
                                            <span class="badge ${badgeClass} me-2">
                                                ${persona.tipo}
                                            </span>
                                            <strong>DNI:</strong> ${persona.dni}
                                        </div>
                                    </div>
                                    `;
                                }).join('') : '<p class="text-muted">No hay personas registradas</p>'}
                            </div>
                        </div>
                    </div>

                    <!-- Procesos -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-tasks me-2"></i>Procesos (${jrd.procesos ? jrd.procesos.length : 0})
                            </h6>
                            ${jrd.procesos && jrd.procesos.length > 0 ? `
                                <div class="list-group">
                                    ${jrd.procesos.map(proceso => `
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-file-alt text-primary me-2"></i>
                                                        ${proceso.nombre}
                                                    </h6>
                                                    <p class="mb-2 text-muted small">${proceso.descripcion}</p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        ${formatFecha(proceso.fecha)}
                                                    </small>
                                                </div>
                                                <span class="badge ${proceso.estado ? getEstadoBadge(proceso.estado) : 'bg-secondary'} ms-3">
                                                    ${proceso.estado || 'Sin estado'}
                                                </span>
                                            </div>
                                            
                                            <!-- Documentos del proceso -->
                                            ${proceso.documentos && proceso.documentos.length > 0 ? `
                                                <div class="mt-3 pt-3 border-top">
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="fas fa-paperclip me-1"></i>
                                                        Documentos adjuntos:
                                                    </small>
                                                    ${proceso.documentos.map(doc => {
                                                        // Determinar icono según tipo de documento
                                                        let icon = 'fa-download';
                                                        if (doc.tipo_documento === 'pdf') icon = 'fa-file-pdf';
                                                        else if (doc.tipo_documento === 'imagen') icon = 'fa-file-image';
                                                        else if (doc.ruta_archivo.includes('drive.google.com')) icon = 'fa-google-drive';
                                                        
                                                        // Verificar si la URL es válida
                                                        const urlValida = isValidUrl(doc.ruta_archivo) || doc.ruta_archivo.startsWith('/');
                                                        
                                                        // Abrir siempre en nueva pestaña con target="_blank" y rel="noopener noreferrer"
                                                        return `
                                                        <a href="${doc.ruta_archivo}" 
                                                           target="_blank"
                                                           rel="noopener noreferrer"
                                                           class="btn btn-sm btn-outline-secondary me-2 mb-1"
                                                           ${!urlValida ? 'onclick="event.preventDefault();" style="cursor: not-allowed; opacity: 0.6;"' : ''}>
                                                            <i class="fas ${icon} me-1"></i>
                                                            ${doc.nombre_original}
                                                        </a>
                                                        `;
                                                    }).join('')}
                                                </div>
                                            ` : ''}
                                        </div>
                                    `).join('')}
                                </div>
                            ` : '<p class="text-muted">No hay procesos registrados</p>'}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    `;
    }).join('');
}

// Función de búsqueda
function filterJrd() {
    const searchTerm = document.getElementById('searchJrd').value.toLowerCase();
    const cards = document.querySelectorAll('.jrd-card');
    
    let visibleCount = 0;
    
    cards.forEach(card => {
        const materia = card.getAttribute('data-materia');
        const id = card.getAttribute('data-id');
        const cardText = card.textContent.toLowerCase();
        
        if (materia.includes(searchTerm) || 
            id.includes(searchTerm) || 
            cardText.includes(searchTerm)) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    if (visibleCount === 0 && searchTerm !== '') {
        document.getElementById('noResults').style.display = 'block';
    } else {
        document.getElementById('noResults').style.display = 'none';
    }
}

// Cargar JRD al iniciar
document.addEventListener('DOMContentLoaded', function() {
    const loading = document.getElementById('loading');
    loading.style.display = 'block';
    
    fetch('{{ route("jrd.obtener") }}')
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success) {
                jrdList = data.jrd_list || data.jrd; // Ajusta según la respuesta de tu API
                renderJrd(jrdList);
            } else {
                document.getElementById('noResults').style.display = 'block';
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            console.error('Error:', error);
            document.getElementById('noResults').style.display = 'block';
        });
    
    // Event listener para búsqueda
    document.getElementById('searchJrd').addEventListener('input', filterJrd);
});
</script>
@endpush

@push('styles')
<style>
.jrd-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.jrd-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.card-header {
    border-bottom: 2px solid #f8f9fa;
}

.list-group-item {
    border-left: 4px solid #AD2B2E;
    margin-bottom: 10px;
    border-radius: 5px;
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.collapse {
    transition: all 0.3s ease-in-out;
}

.btn-outline-secondary:hover {
    background-color: #AD2B2E;
    border-color: #AD2B2E;
    color: white;
}

/* Animación para el icono del collapse */
.btn[data-bs-toggle="collapse"]:not(.collapsed) i {
    transform: rotate(180deg);
    transition: transform 0.3s ease;
}

.btn[data-bs-toggle="collapse"] i {
    transition: transform 0.3s ease;
}

/* Estilo para enlaces deshabilitados */
a[style*="cursor: not-allowed"] {
    pointer-events: none;
}
</style>
@endpush