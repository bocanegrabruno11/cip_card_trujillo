@extends('Admin.app')

@section('title', 'Detalle de Arbitraje #' . $arbitraje->id_arbitraje)
@section('page-title', 'Detalle de Arbitraje')

{{-- AÑADE ESTA SECCIÓN PARA EL CSRF TOKEN --}}
@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

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
                                                'finalizado' => 'bg-success',
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
                                    
                                    <!-- SECCIÓN DE ADMIN: Subir documentos y finalizar proceso -->
                                    @if($proceso->nombre !== 'Validacion de Voucher' && $proceso->estado !== 'Finalizado')
                                        <hr>
                                        <h6 class="mb-3 text-danger">
                                            <i class="fas fa-cog me-2"></i>Acciones de Administración
                                        </h6>
                                        
                                        <!-- Formulario para subir documentos -->
                                        <div class="card border-primary mb-4">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-upload me-2"></i>Subir Documentos a este Proceso
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('arbitraje.documentos.store', $arbitraje->id_arbitraje) }}" 
                                                      method="POST" 
                                                      enctype="multipart/form-data"
                                                      class="row g-3 align-items-end">
                                                    @csrf
                                                    <input type="hidden" name="proceso_id" value="{{ $proceso->id_proceso_arbitraje }}">
                                                    
                                                    <div class="col-md-8">
                                                        <label for="archivo{{ $proceso->id_proceso_arbitraje }}" class="form-label">
                                                            Seleccionar archivo (PDF, JPG, PNG, JPEG) - Máx. 20MB
                                                        </label>
                                                        <input type="file" 
                                                               class="form-control" 
                                                               id="archivo{{ $proceso->id_proceso_arbitraje }}" 
                                                               name="archivo" 
                                                               accept=".pdf,.jpg,.jpeg,.png" 
                                                               required>
                                                        <div class="form-text">
                                                            Formatos permitidos: PDF, JPG, JPEG, PNG. Tamaño máximo: 20MB
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary w-100">
                                                            <i class="fas fa-upload me-2"></i>Subir Documento
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <!-- Botón para finalizar proceso -->
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-flag-checkered me-2"></i>Finalizar Este Proceso
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="mb-3">
                                                    <i class="fas fa-info-circle text-warning me-2"></i>
                                                    Al finalizar este proceso, se creará automáticamente el siguiente proceso en el flujo del arbitraje.
                                                </p>
                                                    <form id="formFinalizarProceso{{ $proceso->id_proceso_arbitraje }}" 
                                                        action="{{ route('arbitraje.siguiente.proceso', $arbitraje->id_arbitraje) }}" 
                                                        method="POST" 
                                                        class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="proceso_actual_id" value="{{ $proceso->id_proceso_arbitraje }}">
                                                        <button type="button" 
                                                                class="btn btn-warning btn-finalizar-proceso"
                                                                data-proceso-nombre="{{ $proceso->nombre }}"
                                                                data-proceso-id="{{ $proceso->id_proceso_arbitraje }}"
                                                                data-form-id="formFinalizarProceso{{ $proceso->id_proceso_arbitraje }}">
                                                            <i class="fas fa-check-circle me-2"></i>Finalizar Proceso
                                                        </button>
                                                    </form>
                                            </div>
                                        </div>
                                        <hr>
                                    @endif

                                    <!-- Documentos existentes -->
                                    @if($proceso->documentos && $proceso->documentos->count() > 0)
                                        <h6 class="mb-3">
                                            <i class="fas fa-paperclip me-2"></i>
                                            Documentos Adjuntos ({{ $proceso->documentos->count() }})
                                        </h6>
                                        <div class="list-group">
                                            @foreach($proceso->documentos as $documento)
                                                @php
                                                    $esVisualizable = in_array(strtolower($documento->tipo_documento), ['pdf', 'imagen']);
                                                @endphp
                                                <div class="list-group-item">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-1 text-center">
                                                            @if($documento->tipo_documento === 'pdf')
                                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                            @elseif($documento->tipo_documento === 'imagen')
                                                                <i class="fas fa-file-image fa-2x text-primary"></i>
                                                            @else
                                                                <i class="fas fa-external-link-alt fa-2x text-warning"></i>
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
                                                                <span class="badge {{ $esVisualizable ? 'bg-secondary' : 'bg-warning text-dark' }}">
                                                                    {{ strtoupper($documento->tipo_documento) }}
                                                                </span>
                                                                @if(!$esVisualizable)
                                                                    <span class="badge bg-info ms-1">
                                                                        <i class="fas fa-link me-1"></i>Enlace
                                                                    </span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                        <div class="col-md-4 text-end">
                                                            @if($esVisualizable)
                                                                {{-- PRIMER PROCESO (Validacion de Voucher) - Botón Ver con Modal --}}
                                                                @if($proceso->nombre === 'Validacion de Voucher')
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-danger me-2"
                                                                            data-bs-toggle="modal" 
                                                                            data-bs-target="#modalDocumento"
                                                                            data-documento-id="{{ $documento->id_documento }}"
                                                                            data-documento-nombre="{{ $documento->nombre_original }}"
                                                                            data-documento-tipo="{{ $documento->tipo_documento }}"
                                                                            data-documento-ruta="{{ $documento->ruta_archivo }}"
                                                                            data-documento-fecha="{{ $documento->fecha_subida ? $documento->fecha_subida->format('d/m/Y H:i') : 'N/A' }}">
                                                                        <i class="fas fa-eye me-1"></i>Ver
                                                                    </button>
                                                                    <a href="{{ asset('storage/' . $documento->ruta_archivo) }}" 
                                                                       download="{{ $documento->nombre_original }}" 
                                                                       class="btn btn-sm btn-danger">
                                                                        <i class="fas fa-download me-1"></i>Descargar
                                                                    </a>
                                                                {{-- DEMÁS PROCESOS - Solo botón para abrir en nueva pestaña --}}
                                                                @else
                                                                    @if($documento->tipo_documento === 'pdf' || $documento->tipo_documento === 'imagen')
                                                                        <a href="{{ asset($documento->ruta_archivo) }}" 
                                                                           target="_blank" 
                                                                           class="btn btn-sm btn-outline-primary me-2"
                                                                           title="Abrir documento en nueva pestaña">
                                                                            <i class="fas fa-external-link-alt me-1"></i>Abrir
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ $documento->ruta_archivo }}" 
                                                                           target="_blank" 
                                                                           class="btn btn-sm btn-outline-warning me-2"
                                                                           title="Abrir enlace en nueva pestaña">
                                                                            <i class="fas fa-external-link-alt me-1"></i>Abrir
                                                                        </a>
                                                                    @endif
                                                                @endif
                                                            @else
                                                                {{-- Documentos que son solo enlaces --}}
                                                                <a href="{{ $documento->ruta_archivo }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-outline-warning me-2"
                                                                   title="Abrir enlace en nueva pestaña">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Abrir
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        @if($proceso->nombre === 'Validacion de Voucher' || $proceso->estado === 'Finalizado')
                                            <hr>
                                            <p class="text-muted text-center py-2">
                                                <i class="fas fa-info-circle me-2"></i>No hay documentos adjuntos en este proceso
                                            </p>
                                        @endif
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
                                <!-- Vista previa de imagen -->
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
                            <i class="fas fa-check-circle me-2"></i>Aceptar Voucher
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-danger w-100" id="btnRechazarArbitraje">
                            <i class="fas fa-times-circle me-2"></i>Rechazar Voucher
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

<!-- Toast para notificaciones -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
    <div id="toastCopiado" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>
                <span id="toastMessage">Enlace copiado al portapapeles</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalDocumento = document.getElementById('modalDocumento');
    const modal = new bootstrap.Modal(modalDocumento);
    const btnRechazarArbitraje = document.getElementById('btnRechazarArbitraje');
    const btnAceptar = document.getElementById('btnAceptar');

    // Elementos del modal
    const imagenPreview = document.getElementById('imagenPreview');
    const pdfPreview = document.getElementById('pdfPreview');
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
    
    // Información del documento
    const infoNombre = document.getElementById('infoNombre');
    const infoTipo = document.getElementById('infoTipo');
    const infoFecha = document.getElementById('infoFecha');
    const btnDescargarModal = document.getElementById('btnDescargarModal');
    
    // Toast
    const toastCopiado = document.getElementById('toastCopiado');
    const toastMessage = document.getElementById('toastMessage');
    const toast = new bootstrap.Toast(toastCopiado);
    
    // Variables de estado
    let documentoActual = null;
    let currentZoom = 1;
    let currentRotation = 0;
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
            fecha: button.getAttribute('data-documento-fecha')
        };
        
        showLoading();
        loadDocumentInfo(documentoActual);
        
        // Asegurar que la ruta sea correcta (con storage/ si es necesario)
        let rutaCompleta = documentoActual.ruta;
        if (!rutaCompleta.startsWith('http')) {
            rutaCompleta = rutaCompleta;
        }
        documentoActual.ruta = rutaCompleta;
        
        setTimeout(() => {
            mostrarVistaPrevia(documentoActual);
        }, 500);
    });
    
    function showLoading() {
        imagenPreview.classList.add('d-none');
        pdfPreview.classList.add('d-none');
        loadingPreview.classList.remove('d-none');
    }
    
    function loadDocumentInfo(doc) {
        infoNombre.textContent = doc.nombre;
        infoTipo.textContent = doc.tipo.toUpperCase();
        infoFecha.textContent = doc.fecha;
        
        if (btnDescargarModal) {
            btnDescargarModal.href = doc.ruta;
            btnDescargarModal.download = doc.nombre;
        }
    }
    
    function mostrarVistaPrevia(doc) {
        loadingPreview.classList.add('d-none');
        
        const extension = doc.nombre.split('.').pop().toLowerCase();
        const esImagen = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension);
        const esPDF = extension === 'pdf';
        
        if (esImagen) {
            imagenPreview.classList.remove('d-none');
            pdfPreview.classList.add('d-none');
            imagenDocumento.src = doc.ruta;
            resetImageControls();
            setupImageDrag();
        } else if (esPDF) {
            imagenPreview.classList.add('d-none');
            pdfPreview.classList.remove('d-none');
            pdfIframe.src = doc.ruta;
        } else {
            console.error('Tipo de documento no soportado');
            modal.hide();
        }
    }
    
    function setupImageDrag() {
        const imageWrapper = imageContainer.querySelector('.image-wrapper');
        
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
    
    function resetImageControls() {
        currentZoom = 1;
        currentRotation = 0;
        imagenDocumento.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
        zoomLevel.textContent = `${Math.round(currentZoom * 100)}%`;
        
        const imageWrapper = imageContainer.querySelector('.image-wrapper');
        if (imageWrapper) {
            imageWrapper.scrollLeft = imageWrapper.scrollWidth / 2 - imageWrapper.clientWidth / 2;
            imageWrapper.scrollTop = imageWrapper.scrollHeight / 2 - imageWrapper.clientHeight / 2;
        }
    }
    
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
    
    // Acción Aceptar
    btnAceptar.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Obtener el ID del arbitraje
        const pathArray = window.location.pathname.split('/');
        const arbitrajeId = pathArray[pathArray.length - 2];
        
        console.log('ID del arbitraje para aceptar:', arbitrajeId);
        
        // Validar ID
        if (!arbitrajeId || isNaN(arbitrajeId)) {
            Swal.fire({
                title: 'Error',
                text: 'ID de arbitraje inválido',
                icon: 'error'
            });
            return;
        }
        
        // Obtener token CSRF
        let csrfToken = '';
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            csrfToken = metaTag.getAttribute('content');
        }
        
        console.log('Token CSRF para aceptar:', csrfToken ? 'Encontrado' : 'No encontrado');
        
        if (!csrfToken) {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo obtener el token de seguridad.',
                icon: 'error'
            });
            return;
        }
        
        // Cerrar el modal primero
        modal.hide();
        
        // Esperar un momento para que se cierre el modal
        setTimeout(() => {
            Swal.fire({
                title: '¿Aceptar voucher y pasar a selección de árbitro?',
                text: 'El arbitraje continuará con el proceso de selección de árbitro.',
                icon: 'question',
                html: `
                    <div class="text-start mt-3">
                        <label for="swal-input-comentario" class="form-label fw-bold">
                            Comentario opcional
                        </label>
                        <textarea 
                            id="swal-input-comentario" 
                            class="form-control" 
                            rows="3"
                            placeholder="Agregue un comentario si lo desea..."
                            maxlength="500"
                            style="resize: vertical;"
                        ></textarea>
                        <small class="text-muted d-block mt-1">
                            <span id="char-count-comentario">0</span>/500 caracteres
                        </small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, aceptar y continuar',
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                preConfirm: () => {
                    const comentario = document.getElementById('swal-input-comentario').value;
                    
                    if (comentario.length > 500) {
                        Swal.showValidationMessage('El comentario no puede exceder 500 caracteres');
                        return false;
                    }
                    
                    return { comentario: comentario.trim() };
                },
                didOpen: () => {
                    const textarea = document.getElementById('swal-input-comentario');
                    const charCount = document.getElementById('char-count-comentario');
                    
                    textarea.addEventListener('input', function() {
                        charCount.textContent = this.value.length;
                        
                        if (this.value.length > 450) {
                            charCount.classList.add('text-danger');
                            charCount.classList.remove('text-muted');
                        } else {
                            charCount.classList.remove('text-danger');
                            charCount.classList.add('text-muted');
                        }
                    });
                    
                    setTimeout(() => {
                        textarea.focus();
                    }, 100);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Aceptando voucher y creando proceso de selección de árbitro',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Crear FormData para enviar
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('comentario', result.value.comentario);
                    
                    // Usar la URL correcta para aceptar
                    const url = `/arbitrajes/${arbitrajeId}/aceptar`;
                    console.log('Enviando aceptación a:', url);
                    
                    // Usar fetch con FormData
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Respuesta recibida. Status:', response.status);
                        
                        if (!response.ok) {
                            throw new Error(`Error ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta del servidor:', data);
                        
                        if (data.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                html: `
                                    <div class="text-start">
                                        <p class="mb-2">${data.message}</p>
                                        <hr>
                                        <small class="text-muted">
                                            <strong>Nuevo proceso creado:</strong> ${data.data.nuevo_proceso}<br>
                                            <strong>Estado del arbitraje:</strong> ${data.data.arbitraje_estado}
                                            ${result.value.comentario ? `<br><strong>Comentario:</strong> ${result.value.comentario}` : ''}
                                        </small>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Recargar la página para ver los cambios
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'Error al aceptar el arbitraje',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud de aceptación:', error);
                        
                        Swal.fire({
                            title: 'Error',
                            html: `
                                <div class="text-start">
                                    <p>Error al procesar la aceptación:</p>
                                    <p class="text-danger">${error.message}</p>
                                    <hr>
                                    <small class="text-muted">
                                        <strong>URL intentada:</strong> ${url}<br>
                                        <strong>Método:</strong> POST
                                    </small>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        }, 300);
    });
    
    // Acción Rechazar con motivo personalizado - VERSIÓN CORREGIDA
    btnRechazarArbitraje.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Obtener el ID del arbitraje
        const pathArray = window.location.pathname.split('/');
        const arbitrajeId = pathArray[pathArray.length - 2];
        
        console.log('ID del arbitraje:', arbitrajeId);
        console.log('Ruta actual:', window.location.pathname);
        
        // Validar ID
        if (!arbitrajeId || isNaN(arbitrajeId)) {
            Swal.fire({
                title: 'Error',
                text: 'ID de arbitraje inválido',
                icon: 'error'
            });
            return;
        }
        
        // Obtener token CSRF
        let csrfToken = '';
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            csrfToken = metaTag.getAttribute('content');
        }
        
        console.log('Token CSRF:', csrfToken ? 'Encontrado' : 'No encontrado');
        console.log('URL a usar:', `/arbitrajes/${arbitrajeId}/rechazar`);
        
        if (!csrfToken) {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo obtener el token de seguridad. Recarga la página e intenta nuevamente.',
                icon: 'error'
            });
            return;
        }
        
        // Cerrar el modal primero
        modal.hide();
        
        // Esperar un momento para que se cierre el modal
        setTimeout(() => {
            Swal.fire({
                title: '¿Rechazar arbitraje completo?',
                text: 'Esta acción rechazará TODO el proceso de arbitraje.',
                icon: 'warning',
                html: `
                    <div class="text-start mt-3">
                        <label for="swal-input-motivo" class="form-label fw-bold">
                            Motivo del rechazo <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            id="swal-input-motivo" 
                            class="form-control" 
                            rows="4"
                            placeholder="Describe el motivo del rechazo..."
                            maxlength="500"
                            style="resize: vertical;"
                        ></textarea>
                        <small class="text-muted d-block mt-1">
                            <span id="char-count">0</span>/500 caracteres
                        </small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, rechazar arbitraje',
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                preConfirm: () => {
                    const motivo = document.getElementById('swal-input-motivo').value;
                    
                    if (!motivo || motivo.trim() === '') {
                        Swal.showValidationMessage('Debe proporcionar un motivo para el rechazo');
                        return false;
                    }
                    
                    if (motivo.length > 500) {
                        Swal.showValidationMessage('El motivo no puede exceder 500 caracteres');
                        return false;
                    }
                    
                    return { motivo: motivo.trim() };
                },
                didOpen: () => {
                    const textarea = document.getElementById('swal-input-motivo');
                    const charCount = document.getElementById('char-count');
                    
                    textarea.addEventListener('input', function() {
                        charCount.textContent = this.value.length;
                        
                        if (this.value.length > 450) {
                            charCount.classList.add('text-danger');
                            charCount.classList.remove('text-muted');
                        } else {
                            charCount.classList.remove('text-danger');
                            charCount.classList.add('text-muted');
                        }
                    });
                    
                    setTimeout(() => {
                        textarea.focus();
                    }, 100);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Rechazando arbitraje',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Crear FormData para enviar
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('motivo', result.value.motivo);
                    
                    // IMPORTANTE: Usar la URL correcta sin /admin/
                    const url = `/arbitrajes/${arbitrajeId}/rechazar`;
                    console.log('Enviando a:', url);
                    
                    // Usar fetch con FormData
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Respuesta recibida. Status:', response.status);
                        
                        if (!response.ok) {
                            throw new Error(`Error ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta del servidor:', data);
                        
                        if (data.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                html: `
                                    <div class="text-start">
                                        <p class="mb-2">${data.message}</p>
                                        <hr>
                                        <small class="text-muted">
                                            <strong>Motivo registrado:</strong><br>
                                            ${result.value.motivo}
                                        </small>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Recargar la página
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'Error al rechazar el arbitraje',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud:', error);
                        
                        Swal.fire({
                            title: 'Error',
                            html: `
                                <div class="text-start">
                                    <p>Error al procesar la solicitud:</p>
                                    <p class="text-danger">${error.message}</p>
                                    <hr>
                                    <small class="text-muted">
                                        <strong>URL intentada:</strong> ${url}<br>
                                        <strong>Método:</strong> POST<br>
                                        <strong>Token:</strong> ${csrfToken ? 'Presente' : 'Ausente'}
                                    </small>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        }, 300);
    });
    
    // Limpiar cuando se cierra el modal
    modalDocumento.addEventListener('hidden.bs.modal', function() {
        imagenDocumento.src = '';
        pdfIframe.src = '';
        documentoActual = null;
        resetImageControls();
        isDragging = false;
    });
    
    // Zoom con rueda del mouse
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
    
    // Copiar enlace al portapapeles
    document.querySelectorAll('.copy-link-btn').forEach(button => {
        button.addEventListener('click', function() {
            const link = this.getAttribute('data-link');
            const nombre = this.getAttribute('data-nombre');
            
            navigator.clipboard.writeText(link).then(function() {
                toastMessage.textContent = `Enlace de "${nombre}" copiado al portapapeles`;
                toast.show();
                
                const originalIcon = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copiado';
                button.classList.remove('btn-outline-info');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalIcon;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-info');
                }, 2000);
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
                
                const textArea = document.createElement('textarea');
                textArea.value = link;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                toastMessage.textContent = `Enlace de "${nombre}" copiado al portapapeles`;
                toast.show();
                
                const originalIcon = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copiado';
                button.classList.remove('btn-outline-info');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalIcon;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-info');
                }, 2000);
            });
        });
    });
});

// Manejar el evento de finalizar proceso con SweetAlert
document.querySelectorAll('.btn-finalizar-proceso').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        const procesoNombre = this.getAttribute('data-proceso-nombre');
        const procesoId = this.getAttribute('data-proceso-id');
        const formId = this.getAttribute('data-form-id');
        const form = document.getElementById(formId);
        
        Swal.fire({
            title: '¿Finalizar proceso?',
            html: `
                <div class="text-start">
                    <p class="mb-3">
                        <strong>Proceso:</strong> ${procesoNombre}
                    </p>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Esta acción no se puede deshacer. Se creará automáticamente el siguiente proceso en el flujo del arbitraje.
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="confirmarProceso">
                        <label class="form-check-label" for="confirmarProceso">
                            Confirmo que deseo finalizar este proceso
                        </label>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, finalizar proceso',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
            preConfirm: () => {
                const confirmado = document.getElementById('confirmarProceso').checked;
                if (!confirmado) {
                    Swal.showValidationMessage('Debes confirmar la acción marcando la casilla');
                    return false;
                }
                return true;
            },
            didOpen: () => {
                document.getElementById('confirmarProceso').focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Finalizando proceso y creando el siguiente paso',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Enviar el formulario
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(new FormData(form))
                })
                .then(response => {
                    if (response.redirected) {
                        // Si hay redirección (respuesta con redirección)
                        return response.text().then(() => {
                            return { redirect: response.url };
                        });
                    } else if (response.headers.get('content-type')?.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text();
                    }
                })
                .then(data => {
                    console.log('Respuesta del servidor:', data);
                    
                    if (data && data.redirect) {
                        // Si hubo redirección, recargar la página
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Proceso finalizado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (data && typeof data === 'string' && data.includes('success')) {
                        // Si la respuesta es texto con "success"
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Proceso finalizado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (data && data.success) {
                        // Si la respuesta es JSON con success
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.message || 'Proceso finalizado correctamente',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error('Respuesta inesperada del servidor');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    Swal.fire({
                        title: 'Error',
                        html: `
                            <div class="text-start">
                                <p>Ocurrió un error al procesar la solicitud:</p>
                                <p class="text-danger">${error.message}</p>
                                <hr>
                                <small class="text-muted">
                                    Si el problema persiste, contacta al administrador del sistema.
                                </small>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
            }
        });
    });
});
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
/* Asegurar que SweetAlert tenga prioridad sobre Bootstrap */
.modal.fade:not(.show) {
    display: none !important;
}

.modal-backdrop.fade:not(.show) {
    display: none !important;
}

.swal2-popup {
    z-index: 99999 !important;
}

.swal2-container {
    z-index: 99998 !important;
}

.swal2-textarea {
    min-height: 100px;
    max-height: 200px;
    resize: vertical;
}

/* Mejorar la visibilidad del textarea */
#swal-input-motivo {
    font-size: 14px;
    line-height: 1.5;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 4px;
    transition: border-color 0.3s;
}

#swal-input-motivo:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    outline: none;
}

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

.pdf-viewer-wrapper {
    border-radius: 8px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.document-info .info-item {
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
}

.document-info .info-item:last-child {
    border-bottom: none;
}

.copy-link-btn:hover {
    background-color: #17a2b8;
    color: white;
    border-color: #17a2b8;
}

.toast {
    min-width: 300px;
}

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
    
    .list-group-item .row > div {
        margin-bottom: 10px;
    }
    
    .list-group-item .text-end {
        text-align: left !important;
    }
}

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

.btn-outline-warning:hover {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    color: #212529;
}

.btn-outline-info:hover {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

#loadingPreview {
    min-height: 300px;
}

.image-container.zoom-active {
    cursor: grab;
}

.image-container.zoom-active:active {
    cursor: grabbing;
}

/* Estilos para los formularios de administración */
.card.border-primary {
    border-width: 2px !important;
}

.card.border-warning {
    border-width: 2px !important;
}

.card-header.bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
}

.card-header.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}
</style>
@endpush