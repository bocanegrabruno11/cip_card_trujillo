@extends('Admin.app')

@section('title', 'Detalle de Arbitraje #' . $arbitraje->id_arbitraje)
@section('page-title', 'Detalle de Arbitraje')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.border-start { border-left-width: 4px !important; }
.accordion-button:not(.collapsed) { background-color: #f8f9fa; color: #000; }
.accordion-button:focus { box-shadow: none; }
.list-group-item { border: 1px solid #dee2e6; margin-bottom: 10px; border-radius: 5px; }
.card.border-primary, .card.border-warning { border-width: 2px !important; }
.btn:hover { transform: translateY(-2px); transition: transform 0.2s ease; }
.image-viewer-wrapper, .voucher-viewer-wrapper {
    position: relative; background-color: #f8f9fa;
    border-radius: 8px; overflow: hidden; min-height: 400px;
}
.image-container { overflow: auto; display: flex; align-items: center; justify-content: center; padding: 20px; }
.zoomable-image { max-width: 100%; max-height: 70vh; transition: transform 0.3s ease; object-fit: contain; cursor: pointer; }
.badge-uploader { font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 20px; }
</style>

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('admin.arbitrajes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al listado
            </a>
        </div>
    </div>

    <!-- Info principal -->
    <div class="card shadow-sm mb-4">
<div class="card-header bg-danger text-white">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-scale-balanced me-2"></i>{{ $arbitraje->nombre_materia }}
                
                <!-- ✅ AGREGAR BADGE DE TIPO EN EL ENCABEZADO -->
                @if(($arbitraje->tipo_arbitraje ?? 'normal') === 'emergencia')
                    <span class="badge bg-warning text-dark ms-2"><i class="fas fa-bolt me-1"></i>EMERGENCIA</span>
                @else
                    <span class="badge bg-light text-dark ms-2"><i class="fas fa-gavel me-1"></i>NORMAL</span>
                @endif
            </h4>
            <small>ID: #{{ $arbitraje->id_arbitraje }}</small>
        </div>
        <div class="col-md-4 text-end">
            @php
                $estadoClass = match(strtolower($arbitraje->estado)) {
                    'validando'  => 'bg-warning text-dark',
                    'iniciado'   => 'bg-info',
                    'observado'  => 'bg-danger',
                    'en proceso' => 'bg-primary',
                    'terminado'  => 'bg-success',
                    'rechazado'  => 'bg-danger',
                    'archivado'  => 'bg-secondary',
                    default      => 'bg-secondary'
                };
            @endphp
            <span class="badge {{ $estadoClass }} px-3 py-2 fs-6">{{ strtoupper($arbitraje->estado) }}</span>
        </div>
    </div>
</div>
        <div class="card-body">
<!-- En la sección de Información General, agregar después de "Designación Arbitral" o donde prefieras -->

<div class="col-md-6">
    <h6 class="text-danger mb-3"><i class="fas fa-info-circle me-2"></i>Información General</h6>
    <table class="table table-sm">
        <tr><th width="40%">Pretensiones:</th><td>{{ $arbitraje->pretenciones ?? 'No especificadas' }}</td></tr>
        <tr><th>Cuantía:</th><td>{{ $arbitraje->cuantia ?? 'No especificada' }}</td></tr>
        <tr><th>Controversia:</th><td>{{ $arbitraje->controversia ?? 'No especificada' }}</td></tr>
        <tr><th>Tasa de Solicitud:</th><td>{{ $arbitraje->tasa_solicitud ?? 'No especificada' }}</td></tr>
        <tr><th>Designación Arbitral:</th><td>{{ $arbitraje->designacion_arbitral ?? 'No especificada' }}</td></tr>
        
        <!-- ✅ AGREGAR ESTA LÍNEA PARA MOSTRAR EL TIPO DE ARBITRAJE -->
        <tr>
            <th>Tipo de Arbitraje:</th>
            <td>
                @if(($arbitraje->tipo_arbitraje ?? 'normal') === 'emergencia')
                    <span class="badge bg-danger"><i class="fas fa-bolt me-1"></i>EMERGENCIA</span>
                @else
                    <span class="badge bg-secondary"><i class="fas fa-gavel me-1"></i>NORMAL</span>
                @endif
            </td>
        </tr>
        
        <tr><th>Fundamentos de hecho:</th><td>{{ $arbitraje->fundamentos_hecho ?? 'No especificada' }}</td></tr>
        <tr><th>Fecha de Inicio:</th><td><i class="fas fa-calendar me-1"></i>{{ $arbitraje->fecha_inicio ? \Carbon\Carbon::parse($arbitraje->fecha_inicio)->format('d/m/Y H:i') : 'No especificada' }}</td></tr>
        <tr><th>Fecha de Finalización:</th><td>
            @if($arbitraje->fecha_finalizacion)
                <i class="fas fa-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($arbitraje->fecha_finalizacion)->format('d/m/Y H:i') }}
            @else
                <span class="text-muted">En proceso</span>
            @endif
        </td></tr>
    </table>
</div>
            @if(!in_array($arbitraje->estado, ['archivado', 'terminado']))
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-warning d-flex justify-content-between align-items-center mb-0">
                        <div><i class="fas fa-archive me-2 fa-lg"></i><strong>Acción de Archivado:</strong> Al archivar, NO se podrán subir más documentos ni crear nuevos procesos.</div>
                        <button type="button" class="btn btn-danger" id="btnArchivarArbitraje">
                            <i class="fas fa-archive me-2"></i>Archivar Arbitraje
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Personas -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="fas fa-users text-danger me-2"></i>Personas Involucradas</h5>
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
                                            <span class="badge {{ $persona->tipo === 'Demandante' ? 'bg-success' : 'bg-warning text-dark' }} mb-2">{{ $persona->tipo }}</span>
                                            <h6 class="mb-1">{{ $persona->nombres }} {{ $persona->apellidos }}</h6>
                                            <p class="mb-0 text-muted small"><i class="fas fa-id-card me-1"></i>DNI: {{ $persona->dni }}</p>
                                            <p class="mb-0 text-muted small"><i class="fas fa-phone-alt me-1"></i>Telefono: {{ $persona->telefono }}</p>
                                            @if($persona->correo)<p class="mb-0 text-muted small"><i class="fas fa-envelope me-1"></i>{{ $persona->correo }}</p>@endif
                                            @if($persona->direccion)<p class="mb-0 text-muted small"><i class="fas fa-home me-1"></i>{{ $persona->direccion }}</p>@endif
                                        </div>
                                        <i class="fas fa-user fa-2x text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-3"><i class="fas fa-info-circle me-2"></i>No hay personas registradas</p>
            @endif
        </div>
    </div>

    <!-- Procesos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="fas fa-tasks text-danger me-2"></i>Procesos del Arbitraje ({{ $arbitraje->procesos->count() }})</h5>
        </div>
        <div class="card-body">
            @if($arbitraje->procesos && $arbitraje->procesos->count() > 0)
                <div class="accordion" id="accordionProcesos">
                    @foreach($arbitraje->procesos as $index => $proceso)
                        @php
                            $etapaNombre    = $proceso->etapa ? $proceso->etapa->nombre : 'Proceso #' . $proceso->id_proceso_de_arbitraje;
                            $estaFinalizado = $proceso->estado === 'finalizado';
                            $estaArchivado  = $arbitraje->estado === 'archivado';
                        @endphp
                        <div class="accordion-item mb-3 border">
                            <h2 class="accordion-header" id="heading{{ $proceso->id_proceso_de_arbitraje }}">
                                <button class="accordion-button {{ $index === 0 && !$estaFinalizado ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $proceso->id_proceso_de_arbitraje }}"
                                        aria-expanded="{{ $index === 0 && !$estaFinalizado ? 'true' : 'false' }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <i class="fas fa-file-alt text-primary me-2"></i><strong>{{ $etapaNombre }}</strong><br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>Iniciado: {{ $proceso->fecha_creacion ? \Carbon\Carbon::parse($proceso->fecha_creacion)->format('d/m/Y H:i') : 'Sin fecha' }}
                                                @if($proceso->fecha_finalizacion)<br><i class="fas fa-check-circle me-1 text-success"></i>Finalizado: {{ \Carbon\Carbon::parse($proceso->fecha_finalizacion)->format('d/m/Y H:i') }}@endif
                                            </small>
                                        </div>
                                        @php $estadoProcesoClass = match(strtolower($proceso->estado)) { 'iniciado' => 'bg-info', 'finalizado' => 'bg-success', default => 'bg-secondary' }; @endphp
                                        <span class="badge {{ $estadoProcesoClass }}">{{ strtoupper($proceso->estado) }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $proceso->id_proceso_de_arbitraje }}"
                                 class="accordion-collapse collapse {{ $index === 0 && !$estaFinalizado ? 'show' : '' }}"
                                 data-bs-parent="#accordionProcesos">
                                <div class="accordion-body">

                                    @if(!$estaArchivado && $proceso->estado !== 'finalizado' && $arbitraje->estado !== 'observado')
                                        <div class="card border-primary mb-4">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Subir Documento a este Proceso</h6>
                                            </div>
                                            <div class="card-body">
                                                <form class="row g-3 form-subir-documento"
                                                      data-arbitraje-id="{{ $arbitraje->id_arbitraje }}"
                                                      data-proceso-id="{{ $proceso->id_proceso_de_arbitraje }}">
                                                    @csrf
                                                    <input type="hidden" name="proceso_id" value="{{ $proceso->id_proceso_de_arbitraje }}">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                                                        <select class="form-select tipo-documento-select" data-proceso-id="{{ $proceso->id_proceso_de_arbitraje }}" name="tipo_documento" required>
                                                            <option value="">Seleccione...</option>
                                                            <option value="archivo">📄 Subir Archivo (PDF, JPG, PNG)</option>
                                                            <option value="link">🔗 Enlace (Google Drive, etc.)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4" id="campo_archivo_proceso{{ $proceso->id_proceso_de_arbitraje }}" style="display:none;">
                                                        <label class="form-label">Archivo <span class="text-danger">*</span></label>
                                                        <input type="file" class="form-control" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                                                        <small class="text-muted">Máx. 20MB</small>
                                                    </div>
                                                    <div class="col-md-4" id="campo_link_proceso{{ $proceso->id_proceso_de_arbitraje }}" style="display:none;">
                                                        <label class="form-label">Enlace <span class="text-danger">*</span></label>
                                                        <input type="url" class="form-control" name="link" placeholder="https://...">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Nombre del Documento <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="nombre_documento" placeholder="Ej: Contrato firmado" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Observaciones</label>
                                                        <input type="text" class="form-control" name="observaciones" placeholder="Comentarios adicionales...">
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i>Subir Documento</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif

                                    @if(!$estaArchivado && $proceso->estado !== 'finalizado' && in_array($arbitraje->estado, ['iniciado', 'en proceso']))
                                        <div class="card border-warning mb-4">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0"><i class="fas fa-flag-checkered me-2"></i>Finalizar Este Proceso</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="mb-3">
                                                    <i class="fas fa-info-circle text-warning me-2"></i>Al finalizar, se creará automáticamente el siguiente proceso.
                                                    @if(!isset($siguienteEtapa) || !$siguienteEtapa)
                                                        <strong class="text-success d-block mt-2">⚠️ Este es el último proceso. Al finalizarlo el arbitraje se marcará como TERMINADO.</strong>
                                                    @endif
                                                </p>
                                                <form id="formFinalizarProceso{{ $proceso->id_proceso_de_arbitraje }}"
                                                      action="{{ route('arbitraje.siguiente.proceso', $arbitraje->id_arbitraje) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="proceso_actual_id" value="{{ $proceso->id_proceso_de_arbitraje }}">
                                                    <button type="button" class="btn btn-warning btn-finalizar-proceso"
                                                            data-proceso-nombre="{{ $etapaNombre }}"
                                                            data-proceso-id="{{ $proceso->id_proceso_de_arbitraje }}"
                                                            data-form-id="formFinalizarProceso{{ $proceso->id_proceso_de_arbitraje }}"
                                                            data-es-ultimo="{{ isset($siguienteEtapa) && $siguienteEtapa ? 'false' : 'true' }}">
                                                        <i class="fas fa-check-circle me-2"></i>Finalizar Proceso
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif

                                    @if($estaArchivado)
                                        <div class="alert alert-secondary text-center">
                                            <i class="fas fa-archive me-2"></i><strong>Arbitraje Archivado</strong><br>No se pueden realizar más acciones.
                                        </div>
                                    @endif

                                    {{-- ═══════════════ DOCUMENTOS ═══════════════ --}}
                                    @if($proceso->documentos && $proceso->documentos->count() > 0)
                                        <h6 class="mb-3"><i class="fas fa-paperclip me-2"></i>Documentos Adjuntos ({{ $proceso->documentos->count() }})</h6>
                                        <div class="list-group">
                                            @foreach($proceso->documentos as $documento)
                                                @php
                                                    $esVisualizable = in_array(strtolower($documento->tipo_documento), ['pdf', 'imagen']);
                                                    $esVoucher      = $documento->tipo_documento === 'voucher';
                                                    $estaAprobado   = str_contains($documento->observaciones ?? '', '[ACEPTADO]');
                                                    $estaRechazado  = str_contains($documento->observaciones ?? '', '[RECHAZADO]');

                                                    // ── Quién subió el documento ─────────────────────────
                                                    $uploaderDni      = optional(optional($documento->user)->persona)->dni;
                                                    $personaMatch     = $uploaderDni
                                                        ? $arbitraje->personas->firstWhere('dni', $uploaderDni)
                                                        : null;

                                                    if ($personaMatch) {
                                                        $upLabel  = $personaMatch->tipo;  // Demandante | Demandado
                                                        $upColor  = $personaMatch->tipo === 'Demandante' ? 'success' : 'warning';
                                                        $upIcono  = $personaMatch->tipo === 'Demandante' ? 'fa-user-check' : 'fa-user-shield';
                                                        $upTxtCls = $personaMatch->tipo === 'Demandante' ? '' : 'text-dark';
                                                    } else {
                                                        $upLabel  = 'Administrador';
                                                        $upColor  = 'danger';
                                                        $upIcono  = 'fa-user-tie';
                                                        $upTxtCls = '';
                                                    }
                                                    $upNombre = optional($documento->user)->name ?? 'N/A';
                                                @endphp

                                                <div class="list-group-item">
                                                    <div class="row align-items-center">

                                                        {{-- Ícono tipo --}}
                                                        <div class="col-md-1 text-center">
                                                            @if($documento->tipo_documento === 'pdf')
                                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                            @elseif($documento->tipo_documento === 'imagen')
                                                                <i class="fas fa-file-image fa-2x text-primary"></i>
                                                            @elseif($documento->tipo_documento === 'voucher')
                                                                <i class="fas fa-receipt fa-2x text-success"></i>
                                                            @else
                                                                <i class="fas fa-external-link-alt fa-2x text-warning"></i>
                                                            @endif
                                                        </div>

                                                        {{-- Nombre + badges --}}
                                                        <div class="col-md-4">
                                                            <strong>{{ $documento->nombre_original }}</strong>

                                                            @if($esVoucher)
                                                                @if($estaAprobado)
                                                                    <span class="badge bg-success ms-1">✓ Aprobado</span>
                                                                @elseif($estaRechazado)
                                                                    <span class="badge bg-danger ms-1">✗ Rechazado</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark ms-1">⏳ Pendiente</span>
                                                                @endif
                                                            @endif

                                                            {{-- ✅ BADGE: QUIÉN SUBIÓ --}}
                                                            <div class="mt-1 d-flex align-items-center gap-1">
                                                                <span class="badge-uploader badge bg-{{ $upColor }} {{ $upTxtCls }}"
                                                                      title="{{ $upNombre }}">
                                                                    <i class="fas {{ $upIcono }} me-1"></i>{{ $upLabel }}
                                                                </span>
                                                                <small class="text-muted" style="font-size:0.7rem;">{{ $upNombre }}</small>
                                                            </div>

                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                {{ $documento->fecha_subida ? \Carbon\Carbon::parse($documento->fecha_subida)->format('d/m/Y H:i') : 'N/A' }}
                                                            </small>
                                                            @if($documento->observaciones)
                                                                <br><small class="text-info"><i class="fas fa-comment me-1"></i>{{ $documento->observaciones }}</small>
                                                            @endif
                                                        </div>

                                                        {{-- Tipo badge --}}
                                                        <div class="col-md-3">
                                                            <span class="badge {{ $esVisualizable || $esVoucher ? 'bg-secondary' : 'bg-warning text-dark' }}">
                                                                {{ strtoupper($documento->tipo_documento) }}
                                                            </span>
                                                            @if(!$esVisualizable && !$esVoucher)
                                                                <span class="badge bg-info ms-1"><i class="fas fa-link me-1"></i>Enlace</span>
                                                            @endif
                                                        </div>

                                                        {{-- Acciones --}}
                                                        <div class="col-md-4 text-end">
                                                            @if($esVisualizable || $esVoucher)
                                                                <button type="button"
                                                                        class="btn btn-sm btn-outline-primary me-2 btn-ver-documento"
                                                                        data-documento-id="{{ $documento->id_proceso_arbitraje_documento }}"
                                                                        data-documento-nombre="{{ $documento->nombre_original }}"
                                                                        data-documento-tipo="{{ $documento->tipo_documento }}"
                                                                        data-documento-ruta="{{ asset($documento->ruta_archivo) }}"
                                                                        data-documento-fecha="{{ $documento->fecha_subida ? \Carbon\Carbon::parse($documento->fecha_subida)->format('d/m/Y H:i') : 'N/A' }}"
                                                                        data-subido-por-label="{{ $upLabel }}"
                                                                        data-subido-por-color="{{ $upColor }}"
                                                                        data-subido-por-icono="{{ $upIcono }}"
                                                                        data-subido-por-nombre="{{ $upNombre }}">
                                                                    <i class="fas fa-eye me-1"></i>Ver
                                                                </button>
                                                                <a href="{{ asset($documento->ruta_archivo) }}" download="{{ $documento->nombre_original }}" class="btn btn-sm btn-danger me-2">
                                                                    <i class="fas fa-download me-1"></i>Descargar
                                                                </a>
                                                            @else
                                                                <a href="{{ $documento->ruta_archivo }}" target="_blank" class="btn btn-sm btn-outline-warning me-2">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Abrir
                                                                </a>
                                                            @endif
                                                            <button type="button" class="btn btn-sm btn-outline-info btn-comentar"
                                                                    data-documento-id="{{ $documento->id_proceso_arbitraje_documento }}"
                                                                    data-documento-nombre="{{ $documento->nombre_original }}"
                                                                    data-observaciones="{{ $documento->observaciones ?? '' }}">
                                                                <i class="fas fa-comment me-1"></i>Comentar
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted text-center py-3"><i class="fas fa-info-circle me-2"></i>No hay documentos adjuntos en este proceso</p>
                                    @endif
                                    {{-- ══════════════════════════════════════════ --}}

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-3"><i class="fas fa-info-circle me-2"></i>No hay procesos registrados</p>
            @endif
        </div>
    </div>
</div>

{{-- ════════════════════ MODALES ════════════════════ --}}

<!-- Modal Comentar -->
<div class="modal fade" id="comentarDocumentoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-comment me-2"></i>Agregar Comentario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="comentarDocumentoForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info mb-3"><small><strong>Documento:</strong> <span id="comentar_documento_nombre"></span></small></div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones / Comentarios</label>
                        <textarea class="form-control" name="observaciones" rows="4" placeholder="Escriba sus comentarios..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Documento -->
<div class="modal fade" id="modalDocumento" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-file me-2"></i>Vista Previa del Documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div id="imagenPreview" class="d-none">
                                    <div class="image-viewer-wrapper">
                                        <div class="image-container"><img id="imagenDocumento" src="" alt="Documento" class="zoomable-image"></div>
                                        <div class="d-flex justify-content-center align-items-center mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="zoomIn"><i class="fas fa-search-plus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="zoomOut"><i class="fas fa-search-minus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="rotateLeft"><i class="fas fa-undo"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="rotateRight"><i class="fas fa-redo"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="resetImage"><i class="fas fa-sync-alt"></i></button>
                                            <span class="badge bg-info mx-2" id="zoomLevel">100%</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="pdfPreview" class="d-none">
                                    <iframe id="pdfIframe" src="" width="100%" height="500px" class="border rounded"></iframe>
                                </div>
                                <div id="voucherPreview" class="d-none">
                                    <div class="voucher-viewer-wrapper">
                                        <div class="image-container"><img id="voucherImagen" src="" alt="Voucher" class="zoomable-image"></div>
                                        <div class="d-flex justify-content-center align-items-center mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="zoomInVoucher"><i class="fas fa-search-plus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="zoomOutVoucher"><i class="fas fa-search-minus"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="rotateLeftVoucher"><i class="fas fa-undo"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="rotateRightVoucher"><i class="fas fa-redo"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mx-1" id="resetImageVoucher"><i class="fas fa-sync-alt"></i></button>
                                            <span class="badge bg-info mx-2" id="zoomLevelVoucher">100%</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="loadingPreview" class="d-none">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                        <div class="spinner-border text-danger" role="status"></div>
                                        <p class="mt-3 text-muted">Cargando documento...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light"><h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h6></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Nombre</label>
                                    <div class="p-2 bg-light rounded"><strong id="infoNombre"></strong></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Tipo</label>
                                    <div class="p-2 bg-light rounded"><span id="infoTipo" class="badge bg-secondary"></span></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Fecha de subida</label>
                                    <div class="p-2 bg-light rounded"><span id="infoFecha"></span></div>
                                </div>
                                {{-- ✅ QUIÉN SUBIÓ en panel lateral del modal --}}
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Subido por</label>
                                    <div class="p-2 bg-light rounded" id="infoSubidoPor">—</div>
                                </div>
                                <div class="mt-4">
                                    <h6 class="border-bottom pb-2">Acciones</h6>
                                    <a href="#" id="btnDescargarModal" class="btn btn-danger w-100"><i class="fas fa-download me-2"></i>Descargar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row w-100" id="modalFooterActions" style="display:none !important;">
                    <div class="col-md-4"><button type="button" class="btn btn-success w-100" id="btnAceptarVoucher"><i class="fas fa-check-circle me-2"></i>Aceptar Voucher</button></div>
                    <div class="col-md-4"><button type="button" class="btn btn-danger w-100" id="btnRechazarVoucher"><i class="fas fa-times-circle me-2"></i>Rechazar Voucher</button></div>
                    <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Cerrar</button></div>
                </div>
                <button type="button" class="btn btn-outline-secondary" id="btnCerrarModal" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div id="toastCopiado" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body"><i class="fas fa-check-circle me-2"></i><span id="toastMessage"></span></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// FIX focus trap Bootstrap + SweetAlert2
document.addEventListener('focusin', e => { if (e.target.closest('.swal2-container')) e.stopImmediatePropagation(); });

let documentoActual = null, arbitrajeActual = {{ $arbitraje->id_arbitraje }}, arbitrajeActualEstado = '{{ $arbitraje->estado }}';
let imgZoom = 1, imgRotate = 0, voucherZoom = 1, voucherRotate = 0;

function abrirModalDocumento(documento) {
    documentoActual = documento;
    document.getElementById('infoNombre').textContent = documento.nombre;
    document.getElementById('infoTipo').textContent   = documento.tipo.toUpperCase();
    document.getElementById('infoFecha').textContent  = documento.fecha;

    // ✅ Panel lateral: quién subió
    const colorMap = { success:'#28a745', warning:'#ffc107', danger:'#dc3545', secondary:'#6c757d' };
    const textMap  = { warning:'#000' };
    const el = document.getElementById('infoSubidoPor');
    el.innerHTML = documento.subidoPorLabel
        ? `<span class="badge" style="background:${colorMap[documento.subidoPorColor]||'#6c757d'};color:${textMap[documento.subidoPorColor]||'#fff'};font-size:.75rem">
               <i class="fas ${documento.subidoPorIcono} me-1"></i>${documento.subidoPorLabel}
           </span> <small class="text-muted ms-1">${documento.subidoPorNombre}</small>`
        : '—';

    const dl = document.getElementById('btnDescargarModal');
    dl.href = documento.ruta; dl.setAttribute('download', documento.nombre);
    ['imagenPreview','pdfPreview','voucherPreview'].forEach(id => document.getElementById(id).classList.add('d-none'));
    document.getElementById('loadingPreview').classList.remove('d-none');
    resetZoomState();

    const fa = document.getElementById('modalFooterActions'), bc = document.getElementById('btnCerrarModal');
    const esVoucher = documento.tipo === 'voucher';
    if (esVoucher && arbitrajeActualEstado === 'validando') { fa.style.cssText='display:flex !important;'; bc.classList.add('d-none'); }
    else { fa.style.cssText='display:none !important;'; bc.classList.remove('d-none'); }

    new bootstrap.Modal(document.getElementById('modalDocumento')).show();
    setTimeout(() => {
        document.getElementById('loadingPreview').classList.add('d-none');
        if (documento.tipo === 'pdf') { document.getElementById('pdfIframe').src = documento.ruta; document.getElementById('pdfPreview').classList.remove('d-none'); }
        else if (documento.tipo === 'voucher') { const i = document.getElementById('voucherImagen'); i.src = documento.ruta; i.onload = () => document.getElementById('voucherPreview').classList.remove('d-none'); document.getElementById('voucherPreview').classList.remove('d-none'); }
        else { const i = document.getElementById('imagenDocumento'); i.src = documento.ruta; i.onload = () => document.getElementById('imagenPreview').classList.remove('d-none'); document.getElementById('imagenPreview').classList.remove('d-none'); }
    }, 300);
}

function applyTransform(el, z, r, s) { el.style.transform=`scale(${z}) rotate(${r}deg)`; if(s) s.textContent=Math.round(z*100)+'%'; }
function resetZoomState() {
    imgZoom=1;imgRotate=0;voucherZoom=1;voucherRotate=0;
    const i1=document.getElementById('imagenDocumento'), i2=document.getElementById('voucherImagen');
    if(i1) i1.style.transform='scale(1) rotate(0deg)';
    if(i2) i2.style.transform='scale(1) rotate(0deg)';
    const s1=document.getElementById('zoomLevel'), s2=document.getElementById('zoomLevelVoucher');
    if(s1) s1.textContent='100%'; if(s2) s2.textContent='100%';
}
function setupZoom() {
    const img = () => document.getElementById('imagenDocumento'), sl = () => document.getElementById('zoomLevel');
    const vch = () => document.getElementById('voucherImagen'),   sv = () => document.getElementById('zoomLevelVoucher');
    document.getElementById('zoomIn')?.addEventListener('click',()=>{imgZoom=Math.min(imgZoom+.1,3);applyTransform(img(),imgZoom,imgRotate,sl());});
    document.getElementById('zoomOut')?.addEventListener('click',()=>{imgZoom=Math.max(imgZoom-.1,.3);applyTransform(img(),imgZoom,imgRotate,sl());});
    document.getElementById('rotateLeft')?.addEventListener('click',()=>{imgRotate-=90;applyTransform(img(),imgZoom,imgRotate,null);});
    document.getElementById('rotateRight')?.addEventListener('click',()=>{imgRotate+=90;applyTransform(img(),imgZoom,imgRotate,null);});
    document.getElementById('resetImage')?.addEventListener('click',()=>{imgZoom=1;imgRotate=0;applyTransform(img(),1,0,sl());});
    document.getElementById('zoomInVoucher')?.addEventListener('click',()=>{voucherZoom=Math.min(voucherZoom+.1,3);applyTransform(vch(),voucherZoom,voucherRotate,sv());});
    document.getElementById('zoomOutVoucher')?.addEventListener('click',()=>{voucherZoom=Math.max(voucherZoom-.1,.3);applyTransform(vch(),voucherZoom,voucherRotate,sv());});
    document.getElementById('rotateLeftVoucher')?.addEventListener('click',()=>{voucherRotate-=90;applyTransform(vch(),voucherZoom,voucherRotate,null);});
    document.getElementById('rotateRightVoucher')?.addEventListener('click',()=>{voucherRotate+=90;applyTransform(vch(),voucherZoom,voucherRotate,null);});
    document.getElementById('resetImageVoucher')?.addEventListener('click',()=>{voucherZoom=1;voucherRotate=0;applyTransform(vch(),1,0,sv());});
}

function procesarVoucher(accion, motivo=null) {
    Swal.fire({title:'Procesando...',allowOutsideClick:false,showConfirmButton:false,didOpen:()=>Swal.showLoading()});
    const fd=new FormData(); fd.append('accion',accion); if(motivo) fd.append('motivo',motivo);
    fetch(`/arbitrajes/${arbitrajeActual}/voucher/${documentoActual.id}/procesar`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'},body:fd})
    .then(r=>r.json()).then(data=>{ if(data.success){Swal.fire({title:'¡Éxito!',text:data.message,icon:'success',confirmButtonColor:'#28a745'}).then(()=>window.location.reload());}else{Swal.fire({title:'Error',text:data.message||'Error',icon:'error',confirmButtonColor:'#dc3545'});}})
    .catch(err=>Swal.fire({title:'Error',text:err.message,icon:'error',confirmButtonColor:'#dc3545'}));
}

function archivarArbitraje() {
    Swal.fire({title:'¿Archivar arbitraje?',html:`<div class="text-start"><p><strong>Arbitraje:</strong> #{{ $arbitraje->id_arbitraje }} - {{ $arbitraje->nombre_materia }}</p><div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><strong>¡ATENCIÓN!</strong><ul class="mb-0 mt-2"><li>NO se podrán subir más documentos</li><li>NO se podrán crear nuevos procesos</li></ul></div><div class="form-check mt-3"><input class="form-check-input" type="checkbox" id="confirmarArchivar"><label class="form-check-label" for="confirmarArchivar">Confirmo que deseo ARCHIVAR este arbitraje</label></div></div>`,
        icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',cancelButtonColor:'#6c757d',confirmButtonText:'Sí, archivar',cancelButtonText:'Cancelar',
        preConfirm:()=>{ if(!document.getElementById('confirmarArchivar').checked){Swal.showValidationMessage('Debes marcar la casilla');return false;} return true; }
    }).then(result=>{ if(!result.isConfirmed) return;
        Swal.fire({title:'Archivando...',allowOutsideClick:false,allowEscapeKey:false,showConfirmButton:false,didOpen:()=>Swal.showLoading()});
        fetch(`/arbitrajes/{{ $arbitraje->id_arbitraje }}/archivar`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest','Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({})})
        .then(r=>r.json()).then(data=>{ if(data.success){Swal.fire({title:'¡Archivado!',text:data.message,icon:'success',confirmButtonColor:'#28a745'}).then(()=>window.location.reload());}else{Swal.fire({title:'Error',text:data.message||'Error',icon:'error',confirmButtonColor:'#dc3545'});}})
        .catch(err=>Swal.fire({title:'Error',text:err.message,icon:'error',confirmButtonColor:'#dc3545'})); });
}

async function subirDocumento(form, arbitrajeId) {
    const btn=form.querySelector('button[type="submit"]'), orig=btn.innerHTML, fd=new FormData(form);
    const tipo=form.querySelector('select[name="tipo_documento"]').value;
    if(!tipo){Swal.fire('Error','Seleccione el tipo de documento','error');return false;}
    const nombre=form.querySelector('input[name="nombre_documento"]').value.trim();
    if(!nombre){Swal.fire('Error','Ingrese el nombre del documento','error');return false;}
    if(tipo==='archivo'){const a=form.querySelector('input[name="archivo"]').files[0];if(!a){Swal.fire('Error','Seleccione un archivo','error');return false;}if(a.size>20*1024*1024){Swal.fire('Error','Máx. 20MB','error');return false;}}
    if(tipo==='link'){const l=form.querySelector('input[name="link"]').value.trim();if(!l){Swal.fire('Error','Ingrese el enlace','error');return false;}if(!l.startsWith('http://') && !l.startsWith('https://')){Swal.fire('Error','El enlace debe comenzar con http:// o https://','error');return false;}}
    btn.innerHTML='<i class="fas fa-spinner fa-spin me-2"></i>Subiendo...'; btn.disabled=true;
    try {
        const r=await fetch(`/arbitraje/${arbitrajeId}/documentos`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:fd});
        const data=await r.json();
        if(data.success){Swal.fire({title:'¡Éxito!',text:'Documento subido',icon:'success',confirmButtonColor:'#28a745'}).then(()=>window.location.reload());return true;}
        else{Swal.fire({title:'Error',text:data.message||'Error',icon:'error',confirmButtonColor:'#dc3545'});btn.innerHTML=orig;btn.disabled=false;return false;}
    } catch(err){Swal.fire({title:'Error',text:'Conexión: '+err.message,icon:'error',confirmButtonColor:'#dc3545'});btn.innerHTML=orig;btn.disabled=false;return false;}
}

let comentarModalInstance=null;
function initComentarModal(){const el=document.getElementById('comentarDocumentoModal');if(el) comentarModalInstance=new bootstrap.Modal(el);}
function abrirModalComentar(id,nombre,obs){
    document.getElementById('comentar_documento_nombre').textContent=nombre;
    document.querySelector('#comentarDocumentoForm textarea[name="observaciones"]').value=obs||'';
    const f=document.getElementById('comentarDocumentoForm');f.dataset.documentoId=id;f.action=`/documentos/${id}/comentar`;
    if(comentarModalInstance) comentarModalInstance.show(); else{initComentarModal();comentarModalInstance?.show();}
}

document.addEventListener('DOMContentLoaded', function () {
    setupZoom(); initComentarModal();
    document.getElementById('btnArchivarArbitraje')?.addEventListener('click', archivarArbitraje);

    document.querySelectorAll('.tipo-documento-select').forEach(sel => {
        const pid=sel.dataset.procesoId, ca=document.getElementById(`campo_archivo_proceso${pid}`), cl=document.getElementById(`campo_link_proceso${pid}`);
        sel.addEventListener('change', function(){
            if(ca) ca.style.display=this.value==='archivo'?'block':'none';
            if(cl) cl.style.display=this.value==='link'?'block':'none';
            ca?.querySelector('input[name="archivo"]') && (ca.querySelector('input[name="archivo"]').required=this.value==='archivo');
            cl?.querySelector('input[name="link"]') && (cl.querySelector('input[name="link"]').required=this.value==='link');
        });
    });

    document.querySelectorAll('.form-subir-documento').forEach(f => {
        f.addEventListener('submit', async function(e){ e.preventDefault(); await subirDocumento(this, this.dataset.arbitrajeId); });
    });

    // ✅ Botón VER — pasa los datos de quién subió al modal
    document.addEventListener('click', function(e){
        const b=e.target.closest('.btn-ver-documento'); if(!b) return;
        e.preventDefault(); e.stopPropagation();
        abrirModalDocumento({
            id: b.dataset.documentoId, nombre: b.dataset.documentoNombre,
            tipo: b.dataset.documentoTipo, ruta: b.dataset.documentoRuta, fecha: b.dataset.documentoFecha,
            subidoPorLabel:  b.dataset.subidoPorLabel  || '',
            subidoPorColor:  b.dataset.subidoPorColor  || 'secondary',
            subidoPorIcono:  b.dataset.subidoPorIcono  || 'fa-user',
            subidoPorNombre: b.dataset.subidoPorNombre || '',
        });
    });

    document.addEventListener('click', function(e){
        const b=e.target.closest('.btn-comentar'); if(!b) return;
        e.preventDefault(); e.stopPropagation();
        if(!b.dataset.documentoId){Swal.fire('Error','No se pudo identificar el documento','error');return;}
        abrirModalComentar(b.dataset.documentoId, b.dataset.documentoNombre, b.dataset.observaciones||'');
    });

    document.getElementById('comentarDocumentoForm')?.addEventListener('submit', function(e){
        e.preventDefault();
        const id=this.dataset.documentoId, txt=this.querySelector('textarea[name="observaciones"]').value.trim();
        if(!txt){Swal.fire('Error','Debe escribir un comentario','error');return;}
        Swal.fire({title:'Guardando...',allowOutsideClick:false,showConfirmButton:false,didOpen:()=>Swal.showLoading()});
        fetch(`/documentos/${id}/comentar`,{method:'PUT',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest','Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({observaciones:txt})})
        .then(r=>r.json()).then(data=>{
            if(data.success){Swal.fire({title:'¡Éxito!',text:data.message||'Guardado',icon:'success',confirmButtonColor:'#28a745'}).then(()=>{comentarModalInstance?.hide();window.location.reload();});}
            else{Swal.fire({title:'Error',text:data.message||'Error',icon:'error',confirmButtonColor:'#dc3545'});}
        }).catch(err=>Swal.fire({title:'Error',text:'Conexión: '+err.message,icon:'error',confirmButtonColor:'#dc3545'}));
    });

    document.getElementById('btnAceptarVoucher')?.addEventListener('click',function(){
        if(!documentoActual) return;
        Swal.fire({title:'¿Aceptar voucher?',text:'Se iniciará el proceso de arbitraje.',icon:'question',showCancelButton:true,confirmButtonColor:'#28a745',cancelButtonColor:'#6c757d',confirmButtonText:'Sí, aceptar',cancelButtonText:'Cancelar'})
        .then(r=>{if(r.isConfirmed) procesarVoucher('aceptar');});
    });

    document.getElementById('btnRechazarVoucher')?.addEventListener('click',function(){
        if(!documentoActual) return;
        Swal.fire({title:'Rechazar voucher',html:`<div class="text-start"><p>Indica el motivo:</p><textarea id="motivoRechazo" class="form-control" rows="4" placeholder="Ej: La imagen es ilegible..."></textarea></div>`,
            icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',cancelButtonColor:'#6c757d',confirmButtonText:'Rechazar',cancelButtonText:'Cancelar',
            didOpen:()=>{ document.getElementById('motivoRechazo')?.focus(); },
            preConfirm:()=>{ const m=document.getElementById('motivoRechazo').value.trim(); if(!m){Swal.showValidationMessage('Debes ingresar un motivo');return false;} return m; }
        }).then(r=>{if(r.isConfirmed) procesarVoucher('rechazar',r.value);});
    });

    document.querySelectorAll('.btn-finalizar-proceso').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.preventDefault();
            const nombre=this.dataset.procesoNombre, pid=this.dataset.procesoId, fid=this.dataset.formId, esUlt=this.dataset.esUltimo==='true';
            const form=document.getElementById(fid);
            const msg=esUlt
                ?'<div class="alert alert-success mt-3"><i class="fas fa-trophy me-2"></i>Este es el último proceso. El arbitraje se marcará como TERMINADO.</div>'
                :'<div class="alert alert-info mt-3"><i class="fas fa-info-circle me-2"></i>Se creará automáticamente el siguiente proceso.</div>';
            Swal.fire({title:'¿Finalizar proceso?',
                html:`<div class="text-start"><p class="mb-3"><strong>Proceso:</strong> ${nombre}</p>${msg}<div class="form-check mt-3"><input class="form-check-input" type="checkbox" id="confirmarProceso${pid}"><label class="form-check-label" for="confirmarProceso${pid}">Confirmo finalizar este proceso</label></div></div>`,
                icon:'question',showCancelButton:true,confirmButtonColor:'#ffc107',cancelButtonColor:'#6c757d',confirmButtonText:'Sí, finalizar',cancelButtonText:'Cancelar',
                preConfirm:()=>{ if(!document.getElementById(`confirmarProceso${pid}`).checked){Swal.showValidationMessage('Debes marcar la casilla');return false;} return true; }
            }).then(result=>{
                if(!result.isConfirmed) return;
                Swal.fire({title:'Procesando...',allowOutsideClick:false,allowEscapeKey:false,showConfirmButton:false,didOpen:()=>Swal.showLoading()});
                fetch(form.action,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:new URLSearchParams(new FormData(form))})
                .then(r=>r.json()).then(data=>{
                    if(data.success){Swal.fire({title:'¡Éxito!',html:`<div class="text-start"><p>${data.message}</p>${data.hay_siguiente===false?'<hr><div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Arbitraje marcado como TERMINADO.</div>':'<hr><div class="alert alert-info"><i class="fas fa-arrow-right me-2"></i>Siguiente proceso creado automáticamente.</div>'}</div>`,icon:'success',confirmButtonColor:'#28a745'}).then(()=>window.location.reload());}
                    else{Swal.fire({title:'Error',text:data.message||'Error',icon:'error',confirmButtonColor:'#dc3545'});}
                }).catch(err=>Swal.fire({title:'Error',text:err.message,icon:'error',confirmButtonColor:'#dc3545'}));
            });
        });
    });
});
</script>
@endsection