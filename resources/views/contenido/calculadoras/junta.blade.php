<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora JPRD - CARD CDLL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* COLORES ACTUALIZADOS A ROJO INSTITUCIONAL */
        :root { --rojo-institucional: #AD2B2E; --rojo-claro: #fdeded; --gris-claro: #f8f9fa; }
        
        body { background-color: var(--gris-claro); font-family: 'Segoe UI', sans-serif; padding: 30px 15px; }
        
        .calc-card {
            background: white; max-width: 700px; margin: 0 auto;
            border-radius: 12px; box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            border-top: 5px solid var(--rojo-institucional); /* ROJO */
            overflow: hidden;
        }
        
        .header-section { text-align: center; padding: 30px 30px 10px; }
        .header-logo { height: 70px; margin-bottom: 15px; }
        .calc-title { color: var(--rojo-institucional); font-weight: 800; text-transform: uppercase; margin: 0; }
        
        .input-section { padding: 20px 40px; background-color: #fff; }
        .result-section { padding: 30px 40px; background-color: #fcfcfc; border-top: 1px solid #eee; }
        
        .result-box {
            background: white; border: 1px solid #e0e0e0; border-left: 4px solid #333;
            border-radius: 6px; padding: 15px; margin-bottom: 15px;
            display: flex; justify-content: space-between; align-items: center;
            transition: all 0.3s;
        }
        /* Highlight ahora es Rojo */
        .result-box.highlight { border-left-color: var(--rojo-institucional); background-color: var(--rojo-claro); }
        .result-box.disabled { border-left-color: #dc3545; background-color: #fff5f5; opacity: 0.8; }
        
        .res-label { font-size: 0.85rem; font-weight: 700; color: #666; text-transform: uppercase; }
        .res-amount { font-size: 1.25rem; font-weight: 800; color: #333; }
        .res-tax { font-size: 0.75rem; color: #999; display: block; text-align: right; }

        .btn-pdf {
            background-color: #333; color: white; border: none;
            font-size: 0.85rem; font-weight: 600; padding: 8px 20px;
            border-radius: 5px; text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px; transition: 0.3s;
        }
        .btn-pdf:hover { background-color: var(--rojo-institucional); color: white; }
    </style>
</head>
<body>

<div class="calc-card">
    <div class="header-section">
        <img src="{{ asset('img/cdlima_encabezado.jpg') }}" alt="Logo CARD" class="header-logo">
        <h4 class="calc-title">Calculadora JPRD</h4>
        <p class="text-muted small mt-2">Junta de Prevención y Resolución de Disputas</p>
    </div>

    <div class="input-section">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold text-danger">MONTO DEL CONTRATO DE OBRA (S/.)</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white text-muted">S/.</span>
                    <input type="number" id="inputMonto" class="form-control fw-bold" placeholder="0.00" oninput="calcular()">
                </div>
                <div class="form-text text-end" id="rangoDetectado">Rango: -</div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold small text-muted">TIPO DE ADJUDICADOR</label>
                <select id="tipoMiembro" class="form-select" onchange="calcular()">
                    <option value="unico">Miembro Único / Adjudicador</option>
                    <option value="tribunal">JRD (3 Miembros)</option>
                </select>
                <div id="alertaTipo" class="alert alert-danger mt-2 d-none py-2 small">
                    <i class="fas fa-exclamation-circle"></i> <span id="textoAlerta"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="result-section">
        <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">HONORARIOS MENSUALES</h6>

        <div class="result-box highlight" id="boxHonorarios">
            <div>
                <div class="res-label" id="labelHonorarios">Honorarios Miembro Único</div>
                <small class="text-muted" style="font-size: 11px;">(Mensual)</small>
            </div>
            <div>
                <div class="res-amount" id="resHonorarios">S/. 0.00</div>
                <span class="res-tax">+ Impuestos de Ley</span>
            </div>
        </div>

        <div class="result-box" id="boxTotalTribunal" style="display: none; background: #f8f9fa; border-left: 4px solid #6c757d;">
            <div>
                <div class="res-label">Total Mensual JRD (3 Miembros)</div>
            </div>
            <div>
                <div class="res-amount text-secondary" id="resTotalTribunal">S/. 0.00</div>
                <span class="res-tax">+ Impuestos</span>
            </div>
        </div>

        <div class="result-box" style="border-left-color: #333;">
            <div>
                <div class="res-label">Tasa Administrativa CARD</div>
                <small class="text-muted" style="font-size: 11px;">(Mensual)</small>
            </div>
            <div>
                <div class="res-amount" id="resGastos">S/. 0.00</div>
                <span class="res-tax">+ IGV ({{ number_format($igv, 0) }}%)</span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <button onclick="window.close()" class="btn btn-sm btn-outline-secondary">Cerrar</button>
            
            <form action="{{ route('calculadora.junta.pdf') }}" method="POST" id="formPdf" target="_blank">
                @csrf
                <input type="hidden" name="monto" id="pdfMonto">
                <input type="hidden" name="tipo_miembro" id="pdfTipo">
                <button type="button" onclick="exportarPDF()" class="btn-pdf">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const escalas = @json($data);
    const igv = {{ $igv }};
    const fmt = (num) => "S/. " + num.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function buscarTarifa(monto, tabla) {
        return tabla.find(t => {
            let min = parseFloat(t.monto_min);
            let max = t.monto_max ? parseFloat(t.monto_max) : Infinity;
            return monto >= min && monto <= max;
        });
    }

    function calcular() {
        const inputVal = document.getElementById('inputMonto').value;
        let monto = parseFloat(inputVal);
        const tipoSeleccionado = document.getElementById('tipoMiembro').value;

        if (isNaN(monto) || monto < 0) {
            limpiarResultados();
            return;
        }

        // 1. TASA ADMINISTRATIVA
        const tarifaAdmin = buscarTarifa(monto, escalas.administrativos);
        const montoAdmin = tarifaAdmin ? parseFloat(tarifaAdmin.monto_fijo) : 0;
        
        if(tarifaAdmin) {
            document.getElementById('rangoDetectado').innerText = "Rango Detectado: " + tarifaAdmin.rango_letra;
        } else {
            document.getElementById('rangoDetectado').innerText = "Fuera de Rango";
        }

        // 2. HONORARIOS
        let montoHonorarioUnitario = 0;
        let montoHonorarioTotal = 0;
        let aplica = false;
        let mensajeError = "";

        if (tipoSeleccionado === 'unico') {
            const tarifaUnico = buscarTarifa(monto, escalas.unico);
            if (tarifaUnico) {
                montoHonorarioUnitario = parseFloat(tarifaUnico.monto_fijo);
                aplica = true;
            } else {
                if (monto > 40000000) mensajeError = "Para montos mayores a S/ 40 Millones, corresponde un TRIBUNAL (3 Miembros).";
                else if (monto > 0 && monto < 5000000) mensajeError = "Monto menor al mínimo requerido (5 Millones).";
            }
        } else { 
            const tarifaTribunal = buscarTarifa(monto, escalas.tribunal);
            if (tarifaTribunal) {
                montoHonorarioUnitario = parseFloat(tarifaTribunal.monto_fijo);
                montoHonorarioTotal = montoHonorarioUnitario * 3;
                aplica = true;
            } else {
                if (monto <= 40000000 && monto >= 5000000) mensajeError = "Para montos menores a S/ 40 Millones, corresponde MIEMBRO ÚNICO.";
                else if (monto > 0) mensajeError = "Monto fuera de rango.";
            }
        }

        actualizarUI(montoAdmin, montoHonorarioUnitario, montoHonorarioTotal, tipoSeleccionado, aplica, mensajeError);
    }

    function actualizarUI(admin, unitario, total, tipo, aplica, error) {
        const elHono = document.getElementById('resHonorarios');
        const elGastos = document.getElementById('resGastos');
        const boxHono = document.getElementById('boxHonorarios');
        const alerta = document.getElementById('alertaTipo');
        const labelHono = document.getElementById('labelHonorarios');
        const boxTotal = document.getElementById('boxTotalTribunal');
        const elTotal = document.getElementById('resTotalTribunal');

        elGastos.innerText = admin > 0 ? fmt(admin) : "S/. 0.00";

        if (!aplica && error !== "") {
            boxHono.classList.add('disabled');
            boxHono.classList.remove('highlight');
            elHono.innerText = "NO APLICA";
            alerta.classList.remove('d-none');
            document.getElementById('textoAlerta').innerText = error;
            boxTotal.style.display = 'none';
        } else {
            boxHono.classList.remove('disabled');
            boxHono.classList.add('highlight');
            alerta.classList.add('d-none');
            
            elHono.innerText = fmt(unitario);

            if (tipo === 'unico') {
                labelHono.innerText = "Honorarios Miembro Único";
                boxTotal.style.display = 'none';
            } else {
                labelHono.innerText = "Honorarios Por Miembro";
                boxTotal.style.display = 'flex';
                elTotal.innerText = fmt(total);
            }
        }
    }

    function limpiarResultados() {
        document.getElementById('resHonorarios').innerText = "S/. 0.00";
        document.getElementById('resGastos').innerText = "S/. 0.00";
        document.getElementById('rangoDetectado').innerText = "Rango: -";
        document.getElementById('alertaTipo').classList.add('d-none');
        document.getElementById('boxHonorarios').classList.remove('disabled');
    }

    // FUNCIÓN PARA EXPORTAR
    function exportarPDF() {
        const monto = document.getElementById('inputMonto').value;
        
        if(!monto || parseFloat(monto) <= 0) {
            alert('Por favor ingrese un monto válido antes de exportar.');
            return;
        }

        // Llenar inputs ocultos y enviar formulario
        document.getElementById('pdfMonto').value = monto;
        document.getElementById('pdfTipo').value = document.getElementById('tipoMiembro').value;
        document.getElementById('formPdf').submit();
    }
</script>

</body>
</html>