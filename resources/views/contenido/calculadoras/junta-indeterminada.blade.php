<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Cuantía Indeterminada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; padding: 20px; }
        .calc-container { background: white; max-width: 100%; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; }
        .header-img { width: 100%; height: 80px; background-color: #fff; object-fit: contain; border-bottom: 1px solid #eee; }
        .info-card { background: #e3f2fd; color: #0d47a1; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #0d47a1; }
        .fixed-fee { font-size: 24px; font-weight: bold; color: #333; }
        .fee-label { font-size: 14px; text-transform: uppercase; color: #666; font-weight: bold; }
        .btn-close-custom { border: 1px solid #ccc; background: white; padding: 5px 15px; border-radius: 4px; text-decoration: none; color: #333; display: inline-block; }
    </style>
</head>
<body>
    <div class="calc-container p-4">
        <div class="text-center mb-4">
             <img src="{{ asset('img/cdlima_encabezado.jpg') }}" alt="Logos CIP" class="header-img mb-3"> 
        </div>

        <h5 class="fw-bold mb-4">Tarifas para Cuantía Indeterminada</h5>

        <div class="info-card">
            <i class="fas fa-info-circle me-2"></i>
            En los casos de cuantía indeterminada, se aplicarán las siguientes tarifas fijas establecidas por el centro.
        </div>

        <div class="row text-center g-3 mb-4">
            <div class="col-12">
                <div class="border rounded p-3 bg-light">
                    <div class="fee-label">Honorarios Árbitro Único</div>
                    <div class="fixed-fee">S/. 5,000.00</div>
                    <small class="text-muted">+ Impuestos</small>
                </div>
            </div>
            <div class="col-12">
                <div class="border rounded p-3 bg-light">
                    <div class="fee-label">Honorarios Tribunal (Cada uno)</div>
                    <div class="fixed-fee">S/. 4,500.00</div>
                    <small class="text-muted">+ Impuestos</small>
                </div>
            </div>
            <div class="col-12">
                <div class="border rounded p-3 bg-light">
                    <div class="fee-label">Tasa Administrativa</div>
                    <div class="fixed-fee">S/. 2,500.00</div>
                    <small class="text-muted">+ IGV</small>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button onclick="window.close()" class="btn-close-custom">
                ❮ Cerrar Ventana
            </button>
        </div>
    </div>
</body>
</html>