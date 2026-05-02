@extends('Admin.app')

@section('title', 'Detalle de JRD #' . $jrd->id_jrd)
@section('page-title', 'Detalle de JRD')

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
                        {{ $jrd->numero_expediente ? "Expediente N° {$jrd->numero_expediente}" : $jrd->nombre_materia }}
                    </h4>
                    @if($jrd->numero_expediente && $jrd->nombre_materia)
                        <br><small class="text-light">Materia: {{ $jrd->nombre_materia }}</small>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    @php
                        $estadoClass = match(strtolower($jrd->estado)) {
                            'en proceso' => 'bg-warning text-dark',
                            'terminado'  => 'bg-success',
                            'observado'  => 'bg-info',
                            'archivado'  => 'bg-secondary',
                            default      => 'bg-secondary'
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
                        @if($jrd->numero_expediente)
                        <tr>
                            <th width="40%">Número de Expediente:</th>
                            <td>
                                <span class="badge bg-dark">{{ $jrd->numero_expediente }}</span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th width="40%">Peticiones:</th>
                            <td>{{ $jrd->pretenciones ?? 'No especificadas' }}</td>
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
                        @if($jrd->cuantia)
                        <tr>
                            <th>Cuantia:</th>
                            <td>{{ $jrd->cuantia }}</td>
                        </tr>
                        @endif
                        @if($jrd->cuantia)
                        <tr>
                            <th>Tasa de Solicitud:</th>
                            <td>{{ $jrd->tasa_solicitud }}</td>
                        </tr>
                        @endif
                        @if($jrd->controversia)
                        <tr>
                            <th>Controversia:</th>
                            <td>{{ $jrd->controversia }}</td>
                        </tr>
                        @endif
                        @if($jrd->designacion_adjudicadores)
                        <tr>
                            <th>Designación Adjudicadores:</th>
                            <td>{{ $jrd->designacion_adjudicadores }}</td>
                        </tr>
                        @endif
                        @if($jrd->fundamentos_hecho)
                        <tr>
                            <th>Fundamentos de hecho:</th>
                            <td>{{ $jrd->fundamentos_hecho }}</td>
                        </tr>
                        @endif
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
                            <div class="card border-start border-4 {{ $persona->tipo === 'Solicitante' ? 'border-success' : 'border-warning' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge {{ $persona->tipo === 'Solicitante' ? 'bg-success' : 'bg-warning text-dark' }} mb-2">
                                                {{ $persona->tipo }}
                                            </span>
                                            <h6 class="mb-0">DNI: {{ $persona->dni }}</h6>
                                            @if($persona->nombres)
                                                <small class="text-muted">{{ $persona->nombres }} {{ $persona->apellidos }}</small>
                                            @endif
                                            @if($persona->correo)
                                                <br><small class="text-muted"><i class="fas fa-envelope me-1"></i>{{ $persona->correo }}</small>
                                            @endif
                                            @if($persona->telefono)
                                                <br><small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $persona->telefono }}</small>
                                            @endif
                                             @if($persona->direccion)
                                                <br><small class="text-muted"><i class="fas fa-home me-1"></i>{{ $persona->direccion }}</small>
                                            @endif
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

    <!-- Procesos / Etapas -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-tasks text-primary me-2"></i>
                Etapas del Proceso ({{ $jrd->procesos->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if($jrd->procesos && $jrd->procesos->count() > 0)

                {{-- Ordenar: activo/observado primero, luego el resto por fecha desc --}}
                @php
                    $procesosOrdenados = $jrd->procesos->sortByDesc(function($p) {
                        // Prioridad: activo=2, observado=1, resto=0; luego por fecha desc
                        $prioridad = 0;
                        if ($p->estado === 'activo')    $prioridad = 2;
                        if ($p->estado === 'observado') $prioridad = 1;
                        return $prioridad . '_' . $p->fecha_creacion?->timestamp;
                    });
                @endphp

                <div class="accordion" id="accordionProcesos">
                    @foreach($procesosOrdenados as $proceso)
                        @php
                            $esProcesoActivo      = $proceso->estado === 'activo';
                            $esProcesoObservado   = $proceso->estado === 'observado';
                            $tieneVoucher         = $proceso->documentos->contains('tipo_documento', 'voucher');
                            $mostrarAccionesAdmin = ($esProcesoActivo || $esProcesoObservado)
                                                    && $jrd->estado !== 'archivado'
                                                    && $jrd->estado !== 'terminado';

                            // Abrir si es activo u observado (el que requiere atención)
                            $debeEstarAbierto = $esProcesoActivo || $esProcesoObservado;

                            $estadoProcesoClass = 'bg-secondary';
                            if ($proceso->estado === 'activo')      $estadoProcesoClass = 'bg-primary';
                            elseif ($proceso->estado === 'finalizado') $estadoProcesoClass = 'bg-success';
                            elseif ($proceso->estado === 'observado')  $estadoProcesoClass = 'bg-warning text-dark';
                        @endphp

                        <div class="accordion-item mb-3 border {{ $esProcesoObservado ? 'border-warning border-2' : '' }}">
                            <h2 class="accordion-header" id="heading{{ $proceso->id_proceso_jrd }}">
                                <button class="accordion-button {{ $debeEstarAbierto ? '' : 'collapsed' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $proceso->id_proceso_jrd }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <i class="fas fa-layer-group text-primary me-2"></i>
                                            <strong>{{ $proceso->etapa->nombre ?? 'Sin etapa' }}</strong>
                                            @if($esProcesoActivo)
                                                <span class="badge bg-success ms-2">ACTIVO</span>
                                            @endif
                                            @if($esProcesoObservado)
                                                <span class="badge bg-warning text-dark ms-2">OBSERVADO</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Inicio: {{ $proceso->fecha_creacion ? $proceso->fecha_creacion->format('d/m/Y H:i') : 'Sin fecha' }}
                                                @if($proceso->fecha_finalizacion)
                                                    | Fin: {{ $proceso->fecha_finalizacion->format('d/m/Y H:i') }}
                                                @endif
                                            </small>
                                        </div>
                                        <span class="badge {{ $estadoProcesoClass }}">
                                            {{ ucfirst($proceso->estado) }}
                                        </span>
                                    </div>
                                </button>
                            </h2>

                            <div id="collapse{{ $proceso->id_proceso_jrd }}"
                                 class="accordion-collapse collapse {{ $debeEstarAbierto ? 'show' : '' }}">
                                <div class="accordion-body">

                                    <!-- SUBIR DOCUMENTOS (ADMIN) -->
                                    @if($mostrarAccionesAdmin)
                                        <div class="card border-primary mb-4">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Subir Documento a esta Etapa</h6>
                                            </div>
                                            <div class="card-body">
                                                <form class="form-subir-documento row g-3"
                                                      data-jrd-id="{{ $jrd->id_jrd }}"
                                                      data-proceso-id="{{ $proceso->id_proceso_jrd }}">
                                                    @csrf
                                                    <div class="col-md-6">
                                                        <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                                                        <select class="form-select" name="tipo_documento" required>
                                                            <option value="archivo">📄 Subir Archivo (PDF, JPG, PNG)</option>
                                                            <option value="link">🔗 Enlace (Google Drive, Dropbox)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Nombre del Documento <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="nombre_documento" required>
                                                    </div>
                                                    <div class="col-md-12 campo-archivo">
                                                        <label class="form-label">Archivo <span class="text-danger">*</span></label>
                                                        <input type="file" class="form-control" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                                                        <small class="text-muted">Formatos: PDF, JPG, PNG (Máx. 20MB)</small>
                                                    </div>
                                                    <div class="col-md-12 campo-link" style="display:none;">
                                                        <label class="form-label">Enlace <span class="text-danger">*</span></label>
                                                        <input type="url" class="form-control" name="link" placeholder="https://drive.google.com/...">
                                                    </div>
                                                    <div class="col-md-10">
                                                        <label class="form-label">Observaciones (opcional)</label>
                                                        <input type="text" class="form-control" name="observaciones" placeholder="Comentario sobre el documento">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="submit" class="btn btn-primary w-100 mt-4">
                                                            <i class="fas fa-upload me-2"></i>Subir
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- GESTIÓN DE ETAPA -->
                                    @if($esProcesoActivo && $jrd->estado !== 'archivado' && $jrd->estado !== 'terminado')
                                        <div class="card border-warning mb-4">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0"><i class="fas fa-forward me-2"></i>Gestión de Etapa</h6>
                                            </div>
                                            <div class="card-body">
                                                @if($tieneVoucher)
                                                    <p class="mb-3">
                                                        <i class="fas fa-receipt text-success me-2"></i>
                                                        Esta etapa contiene un voucher. Valide si es correcto para continuar.
                                                    </p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-success btn-validar-voucher"
                                                                data-jrd-id="{{ $jrd->id_jrd }}"
                                                                data-proceso-id="{{ $proceso->id_proceso_jrd }}">
                                                            <i class="fas fa-check-circle me-2"></i>Aprobar Voucher y Avanzar
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-rechazar-voucher"
                                                                data-jrd-id="{{ $jrd->id_jrd }}">
                                                            <i class="fas fa-times-circle me-2"></i>Rechazar Voucher
                                                        </button>
                                                    </div>
                                                @else
                                                    <p class="mb-3">
                                                        <i class="fas fa-info-circle text-info me-2"></i>
                                                        Esta etapa no requiere validación de voucher. Puede avanzar a la siguiente etapa.
                                                    </p>
                                                    <button type="button" class="btn btn-primary btn-pasar-siguiente"
                                                            data-jrd-id="{{ $jrd->id_jrd }}"
                                                            data-proceso-id="{{ $proceso->id_proceso_jrd }}">
                                                        <i class="fas fa-forward me-2"></i>Pasar a la Siguiente Etapa
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- DOCUMENTOS DEL PROCESO -->
                                    <h6 class="mb-3 mt-3">
                                        <i class="fas fa-paperclip me-2"></i>
                                        Documentos ({{ $proceso->documentos->count() }})
                                    </h6>
                                    @if($proceso->documentos && $proceso->documentos->count() > 0)
                                        <div class="list-group">
                                            @foreach($proceso->documentos as $documento)
                                                <div class="list-group-item">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-1 text-center">
                                                            @if($documento->tipo_documento === 'voucher')
                                                                <i class="fas fa-receipt fa-2x text-success"></i>
                                                            @elseif($documento->tipo_documento === 'pdf')
                                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                            @elseif($documento->tipo_documento === 'imagen')
                                                                <i class="fas fa-file-image fa-2x text-primary"></i>
                                                            @else
                                                                <i class="fas fa-external-link-alt fa-2x text-warning"></i>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-7">
                                                            <strong>{{ $documento->nombre_original }}</strong>
                                                            @if($documento->tipo_documento === 'voucher')
                                                                <span class="badge bg-success ms-2">VOUCHER</span>
                                                            @endif
                                                            <br>
                                                                @php
                                                                    $uploaderDni  = $documento->user?->persona?->dni ?? null;
                                                                    $personaJrd   = $uploaderDni
                                                                        ? $jrd->personas->firstWhere('dni', $uploaderDni)
                                                                        : null;

                                                                    $rolUploader  = 'Usuario';
                                                                    if ($personaJrd) {
                                                                        $rolUploader = $personaJrd->tipo; // Solicitante, Demandado, Contraparte, etc.
                                                                    } elseif ($jrd->user_id == $documento->user_id) {
                                                                        $rolUploader = 'Creador';
                                                                    } else {
                                                                        $rolSistema = strtolower($documento->user?->rol ?? '');
                                                                        if ($rolSistema === 'admin')          $rolUploader = 'Administrador';
                                                                        elseif ($rolSistema === 'mesa_partes') $rolUploader = 'Mesa de Partes';
                                                                    }

                                                                    $rolBadgeConfig = [
                                                                        'Solicitante'    => ['color' => 'success',   'icono' => 'fa-user-check'],
                                                                        'Demandado'      => ['color' => 'warning',   'icono' => 'fa-user-shield'],
                                                                        'Contraparte'    => ['color' => 'danger',    'icono' => 'fa-user-slash'],
                                                                        'Tercero'        => ['color' => 'secondary', 'icono' => 'fa-user-friends'],
                                                                        'Demandante'     => ['color' => 'primary',   'icono' => 'fa-user-plus'],
                                                                        'Creador'        => ['color' => 'info',      'icono' => 'fa-user-tie'],
                                                                        'Administrador'  => ['color' => 'danger',    'icono' => 'fa-user-tie'],
                                                                        'Mesa de Partes' => ['color' => 'info',      'icono' => 'fa-building'],
                                                                        'Usuario'        => ['color' => 'secondary', 'icono' => 'fa-user'],
                                                                    ];
                                                                    $cfg = $rolBadgeConfig[$rolUploader] ?? ['color' => 'secondary', 'icono' => 'fa-user'];

                                                                    $nombreUploader = trim(
                                                                        ($documento->user?->persona?->nombres   ?? '') . ' ' .
                                                                        ($documento->user?->persona?->apellidos ?? '')
                                                                    ) ?: ($documento->user?->name ?? 'Usuario');
                                                                @endphp

                                                                <small class="text-muted">
                                                                    <i class="fas fa-user me-1"></i> Subido por:
                                                                    <span class="badge bg-{{ $cfg['color'] }} {{ $cfg['color'] === 'warning' ? 'text-dark' : '' }}">
                                                                        <i class="fas {{ $cfg['icono'] }} me-1"></i>{{ $rolUploader }}
                                                                    </span>
                                                                    <strong>{{ strtoupper($nombreUploader) }}</strong>
                                                                    <br>
                                                                    <i class="fas fa-calendar me-1"></i> Fecha: {{ $documento->fecha_subida->format('d/m/Y H:i') }}
                                                                    @if($documento->observaciones)
                                                                        <br>
                                                                        <span class="observacion-doc d-inline-block mt-1 px-2 py-1 rounded">
                                                                            <i class="fas fa-comment-dots me-1 text-warning"></i>
                                                                            <strong>Obs:</strong> {{ $documento->observaciones }}
                                                                        </span>
                                                                    @endif
                                                                </small>
                                                        </div>
                                                        <div class="col-md-4 text-end">
                                                            <a href="{{ asset($documento->ruta_archivo) }}"
                                                               target="_blank"
                                                               class="btn btn-sm btn-outline-primary me-2">
                                                                <i class="fas fa-eye me-1"></i>Ver
                                                            </a>
                                                            <a href="{{ asset($documento->ruta_archivo) }}"
                                                               download
                                                               class="btn btn-sm btn-primary">
                                                                <i class="fas fa-download me-1"></i>Descargar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted text-center py-2">
                                            <i class="fas fa-info-circle me-2"></i>No hay documentos en esta etapa
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

    <!-- ARCHIVAR JRD -->
    @if($jrd->estado !== 'archivado' && $jrd->estado !== 'terminado')
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="fas fa-archive me-2"></i>Archivar JRD</h6>
            </div>
            <div class="card-body">
                <p class="mb-3">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Al archivar este JRD, no se podrán subir más documentos ni modificar procesos.
                </p>
                <button type="button" class="btn btn-danger" id="btnArchivarJrd" data-jrd-id="{{ $jrd->id_jrd }}">
                    <i class="fas fa-archive me-2"></i>Archivar JRD
                </button>
            </div>
        </div>
    @endif

</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
.accordion-button:not(.collapsed) { background-color: #f8f9fa; color: #000; }
.accordion-button:focus { box-shadow: none; }
.list-group-item { border: 1px solid #dee2e6; margin-bottom: 10px; border-radius: 5px; }
.table th { font-weight: 600; color: #6c757d; }
.badge { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; }
.card.border-primary { border-width: 2px !important; }
.card.border-warning { border-width: 2px !important; }
.btn:hover { transform: translateY(-2px); transition: transform 0.2s ease; }
.observacion-doc { background-color: #fff8e1; border-left: 3px solid #ffc107; font-size: 0.78rem; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfToken = '{{ csrf_token() }}';

document.addEventListener('DOMContentLoaded', function() {

    // ── SUBIR DOCUMENTO ──────────────────────────────────────────────
    document.querySelectorAll('.form-subir-documento').forEach(form => {
        const tipoSelect   = form.querySelector('select[name="tipo_documento"]');
        const campoArchivo = form.querySelector('.campo-archivo');
        const campoLink    = form.querySelector('.campo-link');
        const inputArchivo = form.querySelector('input[name="archivo"]');
        const inputLink    = form.querySelector('input[name="link"]');

        tipoSelect.addEventListener('change', function() {
            if (this.value === 'archivo') {
                campoArchivo.style.display = 'block';
                campoLink.style.display    = 'none';
                inputArchivo.required = true;
                inputLink.required    = false;
            } else {
                campoArchivo.style.display = 'none';
                campoLink.style.display    = 'block';
                inputArchivo.required = false;
                inputLink.required    = true;
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const jrdId           = this.dataset.jrdId;
            const procesoId       = this.dataset.procesoId;
            const tipo            = tipoSelect.value;
            const nombreDocumento = form.querySelector('input[name="nombre_documento"]').value.trim();
            const observaciones   = form.querySelector('input[name="observaciones"]').value;

            if (!nombreDocumento) { Swal.fire('Error', 'Ingrese el nombre del documento', 'error'); return; }

            const formData = new FormData();
            formData.append('proceso_id',       procesoId);
            formData.append('tipo_documento',   tipo);
            formData.append('nombre_documento', nombreDocumento);
            if (observaciones) formData.append('observaciones', observaciones);

            if (tipo === 'archivo') {
                const archivo = inputArchivo.files[0];
                if (!archivo) { Swal.fire('Error', 'Seleccione un archivo', 'error'); return; }
                if (archivo.size > 20 * 1024 * 1024) { Swal.fire('Error', 'El archivo no debe superar los 20MB', 'error'); return; }
                formData.append('archivo', archivo);
            } else {
                const link = inputLink.value.trim();
                if (!link) { Swal.fire('Error', 'Ingrese el enlace', 'error'); return; }
                formData.append('link', link);
            }

            Swal.fire({ title: 'Subiendo documento...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });

            fetch(`/jrd/${jrdId}/documentos`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) Swal.fire('Éxito', data.message, 'success').then(() => location.reload());
                else Swal.fire('Error', data.message || 'Error al subir el documento', 'error');
            })
            .catch(err => Swal.fire('Error', 'Error de conexión: ' + err.message, 'error'));
        });
    });

    // ── PASAR SIGUIENTE ETAPA ────────────────────────────────────────
    document.querySelectorAll('.btn-pasar-siguiente').forEach(btn => {
        btn.addEventListener('click', function() {
            const jrdId = this.dataset.jrdId;
            Swal.fire({
                title: '¿Pasar a la siguiente etapa?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'Sí, avanzar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (!result.isConfirmed) return;
                Swal.fire({ title: 'Procesando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
                fetch(`/jrd/${jrdId}/proceso/siguiente`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) Swal.fire('Éxito', data.message, 'success').then(() => location.reload());
                    else Swal.fire('Error', data.message, 'error');
                })
                .catch(err => Swal.fire('Error', 'Error al avanzar: ' + err.message, 'error'));
            });
        });
    });

    // ── APROBAR VOUCHER ──────────────────────────────────────────────
    document.querySelectorAll('.btn-validar-voucher').forEach(btn => {
        btn.addEventListener('click', function() {
            const jrdId = this.dataset.jrdId;
            Swal.fire({
                title: '¿Aprobar este voucher?',
                text: 'Se avanzará a la siguiente etapa del proceso.',
                icon: 'question',
                input: 'textarea',
                inputPlaceholder: 'Comentario (opcional)',
                showCancelButton: true,
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (!result.isConfirmed) return;
                Swal.fire({ title: 'Procesando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
                const formData = new FormData();
                formData.append('comentario', result.value || '');
                fetch(`/jrd/${jrdId}/voucher/aceptar`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) Swal.fire('Aprobado', data.message, 'success').then(() => location.reload());
                    else Swal.fire('Error', data.message, 'error');
                })
                .catch(() => Swal.fire('Error', 'Error al procesar la solicitud', 'error'));
            });
        });
    });

    // ── RECHAZAR VOUCHER ─────────────────────────────────────────────
    document.querySelectorAll('.btn-rechazar-voucher').forEach(btn => {
        btn.addEventListener('click', function() {
            const jrdId = this.dataset.jrdId;
            Swal.fire({
                title: '¿Rechazar este voucher?',
                text: 'El JRD será marcado como OBSERVADO.',
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Motivo del rechazo (requerido)',
                inputValidator: value => { if (!value) return 'Debe ingresar un motivo'; },
                showCancelButton: true,
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (!result.isConfirmed) return;
                Swal.fire({ title: 'Procesando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
                const formData = new FormData();
                formData.append('motivo', result.value);
                fetch(`/jrd/${jrdId}/voucher/rechazar`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) Swal.fire('Rechazado', data.message, 'success').then(() => location.reload());
                    else Swal.fire('Error', data.message, 'error');
                })
                .catch(() => Swal.fire('Error', 'Error al procesar la solicitud', 'error'));
            });
        });
    });

    // ── ARCHIVAR JRD ─────────────────────────────────────────────────
    const btnArchivar = document.getElementById('btnArchivarJrd');
    if (btnArchivar) {
        btnArchivar.addEventListener('click', function() {
            const jrdId = this.dataset.jrdId;
            Swal.fire({
                title: '¿Archivar este JRD?',
                text: 'Una vez archivado, no se podrán subir más documentos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, archivar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (!result.isConfirmed) return;
                Swal.fire({ title: 'Procesando...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
                fetch(`/jrd/${jrdId}/archivar`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) Swal.fire('Archivado', data.message, 'success').then(() => location.reload());
                    else Swal.fire('Error', data.message, 'error');
                })
                .catch(() => Swal.fire('Error', 'Error al archivar el JRD', 'error'));
            });
        });
    }

});
</script>

@endsection