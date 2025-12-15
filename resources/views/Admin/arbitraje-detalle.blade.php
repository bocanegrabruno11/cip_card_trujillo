@extends('Admin.app')

@section('title', 'Detalle de Arbitraje #' . $arbitraje->id_arbitraje)
@section('page-title', 'Detalle de Arbitraje')

@section('content')

<div class="container-fluid">
    
    <!-- Botón de regreso -->
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('admin.arbitrajes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al listado
            </a>
        </div>
    </div>

    <!-- Información principal del arbitraje -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0">
                        <i class="fas fa-scale-balanced me-2"></i>
                        {{ $arbitraje->nombre_materia }}
                    </h4>
                    <small>ID: #{{ $arbitraje->id_arbitraje }}</small>
                </div>
                <div class="col-md-4 text-end">
                    @php
                        $estadoClass = match(strtolower($arbitraje->estado)) {
                            'validando' => 'bg-warning text-dark',
                            'iniciado' => 'bg-info',
                            'en proceso' => 'bg-primary',
                            'terminado' => 'bg-success',
                            'rechazado' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $estadoClass }} px-3 py-2 fs-6">
                        {{ strtoupper($arbitraje->estado) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Descripción:</th>
                            <td>{{ $arbitraje->descripcion }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Inicio:</th>
                            <td>
                                <i class="fas fa-calendar me-1"></i>
                                {{ $arbitraje->fecha_inicio ? $arbitraje->fecha_inicio->format('d/m/Y H:i') : 'No especificada' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Finalización:</th>
                            <td>
                                @if($arbitraje->fecha_finalizacion)
                                    <i class="fas fa-calendar-check me-1"></i>
                                    {{ $arbitraje->fecha_finalizacion->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">En proceso</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-user-tie me-2"></i>Creador del Arbitraje
                    </h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Nombre:</th>
                            <td>{{ $arbitraje->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $arbitraje->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>DNI:</th>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $arbitraje->user->persona->dni ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Personas Involucradas -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-users text-danger me-2"></i>Personas Involucradas
            </h5>
        </div>
        <div class="card-body">
            @if($arbitraje->personas && $arbitraje->personas->count() > 0)
                <div class="row">
                    @foreach($arbitraje->personas as $persona)
                        <div class="col-md-6 mb-3">
                            <div class="card border-start border-4 {{ $persona->tipo === 'Demandante' ? 'border-success' : 'border-warning' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge {{ $persona->tipo === 'Demandante' ? 'bg-success' : 'bg-warning text-dark' }} mb-2">
                                                {{ $persona->tipo }}
                                            </span>
                                            <h6 class="mb-0">DNI: {{ $persona->dni }}</h6>
                                        </div>
                                        <i class="fas fa-user fa-2x text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-3">
                    <i class="fas fa-info-circle me-2"></i>No hay personas registradas en este arbitraje
                </p>
            @endif
        </div>
    </div>

    <!-- Procesos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-tasks text-danger me-2"></i>
                Procesos del Arbitraje ({{ $arbitraje->procesos->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if($arbitraje->procesos && $arbitraje->procesos->count() > 0)
                <div class="accordion" id="accordionProcesos">
                    @foreach($arbitraje->procesos as $index => $proceso)
                        <div class="accordion-item mb-3 border">
                            <h2 class="accordion-header" id="heading{{ $proceso->id_proceso_arbitraje }}">
                                <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $proceso->id_proceso_arbitraje }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <strong>{{ $proceso->nombre }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $proceso->fecha ? $proceso->fecha->format('d/m/Y H:i') : 'Sin fecha' }}
                                            </small>
                                        </div>
                                        @php
                                            $estadoProcesoClass = match(strtolower($proceso->estado)) {
                                                'iniciado' => 'bg-info',
                                                'en progreso' => 'bg-primary',
                                                'completado' => 'bg-success',
                                                'rechazado' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $estadoProcesoClass }}">
                                            {{ strtoupper($proceso->estado) }}
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $proceso->id_proceso_arbitraje }}" 
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                 aria-labelledby="heading{{ $proceso->id_proceso_arbitraje }}"
                                 data-bs-parent="#accordionProcesos">
                                <div class="accordion-body">
                                    <h6 class="mb-3">
                                        <i class="fas fa-align-left me-2"></i>Descripción
                                    </h6>
                                    <p class="text-muted">{{ $proceso->descripcion }}</p>

                                    <!-- Documentos -->
                                    @if($proceso->documentos && $proceso->documentos->count() > 0)
                                        <hr>
                                        <h6 class="mb-3">
                                            <i class="fas fa-paperclip me-2"></i>
                                            Documentos Adjuntos ({{ $proceso->documentos->count() }})
                                        </h6>
                                        <div class="list-group">
                                            @foreach($proceso->documentos as $documento)
                                                <div class="list-group-item">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-1 text-center">
                                                            @if($documento->tipo_documento === 'pdf')
                                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                            @elseif($documento->tipo_documento === 'imagen')
                                                                <i class="fas fa-file-image fa-2x text-primary"></i>
                                                            @else
                                                                <i class="fas fa-file fa-2x text-secondary"></i>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-7">
                                                            <strong>{{ $documento->nombre_original }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                Subido: {{ $documento->fecha_subida ? $documento->fecha_subida->format('d/m/Y H:i') : 'N/A' }}
                                                            </small>
                                                            <br>
                                                            <small>
                                                                <span class="badge bg-secondary">
                                                                    {{ strtoupper($documento->tipo_documento) }}
                                                                </span>
                                                            </small>
                                                        </div>
                                                        <div class="col-md-4 text-end">
                                                            <!-- Botón de ojo para abrir modal -->
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger me-2"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#modalDocumento"
                                                                    data-documento-id="{{ $documento->id_documento }}"
                                                                    data-documento-nombre="{{ $documento->nombre_original }}"
                                                                    data-documento-tipo="{{ $documento->tipo_documento }}"
                                                                    data-documento-ruta="{{ $documento->ruta_archivo }}">
                                                                <i class="fas fa-eye me-1"></i>Ver
                                                            </button>
                                                            <a href="{{ $documento->ruta_archivo }}" 
                                                               download 
                                                               class="btn btn-sm btn-danger">
                                                                <i class="fas fa-download me-1"></i>Descargar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <hr>
                                        <p class="text-muted text-center py-2">
                                            <i class="fas fa-info-circle me-2"></i>No hay documentos adjuntos en este proceso
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-3">
                    <i class="fas fa-info-circle me-2"></i>No hay procesos registrados en este arbitraje
                </p>
            @endif
        </div>
    </div>

</div>

<!-- Modal para ver documento -->
<div class="modal fade" id="modalDocumento" tabindex="-1" aria-labelledby="modalDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalDocumentoLabel">
                    <i class="fas fa-file me-2"></i>Vista Previa del Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Panel de vista previa principal -->
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <!-- Vista previa de imagen con controles mejorados -->
                                <div id="imagenPreview" class="d-none">
                                    <div class="image-viewer-wrapper">
                                        <div class="image-container" id="imageContainer">
                                            <div class="image-wrapper">
                                                <img id="imagenDocumento" src="" alt="Documento" class="zoomable-image">
                                            </div>
                                        </div>
                                        <div class="image-controls d-flex justify-content-center align-items-center mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="zoomIn">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="zoomOut">
                                                <i class="fas fa-search-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="rotateLeft">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="rotateRight">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="resetImage">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <span class="badge bg-info mx-2" id="zoomLevel">100%</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Vista previa de PDF -->
                                <div id="pdfPreview" class="d-none">
                                    <div class="pdf-viewer-wrapper">
                                        <iframe id="pdfIframe" src="" width="100%" height="500px" class="border rounded"></iframe>
                                        <div class="pdf-controls d-flex justify-content-between align-items-center mt-2">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="prevPage">
                                                    <i class="fas fa-chevron-left"></i> Anterior
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="nextPage">
                                                    Siguiente <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </div>
                                            <span class="badge bg-danger" id="pageInfo">Página 1</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Vista para otros tipos de documentos -->
                                <div id="otrosPreview" class="d-none">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                        <div class="file-icon mb-3">
                                            <i class="fas fa-file fa-5x text-muted"></i>
                                        </div>
                                        <div class="text-center">
                                            <h5 id="nombreDocumento" class="mb-2">Documento no visualizable</h5>
                                            <p class="text-muted">
                                                Este tipo de documento no puede ser visualizado directamente en el navegador.
                                            </p>
                                            <div class="mt-3">
                                                <a href="#" id="descargarDocumento" class="btn btn-danger">
                                                    <i class="fas fa-download me-2"></i>Descargar para ver
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Cargando -->
                                <div id="loadingPreview" class="d-none">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                        <div class="spinner-border text-danger" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-3 text-muted">Cargando documento...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panel de información del documento -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Información del Documento
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="document-info">
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted small mb-1">Nombre del archivo</label>
                                        <div class="p-2 bg-light rounded">
                                            <strong id="infoNombre"></strong>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted small mb-1">Tipo de documento</label>
                                        <div class="p-2 bg-light rounded">
                                            <span id="infoTipo" class="badge bg-secondary"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted small mb-1">Fecha de subida</label>
                                        <div class="p-2 bg-light rounded">
                                            <span id="infoFecha"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6 class="border-bottom pb-2">Acciones</h6>
                                        <div class="d-grid gap-2">
                                            <a href="#" id="btnDescargarModal" class="btn btn-danger">
                                                <i class="fas fa-download me-2"></i>Descargar documento
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-success w-100" id="btnAceptar">
                            <i class="fas fa-check-circle me-2"></i>Aceptar Documento
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger w-100" id="btnRechazar">
                            <i class="fas fa-times-circle me-2"></i>Rechazar Documento
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar Vista
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalDocumento = document.getElementById('modalDocumento');
    const modal = new bootstrap.Modal(modalDocumento);
    
    // Elementos del modal
    const imagenPreview = document.getElementById('imagenPreview');
    const pdfPreview = document.getElementById('pdfPreview');
    const otrosPreview = document.getElementById('otrosPreview');
    const loadingPreview = document.getElementById('loadingPreview');
    const imagenDocumento = document.getElementById('imagenDocumento');
    const imageContainer = document.getElementById('imageContainer');
    const pdfIframe = document.getElementById('pdfIframe');
    
    // Controles de imagen
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const rotateLeftBtn = document.getElementById('rotateLeft');
    const rotateRightBtn = document.getElementById('rotateRight');
    const resetImageBtn = document.getElementById('resetImage');
    const zoomLevel = document.getElementById('zoomLevel');
    
    // Controles de PDF
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');
    
    // Información del documento
    const infoNombre = document.getElementById('infoNombre');
    const infoTipo = document.getElementById('infoTipo');
    const infoFecha = document.getElementById('infoFecha');
    const descargarDocumento = document.getElementById('descargarDocumento');
    const btnDescargarModal = document.getElementById('btnDescargarModal');
    
    // Botones de acción
    const btnAceptar = document.getElementById('btnAceptar');
    const btnRechazar = document.getElementById('btnRechazar');
    
    // Variables de estado para imágenes
    let documentoActual = null;
    let currentZoom = 1;
    let currentRotation = 0;
    let currentPage = 1;
    let isDragging = false;
    let startX, startY, scrollLeft, scrollTop;
    
    // Cuando se abre el modal
    modalDocumento.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        documentoActual = {
            id: button.getAttribute('data-documento-id'),
            nombre: button.getAttribute('data-documento-nombre'),
            tipo: button.getAttribute('data-documento-tipo'),
            ruta: button.getAttribute('data-documento-ruta'),
            fecha: '15/12/2025 10:30'
        };
        
        // Mostrar loading
        showLoading();
        
        // Cargar información del documento
        loadDocumentInfo(documentoActual);
        
        // Mostrar la vista previa adecuada después de un pequeño delay
        setTimeout(() => {
            mostrarVistaPrevia(documentoActual);
        }, 500);
    });
    
    // Función para mostrar loading
    function showLoading() {
        imagenPreview.classList.add('d-none');
        pdfPreview.classList.add('d-none');
        otrosPreview.classList.add('d-none');
        loadingPreview.classList.remove('d-none');
    }
    
    // Función para cargar información del documento
    function loadDocumentInfo(doc) {
        infoNombre.textContent = doc.nombre;
        infoTipo.textContent = doc.tipo.toUpperCase();
        infoFecha.textContent = doc.fecha;
        
        // Configurar enlaces de descarga
        if (descargarDocumento) {
            descargarDocumento.href = doc.ruta;
        }
        if (btnDescargarModal) {
            btnDescargarModal.href = doc.ruta;
        }
    }
    
    // Función para mostrar la vista previa adecuada
    function mostrarVistaPrevia(doc) {
        loadingPreview.classList.add('d-none');
        
        // Extraer extensión del archivo
        const extension = doc.nombre.split('.').pop().toLowerCase();
        const esImagen = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension);
        const esPDF = extension === 'pdf';
        
        if (esImagen) {
            // Configurar visor de imagen
            imagenPreview.classList.remove('d-none');
            pdfPreview.classList.add('d-none');
            otrosPreview.classList.add('d-none');
            
            // Cargar imagen
            imagenDocumento.src = doc.ruta;
            
            // Resetear controles
            resetImageControls();
            
            // Configurar arrastre para desplazarse
            setupImageDrag();
            
        } else if (esPDF) {
            // Configurar visor de PDF
            imagenPreview.classList.add('d-none');
            pdfPreview.classList.remove('d-none');
            otrosPreview.classList.add('d-none');
            
            // Cargar PDF
            pdfIframe.src = doc.ruta;
            
        } else {
            // Documento no visualizable
            imagenPreview.classList.add('d-none');
            pdfPreview.classList.add('d-none');
            otrosPreview.classList.remove('d-none');
        }
    }
    
    // Configurar arrastre para desplazarse en la imagen
    function setupImageDrag() {
        const imageWrapper = imageContainer.querySelector('.image-wrapper');
        
        // Solo habilitar arrastre si hay zoom
        imageWrapper.addEventListener('mousedown', startDrag);
        imageWrapper.addEventListener('touchstart', startDragTouch);
        
        function startDrag(e) {
            if (currentZoom > 1) {
                isDragging = true;
                startX = e.pageX - imageWrapper.offsetLeft;
                startY = e.pageY - imageWrapper.offsetTop;
                scrollLeft = imageWrapper.scrollLeft;
                scrollTop = imageWrapper.scrollTop;
                
                document.addEventListener('mousemove', drag);
                document.addEventListener('mouseup', stopDrag);
                e.preventDefault();
            }
        }
        
        function startDragTouch(e) {
            if (currentZoom > 1 && e.touches.length === 1) {
                isDragging = true;
                startX = e.touches[0].pageX - imageWrapper.offsetLeft;
                startY = e.touches[0].pageY - imageWrapper.offsetTop;
                scrollLeft = imageWrapper.scrollLeft;
                scrollTop = imageWrapper.scrollTop;
                
                document.addEventListener('touchmove', dragTouch);
                document.addEventListener('touchend', stopDrag);
                e.preventDefault();
            }
        }
        
        function drag(e) {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - imageWrapper.offsetLeft;
            const y = e.pageY - imageWrapper.offsetTop;
            const walkX = (x - startX) * 2;
            const walkY = (y - startY) * 2;
            imageWrapper.scrollLeft = scrollLeft - walkX;
            imageWrapper.scrollTop = scrollTop - walkY;
        }
        
        function dragTouch(e) {
            if (!isDragging || e.touches.length !== 1) return;
            e.preventDefault();
            const x = e.touches[0].pageX - imageWrapper.offsetLeft;
            const y = e.touches[0].pageY - imageWrapper.offsetTop;
            const walkX = (x - startX) * 2;
            const walkY = (y - startY) * 2;
            imageWrapper.scrollLeft = scrollLeft - walkX;
            imageWrapper.scrollTop = scrollTop - walkY;
        }
        
        function stopDrag() {
            isDragging = false;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('touchmove', dragTouch);
            document.removeEventListener('mouseup', stopDrag);
            document.removeEventListener('touchend', stopDrag);
        }
    }
    
    // Funciones para controles de imagen
    function resetImageControls() {
        currentZoom = 1;
        currentRotation = 0;
        imagenDocumento.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
        zoomLevel.textContent = `${Math.round(currentZoom * 100)}%`;
        
        // Restablecer el desplazamiento del contenedor
        const imageWrapper = imageContainer.querySelector('.image-wrapper');
        if (imageWrapper) {
            imageWrapper.scrollLeft = imageWrapper.scrollWidth / 2 - imageWrapper.clientWidth / 2;
            imageWrapper.scrollTop = imageWrapper.scrollHeight / 2 - imageWrapper.clientHeight / 2;
        }
    }
    
    // Event listeners para controles de imagen
    zoomInBtn.addEventListener('click', function() {
        if (currentZoom < 3) {
            currentZoom += 0.1;
            updateImageZoom();
        }
    });
    
    zoomOutBtn.addEventListener('click', function() {
        if (currentZoom > 0.3) {
            currentZoom -= 0.1;
            updateImageZoom();
        }
    });
    
    rotateLeftBtn.addEventListener('click', function() {
        currentRotation -= 90;
        imagenDocumento.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
    });
    
    rotateRightBtn.addEventListener('click', function() {
        currentRotation += 90;
        imagenDocumento.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
    });
    
    resetImageBtn.addEventListener('click', resetImageControls);
    
    function updateImageZoom() {
        imagenDocumento.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
        zoomLevel.textContent = `${Math.round(currentZoom * 100)}%`;
        
        // Habilitar/deshabilitar el arrastre según el nivel de zoom
        const imageWrapper = imageContainer.querySelector('.image-wrapper');
        if (imageWrapper) {
            if (currentZoom > 1) {
                imageWrapper.style.cursor = 'grab';
                imageWrapper.style.overflow = 'auto';
            } else {
                imageWrapper.style.cursor = 'default';
                imageWrapper.style.overflow = 'hidden';
            }
        }
    }
    
    // Event listeners para PDF (funcionalidad básica)
    prevPageBtn.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updatePageInfo();
        }
    });
    
    nextPageBtn.addEventListener('click', function() {
        currentPage++;
        updatePageInfo();
    });
    
    function updatePageInfo() {
        pageInfo.textContent = `Página ${currentPage}`;
    }
    
    // Acciones principales
    btnAceptar.addEventListener('click', function() {
        if (documentoActual) {
            Swal.fire({
                title: '¿Aceptar documento?',
                text: `¿Estás seguro de aceptar el documento "${documentoActual.nombre}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        '¡Aceptado!',
                        'El documento ha sido aceptado correctamente.',
                        'success'
                    );
                    modal.hide();
                }
            });
        }
    });
    
    btnRechazar.addEventListener('click', function() {
        if (documentoActual) {
            Swal.fire({
                title: '¿Rechazar documento?',
                input: 'text',
                inputLabel: 'Motivo del rechazo',
                inputPlaceholder: 'Ingresa el motivo del rechazo...',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire(
                        '¡Rechazado!',
                        `El documento ha sido rechazado. Motivo: ${result.value}`,
                        'warning'
                    );
                    modal.hide();
                }
            });
        }
    });
    
    // Limpiar cuando se cierra el modal
    modalDocumento.addEventListener('hidden.bs.modal', function() {
        imagenDocumento.src = '';
        pdfIframe.src = '';
        documentoActual = null;
        resetImageControls();
        isDragging = false;
    });
    
    // Zoom con rueda del mouse en imágenes
    imageContainer.addEventListener('wheel', function(e) {
        if (documentoActual && documentoActual.tipo === 'imagen') {
            e.preventDefault();
            if (e.deltaY < 0 && currentZoom < 3) {
                currentZoom += 0.1;
            } else if (e.deltaY > 0 && currentZoom > 0.3) {
                currentZoom -= 0.1;
            }
            updateImageZoom();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
/* Estilos principales */
.border-start {
    border-left-width: 4px !important;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #000;
}

.accordion-button:focus {
    box-shadow: none;
}

.list-group-item {
    border: 1px solid #dee2e6;
    margin-bottom: 10px;
    border-radius: 5px;
}

.table th {
    font-weight: 600;
    color: #6c757d;
}

/* Estilos para el modal */
.modal-header {
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Visor de imágenes mejorado con desplazamiento */
.image-viewer-wrapper {
    position: relative;
    background-color: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    min-height: 400px;
    display: flex;
    flex-direction: column;
}

.image-container {
    flex: 1;
    overflow: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    cursor: default;
}

.image-wrapper {
    position: relative;
    width: fit-content;
    height: fit-content;
    overflow: hidden;
    transition: all 0.3s ease;
}

.image-wrapper:active {
    cursor: grabbing;
}

.zoomable-image {
    max-width: 100%;
    max-height: 70vh;
    transition: transform 0.3s ease;
    object-fit: contain;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform-origin: center center;
}

.image-controls {
    background: rgba(255, 255, 255, 0.95);
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    bottom: 0;
    z-index: 10;
}

.image-controls .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.image-controls .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.image-controls .btn:active {
    transform: translateY(0);
}

/* Scrollbar personalizado para el contenedor de imagen */
.image-container::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

.image-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.image-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.image-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Indicador de arrastre */
.image-wrapper.draggable {
    cursor: grab;
}

.image-wrapper.dragging {
    cursor: grabbing;
}

/* Visor de PDF */
.pdf-viewer-wrapper {
    border-radius: 8px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.pdf-controls {
    background: rgba(255, 255, 255, 0.95);
    padding: 10px;
    border-radius: 8px;
}

/* Información del documento */
.document-info .info-item {
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
}

.document-info .info-item:last-child {
    border-bottom: none;
}

.file-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Responsive */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .image-controls {
        flex-wrap: wrap;
        padding: 8px;
    }
    
    .image-controls .btn {
        width: 36px;
        height: 36px;
        margin: 2px;
        font-size: 0.8rem;
    }
    
    .zoomable-image {
        max-height: 60vh;
    }
}

/* Mejoras para botones */
.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
}

.btn-outline-danger:hover {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

/* Hover effects */
.btn:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

/* Animaciones de carga */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

#loadingPreview {
    min-height: 300px;
}

/* Indicador visual cuando se puede arrastrar */
.image-container.zoom-active {
    cursor: grab;
}

.image-container.zoom-active:active {
    cursor: grabbing;
}
</style>
@endpush