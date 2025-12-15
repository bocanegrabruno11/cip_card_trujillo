@extends('mesa-partes.app')

@section('title', 'Control de Arbitrajes')
@section('page-title', 'Mis Arbitrajes')

@section('content')

<div class="container-fluid">
    
    <!-- Header con búsqueda -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Control de Arbitrajes</h3>
            <p class="text-muted">Gestiona y visualiza todos tus procesos de arbitraje</p>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       id="searchArbitraje" 
                       placeholder="Buscar por materia, descripción o ID...">
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
        <p class="mb-0">No tienes arbitrajes registrados o no coinciden con tu búsqueda.</p>
    </div>

    <!-- Lista de Arbitrajes -->
    <div id="arbitrajesList" class="accordion">
        <!-- Los arbitrajes se cargarán dinámicamente aquí -->
    </div>

</div>

@endsection

@push('scripts')
<script>
let arbitrajes = [];

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
        'terminado': 'bg-success',   // Cambiado de 'finalizado' a 'terminado'
        'rechazado': 'bg-danger'
    };
    return badges[estado] || 'bg-secondary';
}

// Función para renderizar arbitrajes
function renderArbitrajes(data) {
    const container = document.getElementById('arbitrajesList');
    
    if (!data || data.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        container.innerHTML = '';
        return;
    }
    
    document.getElementById('noResults').style.display = 'none';
    
    container.innerHTML = data.map((arb, index) => {
        // Badge según el rol del usuario en el arbitraje
        let rolBadge = '';
        if (arb.es_creador) {
            rolBadge = '<span class="badge bg-info ms-2"><i class="fas fa-user-tie me-1"></i>Creador</span>';
        } else if (arb.rol_usuario === 'Demandante') {
            rolBadge = '<span class="badge bg-success ms-2"><i class="fas fa-user-check me-1"></i>Demandante</span>';
        } else if (arb.rol_usuario === 'Demandado') {
            rolBadge = '<span class="badge bg-warning text-dark ms-2"><i class="fas fa-user-shield me-1"></i>Demandado</span>';
        }
        
        return `
        <div class="card mb-3 shadow-sm arbitraje-card" data-materia="${arb.nombre_materia.toLowerCase()}" data-id="${arb.id_arbitraje}">
            <div class="card-header bg-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-1">
                            <i class="fas fa-scale-balanced text-danger me-2"></i>
                            ${arb.nombre_materia}
                            ${rolBadge}
                        </h5>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Iniciado: ${formatFecha(arb.fecha_inicio)}
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge ${getEstadoBadge(arb.estado)} px-3 py-2">
                            ${arb.estado.toUpperCase()}
                        </span>
                        <button class="btn btn-sm btn-outline-secondary ms-2" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse${arb.id_arbitraje}">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="collapse${arb.id_arbitraje}" class="collapse" data-bs-parent="#arbitrajesList">
                <div class="card-body">
                    
                    <!-- Información General -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i>Información General
                            </h6>
                            <p><strong>Descripción:</strong> ${arb.descripcion}</p>
                            <p><strong>ID Arbitraje:</strong> #${arb.id_arbitraje}</p>
                            <p><strong>Tu rol:</strong> <span class="badge ${arb.es_creador ? 'bg-info' : 'bg-success'}">${arb.rol_usuario}</span></p>
                            ${arb.fecha_finalizacion ? `<p><strong>Finalizado:</strong> ${formatFecha(arb.fecha_finalizacion)}</p>` : ''}
                        </div>
                    </div>

                    <!-- Personas Involucradas -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-users me-2"></i>Personas Involucradas
                            </h6>
                            <div class="row">
                                ${arb.personas && arb.personas.length > 0 ? arb.personas.map(persona => `
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 bg-light rounded">
                                            <span class="badge ${persona.tipo === 'Demandante' ? 'bg-success' : 'bg-warning text-dark'} me-2">
                                                ${persona.tipo}
                                            </span>
                                            <strong>DNI:</strong> ${persona.dni}
                                        </div>
                                    </div>
                                `).join('') : '<p class="text-muted">No hay personas registradas</p>'}
                            </div>
                        </div>
                    </div>

                    <!-- Procesos -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-tasks me-2"></i>Procesos (${arb.procesos ? arb.procesos.length : 0})
                            </h6>
                            ${arb.procesos && arb.procesos.length > 0 ? `
                                <div class="list-group">
                                    ${arb.procesos.map(proceso => `
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
                                                <span class="badge ${getEstadoBadge(proceso.estado)} ms-3">
                                                    ${proceso.estado}
                                                </span>
                                            </div>
                                            
                                            <!-- Documentos del proceso -->
                                            ${proceso.documentos && proceso.documentos.length > 0 ? `
                                                <div class="mt-3 pt-3 border-top">
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="fas fa-paperclip me-1"></i>
                                                        Documentos adjuntos:
                                                    </small>
                                                    ${proceso.documentos.map(doc => `
                                                        <a href="${doc.ruta_archivo}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-secondary me-2 mb-1">
                                                            <i class="fas fa-download me-1"></i>
                                                            ${doc.nombre_original}
                                                        </a>
                                                    `).join('')}
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
function filterArbitrajes() {
    const searchTerm = document.getElementById('searchArbitraje').value.toLowerCase();
    const cards = document.querySelectorAll('.arbitraje-card');
    
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

// Cargar arbitrajes al iniciar
document.addEventListener('DOMContentLoaded', function() {
    const loading = document.getElementById('loading');
    loading.style.display = 'block';
    
    fetch('{{ route("arbitrajes.obtener") }}')
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success) {
                arbitrajes = data.arbitrajes;
                renderArbitrajes(arbitrajes);
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
    document.getElementById('searchArbitraje').addEventListener('input', filterArbitrajes);
});
</script>
@endpush

@push('styles')
<style>
.arbitraje-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.arbitraje-card:hover {
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
</style>
@endpush