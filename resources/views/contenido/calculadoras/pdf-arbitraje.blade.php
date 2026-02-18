<!DOCTYPE html>
<html>
<head>
    <title>Estimación Costos Arbitraje</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 14px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #AD2B2E; padding-bottom: 15px; }
        .title { color: #AD2B2E; font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 12px; color: #666; }
        
        .section { margin-bottom: 20px; }
        .section-title { background-color: #AD2B2E; color: white; padding: 5px 10px; font-weight: bold; font-size: 13px; margin-bottom: 10px; }
        
        .row { width: 100%; margin-bottom: 5px; clear: both; }
        .label { float: left; width: 60%; font-weight: bold; color: #555; }
        .value { float: left; width: 40%; text-align: right; }
        
        .total-row { border-top: 1px solid #ddd; padding-top: 5px; margin-top: 5px; font-size: 15px; color: #AD2B2E; font-weight: bold; }
        .footer { position: fixed; bottom: 0; left: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
        
        .note { font-size: 10px; font-style: italic; color: #666; margin-top: 5px; text-align: right; }
        .highlight-box { background-color: #f9f9f9; border: 1px solid #eee; padding: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">CARD - CIP CDLL</div>
        <div class="subtitle">Centro de Arbitraje y Resolución de Disputas</div>
        <div style="margin-top: 5px; font-size: 12px;">Estimación de Costos - Arbitraje</div>
    </div>

    <div class="section">
        <div class="section-title">DATOS DEL CASO</div>
        <div class="row">
            <div class="label">Tipo de Pretensión:</div>
            <div class="value">{{ ucfirst($tipoCuantia) }}</div>
        </div>
        <div class="row">
            <div class="label">Órgano Arbitral:</div>
            <div class="value">{{ $tipoOrgano == 'unico' ? 'Árbitro Único' : 'Tribunal Arbitral (3)' }}</div>
        </div>
        
        @if($tipoCuantia == 'indeterminada')
            <div class="row">
                <div class="label">Monto del Contrato Original:</div>
                <div class="value">S/. {{ number_format($montoInput, 2) }}</div>
            </div>
            <div class="row">
                <div class="label">Valor Referencial (4%):</div>
                <div class="value">S/. {{ number_format($montoCalculo, 2) }}</div>
            </div>
            <div class="row">
                <div class="label">Nro. Pretensiones Indeterminadas:</div>
                <div class="value">{{ $cantidad }}</div>
            </div>
        @else
            <div class="row">
                <div class="label">Cuantía de la Controversia:</div>
                <div class="value">S/. {{ number_format($montoInput, 2) }}</div>
            </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">ESTIMACIÓN DE HONORARIOS</div>
        
        @if($honorariosTotal == -1)
            <div style="text-align: center; padding: 10px;">A criterio del Directorio</div>
        @else
            @if($tipoOrgano == 'tribunal')
                <div class="row">
                    <div class="label">Honorarios por Árbitro:</div>
                    <div class="value">S/. {{ number_format($honorariosTotal / 3, 2) }}</div>
                </div>
            @endif
            
            <div class="row total-row">
                <div class="label">TOTAL HONORARIOS:</div>
                <div class="value">S/. {{ number_format($honorariosTotal, 2) }}</div>
            </div>
            <div class="note">* Más impuestos de ley</div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">GASTOS ADMINISTRATIVOS</div>
        
        @if($gastosTotal == -1)
            <div style="text-align: center; padding: 10px;">A criterio del Directorio</div>
        @else
            <div class="row total-row">
                <div class="label">TASA ADMINISTRATIVA CARD:</div>
                <div class="value">S/. {{ number_format($gastosTotal, 2) }}</div>
            </div>
            <div class="note">* Más IGV ({{ $igv }}%)</div>
        @endif
    </div>

    <div class="highlight-box">
        <div style="font-weight: bold; font-size: 11px;">NOTA IMPORTANTE:</div>
        <div style="font-size: 11px;">Cada parte deberá asumir el 50% de los costos totales calculados en este documento.</div>
    </div>

    <div class="footer">
        Generado el {{ $fecha }} a través de la plataforma virtual del CARD CIP CDLL.
    </div>
</body>
</html>