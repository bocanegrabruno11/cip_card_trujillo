<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Cálculo JPRD</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #AD2B2E; padding-bottom: 10px; }
        .logo { height: 60px; margin-bottom: 10px; }
        .title { color: #AD2B2E; font-size: 20px; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 12px; color: #666; }
        
        .section { margin-bottom: 25px; }
        .section-title { background: #f0f0f0; padding: 5px 10px; font-weight: bold; border-left: 4px solid #AD2B2E; font-size: 14px; margin-bottom: 10px; }
        
        .row { display: table; width: 100%; margin-bottom: 5px; }
        .label { display: table-cell; width: 50%; font-size: 13px; color: #555; }
        .value { display: table-cell; width: 50%; text-align: right; font-weight: bold; font-size: 14px; }
        
        .total-box { border: 1px solid #ddd; padding: 10px; margin-top: 10px; background-color: #fffdfd; }
        .alert { color: red; font-size: 12px; text-align: center; margin-top: 20px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">CARD - CIP CDLL</div>
        <div class="subtitle">Centro de Arbitraje y Resolución de Disputas</div>
        <div style="margin-top: 5px; font-size: 14px;">Estimación de Costos - Junta de Prevención (JPRD)</div>
    </div>

    <div class="section">
        <div class="section-title">DATOS DEL CONTRATO</div>
        <div class="row">
            <div class="label">Monto del Contrato de Obra:</div>
            <div class="value">S/. {{ number_format($monto, 2) }}</div>
        </div>
        <div class="row">
            <div class="label">Tipo de Adjudicación:</div>
            <div class="value">{{ $tipo == 'unico' ? 'Miembro Único' : 'JRD (3 Miembros)' }}</div>
        </div>
        <div class="row">
            <div class="label">Rango Tarifario:</div>
            <div class="value">{{ $rango }}</div>
        </div>
    </div>

    @if($error)
        <div class="alert">
            <strong>AVISO:</strong> {{ $error }}
        </div>
    @else
        <div class="section">
            <div class="section-title">HONORARIOS MENSUALES</div>
            
            @if($tipo == 'unico')
                <div class="row">
                    <div class="label">Honorarios Miembro Único:</div>
                    <div class="value">S/. {{ number_format($honoUnitario, 2) }}</div>
                </div>
            @else
                <div class="row">
                    <div class="label">Honorarios Por Miembro:</div>
                    <div class="value">S/. {{ number_format($honoUnitario, 2) }}</div>
                </div>
                <div class="total-box">
                    <div class="row">
                        <div class="label" style="color: #AD2B2E;">TOTAL JPRD (3 Miembros):</div>
                        <div class="value" style="color: #AD2B2E;">S/. {{ number_format($honoTotal, 2) }}</div>
                    </div>
                </div>
            @endif
            <div style="text-align: right; font-size: 10px; color: #666; margin-top: 2px;">* Más impuestos de ley</div>
        </div>

        <div class="section">
            <div class="section-title">GASTOS ADMINISTRATIVOS</div>
            <div class="row">
                <div class="label">Tasa Administrativa Mensual:</div>
                <div class="value">S/. {{ number_format($gastosAdmin, 2) }}</div>
            </div>
            <div style="text-align: right; font-size: 10px; color: #666; margin-top: 2px;">* Más IGV ({{ $igv }}%)</div>
        </div>
    @endif

    <div class="footer">
        Generado el {{ $fecha }} a través de la plataforma virtual del CARD CIP CDLL.
    </div>
</body>
</html>