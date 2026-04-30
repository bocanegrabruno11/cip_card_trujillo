@extends('mesa-partes.app')
@section('title', 'Control de Arbitrajes')
@section('page-title', 'Mis Arbitrajes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Control de Arbitrajes</h3>
            <p class="text-muted">Gestiona y visualiza todos tus procesos de arbitraje</p>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" id="searchArbitraje" placeholder="Buscar por materia, pretensiones o ID...">
            </div>
        </div>
    </div>

    <div id="loading" class="text-center py-5" style="display:none;">
        <div class="spinner-border text-danger" role="status"></div>
        <p class="mt-3 text-muted">Cargando arbitrajes...</p>
    </div>
    <div id="noResults" class="alert alert-info text-center" style="display:none;">
        <i class="fas fa-info-circle fa-2x mb-3"></i>
        <h5>No se encontraron arbitrajes</h5>
        <p class="mb-0">No tienes arbitrajes registrados o no coinciden con tu búsqueda.</p>
    </div>
    <div id="arbitrajesList" class="accordion"></div>
</div>

<!-- Modal Subir Documento (sin observaciones) -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-cloud-upload-alt me-2"></i>Subir Documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="upload_arbitraje_id">
                    <input type="hidden" id="upload_proceso_id">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                            <option value="">Seleccione...</option>
                            <option value="archivo">📄 Subir Archivo (PDF, JPG, PNG)</option>
                            <option value="link">🔗 Enlace (Google Drive, Dropbox, etc.)</option>
                        </select>
                    </div>
                    <div id="campo_archivo" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Archivo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Formatos: PDF, JPG, PNG (Máx. 20MB)</small>
                        </div>
                    </div>
                    <div id="campo_link" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Enlace <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" name="link" placeholder="https://drive.google.com/...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre del Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre_documento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-upload me-2"></i>Subir Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Re-subir Voucher Rechazado (sin observaciones) -->
<div class="modal fade" id="resubirVoucherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Volver a Subir Voucher de Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="resubirVoucherForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="resub_arbitraje_id">
                <input type="hidden" id="resub_proceso_id">
                <div class="modal-body">
                    <div class="alert alert-danger mb-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-times-circle fa-lg me-3 mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="d-block mb-1">Voucher Rechazado</strong>
                                <span id="motivoRechazoTexto" class="small"></span>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i><strong>¿Qué debo hacer?</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Revisa el motivo de rechazo.</li>
                            <li>Asegúrate de que el nuevo voucher sea legible y correcto.</li>
                            <li>Al subir el nuevo voucher tu solicitud volverá a estado <strong>Validando</strong>.</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nuevo Voucher de Pago <span class="text-danger">*</span></label>
                        <input type="file" class="form-control form-control-lg" id="resub_archivo" name="archivo" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">Formatos: JPG, PNG, PDF — Máx. 20MB</small>
                    </div>
                    <div id="resub_preview_container" class="d-none mb-3">
                        <div class="border rounded p-2 bg-light text-center">
                            <img id="resub_preview_img" src="" class="d-none img-fluid rounded" style="max-height:200px;object-fit:contain;">
                            <div id="resub_preview_pdf" class="d-none py-3">
                                <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                <p class="mb-0 mt-2 small text-muted" id="resub_preview_pdf_name"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Voucher <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="resub_nombre" name="nombre_documento" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="btnSubmitResub"><i class="fas fa-upload me-2"></i>Enviar Nuevo Voucher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Mensajes -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><p id="messageModalBody"></p></div>
            <div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let arbitrajes = [];

// Clave para localStorage
const STORAGE_KEY = 'arbitrajes_abiertos';

// ─── Funciones para manejar localStorage ─────────────────────────────────────
function guardarEstadoPaneles() {
    const panelesAbiertos = [];
    document.querySelectorAll('.collapse.show').forEach(collapse => {
        const id = collapse.id;
        if (id && id.startsWith('collapse')) {
            const arbitrajeId = id.replace('collapse', '');
            panelesAbiertos.push(arbitrajeId);
        }
    });
    localStorage.setItem(STORAGE_KEY, JSON.stringify(panelesAbiertos));
}

function cargarEstadoPaneles() {
    const guardado = localStorage.getItem(STORAGE_KEY);
    if (guardado) {
        try {
            return JSON.parse(guardado);
        } catch(e) {
            return [];
        }
    }
    return [];
}

function estaPanelAbierto(arbitrajeId) {
    const abiertos = cargarEstadoPaneles();
    return abiertos.includes(String(arbitrajeId));
}

function togglePanelState(arbitrajeId, isOpen) {
    let abiertos = cargarEstadoPaneles();
    const idStr = String(arbitrajeId);
    if (isOpen) {
        if (!abiertos.includes(idStr)) {
            abiertos.push(idStr);
        }
    } else {
        abiertos = abiertos.filter(id => id !== idStr);
    }
    localStorage.setItem(STORAGE_KEY, JSON.stringify(abiertos));
}

// ─── Funciones auxiliares ───────────────────────────────────────────────────
function formatFecha(f) {
    if (!f) return 'No especificada';
    return new Date(f).toLocaleDateString('es-PE', { year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' });
}

function formatFechaCorta(f) {
    if (!f) return 'No especificada';
    return new Date(f).toLocaleDateString('es-PE', { year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' });
}

function getEstadoBadge(e) {
    const badges = {
        validando: 'bg-warning text-dark',
        iniciado: 'bg-info',
        'en proceso': 'bg-primary',
        terminado: 'bg-success',
        rechazado: 'bg-danger',
        observado: 'bg-danger',
        archivado: 'bg-secondary',
        finalizado: 'bg-secondary'
    };
    return badges[e] || 'bg-secondary';
}

function getProcesoEstadoBadge(e) { 
    return e === 'finalizado' ? 'bg-success' : 'bg-info'; 
}

function showMessage(title, msg, isError=false) {
    const m = new bootstrap.Modal(document.getElementById('messageModal'));
    document.getElementById('messageModalTitle').textContent = title;
    document.getElementById('messageModalTitle').className = `modal-title text-${isError?'danger':'success'}`;
    document.getElementById('messageModalBody').textContent = msg;
    m.show();
}

function extraerMotivoRechazo(procesos) {
    for (const p of (procesos||[])) {
        for (const d of (p.documentos||[])) {
            if (d.tipo_documento==='voucher' && d.observaciones) {
                const m = d.observaciones.match(/\[RECHAZADO\] Motivo: ([^\n]+)/);
                if (m) return m[1].replace(/ - Fecha:.*$/,'').trim();
            }
        }
    }
    return 'No se especificó un motivo de rechazo.';
}

function obtenerProcesoActivo(procesos) {
    if (!procesos||!procesos.length) return null;
    return procesos.find(p=>p.estado==='iniciado') || procesos[procesos.length-1];
}

function badgeSubidoPor(doc) {
    if (!doc.subido_por) return '';
    const { label, color, icono, nombre } = doc.subido_por;
    const txtCls = color === 'warning' ? 'text-dark' : '';
    return `<span class="badge bg-${color} ${txtCls}" style="font-size:.68rem;" title="${nombre}">
                <i class="fas ${icono} me-1"></i>${label}
            </span>
            <small class="text-muted ms-1" style="font-size:.68rem;">${nombre}</small>`;
}

// ─── Abrir modal re-subir voucher ──────────────────────────────────────────
function abrirResubirVoucher(arbitrajeId, procesoId, motivo) {
    document.getElementById('resub_arbitraje_id').value = arbitrajeId;
    document.getElementById('resub_proceso_id').value   = procesoId;
    document.getElementById('motivoRechazoTexto').textContent = motivo;
    document.getElementById('resubirVoucherForm').reset();
    document.getElementById('resub_preview_container').classList.add('d-none');
    document.getElementById('resub_preview_img').classList.add('d-none');
    document.getElementById('resub_preview_pdf').classList.add('d-none');
    new bootstrap.Modal(document.getElementById('resubirVoucherModal')).show();
}

document.getElementById('resub_archivo')?.addEventListener('change', function () {
    const file = this.files[0]; if (!file) return;
    const cont=document.getElementById('resub_preview_container'), img=document.getElementById('resub_preview_img'), pdf=document.getElementById('resub_preview_pdf');
    cont.classList.remove('d-none'); img.classList.add('d-none'); pdf.classList.add('d-none');
    if (file.type==='application/pdf') { document.getElementById('resub_preview_pdf_name').textContent=file.name; pdf.classList.remove('d-none'); }
    else { const r=new FileReader(); r.onload=e=>{img.src=e.target.result;img.classList.remove('d-none');}; r.readAsDataURL(file); }
    const n=document.getElementById('resub_nombre');
    if (!n.value.trim()) n.value='Voucher de pago - '+file.name.replace(/\.[^/.]+$/,'');
});

document.getElementById('resubirVoucherForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const arbitrajeId=document.getElementById('resub_arbitraje_id').value;
    const procesoId=document.getElementById('resub_proceso_id').value;
    const archivo=document.getElementById('resub_archivo').files[0];
    const nombre=document.getElementById('resub_nombre').value.trim();
    if (!archivo) { showMessage('Error','Debes seleccionar un archivo.',true); return; }
    if (archivo.size>20*1024*1024) { showMessage('Error','El archivo no debe superar los 20MB.',true); return; }
    if (!nombre) { showMessage('Error','Ingresa un nombre para el voucher.',true); return; }
    const fd=new FormData(this);
    fd.append('proceso_id', procesoId);
    fd.append('tipo_documento','voucher');
    fd.append('nombre_documento', nombre);
    const btn=document.getElementById('btnSubmitResub'), orig=btn.innerHTML;
    btn.innerHTML='<i class="fas fa-spinner fa-spin me-2"></i>Enviando...'; btn.disabled=true;
    fetch(`/mesa-partes/arbitraje/${arbitrajeId}/documentos2`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}'},body:fd})
    .then(async r=>{const t=await r.text();try{return{r,json:JSON.parse(t)};}catch(err){throw new Error(t.substring(0,200));}})
    .then(({r,json})=>{
        btn.innerHTML=orig; btn.disabled=false;
        if(r.ok&&json.success){
            bootstrap.Modal.getInstance(document.getElementById('resubirVoucherModal')).hide();
            showMessage('✅ Voucher enviado','Tu nuevo voucher fue enviado. Tu solicitud volvió al estado Validando.');
            document.getElementById('messageModal').addEventListener('hidden.bs.modal',()=>{
                window.location.reload();
            },{once:true});
        } else { showMessage('Error',json.message||json.detalle||'No se pudo subir el voucher.',true); }
    })
    .catch(err=>{btn.innerHTML=orig;btn.disabled=false;showMessage('Error de conexión',err.message,true);});
});

// ─── Tipo documento modal normal ──────────────────────────────────────────
document.getElementById('tipo_documento')?.addEventListener('change', function() {
    document.getElementById('campo_archivo').style.display=this.value==='archivo'?'block':'none';
    document.getElementById('campo_link').style.display=this.value==='link'?'block':'none';
    document.querySelector('input[name="archivo"]').required=(this.value==='archivo');
    document.querySelector('input[name="link"]').required=(this.value==='link');
    if(this.value!=='archivo') document.querySelector('input[name="archivo"]').value='';
    if(this.value!=='link') document.querySelector('input[name="link"]').value='';
});

function subirDocumento(arbId, procId) {
    document.getElementById('upload_arbitraje_id').value=arbId;
    document.getElementById('upload_proceso_id').value=procId;
    document.getElementById('uploadDocumentForm').reset();
    document.getElementById('campo_archivo').style.display='none';
    document.getElementById('campo_link').style.display='none';
    document.getElementById('tipo_documento').value='';
    new bootstrap.Modal(document.getElementById('uploadDocumentModal')).show();
}

// ─── Generar HTML de un card individual ────────────────────────────────────
function generarCardHTML(arb) {
    let rolBadge='';
    if(arb.es_creador) rolBadge='<span class="badge bg-info ms-2"><i class="fas fa-user-tie me-1"></i>Creador</span>';
    else if(arb.rol_usuario==='Demandante') rolBadge='<span class="badge bg-success ms-2"><i class="fas fa-user-check me-1"></i>Demandante</span>';
    else if(arb.rol_usuario==='Demandado') rolBadge='<span class="badge bg-warning text-dark ms-2"><i class="fas fa-user-shield me-1"></i>Demandado</span>';

    // ✅ CORREGIDO: declarar tipoBadge AQUÍ, fuera del template string
    const tipoBadge = arb.tipo_arbitraje === 'emergencia'
        ? '<span class="badge bg-danger ms-2"><i class="fas fa-bolt me-1"></i>Emergencia</span>'
        : '<span class="badge bg-secondary ms-2"><i class="fas fa-gavel me-1"></i>Normal</span>';

    const esObservado = arb.estado==='observado';
    const puedeResub  = esObservado && arb.es_creador;
    const motivo      = puedeResub ? extraerMotivoRechazo(arb.procesos) : '';
    const procActivo  = obtenerProcesoActivo(arb.procesos);
    const panelId = `collapse${arb.id_arbitraje}`;
    
    // Verificar si debe estar abierto según localStorage
    const debeEstarAbierto = estaPanelAbierto(arb.id_arbitraje) || puedeResub;
    const expandedClass = debeEstarAbierto ? 'show' : '';
    const ariaExpanded = debeEstarAbierto ? 'true' : 'false';

    const alertaRechazo = puedeResub ? `
        <div class="alert alert-danger border-start border-4 border-danger shadow-sm mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-start gap-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger flex-shrink-0 mt-1"></i>
                    <div>
                        <h6 class="mb-1 text-danger"><strong>Tu voucher de pago fue rechazado</strong></h6>
                        <p class="mb-1 small"><strong>Motivo:</strong> ${motivo}</p>
                        <p class="mb-0 small text-muted"><i class="fas fa-info-circle me-1"></i>Debes volver a subir el voucher corregido para continuar.</p>
                    </div>
                </div>
                <button type="button" class="btn btn-danger flex-shrink-0"
                        onclick="abrirResubirVoucher(${arb.id_arbitraje},${procActivo?procActivo.id_proceso_de_arbitraje:'null'},\`${motivo.replace(/`/g,"'")}\`)">
                    <i class="fas fa-upload me-2"></i>Volver a Subir Voucher
                </button>
            </div>
        </div>` : '';

    return `
    <div class="card mb-3 shadow-sm arbitraje-card ${esObservado?'border-danger':''}"
         data-materia="${(arb.nombre_materia||'').toLowerCase()}" data-id="${arb.id_arbitraje}">
        <div class="card-header bg-white ${esObservado?'border-bottom border-danger':''}">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-1"><i class="fas fa-scale-balanced text-danger me-2"></i>${arb.nombre_materia||'Sin materia'}${rolBadge}${tipoBadge}</h5>
                    <small class="text-muted"><i class="fas fa-calendar me-1"></i>Iniciado: ${formatFecha(arb.fecha_inicio)}</small>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge ${getEstadoBadge(arb.estado)} px-3 py-2">${(arb.estado||'iniciado').toUpperCase()}</span>
                    <button class="btn btn-sm btn-outline-secondary ms-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#${panelId}"
                            aria-expanded="${ariaExpanded}"
                            onclick="event.stopPropagation();">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="${panelId}" class="collapse ${expandedClass}" data-bs-parent="#arbitrajesList">
            <div class="card-body">
                ${alertaRechazo}

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-danger border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                        <p><strong>Pretensiones:</strong> ${arb.pretenciones||'No especificadas'}</p>
                        <p><strong>Controversia:</strong> ${arb.controversia||'No especificadas'}</p>
                        <p><strong>Fundamentos de hecho:</strong> ${arb.fundamentos_hecho||'No especificadas'}</p>
                        ${arb.cuantia?`<p><strong>Cuantía:</strong> ${arb.cuantia}</p>`:''}
                        ${arb.tasa_solicitud?`<p><strong>Tasa de Solicitud:</strong> ${arb.tasa_solicitud}</p>`:''}
                        ${arb.designacion_arbitral?`<p><strong>Designación Arbitral:</strong> ${arb.designacion_arbitral}</p>`:''}
                        <p><strong>ID Arbitraje:</strong> #${arb.id_arbitraje}</p>
                        <p><strong>Tipo de Arbitraje:</strong> 
                            ${arb.tipo_arbitraje === 'emergencia'
                                ? '<span class="badge bg-danger"><i class="fas fa-bolt me-1"></i>Emergencia</span>'
                                : '<span class="badge bg-secondary"><i class="fas fa-gavel me-1"></i>Normal</span>'}
                        </p>
                        <p><strong>Tu rol:</strong> <span class="badge ${arb.es_creador?'bg-info':'bg-success'}">${arb.rol_usuario||'Observador'}</span></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-danger border-bottom pb-2 mb-3"><i class="fas fa-users me-2"></i>Personas Involucradas</h6>
                        <div class="row">
                            ${arb.personas&&arb.personas.length?arb.personas.map(p=>`
                                <div class="col-md-6 mb-2">
                                    <div class="p-2 bg-light rounded">
                                        <span class="badge ${p.tipo==='Demandante'?'bg-success':'bg-warning text-dark'} me-2">${p.tipo}</span>
                                        <strong>${p.nombres||''} ${p.apellidos||''}</strong><br>
                                        <small class="text-muted">DNI: ${p.dni||''}</small>
                                        ${p.correo?`<br><small><i class="fas fa-envelope me-1"></i>${p.correo}</small>`:''}
                                    </div>
                                </div>`).join(''):'<p class="text-muted">No hay personas registradas</p>'}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-danger border-bottom pb-2 mb-3"><i class="fas fa-tasks me-2"></i>Procesos</h6>
                        ${arb.procesos&&arb.procesos.length?`
                            <div class="list-group">
                                ${arb.procesos.map(proc=>`
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><i class="fas ${proc.etapa?'fa-tasks':'fa-file-alt'} text-primary me-2"></i>${proc.etapa?proc.etapa.nombre:'Proceso #'+proc.id_proceso_de_arbitraje}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>Iniciado: ${formatFecha(proc.fecha_creacion)}
                                                    ${proc.fecha_finalizacion?`<br><i class="fas fa-check-circle me-1"></i>Finalizado: ${formatFecha(proc.fecha_finalizacion)}`:''}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge ${getProcesoEstadoBadge(proc.estado)} mb-2">${(proc.estado||'iniciado').toUpperCase()}</span>
                                                ${proc.estado==='iniciado'&&arb.estado!=='observado'?`
                                                    <button class="btn btn-sm btn-danger ms-2" onclick="subirDocumento(${arb.id_arbitraje},${proc.id_proceso_de_arbitraje})">
                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                    </button>`:''
                                                }
                                            </div>
                                        </div>

                                        ${proc.documentos&&proc.documentos.length?`
                                            <div class="mt-3 pt-3 border-top">
                                                <small class="text-muted d-block mb-2"><i class="fas fa-paperclip me-1"></i>Documentos adjuntos (${proc.documentos.length}):</small>
                                                <div class="d-flex flex-column gap-2">
                                                    ${proc.documentos.map(doc=>{
                                                        const fechaSubida = doc.fecha_subida ? formatFechaCorta(doc.fecha_subida) : 'Fecha no disponible';
                                                        const icono = doc.tipo_documento==='pdf'?'fa-file-pdf text-danger'
                                                                    : doc.tipo_documento==='voucher'?'fa-receipt text-success'
                                                                    : doc.tipo_documento==='imagen'?'fa-file-image text-primary':'fa-link text-warning';
                                                        const esRech = doc.observaciones&&doc.observaciones.includes('[RECHAZADO]');
                                                        const esAcep = doc.observaciones&&doc.observaciones.includes('[ACEPTADO]');
                                                        const voucherBadge = doc.tipo_documento==='voucher'
                                                            ? (esAcep?'<span class="badge bg-success ms-1">✓ Aprobado</span>'
                                                             :esRech?'<span class="badge bg-danger ms-1">✗ Rechazado</span>'
                                                                    :'<span class="badge bg-warning text-dark ms-1">⏳ Pendiente</span>'):'';
                                                        const uploaderBadge = badgeSubidoPor(doc);
                                                        return `
                                                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded documento-item">
                                                            <div class="d-flex flex-column gap-1 flex-grow-1">
                                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                    <i class="fas ${icono}"></i>
                                                                    <span class="small fw-semibold">${doc.nombre_original}${voucherBadge}</span>
                                                                    <span class="d-flex align-items-center gap-1">${uploaderBadge}</span>
                                                                </div>
                                                                <small class="text-muted ms-4 fecha-subida">
                                                                    <i class="far fa-calendar-alt me-1"></i>Subido: ${fechaSubida}
                                                                </small>
                                                            </div>
                                                            <a href="${doc.ruta_archivo}" target="_blank" class="btn btn-sm btn-outline-secondary ms-2 flex-shrink-0">
                                                                <i class="fas fa-eye me-1"></i>Ver
                                                            </a>
                                                        </div>`;
                                                    }).join('')}
                                                </div>
                                            </div>` : '<p class="text-muted mt-2 mb-0 small">Sin documentos subidos</p>'}
                                    </div>`).join('')}
                            </div>` : '<p class="text-muted">No hay procesos registrados</p>'}
                    </div>
                </div>
            </div>
        </div>
    </div>`;
}

// ─── Render completo ───────────────────────────────────────────────────────
function renderArbitrajes(data) {
    const container = document.getElementById('arbitrajesList');
    if (!data || !data.length) {
        document.getElementById('noResults').style.display = 'block';
        container.innerHTML = '';
        return;
    }
    document.getElementById('noResults').style.display = 'none';
    container.innerHTML = data.map(arb => generarCardHTML(arb)).join('');
    
    // Agregar event listeners para guardar el estado cuando se abren/cierran paneles
    document.querySelectorAll('.collapse').forEach(collapse => {
        const arbitrajeId = collapse.id.replace('collapse', '');
        
        collapse.addEventListener('shown.bs.collapse', function() {
            togglePanelState(arbitrajeId, true);
        });
        
        collapse.addEventListener('hidden.bs.collapse', function() {
            togglePanelState(arbitrajeId, false);
        });
    });
}

// ─── Subir documento y actualizar SOLO el arbitraje afectado ─────────────────
document.getElementById('uploadDocumentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const tipo = document.getElementById('tipo_documento').value;
    const nombre = document.querySelector('#uploadDocumentForm input[name="nombre_documento"]').value.trim();
    
    if(!tipo){ showMessage('Error','Seleccione el tipo de documento',true); return; }
    if(!nombre){ showMessage('Error','Ingrese el nombre del documento',true); return; }
    
    if(tipo === 'archivo'){
        const archivo = document.querySelector('#uploadDocumentForm input[name="archivo"]').files[0];
        if(!archivo){ showMessage('Error','Seleccione un archivo',true); return; }
        if(archivo.size > 20 * 1024 * 1024){ showMessage('Error','El archivo no debe superar los 20MB',true); return; }
    }
    if(tipo === 'link'){
        const link = document.querySelector('#uploadDocumentForm input[name="link"]').value.trim();
        if(!link){ showMessage('Error','Ingrese el enlace',true); return; }
        if(!link.startsWith('http://') && !link.startsWith('https://')){ showMessage('Error','El enlace debe comenzar con http:// o https://',true); return; }
    }
    
    const formData = new FormData();
    const arbitrajeId = document.getElementById('upload_arbitraje_id').value;
    const procesoId = document.getElementById('upload_proceso_id').value;
    
    formData.append('id_arbitraje', arbitrajeId);
    formData.append('proceso_id', procesoId);
    formData.append('tipo_documento', tipo);
    formData.append('nombre_documento', nombre);
    
    if(tipo === 'archivo') formData.append('archivo', document.querySelector('#uploadDocumentForm input[name="archivo"]').files[0]);
    else formData.append('link', document.querySelector('#uploadDocumentForm input[name="link"]').value.trim());
    
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subiendo...';
    btn.disabled = true;
    
    fetch(`/mesa-partes/arbitraje/${arbitrajeId}/documentos2`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' },
        body: formData
    })
    .then(async response => {
        const text = await response.text();
        try {
            return { response, json: JSON.parse(text) };
        } catch(err) {
            throw new Error(text.substring(0, 200));
        }
    })
    .then(({ response, json }) => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        if(response.ok && json.success){
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal')).hide();
            this.reset();
            
            // Recargar solo este arbitraje desde el servidor
            return fetch('{{ route("arbitrajes.obtener") }}')
                .then(r => r.json())
                .then(data => {
                    if(data.success){
                        const arbitrajeActualizado = data.arbitrajes.find(a => a.id_arbitraje == arbitrajeId);
                        if(arbitrajeActualizado){
                            // Actualizar en el array local
                            const index = arbitrajes.findIndex(a => a.id_arbitraje == arbitrajeId);
                            if(index !== -1){
                                arbitrajes[index] = arbitrajeActualizado;
                            }
                            // Reemplazar el card en el DOM
                            const cardExistente = document.querySelector(`.arbitraje-card[data-id="${arbitrajeId}"]`);
                            if(cardExistente){
                                const nuevoHtml = generarCardHTML(arbitrajeActualizado);
                                cardExistente.outerHTML = nuevoHtml;
                                
                                // Restaurar event listeners del nuevo card
                                const nuevoCard = document.querySelector(`.arbitraje-card[data-id="${arbitrajeId}"]`);
                                if(nuevoCard){
                                    const collapse = nuevoCard.querySelector(`#collapse${arbitrajeId}`);
                                    if(collapse){
                                        collapse.addEventListener('shown.bs.collapse', () => togglePanelState(arbitrajeId, true));
                                        collapse.addEventListener('hidden.bs.collapse', () => togglePanelState(arbitrajeId, false));
                                    }
                                }
                            }
                        }
                        showMessage('Éxito', 'Documento subido correctamente');
                    }
                });
        } else {
            showMessage('Error', json.message || json.detalle || 'Error al subir', true);
        }
    })
    .catch(error => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        showMessage('Error', 'Error de conexión: ' + error.message, true);
    });
});

// ─── Cargar arbitrajes inicial ─────────────────────────────────────────────
function cargarArbitrajes() {
    document.getElementById('loading').style.display = 'block';
    fetch('{{ route("arbitrajes.obtener") }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('loading').style.display = 'none';
            if(data.success){
                arbitrajes = data.arbitrajes;
                renderArbitrajes(arbitrajes);
            } else {
                document.getElementById('noResults').style.display = 'block';
            }
        })
        .catch(error => {
            document.getElementById('loading').style.display = 'none';
            showMessage('Error', 'Error al cargar los arbitrajes', true);
            document.getElementById('noResults').style.display = 'block';
        });
}

// ─── Filtro de búsqueda ───────────────────────────────────────────────────
function filterArbitrajes() {
    const searchTerm = document.getElementById('searchArbitraje').value.toLowerCase();
    const cards = document.querySelectorAll('.arbitraje-card');
    let visibleCount = 0;
    cards.forEach(card => {
        const matches = card.textContent.toLowerCase().includes(searchTerm) || 
                       card.dataset.id.includes(searchTerm);
        card.style.display = matches ? 'block' : 'none';
        if(matches) visibleCount++;
    });
    document.getElementById('noResults').style.display = (visibleCount === 0 && searchTerm !== '') ? 'block' : 'none';
}

// ─── Inicialización ────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    cargarArbitrajes();
    document.getElementById('searchArbitraje')?.addEventListener('input', filterArbitrajes);
});
</script>
@endpush

@push('styles')
<style>
.arbitraje-card { transition:all .3s ease; border:1px solid #dee2e6; }
.arbitraje-card:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,.1)!important; }
.arbitraje-card.border-danger { border-color:#dc3545!important; border-width:2px!important; }
.card-header { border-bottom:2px solid #f8f9fa; }
.list-group-item { border-left:4px solid #AD2B2E; margin-bottom:10px; border-radius:5px; transition:all .3s ease; }
.list-group-item:hover { background-color:#f8f9fa; }
.badge { font-size:.75rem; font-weight:600; letter-spacing:.5px; }
.btn-danger { background-color:#AD2B2E; border-color:#AD2B2E; }
.btn-danger:hover { background-color:#8B2326; border-color:#8B2326; }
.gap-2 { gap:.5rem; }
.documento-item { transition:all .2s ease; }
.documento-item:hover { background-color:#f0f0f0!important; transform:translateX(5px); }
.fecha-subida { font-size:0.7rem; color:#6c757d; }
.ms-4 { margin-left:1.5rem; }
</style>
@endpush