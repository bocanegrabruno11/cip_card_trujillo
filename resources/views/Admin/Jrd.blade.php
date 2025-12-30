@extends('Admin.app')

@section('title', 'Gestión de JRD')
@section('page-title', 'Administración de JRD')

@section('content')

<div class="container-fluid">
    
    <!-- Header con filtros -->
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
                            <h3 class="mb-0" id="totalJrd">0</h3>
                        </div>
                        <i class="fas fa-gavel fa-3x opacity-50"></i>
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
        <p class="mt-3 text-muted">Cargando JPRD...</p>
    </div>

    <!-- Mensaje sin resultados -->
    <div id="noResults" class="alert alert-info text-center" style="display: none;">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>No se encontraron JPRD</h5>
        <p class="mb-0">No hay JPRD registrados o no coinciden con tu búsqueda.</p>
    </div>

    <!-- Tabla de JRD -->
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Listado de JPRD
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaJrd">
                    <thead class="table-light">
                        <tr>
                            <th width="80">ID</th>
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
                        <!-- Se llenará dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
let jrdList = [];
// CAMBIO IMPORTANTE: Usar el nombre de ruta correcto
const detalleRoute = '{{ route("admin.jrd.detalle", ":id") }}';

// Función para formatear fechas
function formatFecha(fecha) {
    if (!fecha) return 'No especificada';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-PE', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric'
    });
}

// Función para obtener badge de estado
function getEstadoBadge(estado) {
    const badges = {
        'validando': 'bg-warning text-dark',
        'iniciado': 'bg-info',
        'en proceso': 'bg-primary',
        'terminado': 'bg-success',
        'rechazado': 'bg-danger'
    };
    const badgeClass = badges[estado.toLowerCase()] || 'bg-secondary';
    return `<span class="badge ${badgeClass}">${estado.toUpperCase()}</span>`;
}

// Función para actualizar estadísticas
function actualizarEstadisticas(data) {
    const total = data.length;
    const validando = data.filter(j => j.estado.toLowerCase() === 'validando').length;
    const proceso = data.filter(j => j.estado.toLowerCase() === 'en proceso' || j.estado.toLowerCase() === 'iniciado').length;
    const terminados = data.filter(j => j.estado.toLowerCase() === 'terminado').length;
    
    document.getElementById('totalJrd').textContent = total;
    document.getElementById('totalValidando').textContent = validando;
    document.getElementById('totalProceso').textContent = proceso;
    document.getElementById('totalTerminados').textContent = terminados;
}

// Función para renderizar tabla de JRD
function renderJrd(data) {
    const tbody = document.getElementById('jrdTableBody');
    
    if (!data || data.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        tbody.innerHTML = '';
        actualizarEstadisticas([]);
        return;
    }
    
    document.getElementById('noResults').style.display = 'none';
    actualizarEstadisticas(data);
    
    tbody.innerHTML = data.map(jrd => {
        // Obtener DNIs de las personas involucradas
        const dnis = jrd.personas && jrd.personas.length > 0 
            ? jrd.personas.map(p => `${p.dni} (${p.tipo})`).join('<br>')
            : '<span class="text-muted">Sin personas</span>';
        
        // Usar la ruta con nombre
        const detalleUrl = detalleRoute.replace(':id', jrd.id_jrd);
        
        return `
            <tr>
                <td><strong>#${jrd.id_jrd}</strong></td>
                <td>
                    <strong>${jrd.nombre_materia}</strong>
                    <br>
                    <small class="text-muted">${(jrd.descripcion || '').substring(0, 50)}${(jrd.descripcion && jrd.descripcion.length > 50) ? '...' : ''}</small>
                </td>
                <td>${jrd.creador_nombre || 'N/A'}</td>
                <td><span class="badge bg-secondary">${jrd.creador_dni || 'N/A'}</span></td>
                <td><small>${dnis}</small></td>
                <td>
                    <small>
                        <i class="fas fa-calendar me-1"></i>
                        ${formatFecha(jrd.fecha_inicio)}
                    </small>
                </td>
                <td>${getEstadoBadge(jrd.estado)}</td>
                <td class="text-center">
                    <a href="${detalleUrl}" 
                       class="btn btn-sm btn-danger"
                       title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
        `;
    }).join('');
}

// Función para cargar JRD
function cargarJrd(dni = '') {
    const loading = document.getElementById('loading');
    loading.style.display = 'block';
    
    let url = '{{ route("admin.jrd.obtener") }}';
    if (dni) {
        url += `?dni=${encodeURIComponent(dni)}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success) {
                jrdList = data.jrd || data.jrd_list || [];
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
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cargar JRD al iniciar
    cargarJrd();
    
    // Botón de búsqueda
    document.getElementById('btnBuscar').addEventListener('click', function() {
        const dni = document.getElementById('searchDni').value.trim();
        cargarJrd(dni);
    });
    
    // Búsqueda al presionar Enter
    document.getElementById('searchDni').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const dni = this.value.trim();
            cargarJrd(dni);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.card {
    border: none;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 0.5em 0.75em;
}

.opacity-50 {
    opacity: 0.5;
}

.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #495057;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endpush