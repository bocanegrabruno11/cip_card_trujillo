<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsistenteFinalCipcdll;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AsistentasCipcdllFinalController extends Controller
{
    public function index()
    {
        if (!session('usuario')) {
            return redirect('/login-eventos');
        }

        $asistentes = AsistenteFinalCipcdll::all();
        return view('eventoscipcdll.envio-tarjetas', compact('asistentes'));
    }


public function generarTarjetas(Request $request)
{
    if (!session('usuario')) {
        // Si es AJAX devuelve JSON, si no redirige
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['mensaje' => 'No autenticado.'], 401);
        }
        return redirect('/login-eventos');
    }
 
    $loteActual = max(1, (int) $request->input('lote', 1));
    $tamano     = max(1, min(50, (int) $request->input('tamano', 20)));
    $offset     = ($loteActual - 1) * $tamano;
 
    // ── 1. Total de registros ──────────────────────────────────────────────
    $total = AsistenteFinalCipcdll::count();
 
    if ($total === 0) {
        return response()->json([
            'mensaje'     => 'No hay asistentes registrados.',
            'procesados'  => 0,
            'total'       => 0,
            'lote_actual' => $loteActual,
            'finalizado'  => true,
            'generadas'   => 0,
            'errores'     => [],
        ]);
    }
 
    // ── 2. Cargar sólo el lote actual ─────────────────────────────────────
    $asistentes = AsistenteFinalCipcdll::orderBy('id')
        ->skip($offset)
        ->take($tamano)
        ->get();
 
    // ── 3. Rutas y configuración de fuentes ───────────────────────────────
    $imagenBase     = public_path('img/tarjeta.png');
    $carpetaDestino = storage_path('app/public/tarjetas');
 
    if (!file_exists($carpetaDestino)) {
        mkdir($carpetaDestino, 0755, true);
    }
 
    $carpetaFuentes = storage_path('app/fonts/Noto_Serif/static');
    if (!is_dir($carpetaFuentes)) $carpetaFuentes = storage_path('fonts/Noto_Serif/static');
    if (!is_dir($carpetaFuentes)) $carpetaFuentes = base_path('storage/app/fonts/Noto_Serif/static');
 
    $fuentesEncontradas = [];
    if (is_dir($carpetaFuentes)) {
        foreach (scandir($carpetaFuentes) as $archivo) {
            if (pathinfo($archivo, PATHINFO_EXTENSION) === 'ttf') {
                $fuentesEncontradas[] = $archivo;
            }
        }
    }
 
    $fuenteNombreRuta = null;
    $fuenteCipRuta    = null;
 
    foreach ($fuentesEncontradas as $fuente) {
        if (strpos($fuente, 'ExtraCondensed-MediumItalic') !== false) {
            $fuenteNombreRuta = $carpetaFuentes . '/' . $fuente;
            $fuenteCipRuta    = $carpetaFuentes . '/' . $fuente;
            break;
        }
    }
    if (!$fuenteNombreRuta) {
        foreach ($fuentesEncontradas as $fuente) {
            if (strpos($fuente, 'MediumItalic') !== false) {
                $fuenteNombreRuta = $carpetaFuentes . '/' . $fuente;
                $fuenteCipRuta    = $carpetaFuentes . '/' . $fuente;
                break;
            }
        }
    }
    if (!$fuenteNombreRuta && count($fuentesEncontradas) > 0) {
        $fuenteNombreRuta = $carpetaFuentes . '/' . $fuentesEncontradas[0];
        $fuenteCipRuta    = $fuenteNombreRuta;
    }
 
    $fuenteExiste = $fuenteNombreRuta && file_exists($fuenteNombreRuta);
 
    if ($loteActual === 1) {
        if ($fuenteExiste) {
            \Log::info("✅ Fuente: " . basename($fuenteNombreRuta));
        } else {
            \Log::warning("❌ No se encontró NotoSerif_ExtraCondensed-MediumItalic en: {$carpetaFuentes}");
        }
    }
 
    // ── 4. Cargar imagen base (sólo una vez por request) ──────────────────
    $imagenOriginal = @\imagecreatefrompng($imagenBase);
 
    if (!$imagenOriginal) {
        return response()->json([
            'mensaje'     => '❌ No se pudo cargar tarjeta.png',
            'procesados'  => $offset,
            'total'       => $total,
            'lote_actual' => $loteActual,
            'finalizado'  => true,
            'generadas'   => 0,
            'errores'     => ['No se pudo cargar la imagen base tarjeta.png'],
        ], 500);
    }
 
    $imgAncho = \imagesx($imagenOriginal);
    $imgAlto  = \imagesy($imagenOriginal);
 
    // ── 5. Coordenadas del área de texto ──────────────────────────────────
    $cuadroXPorcentaje     = 0.63;
    $cuadroYPorcentaje     = 0.10;
    $cuadroAnchoPorcentaje = 0.33;
    $cuadroAltoPorcentaje  = 0.70;
 
    $cuadroX     = (int)($imgAncho * $cuadroXPorcentaje);
    $cuadroY     = (int)($imgAlto  * $cuadroYPorcentaje);
    $cuadroAncho = (int)($imgAncho * $cuadroAnchoPorcentaje);
    $cuadroAlto  = (int)($imgAlto  * $cuadroAltoPorcentaje);
 
    // ── 6. QR: posición ajustada ──────────────────────────────────────────
    $qrSize            = (int)($cuadroAncho * 0.40 * 1.5);
    $qrDesplazamientoX = 210;
    $qrDesplazamientoY = 10;
    $mitadDerechaX     = (int)($imgAncho / 2);
    $mitadDerechaAncho = (int)($imgAncho / 2);
    $qrXFijo           = $mitadDerechaX + (int)(($mitadDerechaAncho - $qrSize) / 2) + $qrDesplazamientoX;
    $qrY               = (int)($imgAlto * 0.55) + $qrDesplazamientoY;
 
    // ── 7. Helpers de texto ───────────────────────────────────────────────
    $dividirTexto = function ($texto, $maxCaracteres = 15) {
        $lineas      = [];
        $palabras    = explode(' ', $texto);
        $lineaActual = '';
 
        foreach ($palabras as $palabra) {
            $prueba = empty($lineaActual) ? $palabra : $lineaActual . ' ' . $palabra;
            if (strlen($prueba) <= $maxCaracteres) {
                $lineaActual = $prueba;
            } else {
                if (!empty($lineaActual)) $lineas[] = $lineaActual;
                $lineaActual = $palabra;
            }
        }
        if (!empty($lineaActual)) $lineas[] = $lineaActual;
 
        return $lineas;
    };
 
    $dibujarTexto = function ($imagen, $fontSize, $fuente, $texto, $cuadroX, $cuadroAncho, $y, $colorSombra, $colorPrincipal) use ($dividirTexto) {
        $lineas     = $dividirTexto($texto, 15);
        $fontExists = $fuente && file_exists($fuente);
        $altoTotal  = 0;
        $yActual    = $y;
 
        foreach ($lineas as $linea) {
            if (!$fontExists) {
                $x = $cuadroX + (int)(($cuadroAncho - (strlen($linea) * $fontSize * 0.6)) / 2);
                imagestring($imagen, 5, $x, $yActual, $linea, $colorPrincipal);
                $altoLinea = $fontSize;
            } else {
                $bbox   = \imagettfbbox($fontSize, 0, $fuente, $linea);
                $tAncho = abs($bbox[4] - $bbox[0]);
                $tAlto  = abs($bbox[5] - $bbox[1]);
                $x      = $cuadroX + (int)(($cuadroAncho - $tAncho) / 2);
 
                if ($fontSize > 0) {
                    \imagettftext($imagen, $fontSize, 0, $x + 2, $yActual + $fontSize - 5, $colorSombra, $fuente, $linea);
                    \imagettftext($imagen, $fontSize, 0, $x,     $yActual + $fontSize - 5, $colorPrincipal, $fuente, $linea);
                }
                $altoLinea = $tAlto;
            }
 
            $espaciado  = 100;
            $altoTotal += $altoLinea + $espaciado;
            $yActual   += $altoLinea + $espaciado;
        }
 
        return $altoTotal;
    };
 
    // ── 8. Procesar lote ──────────────────────────────────────────────────
    $writerQR  = new \Endroid\QrCode\Writer\PngWriter();
    $generadas = 0;
    $errores   = [];
 
    foreach ($asistentes as $asistente) {
        try {
            $imagen = \imagecreatetruecolor($imgAncho, $imgAlto);
            \imagealphablending($imagen, false);
            \imagesavealpha($imagen, true);
            \imagecopy($imagen, $imagenOriginal, 0, 0, 0, 0, $imgAncho, $imgAlto);
            \imagealphablending($imagen, true);
 
            // Colores
            $colorNombre = \imagecolorallocate($imagen, 223, 0, 0);
            $colorCip    = \imagecolorallocate($imagen, 212, 175, 55);
            $colorSombra = \imagecolorallocate($imagen, 255, 200, 200);
 
            // Tamaños de fuente
            $fontSizeNombre = (int)($imgAncho * 0.032);
            $fontSizeCip    = (int)($imgAncho * 0.028);
 
            $nombreCompleto = strtoupper(trim(($asistente->nombres ?? '') . ' ' . ($asistente->apellidos ?? '')));
            $cipTexto       = 'CIP: ' . ($asistente->cip ?? '');
 
            // Nombre
            $yNombre    = $cuadroY + (int)($cuadroAlto * 0.05);
            $altoNombre = $dibujarTexto($imagen, $fontSizeNombre, $fuenteNombreRuta, $nombreCompleto, $cuadroX, $cuadroAncho, $yNombre, $colorSombra, $colorNombre);
 
            // CIP
            $yCip = $yNombre + $altoNombre + 50;
            $dibujarTexto($imagen, $fontSizeCip, $fuenteCipRuta, $cipTexto, $cuadroX, $cuadroAncho, $yCip, $colorSombra, $colorCip);
 
            // QR
            $qrCode = \Endroid\QrCode\QrCode::create($asistente->dni ?? 'SIN-DNI')
                ->setSize(180)
                ->setMargin(2);
 
            $qrImg   = \imagecreatefromstring($writerQR->write($qrCode)->getString());
            $qrAncho = \imagesx($qrImg);
            $qrAlto  = \imagesy($qrImg);
 
            \imagecopyresampled(
                $imagen, $qrImg,
                $qrXFijo, $qrY,
                0, 0,
                $qrSize, $qrSize,
                $qrAncho, $qrAlto
            );
 
            \imagedestroy($qrImg);
 
            // Guardar
            $cip         = preg_replace('/[^a-zA-Z0-9_-]/', '_', $asistente->cip ?? 'sin_cip');
            $rutaDestino = $carpetaDestino . '/' . $cip . '.png';
 
            \imagesavealpha($imagen, true);
            \imagepng($imagen, $rutaDestino, 6);
            \imagedestroy($imagen);
 
            $generadas++;
 
        } catch (\Exception $e) {
            $errores[] = "Error con CIP {$asistente->cip}: " . $e->getMessage();
        }
    }
 
    // ── 9. Liberar imagen base ────────────────────────────────────────────
    \imagedestroy($imagenOriginal);
 
    // ── 10. Calcular progreso y si terminamos ─────────────────────────────
    $procesados = $offset + $asistentes->count();
    $finalizado = $procesados >= $total;
 
    $nombreFuente  = $fuenteExiste ? basename($fuenteNombreRuta) : 'No encontrada';
    $mensajeFuente = $fuenteExiste
        ? "Fuente: {$nombreFuente}"
        : "⚠️ No se encontró NotoSerif_ExtraCondensed-MediumItalic";
 
    // ── 11. Si la petición no es AJAX, redirigir (retrocompatibilidad) ────
    if (!$request->expectsJson() && !$request->ajax()) {
        return back()->with([
            'success' => "✅ Se generaron {$generadas} tarjetas. {$mensajeFuente}",
            'errores' => $errores,
        ]);
    }
 
    return response()->json([
        'mensaje'     => $finalizado
            ? "✅ Proceso completo. {$mensajeFuente}"
            : "Lote {$loteActual} procesado.",
        'procesados'  => $procesados,
        'total'       => $total,
        'lote_actual' => $loteActual,
        'finalizado'  => $finalizado,
        'generadas'   => $generadas,
        'errores'     => $errores,
    ]);
}
}