<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora JPRD - CARD CIP CDLL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
        
        .calc-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            max-width: 100%;
        }

        .header-section {
            padding: 20px;
            background: #fff;
            border-bottom: 2px solid #AD2B2E;
            text-align: center;
        }
        .header-logo { max-height: 70px; object-fit: contain; }

        .calc-body { padding: 30px; }

        .calc-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
            text-transform: uppercase;
        }

        /* Inputs */
        .form-label { font-size: 13px; font-weight: 700; color: #555; text-transform: uppercase; margin-bottom: 5px; }
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #AD2B2E;
            box-shadow: 0 0 0 0.2rem rgba(173, 43, 46, 0.25);
        }

        /* Resultados */
        .results-section { margin-top: 30px; display: none; /* Oculto al inicio */ }
        
        .result-group {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #fff;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .group-header {
            background-color: #f8f9fa;
            padding: 12px 20px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
            font-weight: 800;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            border-bottom: 1px dashed #eee;
            font-size: 14px;
        }
        .result-row:last-child { border-bottom: none; }

        .row-label { color: #666; font-weight: 500; }
        .row-value { font-weight: 700; color: #333; font-size: 15px; }
        .tax-suffix { font-size: 11px; color: #999; font-weight: 400; margin-left: 5px; }

        .total-row {
            background-color: #fff9f9;
            color: #AD2B2E;
        }

        /* Botón cerrar */
        .footer-actions { text-align: center; margin-top: 30px; }
        .btn-close-custom {
            background: #fff;
            color: #555;
            border: 1px solid #ccc;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-close-custom:hover { background: #eee; color: #333; text-decoration: none; }

        @media (max-width: 576px) {
            .result-row { flex-direction: column; align-items: flex-start; gap: 5px; }
            .row-value { align-self: flex-end; }
        }
    </style>
</head>
<body>

<div class="calc-wrapper">
    
    {{-- Header con Logo --}}
    <div class="header-section">
        <img src="{{ asset('img/cdlima_encabezado.jpg') }}" alt="CIP CDLL" class="header-logo">
    </div>

    <div class="calc-body">
        
        <h1 class="calc-title">Calculadora de Tarifario de Junta de Prevención y Resolución de Disputas (JPRD)</h1>

        {{-- Formulario de Entrada --}}
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <label class="form-label">Monto Contractual (Soles)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">S/.</span>
                    <input type="number" id="inputMonto" class="form-control border-start-0 ps-0" placeholder="0.00" min="0" step="0.01" oninput="calcular()">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Nro. Adjudicador(es)</label>
                <select id="selectTipo" class="form-select" onchange="calcular()">
                    <option value="unico">Único</option>
                    <option value="tribunal">Tribunal</option>
                </select>
            </div>
        </div>

        {{-- Sección de Resultados --}}
        <div id="resultsContainer" class="results-section">
            
            {{-- BLOQUE 1: PAGO ENTIDAD --}}
            <div class="result-group">
                <div class="group-header">Monto Mensual de Pago para ENTIDAD:</div>
                
                {{-- Tasa Administrativa --}}
                <div class="result-row">
                    <span class="row-label">Tasa administrativa CARD</span>
                    <div class="text-end">
                        <span class="row-value" id="valTasaEntidad">S/. 0.00</span>
                        <span class="tax-suffix">+ IGV</span>
                    </div>
                </div>

                {{-- Honorarios Dinámicos --}}
                <div id="rowsHonorariosEntidad">
                    </div>
            </div>

            {{-- BLOQUE 2: PAGO CONTRATISTA --}}
            <div class="result-group">
                <div class="group-header">Monto Mensual de Pago para CONTRATISTA:</div>
                
                {{-- Tasa Administrativa --}}
                <div class="result-row">
                    <span class="row-label">Tasa administrativa CARD</span>
                    <div class="text-end">
                        <span class="row-value" id="valTasaContratista">S/. 0.00</span>
                        <span class="tax-suffix">+ IGV</span>
                    </div>
                </div>

                {{-- Honorarios Dinámicos --}}
                <div id="rowsHonorariosContratista">
                    </div>
            </div>

        </div>

        {{-- Mensaje si no hay cálculo --}}
        <div id="emptyState" class="text-center text-muted py-5">
            <i class="fas fa-calculator fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
            <p>Ingrese el monto contractual para ver el cálculo.</p>
        </div>

        <div class="footer-actions">
            <button onclick="window.close()" class="btn-close-custom">
                <i class="fas fa-arrow-left me-2"></i> Volver / Cerrar
            </button>
        </div>

    </div>
</div>

{{-- DATOS DESDE LARAVEL A JS --}}
<script>
    // Convertimos las colecciones de Laravel a JSON
    const dataUnico = @json($data['unico']);
    const dataTribunal = @json($data['tribunal']);
    const dataGastos = @json($data['gastos']);
    
    // Configuración
    const IGV = {{ $igv }}; 
    const PORCENTAJE_INDETERMINADO = {{ $porcentajeIndeterminado }}; // No se usa en esta lógica específica, pero lo tenemos disponible

    function calcular() {
        const montoInput = document.getElementById('inputMonto');
        const tipoSelect = document.getElementById('selectTipo');
        const resultsContainer = document.getElementById('resultsContainer');
        const emptyState = document.getElementById('emptyState');

        const monto = parseFloat(montoInput.value);
        const tipo = tipoSelect.value; // 'unico' o 'tribunal'

        if (!monto || monto <= 0) {
            resultsContainer.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        // 1. Calcular GASTOS ADMINISTRATIVOS (Tasa)
        // Buscamos en la tabla de gastos
        const costoTasaTotal = calcularTarifa(monto, dataGastos);
        // Según la imagen, la tasa se divide 50% Entidad y 50% Contratista
        const tasaParte = costoTasaTotal / 2; 

        // 2. Calcular HONORARIOS
        let honorarioTotal = 0;
        let tablaHonorarios = [];

        if (tipo === 'unico') {
            tablaHonorarios = dataUnico;
        } else {
            tablaHonorarios = dataTribunal;
        }

        // Obtenemos el honorario TOTAL por adjudicador según la tabla
        const honorarioPorAdjudicador = calcularTarifa(monto, tablaHonorarios);
        
        // El honorario calculado también se divide 50/50 entre las partes
        const honorarioParte = honorarioPorAdjudicador / 2;

        // 3. Renderizar Resultados
        
        // A) Llenar Tasas
        document.getElementById('valTasaEntidad').innerText = formatMoney(tasaParte);
        document.getElementById('valTasaContratista').innerText = formatMoney(tasaParte);

        // B) Generar HTML para Honorarios (Entidad y Contratista es igual)
        const htmlHonorarios = generarHtmlHonorarios(tipo, honorarioParte);
        
        document.getElementById('rowsHonorariosEntidad').innerHTML = htmlHonorarios;
        document.getElementById('rowsHonorariosContratista').innerHTML = htmlHonorarios;

        // Mostrar
        resultsContainer.style.display = 'block';
        emptyState.style.display = 'none';
    }

    // Función core para buscar en los rangos
    function calcularTarifa(monto, tabla) {
        // Encontrar el rango correspondiente
        const rango = tabla.find(r => {
            // Si monto_max es null, es infinito
            const max = r.monto_max === null ? Infinity : parseFloat(r.monto_max);
            const min = parseFloat(r.monto_min);
            return monto >= min && monto <= max;
        });

        if (!rango) return 0; // Si no encuentra rango (raro)

        const fijo = parseFloat(rango.monto_fijo);
        
        // Cálculo del exceso
        let exceso = 0;
        if (parseFloat(rango.porcentaje_exceso) > 0) {
            const base = parseFloat(rango.base_exceso);
            const excedente = monto - base;
            if (excedente > 0) {
                exceso = excedente * (parseFloat(rango.porcentaje_exceso) / 100);
            }
        }

        return fijo + exceso;
    }

    function generarHtmlHonorarios(tipo, monto) {
        let html = '';
        const sufijo = ' + Impuesto (Retención o IGV)';
        const valorFmt = formatMoney(monto);

        if (tipo === 'unico') {
            html += `
                <div class="result-row">
                    <span class="row-label">Honorarios del adjudicador único</span>
                    <div class="text-end">
                        <span class="row-value">${valorFmt}</span>
                        <span class="tax-suffix">${sufijo}</span>
                    </div>
                </div>`;
        } else {
            // Tribunal (3 miembros)
            // Según la imagen, muestra Adjudicador 1, 2 y 3. 
            // Asumiremos que todos cobran lo mismo basado en la tabla.
            for (let i = 1; i <= 3; i++) {
                html += `
                    <div class="result-row">
                        <span class="row-label">Honorarios para el adjudicador ${i}</span>
                        <div class="text-end">
                            <span class="row-value">${valorFmt}</span>
                            <span class="tax-suffix">${sufijo}</span>
                        </div>
                    </div>`;
            }
        }
        return html;
    }

    function formatMoney(amount) {
        return 'S/. ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
</script>

</body>
</html>