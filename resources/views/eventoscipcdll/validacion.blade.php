@extends('eventoscipcdll.layout')
@section('title', 'Módulo de Validación')
@section('content')

<style>
*{box-sizing:border-box;margin:0;padding:0}
.val-wrap{padding:1.5rem;font-family:inherit}
.val-topbar{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;flex-wrap:wrap;gap:10px}
.val-topbar h2{font-size:18px;font-weight:600;color:#1a1a1a;margin:0}
.val-topbar p{font-size:12px;color:#888;margin-top:3px}
.val-stats{display:flex;gap:7px;flex-wrap:wrap}
.val-stat{display:inline-flex;align-items:center;gap:6px;padding:5px 13px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid;transition:all .2s;cursor:pointer}
.val-stat.pend{background:#FAEEDA;color:#854F0B;border-color:#EF9F27}
.val-stat.ap  {background:#EAF3DE;color:#3B6D11;border-color:#97C459}
.val-stat.re  {background:#FCEBEB;color:#A32D2D;border-color:#F09595}
.val-stat .dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.val-stat.pend .dot{background:#BA7517}
.val-stat.ap   .dot{background:#639922}
.val-stat.re   .dot{background:#E24B4A}
.val-stat.active{box-shadow:0 0 0 2px #fff, 0 0 0 4px currentColor;opacity:1}

.val-filtros{display:flex;gap:8px;margin-bottom:1rem;flex-wrap:wrap}
.btn-filtro{padding:8px 20px;border-radius:30px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .2s;background:#f2f1ec;color:#666}
.btn-filtro:hover{background:#e3e2dc}
.btn-filtro.activo{background:#1a1a1a;color:#fff}
.btn-filtro.todos.activo{background:#1a1a1a}
.btn-filtro.aprobados.activo{background:#639922;color:#fff}
.btn-filtro.rechazados.activo{background:#E24B4A;color:#fff}

.val-alerta{display:none;align-items:center;gap:8px;border-radius:8px;padding:9px 13px;font-size:13px;margin-bottom:.85rem}
.val-alerta.show{display:flex}
.val-alerta.ok  {background:#EAF3DE;color:#3B6D11;border:1px solid #97C459}
.val-alerta.err {background:#FCEBEB;color:#A32D2D;border:1px solid #F09595}
.val-alerta.info{background:#E6F1FB;color:#185FA5;border:1px solid #85B7EB}
.val-alerta .ico{width:12px;height:12px;border-radius:50%;flex-shrink:0}
.val-alerta.ok   .ico{background:#639922}
.val-alerta.err  .ico{background:#E24B4A}
.val-alerta.info .ico{background:#378ADD}

.val-cols{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:1rem}
.val-col{border:1px solid #e8e7e0;border-radius:10px;overflow:hidden}
.val-col-head{padding:8px 12px;font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;display:flex;align-items:center;gap:6px}
.val-col-head.ap{background:#EAF3DE;color:#3B6D11;border-bottom:1px solid #C0DD97}
.val-col-head.re{background:#FCEBEB;color:#A32D2D;border-bottom:1px solid #F7C1C1}
.val-col-head .chd{width:7px;height:7px;border-radius:50%}
.val-col-head.ap .chd{background:#639922}
.val-col-head.re .chd{background:#E24B4A}
.val-col-list{max-height:220px;overflow-y:auto}
.val-col-item{display:flex;justify-content:space-between;align-items:center;padding:7px 11px;border-bottom:1px solid #f2f1ec;font-size:12px}
.val-col-item:last-child{border-bottom:none}
.val-col-item:hover{background:#fafaf8}
.val-col-nombre{font-weight:500;color:#1a1a1a}
.val-col-cip{font-family:monospace;font-size:10px;color:#bbb;margin-top:1px}
.btn-undo{border:none;background:none;cursor:pointer;font-size:11px;color:#bbb;padding:2px 6px;border-radius:4px}
.btn-undo:hover{background:#f0efea;color:#555}
.val-col-empty{padding:1.25rem;text-align:center;color:#ccc;font-size:12px;font-style:italic}

.val-tbl-wrap{border:1px solid #d8d7d0;border-radius:10px;overflow:hidden;margin-bottom:1rem}
.val-tbl-wrap table{width:100%;border-collapse:collapse;table-layout:fixed}
.val-tbl-wrap col.cn{width:32px}
.val-tbl-wrap col.cc{width:90px}
.val-tbl-wrap col.cd{width:90px}
.val-tbl-wrap col.ce{width:85px}
.val-tbl-wrap col.ca{width:155px}
.val-tbl-wrap thead{background:#f2f1ec}
.val-tbl-wrap th{padding:9px 11px;font-size:10px;font-weight:700;color:#999;text-align:left;border-bottom:1px solid #e0dfd8;text-transform:uppercase;letter-spacing:.05em}
.val-tbl-wrap td{padding:10px 11px;font-size:13px;color:#1a1a1a;border-bottom:1px solid #ebebeb;vertical-align:middle;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.val-tbl-wrap tbody tr:nth-child(even){background:#fafaf8}
.val-tbl-wrap tbody tr:last-child td{border-bottom:none}
.val-tbl-wrap tbody tr:hover{background:#f0f7e8}
.val-tbl-wrap tbody tr.marcado-ap{background:#edf7e1 !important}
.val-tbl-wrap tbody tr.marcado-re{background:#fdf0f0 !important}

.val-badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:600}
.val-badge.registrado{background:#FAEEDA;color:#854F0B}
.val-badge.aprobado  {background:#EAF3DE;color:#3B6D11}
.val-badge.rechazado {background:#FCEBEB;color:#A32D2D}

.val-acciones{display:flex;gap:4px;justify-content:center}
.btn-ap,.btn-re,.btn-und{border:none;border-radius:5px;padding:4px 10px;font-size:11px;font-weight:600;cursor:pointer;transition:all .12s}
.btn-ap{background:#EAF3DE;color:#3B6D11}
.btn-ap:hover{background:#C0DD97}
.btn-ap.active{background:#639922;color:#fff}
.btn-re{background:#FCEBEB;color:#A32D2D}
.btn-re:hover{background:#F7C1C1}
.btn-re.active{background:#E24B4A;color:#fff}
.btn-und{background:#f0efea;color:#888;font-size:11px;padding:4px 8px}
.btn-und:hover{background:#ddd;color:#333}

.val-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.val-paginacion{display:flex;align-items:center;gap:8px}
.btn-pag{background:#fff;border:1px solid #d0cfca;border-radius:6px;padding:5px 12px;font-size:12px;cursor:pointer;color:#1a1a1a}
.btn-pag:hover:not(:disabled){background:#f5f5f3}
.btn-pag:disabled{opacity:.35;cursor:not-allowed}
.pag-info{font-size:12px;color:#888}
.pag-info b{color:#1a1a1a;font-weight:600}
.btn-guardar{background:#1a1a1a;color:#fff;border:none;border-radius:7px;padding:7px 22px;font-size:13px;font-weight:500;cursor:pointer;transition:background .15s}
.btn-guardar:hover:not(:disabled){background:#333}
.btn-guardar:disabled{background:#ccc;cursor:not-allowed}
.val-empty-td{text-align:center;color:#bbb;padding:2rem;font-size:13px}
.td-num{font-size:11px;color:#ccc;font-family:monospace;text-align:right;padding-right:6px !important}
.td-mono{font-family:monospace;font-size:11px;color:#999}
</style>

<div class="val-wrap">

  <div class="val-topbar">
    <div>
      <h2>Módulo de validación</h2>
      <p>Marca localmente — guarda todo de una vez</p>
    </div>
    <div class="val-stats">
      <div class="val-stat pend"><span class="dot"></span><span id="cnt-pend">—</span> pendientes</div>
      <div class="val-stat ap">  <span class="dot"></span><span id="cnt-ap">0</span> aprobados</div>
      <div class="val-stat re">  <span class="dot"></span><span id="cnt-re">0</span> rechazados</div>
    </div>
  </div>

  <!-- 3 BOTONES DE FILTRO -->
  <div class="val-filtros">
    <button class="btn-filtro todos activo" id="filtro-todos" onclick="cambiarFiltro('todos')">📋 Pendientes</button>
    <button class="btn-filtro aprobados" id="filtro-aprobados" onclick="cambiarFiltro('aprobados')">✅ Aprobados</button>
    <button class="btn-filtro rechazados" id="filtro-rechazados" onclick="cambiarFiltro('rechazados')">❌ Rechazados</button>
  </div>

  <div class="val-alerta" id="alerta"><div class="ico"></div><span id="alerta-msg"></span></div>

  <div class="val-cols">
    <div class="val-col">
      <div class="val-col-head ap"><span class="chd"></span>Aprobados (marcados localmente)</div>
      <div class="val-col-list" id="lista-ap"><div class="val-col-empty">Ninguno marcado aún</div></div>
    </div>
    <div class="val-col">
      <div class="val-col-head re"><span class="chd"></span>Rechazados (marcados localmente)</div>
      <div class="val-col-list" id="lista-re"><div class="val-col-empty">Ninguno marcado aún</div></div>
    </div>
  </div>

  <div class="val-tbl-wrap">
    <table>
      <colgroup>
        <col class="cn"><col class="cc"><col class="cd"><col><col><col class="ce"><col class="ca">
      </colgroup>
      <thead>
        <tr>
          <th>#</th>
          <th>CIP</th>
          <th>DNI</th>
          <th>Nombres</th>
          <th>Apellidos</th>
          <th>Estado</th>
          <th style="text-align:center">Acción</th>
        </tr>
      </thead>
      <tbody id="tbody">
        <tr><td colspan="7" class="val-empty-td">Cargando......</td></tr>
      </tbody>
  </div>

  <div class="val-footer">
    <div class="val-paginacion">
      <button class="btn-pag" id="btn-ant" disabled onclick="anterior()">← Anterior</button>
      <span class="pag-info">Página <b id="pg">1</b> de <b id="pg-total">1</b></span>
      <button class="btn-pag" id="btn-sig" onclick="siguiente()">Siguiente →</button>
    </div>
    <button class="btn-guardar" id="btn-guardar" disabled onclick="guardarTodo()">
      Guardar cambios
    </button>
  </div>

</div>

<script>
let pagina = 1, totalPaginas = 1, totalRegistros = 0;
let marcados = {};
let filtroActual = 'todos';
let datosPendientes = [];
let datosAprobados = [];
let datosRechazados = [];
let datosActuales = [];
const csrfToken = '{{ csrf_token() }}';

function cambiarFiltro(filtro) {
    filtroActual = filtro;
    pagina = 1;
    
    // Actualizar clases de los botones
    document.querySelectorAll('.btn-filtro').forEach(btn => btn.classList.remove('activo'));
    
    if (filtro === 'todos') {
        document.getElementById('filtro-todos').classList.add('activo');
        datosActuales = [...datosPendientes];
        totalRegistros = datosPendientes.length;
    } else if (filtro === 'aprobados') {
        document.getElementById('filtro-aprobados').classList.add('activo');
        datosActuales = [...datosAprobados];
        totalRegistros = datosAprobados.length;
    } else if (filtro === 'rechazados') {
        document.getElementById('filtro-rechazados').classList.add('activo');
        datosActuales = [...datosRechazados];
        totalRegistros = datosRechazados.length;
    }
    
    totalPaginas = Math.ceil(totalRegistros / 10);
    if (totalPaginas === 0) totalPaginas = 1;
    
    renderPagina();
}

function renderPagina() {
    document.getElementById('pg').textContent = pagina;
    document.getElementById('pg-total').textContent = totalPaginas;
    document.getElementById('btn-ant').disabled = pagina <= 1;
    document.getElementById('btn-sig').disabled = pagina >= totalPaginas;

    const inicio = (pagina - 1) * 10;
    const fin = inicio + 10;
    const datosPagina = datosActuales.slice(inicio, fin);
    
    const tbody = document.getElementById('tbody');

    if (!datosPagina.length) {
        let mensaje = '';
        if (filtroActual === 'aprobados') mensaje = 'No hay asistentes aprobados en el sistema.';
        else if (filtroActual === 'rechazados') mensaje = 'No hay asistentes rechazados en el sistema.';
        else mensaje = 'No hay registros pendientes.';
        tbody.innerHTML = `<tr><td colspan="7" class="val-empty-td">${mensaje}</td></tr>`;
        actualizarStats();
        return;
    }

    tbody.innerHTML = datosPagina.map((a, i) => {
        const m        = marcados[a.cip];
        const rowClass = m ? (m.op === 'si' ? 'marcado-ap' : 'marcado-re') : '';
        const apActive = m && m.op === 'si' ? 'active' : '';
        const reActive = m && m.op === 'no' ? 'active' : '';
        const undShow  = m ? 'inline-block' : 'none';
        
        // Deshabilitar botones según el estado actual del registro
        const disabledAp = (a.estado === 'aprobado') ? 'disabled' : '';
        const disabledRe = (a.estado === 'rechazado') ? 'disabled' : '';
        
        return `
        <tr id="f-${a.cip}" class="${rowClass}">
            <td class="td-num">${inicio + i + 1}</td>
            <td class="td-mono">${escapeHtml(a.cip)}</td>
            <td class="td-mono">${escapeHtml(a.dni)}</td>
            <td>${escapeHtml(a.nombres)}</td>
            <td>${escapeHtml(a.apellidos)}</td>
            <td><span class="val-badge ${a.estado}">${a.estado}</span></td>
            <td><div class="val-acciones">
                <button class="btn-ap ${apActive}" ${disabledAp} onclick="marcar('${escapeHtml(a.cip)}','si','${escapeHtml(a.nombres)}','${escapeHtml(a.apellidos)}','${escapeHtml(a.dni)}')">Aprobar</button>
                <button class="btn-re ${reActive}" ${disabledRe} onclick="marcar('${escapeHtml(a.cip)}','no','${escapeHtml(a.nombres)}','${escapeHtml(a.apellidos)}','${escapeHtml(a.dni)}')">Rechazar</button>
                <button class="btn-und" style="display:${undShow}" onclick="desmarcar('${escapeHtml(a.cip)}')">↩</button>
            </div></td>
          </tr>`;
    }).join('');
    
    actualizarStats();
}

// Función para escapar HTML y prevenir XSS
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function actualizarStats() {
    const vals  = Object.values(marcados);
    const ap    = vals.filter(v => v.op === 'si').length;
    const re    = vals.filter(v => v.op === 'no').length;
    const pend  = datosPendientes.length - ap - re;
    document.getElementById('cnt-ap').textContent = ap;
    document.getElementById('cnt-re').textContent = re;
    document.getElementById('cnt-pend').textContent = pend >= 0 ? pend : datosPendientes.length;
    document.getElementById('btn-guardar').disabled = (ap + re) === 0;
    actualizarListas();
}

function actualizarListas() {
    const apItems = Object.values(marcados).filter(v => v.op === 'si');
    const reItems = Object.values(marcados).filter(v => v.op === 'no');

    const itemHtml = v => `
        <div class="val-col-item">
            <div>
                <div class="val-col-nombre">${escapeHtml(v.nombres)} ${escapeHtml(v.apellidos)}</div>
                <div class="val-col-cip">CIP ${escapeHtml(v.cip)} &middot; DNI ${escapeHtml(v.dni)}</div>
            </div>
            <button class="btn-undo" onclick="desmarcar('${escapeHtml(v.cip)}')">↩ deshacer</button>
        </div>`;

    document.getElementById('lista-ap').innerHTML = apItems.length
        ? apItems.map(itemHtml).join('')
        : '<div class="val-col-empty">Ninguno marcado aún</div>';

    document.getElementById('lista-re').innerHTML = reItems.length
        ? reItems.map(itemHtml).join('')
        : '<div class="val-col-empty">Ninguno marcado aún</div>';
}

function marcar(cip, op, nombres, apellidos, dni) {
    marcados[cip] = { cip, op, nombres, apellidos, dni };
    const fila = document.getElementById('f-' + cip);
    if (fila) {
        fila.className = op === 'si' ? 'marcado-ap' : 'marcado-re';
        const btnAp = fila.querySelector('.btn-ap');
        const btnRe = fila.querySelector('.btn-re');
        const btnUnd = fila.querySelector('.btn-und');
        if (btnAp) btnAp.classList.toggle('active', op === 'si');
        if (btnRe) btnRe.classList.toggle('active', op === 'no');
        if (btnUnd) btnUnd.style.display = 'inline-block';
    }
    actualizarStats();
}

function desmarcar(cip) {
    delete marcados[cip];
    const fila = document.getElementById('f-' + cip);
    if (fila) {
        fila.className = '';
        const btnAp = fila.querySelector('.btn-ap');
        const btnRe = fila.querySelector('.btn-re');
        const btnUnd = fila.querySelector('.btn-und');
        if (btnAp) btnAp.classList.remove('active');
        if (btnRe) btnRe.classList.remove('active');
        if (btnUnd) btnUnd.style.display = 'none';
    }
    actualizarStats();
}

function cargarDatos() {
    document.getElementById('tbody').innerHTML = '<tr><td colspan="7" class="val-empty-td">Cargando...</td></tr>';

    // Cargar TODOS los pendientes (registrados)
    fetch('/ver-pendientes')
        .then(response => {
            if (response.status === 401) {
                window.location.href = '/login-eventos';
                throw new Error('Sesión expirada');
            }
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            datosPendientes = data.data || [];
            return fetch('/ver-aprobados');
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(ap => {
            datosAprobados = ap.data || [];
            return fetch('/ver-rechazados');
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(re => {
            datosRechazados = re.data || [];
            
            // Actualizar textos de botones con los conteos TOTALES
            document.getElementById('filtro-todos').innerHTML = `📋 Pendientes (${datosPendientes.length})`;
            document.getElementById('filtro-aprobados').innerHTML = `✅ Aprobados (${datosAprobados.length})`;
            document.getElementById('filtro-rechazados').innerHTML = `❌ Rechazados (${datosRechazados.length})`;
            
            // Actualizar estadísticas superiores
            document.getElementById('cnt-pend').textContent = datosPendientes.length;
            document.getElementById('cnt-ap').textContent = datosAprobados.length;
            document.getElementById('cnt-re').textContent = datosRechazados.length;
            
            // Iniciar con vista según filtro actual
            if (filtroActual === 'todos') {
                datosActuales = [...datosPendientes];
                totalRegistros = datosPendientes.length;
            } else if (filtroActual === 'aprobados') {
                datosActuales = [...datosAprobados];
                totalRegistros = datosAprobados.length;
            } else if (filtroActual === 'rechazados') {
                datosActuales = [...datosRechazados];
                totalRegistros = datosRechazados.length;
            }
            
            totalPaginas = Math.ceil(totalRegistros / 10);
            if (totalPaginas === 0) totalPaginas = 1;
            renderPagina();
        })
        .catch((error) => {
            if (error.message !== 'Sesión expirada') {
                console.error('Error:', error);
                document.getElementById('tbody').innerHTML = '<tr><td colspan="7" class="val-empty-td" style="color:#A32D2D">Error al cargar los datos. Verifique su conexión.</td></tr>';
                mostrarAlerta('err', 'Error al cargar los datos: ' + error.message);
            }
        });
}

function mostrarAlerta(tipo, msg) {
    const el = document.getElementById('alerta');
    el.className = 'val-alerta show ' + tipo;
    document.getElementById('alerta-msg').textContent = msg;
    setTimeout(() => el.className = 'val-alerta', 5000);
}

function guardarTodo() {
    const lista = Object.values(marcados);
    if (!lista.length) return;

    const btn = document.getElementById('btn-guardar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';
    mostrarAlerta('info', `Guardando ${lista.length} registro(s) en lote...`);

    // Preparar el array de validaciones
    const validaciones = lista.map(item => ({
        cip: item.cip,
        respuesta: item.op
    }));

    // Enviar todas las validaciones en UNA sola petición
    fetch('/validar-asistencias-batch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ validaciones: validaciones })
    })
    .then(response => {
        if (response.status === 401) {
            window.location.href = '/login-eventos';
            throw new Error('Sesión expirada');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Limpiar marcados locales
            marcados = {};
            
            // Recargar todos los datos
            cargarDatos();
            
            // Mostrar mensaje de éxito
            if (data.errores && data.errores.length > 0) {
                mostrarAlerta('err', `Parcial: ${data.message}`);
            } else {
                mostrarAlerta('ok', data.message);
            }
        } else {
            mostrarAlerta('err', data.message || 'Error al guardar los cambios');
            btn.disabled = false;
            btn.textContent = 'Guardar cambios';
        }
    })
    .catch((error) => {
        if (error.message !== 'Sesión expirada') {
            console.error('Error:', error);
            btn.disabled = false;
            btn.textContent = 'Guardar cambios';
            mostrarAlerta('err', 'Error de conexión. Intenta nuevamente.');
        }
    });
}

function siguiente() { 
    if (pagina < totalPaginas) { 
        pagina++; 
        renderPagina(); 
    } 
}

function anterior() { 
    if (pagina > 1) { 
        pagina--; 
        renderPagina(); 
    } 
}

// Inicializar
cargarDatos();
</script>

@endsection