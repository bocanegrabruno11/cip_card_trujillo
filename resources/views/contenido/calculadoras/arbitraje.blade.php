<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Arbitraje - CARD CDLL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --rojo-institucional: #AD2B2E; --gris-claro: #f8f9fa; }
        body { background-color: var(--gris-claro); font-family: 'Segoe UI', sans-serif; padding: 30px 15px; }
        
        .calc-card {
            background: white; max-width: 700px; margin: 0 auto;
            border-radius: 12px; box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            border-top: 5px solid var(--rojo-institucional);
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
            transition: transform 0.2s;
        }
        .result-box.highlight { border-left-color: var(--rojo-institucional); background-color: #fffdfd; }
        
        .res-label { font-size: 0.85rem; font-weight: 700; color: #666; text-transform: uppercase; }
        .res-amount { font-size: 1.25rem; font-weight: 800; color: #333; }
        .res-tax { font-size: 0.75rem; color: #999; display: block; text-align: right; }

        .info-pill {
            background-color: #e3f2fd; color: #0d47a1; font-size: 0.75rem;
            padding: 5px 10px; border-radius: 20px; display: inline-block; margin-top: 5px;
        }

        .reference-box {
            background-color: #eaf4ff;
            border: 1px solid #cce5ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }

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
        <h4 class="calc-title">Calculadora de Arbitraje</h4>
        <p class="text-muted small mt-2">Estimación de gastos y honorarios</p>
    </div>

    <div class="input-section">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">TIPO DE PRETENSIONES</label>
                <select id="tipoCuantia" class="form-select" onchange="actualizarInterfaz()">
                    <option value="determinada">Cuantía Determinada</option>
                    <option value="indeterminada">Cuantía Indeterminada</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">TIPO DE ÓRGANO</label>
                <select id="tipoOrgano" class="form-select" onchange="calcular()">
                    <option value="unico">Árbitro Único</option>
                    <option value="tribunal">Tribunal Arbitral (3 Árbitros)</option>
                </select>
            </div>

            <div class="col-12">
                <label id="labelMonto" class="form-label fw-bold text-danger">CUANTÍA DE LA CONTROVERSIA (S/.)</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white text-muted">S/.</span>
                    <input type="number" id="inputMonto" class="form-control fw-bold" placeholder="0.00" oninput="calcular()">
                </div>
                <div id="infoIndeterminada" class="info-pill d-none">
                    <i class="fas fa-info-circle me-1"></i> Se aplicará el 4% al monto del contrato.
                </div>
            </div>

            <div id="divCantidadPretensiones" class="col-12 d-none">
                <label class="form-label fw-bold small text-muted">NRO. DE PRETENSIONES INDETERMINADAS</label>
                <input type="number" id="cantidadPretensiones" class="form-control" value="1" min="1" oninput="calcular()">
                <div class="form-text text-muted" style="font-size: 11px;">
                    * Según Art. V.3.d: Se multiplicará el resultado final por este número.
                </div>
            </div>
        </div>
    </div>

    <div class="result-section">
        
        <div id="boxReferencia" class="reference-box d-none">
            <div class="row text-center align-items-center">
                <div class="col-6 border-end border-secondary">
                    <div class="small text-muted fw-bold mb-1">MONTO DEL CONTRATO</div>
                    <div class="fs-6 fw-bold text-dark" id="txtMontoContrato">S/. 0.00</div>
                </div>
                <div class="col-6">
                    <div class="small text-primary fw-bold mb-1">VALOR DE REFERENCIA (4%)</div>
                    <div class="fs-5 fw-bold text-primary" id="txtReferencia">S/. 0.00</div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">RESULTADOS ESTIMADOS</h6>

        <div class="result-box highlight">
            <div>
                <div class="res-label" id="labelHonorarios">Honorarios Árbitro Único</div>
                <small class="text-muted" style="font-size: 11px;">(Por árbitro)</small>
            </div>
            <div>
                <div class="res-amount" id="resHonorarios">S/. 0.00</div>
                <span class="res-tax">+ Impuestos de Ley</span>
            </div>
        </div>

        <div class="result-box" id="boxTotalTribunal" style="display: none; background: #f8f9fa; border-left: 4px solid #6c757d;">
            <div>
                <div class="res-label">Total Tribunal (3 Árbitros)</div>
            </div>
            <div>
                <div class="res-amount text-secondary" id="resTotalTribunal">S/. 0.00</div>
                <span class="res-tax">+ Impuestos</span>
            </div>
        </div>

        <div class="result-box" style="border-left-color: #D4AF37;">
            <div>
                <div class="res-label">Tasa Administrativa CARD</div>
            </div>
            <div>
                <div class="res-amount" id="resGastos">S/. 0.00</div>
                <span class="res-tax">+ IGV ({{ number_format($igv, 0) }}%)</span>
            </div>
        </div>

        <div class="alert alert-warning d-flex align-items-center mt-3 mb-0 py-2" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div style="font-size: 12px; line-height: 1.2;">
                <strong>Nota:</strong> Cada parte deberá asumir el 50% de los costos totales calculados.
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
             <button onclick="window.close()" class="btn btn-sm btn-outline-secondary">Cerrar</button>

             <form action="{{ route('calculadora.arbitraje.pdf') }}" method="POST" id="formPdf" target="_blank">
                @csrf
                <input type="hidden" name="monto" id="pdfMonto">
                <input type="hidden" name="tipo_cuantia" id="pdfTipoCuantia">
                <input type="hidden" name="tipo_organo" id="pdfTipoOrgano">
                <input type="hidden" name="cantidad_pretensiones" id="pdfCantidad">
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

    function actualizarInterfaz() {
        const tipo = document.getElementById('tipoCuantia').value;
        const label = document.getElementById('labelMonto');
        const info = document.getElementById('infoIndeterminada');
        const boxRef = document.getElementById('boxReferencia');
        const divPretensiones = document.getElementById('divCantidadPretensiones');

        if (tipo === 'indeterminada') {
            label.innerText = "MONTO DEL CONTRATO ORIGINAL (S/.)";
            info.classList.remove('d-none');
            boxRef.classList.remove('d-none');
            divPretensiones.classList.remove('d-none');
        } else {
            label.innerText = "CUANTÍA DE LA CONTROVERSIA (S/.)";
            info.classList.add('d-none');
            boxRef.classList.add('d-none');
            divPretensiones.classList.add('d-none');
            document.getElementById('cantidadPretensiones').value = 1;
        }
        calcular();
    }

    function buscarEnTabla(monto, tabla) {
        return tabla.find(t => {
            let min = parseFloat(t.monto_min);
            let max = t.monto_max ? parseFloat(t.monto_max) : Infinity;
            return monto >= min && monto <= max;
        });
    }

    function calcularTarifa(montoBase, tabla) {
        const rango = buscarEnTabla(montoBase, tabla);
        if (!rango) return 0;

        let fijo = parseFloat(rango.monto_fijo);
        let porcentaje = parseFloat(rango.porcentaje_exceso);
        let baseExceso = parseFloat(rango.base_exceso);

        if (fijo === 0 && porcentaje === 0) return -1; 

        let variable = 0;
        if (porcentaje > 0) {
            variable = (montoBase - baseExceso) * (porcentaje / 100);
        }

        return fijo + variable;
    }

    function calcular() {
        const inputVal = document.getElementById('inputMonto').value;
        let montoInput = parseFloat(inputVal);
        if (isNaN(montoInput) || montoInput < 0) montoInput = 0;

        const tipoCuantia = document.getElementById('tipoCuantia').value;
        const tipoOrgano = document.getElementById('tipoOrgano').value;
        
        let cantidad = 1;
        if (tipoCuantia === 'indeterminada') {
            const cantVal = document.getElementById('cantidadPretensiones').value;
            cantidad = parseInt(cantVal);
            if (isNaN(cantidad) || cantidad < 1) cantidad = 1;
        }

        // --- LÓGICA PRINCIPAL ---
        let montoCalculo = montoInput;

        if (tipoCuantia === 'indeterminada') {
            document.getElementById('txtMontoContrato').innerText = fmt(montoInput);
            let refValue = montoInput * 0.04;
            document.getElementById('txtReferencia').innerText = fmt(refValue);
            
            montoCalculo = refValue;
        }

        let tablaHonorarios = (tipoOrgano === 'unico') ? escalas.unico : escalas.tribunal;
        let honorariosBase = calcularTarifa(montoCalculo, tablaHonorarios);
        let gastosBase = calcularTarifa(montoCalculo, escalas.gastos);

        // --- MULTIPLICADOR (Solo Indeterminadas) ---
        let honorariosFinal = (honorariosBase === -1) ? -1 : (honorariosBase * cantidad);
        let gastosFinal = (gastosBase === -1) ? -1 : (gastosBase * cantidad);

        mostrarResultados(honorariosFinal, gastosFinal, tipoOrgano);
    }

    function mostrarResultados(honorarios, gastos, tipoOrgano) {
        const elHono = document.getElementById('resHonorarios');
        const elTotalTrib = document.getElementById('resTotalTribunal');
        const boxTotalTrib = document.getElementById('boxTotalTribunal');
        const elGastos = document.getElementById('resGastos');
        const labelHono = document.getElementById('labelHonorarios');

        if (honorarios === -1) {
            elHono.innerText = "A criterio del Directorio";
            elTotalTrib.innerText = "A criterio";
        } else {
            if (tipoOrgano === 'unico') {
                elHono.innerText = fmt(honorarios);
                boxTotalTrib.style.display = 'none';
                labelHono.innerText = "Honorarios Árbitro Único";
            } else {
                let porArbitro = honorarios / 3;
                elHono.innerText = fmt(porArbitro);
                elTotalTrib.innerText = fmt(honorarios);
                boxTotalTrib.style.display = 'flex';
                labelHono.innerText = "Honorarios Por Árbitro";
            }
        }

        if (gastos === -1) {
            elGastos.innerText = "A criterio del Directorio";
        } else {
            elGastos.innerText = fmt(gastos);
        }
    }

    // FUNCIÓN EXPORTAR PDF
    function exportarPDF() {
        const monto = document.getElementById('inputMonto').value;
        
        if(!monto || parseFloat(monto) <= 0) {
            alert('Por favor ingrese un monto válido antes de exportar.');
            return;
        }

        // Llenar inputs ocultos
        document.getElementById('pdfMonto').value = monto;
        document.getElementById('pdfTipoCuantia').value = document.getElementById('tipoCuantia').value;
        document.getElementById('pdfTipoOrgano').value = document.getElementById('tipoOrgano').value;
        document.getElementById('pdfCantidad').value = document.getElementById('cantidadPretensiones').value;

        // Enviar
        document.getElementById('formPdf').submit();
    }
</script>

</body>
</html>