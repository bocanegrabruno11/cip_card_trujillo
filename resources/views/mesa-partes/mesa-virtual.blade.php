@extends('mesa-partes.app')

@section('title', 'Mesa de Partes Virtual')
@section('page-title', 'Mesa de Partes Virtual')

@section('content')
<div class="container-fluid">
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-laptop me-2"></i> Mesa de Partes Virtual</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Seleccione el tipo de proceso y luego el expediente para subir documentos.
                    </p>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo de Proceso</label>
                            <select class="form-select" id="tipoProceso">
                                <option value="">Seleccione...</option>
                                <option value="arbitraje">📋 Arbitraje</option>
                                <option value="jrd">⚖️ JPRD</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expediente / Proceso</label>
                            <select class="form-select" id="selectExpediente" disabled>
                                <option value="">Primero seleccione un tipo de proceso</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-danger w-100" id="btnCargarProceso" disabled>
                                <i class="fas fa-folder-open me-2"></i>Cargar Proceso
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Proceso Activo -->
    <div id="panelProceso" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Proceso Activo</h5>
                    </div>
                    <div class="card-body" id="contenidoProceso">
                        <div class="text-center py-5">
                            <div class="spinner-border text-danger" role="status"></div>
                            <p class="mt-3 text-muted">Cargando información del proceso...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario para subir documentos -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-cloud-upload-alt me-2"></i> Subir Documento</h5>
                    </div>
                    <div class="card-body">
                        <form id="formSubirDocumento" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="upload_tipo" name="tipo">
                            <input type="hidden" id="upload_id" name="id">
                            <input type="hidden" id="upload_proceso_id" name="proceso_id">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                        <option value="">Seleccione...</option>
                                        <option value="archivo">📄 Subir Archivo (PDF, JPG, PNG)</option>
                                        <option value="link">🔗 Enlace (Google Drive, Dropbox, etc.)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombre del Documento <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_documento" name="nombre_documento" placeholder="Ej: Escrito de demanda, Anexos, etc." required>
                                </div>
                            </div>

                            <div id="campo_archivo" style="display:none;" class="mt-3">
                                <label class="form-label fw-semibold">Archivo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="archivo_input" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Formatos: PDF, JPG, PNG (Máx. 20MB)</small>
                            </div>

                            <div id="campo_link" style="display:none;" class="mt-3">
                                <label class="form-label fw-semibold">Enlace <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="link_input" name="link" placeholder="https://drive.google.com/...">
                                <small class="text-muted">Enlace a Google Drive, Dropbox, OneDrive, etc.</small>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-danger px-4" id="btnSubirDocumento">
                                    <i class="fas fa-upload me-2"></i> Subir Documento
                                </button>
                                <button type="button" class="btn btn-secondary px-4 ms-2" id="btnLimpiar">
                                    <i class="fas fa-eraser me-2"></i> Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos del proceso actual -->
    <div class="row mt-4" id="panelDocumentos" style="display: none;">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i> Documentos del Proceso</h5>
                </div>
                <div class="card-body" id="listaDocumentos">
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-info-circle me-2"></i> Los documentos subidos aparecerán aquí
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Mensajes -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><p id="messageModalBody"></p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .documento-item {
        transition: all 0.2s ease;
        border-left: 3px solid #AD2B2E;
    }
    .documento-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(5px);
    }
    .fecha-subida {
        font-size: 0.7rem;
        color: #6c757d;
    }
    .badge-subido-por {
        font-size: 0.68rem;
    }
    .process-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
    }
</style>
@endpush

@push('scripts')
<script>
let procesoActual = null;

// ─── Funciones auxiliares ───────────────────────────────────────────────────
function formatFecha(fecha) {
    if (!fecha) return 'No especificada';
    return new Date(fecha).toLocaleDateString('es-PE', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function formatFechaCorta(fecha) {
    if (!fecha) return 'No especificada';
    return new Date(fecha).toLocaleDateString('es-PE', {
        year: 'numeric', month: 'short', day: 'numeric'
    });
}

function showMessage(title, msg, isError = false) {
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    document.getElementById('messageModalTitle').textContent = title;
    document.getElementById('messageModalTitle').className = `modal-title text-${isError ? 'danger' : 'success'}`;
    document.getElementById('messageModalBody').textContent = msg;
    modal.show();
}

function getEstadoBadge(estado) {
    const badges = {
        'validando': 'bg-warning text-dark',
        'iniciado': 'bg-info',
        'en proceso': 'bg-primary',
        'activo': 'bg-primary',
        'terminado': 'bg-success',
        'finalizado': 'bg-success',
        'rechazado': 'bg-danger',
        'observado': 'bg-danger',
        'archivado': 'bg-secondary'
    };
    return badges[estado] || 'bg-secondary';
}

// ─── Cargar expedientes según tipo ──────────────────────────────────────────
document.getElementById('tipoProceso').addEventListener('change', function() {
    const tipo = this.value;
    const selectExpediente = document.getElementById('selectExpediente');
    const btnCargar = document.getElementById('btnCargarProceso');
    
    if (!tipo) {
        selectExpediente.disabled = true;
        selectExpediente.innerHTML = '<option value="">Primero seleccione un tipo de proceso</option>';
        btnCargar.disabled = true;
        return;
    }
    
    // Mostrar loading
    selectExpediente.disabled = false;
    selectExpediente.innerHTML = '<option value="">Cargando expedientes...</option>';
    btnCargar.disabled = true;
    
    // Cargar expedientes según tipo
    const url = tipo === 'arbitraje' 
        ? '{{ route("arbitrajes.obtener") }}'
        : '{{ route("jrd.obtener.mesapartes") }}';
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const items = tipo === 'arbitraje' ? data.arbitrajes : data.jrd;
                const procesosActivos = items.filter(item => 
                    item.estado !== 'terminado' && 
                    item.estado !== 'archivado' && 
                    item.estado !== 'finalizado'
                );
                
                if (procesosActivos.length === 0) {
                    selectExpediente.innerHTML = '<option value="">No hay procesos activos</option>';
                    btnCargar.disabled = true;
                } else {
                    let options = '<option value="">Seleccione un expediente...</option>';
                    procesosActivos.forEach(item => {
                        const titulo = item.titulo_expediente || 
                                     (item.numero_expediente ? `Expediente N° ${item.numero_expediente}` : 
                                     (item.nombre_materia || 'Sin título'));
                        options += `<option value="${item.id_arbitraje || item.id_jrd}" 
                                          data-procesos='${JSON.stringify(item.procesos || [])}'
                                          data-titulo="${titulo}"
                                          data-estado="${item.estado}">
                                        ${titulo} - ${item.estado.toUpperCase()}
                                    </option>`;
                    });
                    selectExpediente.innerHTML = options;
                    btnCargar.disabled = false;
                }
            } else {
                selectExpediente.innerHTML = '<option value="">Error al cargar datos</option>';
                btnCargar.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            selectExpediente.innerHTML = '<option value="">Error al cargar expedientes</option>';
            btnCargar.disabled = true;
            showMessage('Error', 'Error al cargar los expedientes', true);
        });
});

// ─── Cargar proceso seleccionado ────────────────────────────────────────────
document.getElementById('btnCargarProceso').addEventListener('click', function() {
    const tipo = document.getElementById('tipoProceso').value;
    const selectExpediente = document.getElementById('selectExpediente');
    const selectedOption = selectExpediente.options[selectExpediente.selectedIndex];
    const expedienteId = selectExpediente.value;
    
    if (!tipo || !expedienteId) {
        showMessage('Advertencia', 'Seleccione un tipo de proceso y un expediente', true);
        return;
    }
    
    // Obtener el proceso activo (el último proceso creado que está en estado iniciado/activo)
    const procesosData = JSON.parse(selectedOption.dataset.procesos || '[]');
    const procesoActivo = procesosData.find(p => p.estado === 'iniciado' || p.estado === 'activo') 
                         || procesosData[procesosData.length - 1];
    
    if (!procesoActivo) {
        showMessage('Error', 'No se encontró un proceso activo para este expediente', true);
        return;
    }
    
    // Guardar datos del proceso actual
    procesoActual = {
        tipo: tipo,
        id: expedienteId,
        procesoId: procesoActivo.id_proceso_de_arbitraje || procesoActivo.id_proceso_jrd,
        titulo: selectedOption.dataset.titulo,
        estado: selectedOption.dataset.estado,
        procesoNombre: procesoActivo.etapa?.nombre || 'Proceso actual'
    };
    
    // Actualizar formulario
    document.getElementById('upload_tipo').value = tipo;
    document.getElementById('upload_id').value = expedienteId;
    document.getElementById('upload_proceso_id').value = procesoActual.procesoId;
    
    // Mostrar paneles
    document.getElementById('panelProceso').style.display = 'block';
    document.getElementById('panelDocumentos').style.display = 'block';
    
    // Cargar información del proceso y documentos
    cargarInfoProceso();
    cargarDocumentos();
    
    // Scroll suave al panel
    document.getElementById('panelProceso').scrollIntoView({ behavior: 'smooth', block: 'start' });
});

// ─── Cargar información del proceso ─────────────────────────────────────────
function cargarInfoProceso() {
    const container = document.getElementById('contenidoProceso');
    container.innerHTML = `
        <div class="process-info">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="text-danger mb-3"><i class="fas fa-info-circle me-2"></i>Detalles del Proceso</h6>
                    <p><strong>📋 Tipo:</strong> ${procesoActual.tipo === 'arbitraje' ? 'Arbitraje' : 'JPRD'}</p>
                    <p><strong>📄 Expediente:</strong> ${procesoActual.titulo}</p>
                    <p><strong>⚙️ Proceso Actual:</strong> ${procesoActual.procesoNombre}</p>
                    <p><strong>📊 Estado:</strong> <span class="badge ${getEstadoBadge(procesoActual.estado)}">${procesoActual.estado.toUpperCase()}</span></p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-folder-open fa-4x text-muted opacity-50"></i>
                </div>
            </div>
        </div>
    `;
}

// ─── Cargar documentos del proceso ──────────────────────────────────────────
function cargarDocumentos() {
    const url = procesoActual.tipo === 'arbitraje'
        ? '{{ route("arbitrajes.obtener") }}'
        : '{{ route("jrd.obtener.mesapartes") }}';
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const items = procesoActual.tipo === 'arbitraje' ? data.arbitrajes : data.jrd;
                const expediente = items.find(item => 
                    (item.id_arbitraje || item.id_jrd) == procesoActual.id
                );
                
                if (expediente && expediente.procesos) {
                    const proceso = expediente.procesos.find(p => 
                        (p.id_proceso_de_arbitraje || p.id_proceso_jrd) == procesoActual.procesoId
                    );
                    
                    if (proceso && proceso.documentos && proceso.documentos.length > 0) {
                        renderDocumentos(proceso.documentos);
                    } else {
                        renderDocumentos([]);
                    }
                } else {
                    renderDocumentos([]);
                }
            }
        })
        .catch(error => {
            console.error('Error cargando documentos:', error);
            renderDocumentos([]);
        });
}

function renderDocumentos(documentos) {
    const container = document.getElementById('listaDocumentos');
    
    if (!documentos || documentos.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No hay documentos subidos en este proceso aún
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-file me-1"></i> Documento</th>
                        <th><i class="fas fa-user me-1"></i> Subido por</th>
                        <th><i class="fas fa-calendar me-1"></i> Fecha</th>
                        <th><i class="fas fa-cog me-1"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${documentos.map(doc => {
                        let icono = 'fa-file-alt';
                        if (doc.tipo_documento === 'voucher') icono = 'fa-receipt';
                        else if (doc.tipo_documento === 'pdf') icono = 'fa-file-pdf';
                        else if (doc.tipo_documento === 'imagen') icono = 'fa-file-image';
                        else if (doc.ruta_archivo && doc.ruta_archivo.includes('drive.google.com')) icono = 'fa-google-drive';
                        
                        const subidoPor = doc.subido_por 
                            ? `<span class="badge bg-${doc.subido_por.color} badge-subido-por">
                                <i class="fas ${doc.subido_por.icono} me-1"></i>${doc.subido_por.label}
                               </span><br><small class="text-muted">${doc.subido_por.nombre}</small>`
                            : '<span class="text-muted">Sistema</span>';
                        
                        const fecha = doc.fecha_subida ? formatFechaCorta(doc.fecha_subida) : 'Fecha no disponible';
                        const badgeRechazo = doc.observaciones && doc.observaciones.includes('RECHAZADO') 
                            ? '<span class="badge bg-danger ms-2">Rechazado</span>'
                            : (doc.observaciones && doc.observaciones.includes('ACEPTADO') 
                                ? '<span class="badge bg-success ms-2">Aprobado</span>'
                                : '');
                        
                        return `
                            <tr class="documento-item">
                                <td>
                                    <i class="fas ${icono} text-danger me-2"></i>
                                    <span class="fw-semibold">${doc.nombre_original || 'Sin nombre'}</span>
                                    ${badgeRechazo}
                                    ${doc.observaciones ? `<br><small class="text-muted"><i class="fas fa-comment me-1"></i>${doc.observaciones.substring(0, 100)}</small>` : ''}
                                </td>
                                <td>${subidoPor}</td>
                                <td><small>${fecha}</small></td>
                                <td>
                                    <a href="${doc.ruta_archivo}" target="_blank" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-eye me-1"></i>Ver
                                    </a>
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// ─── Manejo de tipo de documento en formulario ──────────────────────────────
document.getElementById('tipo_documento').addEventListener('change', function() {
    const campoArchivo = document.getElementById('campo_archivo');
    const campoLink = document.getElementById('campo_link');
    const archivoInput = document.getElementById('archivo_input');
    const linkInput = document.getElementById('link_input');
    
    if (this.value === 'archivo') {
        campoArchivo.style.display = 'block';
        campoLink.style.display = 'none';
        archivoInput.required = true;
        linkInput.required = false;
        linkInput.value = '';
    } else if (this.value === 'link') {
        campoArchivo.style.display = 'none';
        campoLink.style.display = 'block';
        archivoInput.required = false;
        linkInput.required = true;
        archivoInput.value = '';
    } else {
        campoArchivo.style.display = 'none';
        campoLink.style.display = 'none';
        archivoInput.required = false;
        linkInput.required = false;
    }
});

// ─── Botón limpiar formulario ───────────────────────────────────────────────
document.getElementById('btnLimpiar').addEventListener('click', function() {
    document.getElementById('formSubirDocumento').reset();
    document.getElementById('campo_archivo').style.display = 'none';
    document.getElementById('campo_link').style.display = 'none';
    document.getElementById('tipo_documento').value = '';
});

// ─── Subir documento ────────────────────────────────────────────────────────
document.getElementById('formSubirDocumento').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const tipoDoc = document.getElementById('tipo_documento').value;
    const nombreDoc = document.getElementById('nombre_documento').value.trim();
    
    if (!tipoDoc) {
        showMessage('Error', 'Seleccione el tipo de documento', true);
        return;
    }
    
    if (!nombreDoc) {
        showMessage('Error', 'Ingrese el nombre del documento', true);
        return;
    }
    
    const formData = new FormData();
    formData.append('proceso_id', procesoActual.procesoId);
    formData.append('tipo_documento', tipoDoc);
    formData.append('nombre_documento', nombreDoc);
    
    if (tipoDoc === 'archivo') {
        const archivo = document.getElementById('archivo_input').files[0];
        if (!archivo) {
            showMessage('Error', 'Seleccione un archivo', true);
            return;
        }
        if (archivo.size > 20 * 1024 * 1024) {
            showMessage('Error', 'El archivo no debe superar los 20MB', true);
            return;
        }
        formData.append('archivo', archivo);
    } else {
        const link = document.getElementById('link_input').value.trim();
        if (!link || (!link.startsWith('http://') && !link.startsWith('https://'))) {
            showMessage('Error', 'Ingrese un enlace válido (http:// o https://)', true);
            return;
        }
        formData.append('link', link);
    }
    
    const btn = document.getElementById('btnSubirDocumento');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subiendo...';
    btn.disabled = true;
    
    let url = '';
    if (procesoActual.tipo === 'arbitraje') {
        url = `/mesa-partes/arbitraje/${procesoActual.id}/documentos2`;
    } else {
        url = `/mesa-partes/jrd/${procesoActual.id}/documentos/mesapartes`;
    }
    
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(async res => {
        const text = await res.text();
        try {
            return { ok: res.ok, json: JSON.parse(text) };
        } catch(err) {
            throw new Error(text.substring(0, 200));
        }
    })
    .then(({ ok, json }) => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (ok && json.success) {
            showMessage('Éxito', 'Documento subido correctamente');
            document.getElementById('formSubirDocumento').reset();
            document.getElementById('campo_archivo').style.display = 'none';
            document.getElementById('campo_link').style.display = 'none';
            document.getElementById('tipo_documento').value = '';
            
            // Recargar documentos
            setTimeout(() => cargarDocumentos(), 1000);
        } else {
            showMessage('Error', json.message || json.detalle || 'Error al subir el documento', true);
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showMessage('Error', 'Error de conexión: ' + error.message, true);
    });
});
</script>
@endpush