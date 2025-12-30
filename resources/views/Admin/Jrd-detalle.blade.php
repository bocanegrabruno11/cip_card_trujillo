@extends('Admin.app')

@section('title', 'Detalle de JRD #' . $jrd->id_jrd)
@section('page-title', 'Detalle de JRD')

{{-- AÑADE ESTA SECCIÓN PARA EL CSRF TOKEN --}}
@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container-fluid">
    
    <!-- Botón de regreso -->
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('admin.jrd.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al listado
            </a>
        </div>
    </div>

    <!-- Información principal del JRD -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0">
                        <i class="fas fa-gavel me-2"></i>
                        {{ $jrd->nombre_materia }}
                    </h4>
                    <small>ID: #{{ $jrd->id_jrd }}</small>
                </div>
                <div class="col-md-4 text-end">
                    @php
                        $estadoClass = match(strtolower($jrd->estado)) {
                            'validando' => 'bg-warning text-dark',
                            'iniciado' => 'bg-info',
                            'en proceso' => 'bg-primary',
                            'terminado' => 'bg-success',
                            'rechazado' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $estadoClass }} px-3 py-2 fs-6">
                        {{ strtoupper($jrd->estado) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Descripción:</th>
                            <td>{{ $jrd->descripcion }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Inicio:</th>
                            <td>
                                <i class="fas fa-calendar me-1"></i>
                                {{ $jrd->fecha_inicio ? $jrd->fecha_inicio->format('d/m/Y H:i') : 'No especificada' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Finalización:</th>
                            <td>
                                @if($jrd->fecha_finalizacion)
                                    <i class="fas fa-calendar-check me-1"></i>
                                    {{ $jrd->fecha_finalizacion->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">En proceso</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-user-tie me-2"></i>Creador del JPRD
                    </h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Nombre:</th>
                            <td>{{ $jrd->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $jrd->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>DNI:</th>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $jrd->user->persona->dni ?? 'N/A' }}
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
                <i class="fas fa-users text-primary me-2"></i>Personas Involucradas
            </h5>
        </div>
        <div class="card-body">
            @if($jrd->personas && $jrd->personas->count() > 0)
                <div class="row">
                    @foreach($jrd->personas as $persona)
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
                    <i class="fas fa-info-circle me-2"></i>No hay personas registradas en este JRD
                </p>
            @endif
        </div>
    </div>

<!-- Procesos -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-tasks text-primary me-2"></i>
            Procesos del JPRD ({{ $jrd->procesos->count() }})
        </h5>
    </div>
    <div class="card-body">
        @if($jrd->procesos && $jrd->procesos->count() > 0)
            <div class="accordion" id="accordionProcesos">
                @foreach($jrd->procesos as $index => $proceso)
                    @php
                        // VERSIÓN FINAL LIMPIA
                        // Determinar si es proceso de validación por NOMBRE (no por índice)
                        $esProcesoValidacion = str_contains(strtolower($proceso->nombre), 'validacion') && 
                                            (str_contains(strtolower($proceso->nombre), 'voucher') || 
                                            str_contains(strtolower($proceso->nombre), 'pago'));
                        
                        $estadoProceso = strtolower(trim($proceso->estado));
                        
                        // Definir estados
                        $esActivo = in_array($estadoProceso, ['activo', 'en proceso', 'en progreso', 'iniciado']);
                        $esFinalizado = in_array($estadoProceso, ['finalizado', 'rechazado', 'completado', 'terminado']);
                        
                        // Determinar si es el primer proceso (para mostrar validación de voucher)
                        $esPrimerProceso = $index === 0;
                        
                        // REGLA: Mostrar acciones si NO es proceso de validación, está activo y NO está finalizado
                        $mostrarAccionesAdmin = !$esProcesoValidacion && $esActivo && !$esFinalizado;
                        
                        // Clase para el badge
                        $estadoProcesoClass = 'bg-secondary';
                        if (in_array($estadoProceso, ['activo', 'en proceso', 'en progreso'])) {
                            $estadoProcesoClass = 'bg-primary';
                        } elseif (in_array($estadoProceso, ['iniciado'])) {
                            $estadoProcesoClass = 'bg-info';
                        } elseif (in_array($estadoProceso, ['completado', 'finalizado', 'terminado'])) {
                            $estadoProcesoClass = 'bg-success';
                        } elseif (in_array($estadoProceso, ['rechazado'])) {
                            $estadoProcesoClass = 'bg-danger';
                        }
                    @endphp
                    
                    <div class="accordion-item mb-3 border">
                        <h2 class="accordion-header" id="heading{{ $proceso->id_proceso_jrd }}">
                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $proceso->id_proceso_jrd }}"
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
                                    <span class="badge {{ $estadoProcesoClass }}">
                                        {{ $proceso->estado }}
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $proceso->id_proceso_jrd }}" 
                             class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                             aria-labelledby="heading{{ $proceso->id_proceso_jrd }}"
                             data-bs-parent="#accordionProcesos">
                            <div class="accordion-body">
                                <h6 class="mb-3">
                                    <i class="fas fa-align-left me-2"></i>Descripción
                                </h6>
                                <p class="text-muted">{{ $proceso->descripcion }}</p>
                                
                                <!-- SECCIÓN DE ADMIN: Subir documentos y finalizar proceso -->
                                @if($mostrarAccionesAdmin)
                                    <hr>
                                    <h6 class="mb-3 text-primary">
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
                                            <form action="{{ route('jrd.documento.store', $jrd->id_jrd) }}" 
                                                  method="POST" 
                                                  enctype="multipart/form-data"
                                                  class="row g-3 align-items-end">
                                                @csrf
                                                <input type="hidden" name="proceso_id" value="{{ $proceso->id_proceso_jrd }}">
                                                
                                                <div class="col-md-8">
                                                    <label for="archivo{{ $proceso->id_proceso_jrd }}" class="form-label">
                                                        Seleccionar archivo (PDF, JPG, PNG, JPEG) - Máx. 20MB
                                                    </label>
                                                 <input type="file" 
                                                            class="form-control" 
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
                                                Al finalizar este proceso, se creará automáticamente el siguiente proceso en el flujo del JRD.
                                            </p>
                                            <form id="formFinalizarProceso{{ $proceso->id_proceso_jrd }}" 
                                                  action="{{ route('jrd.proceso.siguiente', $jrd->id_jrd) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <input type="hidden" name="proceso_actual_id" value="{{ $proceso->id_proceso_jrd }}">
                                                <button type="button" 
                                                        class="btn btn-warning btn-finalizar-proceso"
                                                        data-proceso-nombre="{{ $proceso->nombre }}"
                                                        data-proceso-id="{{ $proceso->id_proceso_jrd }}">
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
                                                            @if($esPrimerProceso && !$esFinalizado && $esProcesoValidacion)
                                                                <!-- Primer proceso de validación - Botón para validar voucher -->
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-primary me-2 btn-validar-voucher"
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#modalDocumentoVoucher"
                                                                        data-documento-id="{{ $documento->id_documento }}"
                                                                        data-documento-nombre="{{ $documento->nombre_original }}"
                                                                        data-documento-tipo="{{ $documento->tipo_documento }}"
                                                                        data-documento-ruta="{{ $documento->ruta_archivo }}"
                                                                        data-documento-fecha="{{ $documento->fecha_subida ? $documento->fecha_subida->format('d/m/Y H:i') : 'N/A' }}"
                                                                        data-proceso-id="{{ $proceso->id_proceso_jrd }}"
                                                                        data-jrd-id="{{ $jrd->id_jrd }}">
                                                                    <i class="fas fa-check-circle me-1"></i>Validar Voucher
                                                                </button>
                                                                <a href="{{ asset($documento->ruta_archivo) }}" 
                                                                   download="{{ $documento->nombre_original }}" 
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download me-1"></i>Descargar
                                                                </a>
                                                            @else
                                                                <!-- Demás procesos - Solo botón para abrir -->
                                                                <a href="{{ asset($documento->ruta_archivo) }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-outline-primary me-2"
                                                                   title="Abrir documento en nueva pestaña">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Abrir
                                                                </a>
                                                                <a href="{{ asset($documento->ruta_archivo) }}" 
                                                                   download="{{ $documento->nombre_original }}" 
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download me-1"></i>Descargar
                                                                </a>
                                                            @endif
                                                        @else
                                                            <!-- Documentos que son solo enlaces -->
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
                <i class="fas fa-info-circle me-2"></i>No hay procesos registrados en este JRD
            </p>
        @endif
    </div>
</div>

</div>

<!-- Modal para validar voucher -->
<div class="modal fade" id="modalDocumentoVoucher" tabindex="-1" aria-labelledby="modalDocumentoVoucherLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDocumentoVoucherLabel">
                    <i class="fas fa-money-check-alt me-2"></i>Validación de Voucher
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Panel de vista previa -->
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <!-- Vista previa -->
                                <div id="previewContainerVoucher" class="d-none">
                                    <div id="imagenPreviewVoucher" class="d-none">
                                        <div class="image-viewer-wrapper">
                                            <div class="image-container" id="imageContainerVoucher">
                                                <div class="image-wrapper">
                                                    <img id="imagenDocumentoVoucher" src="" alt="Voucher" class="zoomable-image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="pdfPreviewVoucher" class="d-none">
                                        <div class="pdf-viewer-wrapper">
                                            <iframe id="pdfIframeVoucher" src="" width="100%" height="500px" class="border rounded"></iframe>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Cargando -->
                                <div id="loadingPreviewVoucher" class="d-flex flex-column align-items-center justify-content-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Cargando voucher...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Panel de información -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Información del Voucher
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="document-info">
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted small mb-1">Nombre del archivo</label>
                                        <div class="p-2 bg-light rounded">
                                            <strong id="infoNombreVoucher"></strong>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted small mb-1">Tipo de documento</label>
                                        <div class="p-2 bg-light rounded">
                                            <span id="infoTipoVoucher" class="badge bg-secondary"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted small mb-1">Fecha de subida</label>
                                        <div class="p-2 bg-light rounded">
                                            <span id="infoFechaVoucher"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted small mb-1">JRD ID</label>
                                        <div class="p-2 bg-light rounded">
                                            <strong id="infoJrdIdVoucher"></strong>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted small mb-1">Proceso ID</label>
                                        <div class="p-2 bg-light rounded">
                                            <strong id="infoProcesoIdVoucher"></strong>
                                        </div>
                                    </div>
                                    
                                    <!-- Campos ocultos para guardar los IDs -->
                                    <input type="hidden" id="hiddenJrdId" value="">
                                    <input type="hidden" id="hiddenProcesoId" value="">
                                    
                                    <div class="mt-4">
                                        <h6 class="border-bottom pb-2">Validación</h6>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Verifique la autenticidad del pago antes de continuar.
                                        </div>
                                        
                                        <div class="d-grid gap-2 mt-3">
                                            <button type="button" class="btn btn-success" id="btnAprobarVoucher">
                                                <i class="fas fa-check-circle me-2"></i>Aprobar Voucher
                                            </button>
                                            <button type="button" class="btn btn-danger" id="btnRechazarVoucher">
                                                <i class="fas fa-times-circle me-2"></i>Rechazar Voucher
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalVoucher = new bootstrap.Modal(document.getElementById('modalDocumentoVoucher'));

    // Cuando se hace clic en "Validar Voucher"
    document.querySelectorAll('.btn-validar-voucher').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const jrdId = this.getAttribute('data-jrd-id');
            const procesoId = this.getAttribute('data-proceso-id');
            const documentoData = {
                id: this.getAttribute('data-documento-id'),
                nombre: this.getAttribute('data-documento-nombre'),
                tipo: this.getAttribute('data-documento-tipo'),
                ruta: this.getAttribute('data-documento-ruta'),
                fecha: this.getAttribute('data-documento-fecha')
            };
            
            console.log('Datos obtenidos del botón:', {
                jrdId,
                procesoId,
                documentoData
            });
            
            // Guardar los IDs en campos ocultos del modal
            document.getElementById('hiddenJrdId').value = jrdId;
            document.getElementById('hiddenProcesoId').value = procesoId;
            
            // Cargar información visible
            document.getElementById('infoNombreVoucher').textContent = documentoData.nombre;
            document.getElementById('infoTipoVoucher').textContent = documentoData.tipo.toUpperCase();
            document.getElementById('infoFechaVoucher').textContent = documentoData.fecha;
            document.getElementById('infoJrdIdVoucher').textContent = `JRD #${jrdId}`;
            document.getElementById('infoProcesoIdVoucher').textContent = `Proceso #${procesoId}`;
            
            // Mostrar loading
            document.getElementById('loadingPreviewVoucher').classList.remove('d-none');
            document.getElementById('previewContainerVoucher').classList.add('d-none');
            
            // Configurar vista previa después de un breve retraso
            setTimeout(() => {
                mostrarVistaPreviaVoucher(documentoData);
            }, 300);
        });
    });
    
    function mostrarVistaPreviaVoucher(voucher) {
        document.getElementById('loadingPreviewVoucher').classList.add('d-none');
        document.getElementById('previewContainerVoucher').classList.remove('d-none');
        
        const extension = voucher.nombre.split('.').pop().toLowerCase();
        const esImagen = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension);
        const esPDF = extension === 'pdf';
        
        if (esImagen) {
            document.getElementById('imagenPreviewVoucher').classList.remove('d-none');
            document.getElementById('pdfPreviewVoucher').classList.add('d-none');
            document.getElementById('imagenDocumentoVoucher').src = voucher.ruta;
        } else if (esPDF) {
            document.getElementById('imagenPreviewVoucher').classList.add('d-none');
            document.getElementById('pdfPreviewVoucher').classList.remove('d-none');
            document.getElementById('pdfIframeVoucher').src = voucher.ruta;
        }
    }
    
    // Acción Aprobar Voucher
    document.getElementById('btnAprobarVoucher').addEventListener('click', function() {
        // Obtener los IDs de los campos ocultos
        const jrdId = document.getElementById('hiddenJrdId').value;
        const procesoId = document.getElementById('hiddenProcesoId').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        console.log('Datos para aprobar:', { jrdId, procesoId });
        
        if (!jrdId || !procesoId) {
            console.error('Faltan datos:', { jrdId, procesoId });
            Swal.fire('Error', 'No se encontraron los datos necesarios para la validación', 'error');
            return;
        }
        
        modalVoucher.hide();
        
        setTimeout(() => {
            Swal.fire({
                title: '¿Aprobar este voucher?',
                text: 'Se continuará con el siguiente proceso en el JRD.',
                icon: 'question',
                html: `
                    <div class="text-start mt-3">
                        <label for="swal-input-comentario-voucher" class="form-label fw-bold">
                            Comentario (opcional)
                        </label>
                        <textarea 
                            id="swal-input-comentario-voucher" 
                            class="form-control" 
                            rows="3"
                            placeholder="Agregue un comentario sobre la aprobación..."
                            maxlength="500"
                            style="resize: vertical;"
                        ></textarea>
                        <small class="text-muted d-block mt-1">
                            <span id="char-count-comentario-voucher">0</span>/500 caracteres
                        </small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, aprobar y continuar',
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                preConfirm: () => {
                    const comentario = document.getElementById('swal-input-comentario-voucher').value;
                    
                    if (comentario.length > 500) {
                        Swal.showValidationMessage('El comentario no puede exceder 500 caracteres');
                        return false;
                    }
                    
                    return { 
                        comentario: comentario.trim()
                    };
                },
                didOpen: () => {
                    const textarea = document.getElementById('swal-input-comentario-voucher');
                    const charCount = document.getElementById('char-count-comentario-voucher');
                    
                    if (textarea && charCount) {
                        textarea.addEventListener('input', function() {
                            charCount.textContent = this.value.length;
                        });
                        setTimeout(() => textarea.focus(), 100);
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Aprobando voucher y continuando con el proceso',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    // Crear FormData con todos los datos necesarios
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('proceso_actual_id', procesoId);
                    if (result.value.comentario) {
                        formData.append('comentario', result.value.comentario);
                    }
                    
                    const url = `/jrd/${jrdId}/proceso/siguiente`;
                    console.log('URL para aprobar voucher:', url);
                    console.log('Datos enviados:', {
                        jrdId: jrdId,
                        proceso_actual_id: procesoId,
                        comentario: result.value.comentario,
                        _token: csrfToken
                    });
                    
                    // Hacer la petición con manejo de errores mejorado
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Respuesta recibida. Status:', response.status);
                        console.log('Content-Type:', response.headers.get('content-type'));
                        
                        // Verificar si la respuesta es JSON
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            // Si no es JSON, obtener el texto para ver el error
                            return response.text().then(text => {
                                console.error('Respuesta no JSON recibida (primeros 500 chars):', text.substring(0, 500));
                                throw new Error('El servidor devolvió una respuesta no JSON. ¿Estás autenticado?');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta JSON del servidor:', data);
                        if (data && data.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: data.message || 'Voucher aprobado correctamente',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => window.location.reload());
                        } else {
                            throw new Error(data ? data.message : 'Error al aprobar el voucher');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la petición:', error);
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'Error al procesar la solicitud',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        }, 300);
    });
    
    // Acción Rechazar Voucher
    document.getElementById('btnRechazarVoucher').addEventListener('click', function() {
        // Obtener los IDs de los campos ocultos
        const jrdId = document.getElementById('hiddenJrdId').value;
        const procesoId = document.getElementById('hiddenProcesoId').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        console.log('Datos para rechazar:', { jrdId, procesoId });
        
        if (!jrdId || !procesoId) {
            console.error('Faltan datos:', { jrdId, procesoId });
            Swal.fire('Error', 'No se encontraron los datos necesarios para la validación', 'error');
            return;
        }
        
        modalVoucher.hide();
        
        setTimeout(() => {
            Swal.fire({
                title: '¿Rechazar este voucher?',
                text: 'El proceso será marcado como rechazado.',
                icon: 'warning',
                html: `
                    <div class="text-start mt-3">
                        <label for="swal-input-motivo-voucher" class="form-label fw-bold">
                            Motivo del rechazo <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            id="swal-input-motivo-voucher" 
                            class="form-control" 
                            rows="4"
                            placeholder="Describe por qué se rechaza el voucher..."
                            maxlength="500"
                            style="resize: vertical;"
                            required
                        ></textarea>
                        <small class="text-muted d-block mt-1">
                            <span id="char-count-motivo-voucher">0</span>/500 caracteres
                        </small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, rechazar voucher',
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                preConfirm: () => {
                    const motivo = document.getElementById('swal-input-motivo-voucher').value;
                    
                    if (!motivo || motivo.trim() === '') {
                        Swal.showValidationMessage('Debe proporcionar un motivo para el rechazo');
                        return false;
                    }
                    
                    if (motivo.length > 500) {
                        Swal.showValidationMessage('El motivo no puede exceder 500 caracteres');
                        return false;
                    }
                    
                    return { 
                        motivo: motivo.trim()
                    };
                },
                didOpen: () => {
                    const textarea = document.getElementById('swal-input-motivo-voucher');
                    const charCount = document.getElementById('char-count-motivo-voucher');
                    
                    if (textarea && charCount) {
                        textarea.addEventListener('input', function() {
                            charCount.textContent = this.value.length;
                        });
                        setTimeout(() => textarea.focus(), 100);
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Rechazando voucher',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    // Primero actualizar el estado del proceso
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('estado', 'rechazado');
                    formData.append('comentario_rechazo', result.value.motivo);
                    
                    const urlActualizar = `/jrd/${jrdId}/proceso/${procesoId}/actualizar`;
                    console.log('URL para actualizar proceso:', urlActualizar);
                    
                    fetch(urlActualizar, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Respuesta actualizar proceso. Status:', response.status);
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            return response.text().then(text => {
                                console.error('Respuesta no JSON recibida:', text.substring(0, 500));
                                throw new Error('Error del servidor: respuesta no JSON');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta actualizar proceso:', data);
                        if (data && data.success) {
                            // Luego rechazar el JRD completo
                            const formDataJrd = new FormData();
                            formDataJrd.append('_token', csrfToken);
                            formDataJrd.append('motivo', `Voucher rechazado: ${result.value.motivo}`);
                            
                            const urlRechazar = `/jrd/${jrdId}/rechazar`;
                            console.log('URL para rechazar JRD:', urlRechazar);
                            
                            return fetch(urlRechazar, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: formDataJrd
                            });
                        } else {
                            throw new Error(data ? data.message : 'Error al actualizar el proceso');
                        }
                    })
                    .then(response => {
                        console.log('Respuesta rechazar JPRD. Status:', response.status);
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            return response.text().then(text => {
                                console.error('Respuesta no JSON recibida:', text.substring(0, 500));
                                throw new Error('Error del servidor al rechazar JPRD');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta rechazar JPRD:', data);
                        if (data && data.success) {
                            Swal.fire({
                                title: '¡Voucher rechazado!',
                                html: `
                                    <div class="text-start">
                                        <p class="mb-2">${data.message}</p>
                                        <hr>
                                        <small class="text-muted">
                                            <strong>Motivo:</strong> ${result.value.motivo}
                                        </small>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => window.location.reload());
                        } else {
                            throw new Error(data ? data.message : 'Error al rechazar el JRD');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la petición:', error);
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'Error al procesar la solicitud',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        }, 300);
    });
    
    // Botones de finalizar proceso - CORREGIDO
    document.querySelectorAll('.btn-finalizar-proceso').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const procesoNombre = this.getAttribute('data-proceso-nombre');
            const procesoId = this.getAttribute('data-proceso-id');
            
            // Encontrar el formulario correspondiente
            const form = this.closest('form');
            if (!form) {
                console.error('No se encontró el formulario');
                return;
            }
            
            // Obtener el JRD ID del action del formulario
            const formAction = form.getAttribute('action');
            const jrdId = formAction.split('/')[4]; // /jrd/{id}/proceso/siguiente
            
            console.log('Datos para finalizar proceso:', { jrdId, procesoId, procesoNombre });
            
            Swal.fire({
                title: '¿Finalizar proceso?',
                html: `
                    <div class="text-start">
                        <p class="mb-3">
                            <strong>Proceso:</strong> ${procesoNombre}
                        </p>
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Esta acción creará automáticamente el siguiente proceso en el flujo del JRD.
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
                        Swal.showValidationMessage('Debes confirmar la acción');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Finalizando proceso y creando el siguiente paso',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    // Usar el formulario existente
                    const csrfToken = form.querySelector('input[name="_token"]').value;
                    
                    const formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('proceso_actual_id', procesoId);
                    
                    const url = `/jrd/${jrdId}/proceso/siguiente`;
                    console.log('URL para finalizar proceso:', url);
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => {
                        console.log('Respuesta finalizar proceso. Status:', response.status);
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            return response.text().then(text => {
                                console.error('Respuesta no JSON recibida:', text.substring(0, 500));
                                throw new Error('Error del servidor: respuesta no JSON. Verifica la autenticación.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respuesta finalizar proceso:', data);
                        if (data && data.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: data.message || 'Proceso finalizado correctamente',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => window.location.reload());
                        } else {
                            throw new Error(data ? data.message : 'Error al procesar');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la petición:', error);
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'Error al procesar la solicitud',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        });
    });
    
    // Limpiar modal al cerrar
    document.getElementById('modalDocumentoVoucher').addEventListener('hidden.bs.modal', function() {
        document.getElementById('imagenDocumentoVoucher').src = '';
        document.getElementById('pdfIframeVoucher').src = '';
        document.getElementById('loadingPreviewVoucher').classList.remove('d-none');
        document.getElementById('previewContainerVoucher').classList.add('d-none');
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

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
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