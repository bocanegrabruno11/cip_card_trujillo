<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Cuantía Indeterminada - Institución Arbitral</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; padding: 20px; }
        .calc-container { background: white; max-width: 100%; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; }
        .header-img { width: 100%; height: auto; max-height: 100px; object-fit: contain; margin-bottom: 20px; }
        
        /* Inputs */
        .form-label { font-size: 13px; font-weight: 700; color: #333; margin-bottom: 5px; display: block; height: 35px; line-height: 1.2; }
        .form-control, .form-select { border-radius: 6px; border: 1px solid #ccc; padding: 10px; font-size: 15px; }
        
        /* Resultados */
        .result-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .result-title { font-weight: bold; font-size: 15px; color: #333; margin-bottom: 5px; }
        .result-value { color: #666; font-size: 16px; font-weight: 500; }
        
        .btn-close-custom { border: 1px solid #ccc; background: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; color: #333; display: inline-block; margin-top: 10px; font-weight: bold; font-size: 14px; transition: all 0.2s; }
        .btn-close-custom:hover { background: #eee; }

        .warning-box { border: 1px solid #ff5252; color: #ff5252; padding: 12px; text-align: center; border-radius: 6px; font-weight: bold; font-size: 13px; margin-top: 25px; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>

    <div class="calc-container p-4">
        
        <div class="text-center">
            <img src="{{ asset('img/cdlima_encabezado.jpg') }}" alt="Logos CIP" class="header-img"> 
        </div>

        <h5 class="fw-bold mb-1">Calculadora de tarifario de arbitraje</h5>
        <p class="text-muted mb-4 small">Para cuantías indeterminadas</p>

        <div class="row g-3 mb-4">
            <div class="col-md-4 col-6">
                <label class="form-label">Monto contractual <br>(Soles)</label>
                <input type="number" id="inputMonto" class="form-control" placeholder="0.00" oninput="calcular()">
            </div>
            <div class="col-md-4 col-6">
                <label class="form-label">Cantidad de pretensiones <br>indeterminadas</label>
                <input type="number" id="inputCantidad" class="form-control" value="1" min="1" oninput="calcular()">
            </div>
            <div class="col-md-4 col-12">
                <label class="form-label"><br>Nro. Árbitro(s)</label>
                <select id="selectArbitros" class="form-select" onchange="calcular()">
                    <option value="unico">Único</option>
                    <option value="tribunal">Tribunal</option>
                </select>
            </div>
        </div>

        <div id="resultadosContainer">
            <div class="result-card">
                <div class="result-title" id="tituloArbitro1">Honorarios del árbitro único</div>
                <div class="result-value" id="resArbitro1">S/. 0.00 + Impuesto (Retención o IGV)</div>
            </div>
            
            <div class="result-card" id="cardArbitro2" style="display: none;">
                <div class="result-title">Honorarios para el árbitro 2</div>
                <div class="result-value" id="resArbitro2">S/. 0.00 + Impuesto (Retención o IGV)</div>
            </div>

            <div class="result-card" id="cardArbitro3" style="display: none;">
                <div class="result-title">Honorarios para el árbitro 3</div>
                <div class="result-value" id="resArbitro3">S/. 0.00 + Impuesto (Retención o IGV)</div>
            </div>

            <div class="result-card">
                <div class="result-title">Tasa administrativa CARD</div>
                <div class="result-value" id="resTasa">S/. 0.00 + IGV ({{ number_format($igv, 0) }}%)</div>
            </div>
        </div>

        <div class="warning-box">
            CADA PARTE DEBERÁ ASUMIR EL 50% DE LOS COSTOS
        </div>

        <button onclick="window.close()" class="btn-close-custom">
            ❮ Atrás
        </button>
        
        <div class="text-center mt-3 text-muted" style="font-size: 11px;">Versión v3.0</div>
    </div>

    <script>
        // === 1. DATOS DINÁMICOS ===
        const escalasData = @json($data);
        const igv = {{ $igv }}; 
        const porcentajeIndeterminado = {{ $porcentajeIndeterminado }}; // Generalmente 5.00

        const fmt = (num) => "S/. " + num.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        // === 2. LÓGICA REUTILIZABLE DE ESCALAS (Igual que en determinada) ===
        function calcularMontoPorEscala(montoBase, tipoEscala) {
            let tabla = [];
            if (tipoEscala === 'unico') tabla = escalasData.unico;
            else if (tipoEscala === 'tribunal') tabla = escalasData.tribunal;
            else if (tipoEscala === 'gastos') tabla = escalasData.gastos;

            let tarifaEncontrada = tabla.find(t => {
                let min = parseFloat(t.monto_min);
                let max = t.monto_max ? parseFloat(t.monto_max) : Infinity;
                return montoBase >= min && montoBase <= max;
            });

            if (!tarifaEncontrada) return 0;

            let fijo = parseFloat(tarifaEncontrada.monto_fijo);
            let porcentaje = parseFloat(tarifaEncontrada.porcentaje_exceso);
            let base = parseFloat(tarifaEncontrada.base_exceso);

            if (fijo === 0 && porcentaje === 0) return -1; // A criterio

            let variable = 0;
            if (porcentaje > 0) variable = (montoBase - base) * (porcentaje / 100);

            return fijo + variable;
        }

        // === 3. LÓGICA PRINCIPAL (INDETERMINADA) ===
        function calcular() {
            const montoContractual = parseFloat(document.getElementById('inputMonto').value) || 0;
            const cantidadPretensiones = parseInt(document.getElementById('inputCantidad').value) || 1;
            const tipoArbitraje = document.getElementById('selectArbitros').value;

            // --- A. CÁLCULO DEL "MONTO BASE" (Lógica del PDF Punto V) ---
            // 1. Calculamos el valor por pretensión: Monto Contrato * (5% / 100)
            let valorPorPretension = montoContractual * (porcentajeIndeterminado / 100);
            
            // 2. Sumamos las pretensiones
            let montoBaseCalculo = valorPorPretension * cantidadPretensiones;

            // 3. Regla 4 del PDF: "El valor máximo... no será mayor al monto del contrato original"
            if (montoBaseCalculo > montoContractual) {
                montoBaseCalculo = montoContractual;
            }

            // --- B. APLICAR ESCALAS CON EL MONTO CALCULADO ---
            let totalHonorarios = 0;
            let textoHonorario = "S/. 0.00";
            let esCriterio = false;

            if (montoBaseCalculo > 0) {
                if (tipoArbitraje === 'unico') {
                    totalHonorarios = calcularMontoPorEscala(montoBaseCalculo, 'unico');
                } else {
                    totalHonorarios = calcularMontoPorEscala(montoBaseCalculo, 'tribunal');
                }

                if (totalHonorarios === -1) {
                    textoHonorario = "A criterio del Directorio";
                    esCriterio = true;
                } else {
                    if (tipoArbitraje === 'tribunal') totalHonorarios = totalHonorarios / 3;
                    textoHonorario = fmt(totalHonorarios) + " + Impuesto (Retención o IGV)";
                }
            } else {
                textoHonorario = "S/. 0.00 + Impuesto (Retención o IGV)";
            }

            // Gastos Admin
            let gastosAdmin = 0;
            let textoGastos = "S/. 0.00 + IGV";
            if (montoBaseCalculo > 0) {
                gastosAdmin = calcularMontoPorEscala(montoBaseCalculo, 'gastos');
                if (gastosAdmin === -1) textoGastos = "A criterio del Directorio";
                else textoGastos = fmt(gastosAdmin) + " + IGV";
            }

            // --- C. RENDERIZADO ---
            document.getElementById('resTasa').innerText = textoGastos;

            const card2 = document.getElementById('cardArbitro2');
            const card3 = document.getElementById('cardArbitro3');
            const titulo1 = document.getElementById('tituloArbitro1');

            if (tipoArbitraje === 'unico') {
                card2.style.display = 'none';
                card3.style.display = 'none';
                titulo1.innerText = "Honorarios del árbitro único";
            } else {
                card2.style.display = 'block';
                card3.style.display = 'block';
                titulo1.innerText = "Honorarios para el árbitro 1";
            }

            if(esCriterio) {
                document.getElementById('resArbitro1').innerText = textoHonorario;
                document.getElementById('resArbitro2').innerText = textoHonorario;
                document.getElementById('resArbitro3').innerText = textoHonorario;
            } else {
                document.getElementById('resArbitro1').innerText = textoHonorario;
                document.getElementById('resArbitro2').innerText = textoHonorario;
                document.getElementById('resArbitro3').innerText = textoHonorario;
            }
        }
    </script>
</body>
</html>