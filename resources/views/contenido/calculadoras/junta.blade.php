<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Cuantía Determinada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; padding: 20px; }
        .calc-container { background: white; max-width: 100%; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; }
        .header-img { width: 100%; height: 80px; background-color: #fff; object-fit: contain; border-bottom: 1px solid #eee; }
        .result-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fff; }
        .result-title { font-weight: bold; font-size: 14px; color: #333; margin-bottom: 5px; }
        .result-value { color: #666; font-size: 15px; }
        .btn-close-custom { border: 1px solid #ccc; background: white; padding: 5px 15px; border-radius: 4px; text-decoration: none; color: #333; display: inline-block; margin-top: 10px; }
        .warning-box { border: 1px solid #ff5252; color: #ff5252; padding: 10px; text-align: center; border-radius: 6px; font-weight: bold; font-size: 13px; margin-top: 20px; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="calc-container p-4">
        
        <div class="text-center mb-4">
            <img src="{{ asset('img/cdlima_encabezado.jpg') }}" alt="Logos CIP" class="header-img mb-3"> 
        </div>

        <h5 class="fw-bold">Calculadora de tarifario de arbitraje</h5>
        <p class="text-muted mb-4">Para cuantías determinadas</p>

        <div class="row g-3 mb-4">
            <div class="col-7">
                <label class="form-label fw-bold small">Cuantía de pretensiones (Soles)</label>
                <input type="number" id="inputCuantia" class="form-control" placeholder="0.00" oninput="calcular()">
            </div>
            <div class="col-5">
                <label class="form-label fw-bold small">Nro. Árbitro(s)</label>
                <select id="selectArbitros" class="form-select" onchange="calcular()">
                    <option value="unico">Árbitro Único</option>
                    <option value="tribunal">Tribunal (3)</option>
                </select>
            </div>
        </div>

        <div id="resultadosContainer">
            <div class="result-card">
                <div class="result-title">Honorarios para el árbitro</div>
                <div class="result-value" id="resArbitro1">S/. 0.00 + Impuesto</div>
            </div>
            
            <div class="result-card" id="cardArbitro2" style="display: none;">
                <div class="result-title">Honorarios para el árbitro 2</div>
                <div class="result-value" id="resArbitro2">S/. 0.00 + Impuesto</div>
            </div>

            <div class="result-card" id="cardArbitro3" style="display: none;">
                <div class="result-title">Honorarios para el árbitro 3</div>
                <div class="result-value" id="resArbitro3">S/. 0.00 + Impuesto</div>
            </div>

            <div class="result-card">
                <div class="result-title">Tasa administrativa CARD</div>
                <div class="result-value" id="resTasa">S/. 0.00 + IGV</div>
            </div>
        </div>

        <div class="warning-box">
            CADA PARTE DEBERÁ ASUMIR EL 50% DE LOS COSTOS
        </div>

        <button onclick="window.close()" class="btn-close-custom mt-3">
            ❮ Atrás
        </button>
        
        <div class="text-center mt-3 text-muted small">Versión v3.0</div>
    </div>

    <script>
        function calcular() {
            const cuantia = parseFloat(document.getElementById('inputCuantia').value) || 0;
            const tipo = document.getElementById('selectArbitros').value;
            
            // === LÓGICA DE EJEMPLO (CAMBIAR POR TABLA REAL DEL CIP) ===
            // Aquí debes implementar los rangos reales.
            // Ejemplo: 5% honorarios, 2% tasa administrativa
            let honorarioBase = 0;
            let tasaAdmin = 0;

            if (cuantia > 0) {
                // Ejemplo simple:
                if (cuantia <= 10000) { honorarioBase = 1500; tasaAdmin = 500; }
                else if (cuantia <= 50000) { honorarioBase = cuantia * 0.05; tasaAdmin = cuantia * 0.02; }
                else { honorarioBase = cuantia * 0.03; tasaAdmin = cuantia * 0.01; }
            }

            // Formatear moneda
            const fmt = (num) => "S/. " + num.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // Mostrar resultados
            document.getElementById('resTasa').innerText = fmt(tasaAdmin) + " + IGV";

            // Lógica Tribunal vs Único
            if (tipo === 'unico') {
                document.getElementById('resArbitro1').innerText = fmt(honorarioBase) + " + Impuesto";
                document.getElementById('cardArbitro2').style.display = 'none';
                document.getElementById('cardArbitro3').style.display = 'none';
                
                // Cambiar título de la tarjeta 1
                document.querySelector('#resultadosContainer .result-card:first-child .result-title').innerText = "Honorarios para Árbitro Único";
            } else {
                // En tribunal, a veces el presidente gana más, o todos igual. Asumiremos igual aquí.
                document.getElementById('resArbitro1').innerText = fmt(honorarioBase) + " + Impuesto";
                document.getElementById('resArbitro2').innerText = fmt(honorarioBase) + " + Impuesto";
                document.getElementById('resArbitro3').innerText = fmt(honorarioBase) + " + Impuesto";
                
                document.getElementById('cardArbitro2').style.display = 'block';
                document.getElementById('cardArbitro3').style.display = 'block';
                
                document.querySelector('#resultadosContainer .result-card:first-child .result-title').innerText = "Honorarios para el árbitro 1 (Presidente)";
            }
        }
    </script>
</body>
</html>