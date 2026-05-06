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

public function generarTarjetas()
{
    if (!session('usuario')) {
        return redirect('/login-eventos');
    }

    $asistentes     = AsistenteFinalCipcdll::all();
    $imagenBase     = public_path('img/tarjeta.png');
    $carpetaDestino = storage_path('app/public/tarjetas');

    if (!file_exists($carpetaDestino)) {
        mkdir($carpetaDestino, 0755, true);
    }

    // ──────────────────────────────────────────────
    // CONFIGURACIÓN DE FUENTES NOTO SERIF
    // ──────────────────────────────────────────────
    $carpetaFuentes = storage_path('app/fonts/Noto_Serif/static');
    
    if (!is_dir($carpetaFuentes)) {
        $carpetaFuentes = storage_path('fonts/Noto_Serif/static');
    }
    
    if (!is_dir($carpetaFuentes)) {
        $carpetaFuentes = base_path('storage/app/fonts/Noto_Serif/static');
    }
    
    // BUSCAR TODOS LOS ARCHIVOS .ttf
    $fuentesEncontradas = [];
    if (is_dir($carpetaFuentes)) {
        $archivos = scandir($carpetaFuentes);
        foreach ($archivos as $archivo) {
            if (pathinfo($archivo, PATHINFO_EXTENSION) === 'ttf') {
                $fuentesEncontradas[] = $archivo;
            }
        }
    }
    
    // ✅ USAR NotoSerif_ExtraCondensed-MediumItalic para AMBAS (nombre y CIP)
    $fuenteNombreRuta = null;
    $fuenteCipRuta = null;
    
    // Buscar NotoSerif_ExtraCondensed-MediumItalic
    foreach ($fuentesEncontradas as $fuente) {
        if (strpos($fuente, 'ExtraCondensed-MediumItalic') !== false) {
            $fuenteNombreRuta = $carpetaFuentes . '/' . $fuente;
            $fuenteCipRuta = $carpetaFuentes . '/' . $fuente;
            break;
        }
    }
    
    // Si no encuentra esa, buscar cualquier MediumItalic
    if (!$fuenteNombreRuta) {
        foreach ($fuentesEncontradas as $fuente) {
            if (strpos($fuente, 'MediumItalic') !== false) {
                $fuenteNombreRuta = $carpetaFuentes . '/' . $fuente;
                $fuenteCipRuta = $carpetaFuentes . '/' . $fuente;
                break;
            }
        }
    }
    
    // Si no encuentra, buscar cualquier fuente disponible
    if (!$fuenteNombreRuta && count($fuentesEncontradas) > 0) {
        $fuenteNombreRuta = $carpetaFuentes . '/' . $fuentesEncontradas[0];
        $fuenteCipRuta = $fuenteNombreRuta;
    }
    
    $fuenteExiste = $fuenteNombreRuta && file_exists($fuenteNombreRuta);
    
    // Log para depuración
    if ($fuenteExiste) {
        \Log::info("✅ Fuente encontrada: " . basename($fuenteNombreRuta));
    } else {
        \Log::warning("❌ No se encontró NotoSerif_ExtraCondensed-MediumItalic en: " . $carpetaFuentes);
    }

    $cuadroXPorcentaje     = 0.63;
    $cuadroYPorcentaje     = 0.10;
    $cuadroAnchoPorcentaje = 0.33;
    $cuadroAltoPorcentaje  = 0.70;

    $writerQR       = new \Endroid\QrCode\Writer\PngWriter();
    $imagenOriginal = @\imagecreatefrompng($imagenBase);

    if (!$imagenOriginal) {
        return back()->with(['success' => '❌ No se pudo cargar tarjeta.png', 'errores' => []]);
    }

    $imgAncho = \imagesx($imagenOriginal);
    $imgAlto  = \imagesy($imagenOriginal);

    $cuadroX     = (int)($imgAncho * $cuadroXPorcentaje);
    $cuadroY     = (int)($imgAlto  * $cuadroYPorcentaje);
    $cuadroAncho = (int)($imgAncho * $cuadroAnchoPorcentaje);
    $cuadroAlto  = (int)($imgAlto  * $cuadroAltoPorcentaje);

    // ── QR: posición ajustada ────
    $qrSize = (int)($cuadroAncho * 0.40 * 1.5);
    
    $qrDesplazamientoX = 210;
    $qrDesplazamientoY = 10;
    
    $mitadDerechaX = (int)($imgAncho / 2);
    $mitadDerechaAncho = (int)($imgAncho / 2);
    $qrXFijo = $mitadDerechaX + (int)(($mitadDerechaAncho - $qrSize) / 2) + $qrDesplazamientoX;
    $qrY = (int)($imgAlto * 0.55) + $qrDesplazamientoY;

    /**
     * Función para dividir texto en líneas de máximo 15 caracteres (incluyendo espacios)
     */
    $dividirTexto = function($texto, $maxCaracteres = 15) {
        $lineas = [];
        $palabras = explode(' ', $texto);
        $lineaActual = '';
        
        foreach ($palabras as $palabra) {
            $prueba = empty($lineaActual) ? $palabra : $lineaActual . ' ' . $palabra;
            
            if (strlen($prueba) <= $maxCaracteres) {
                $lineaActual = $prueba;
            } else {
                if (!empty($lineaActual)) {
                    $lineas[] = $lineaActual;
                }
                $lineaActual = $palabra;
            }
        }
        
        if (!empty($lineaActual)) {
            $lineas[] = $lineaActual;
        }
        
        return $lineas;
    };

    $dibujarTexto = function($imagen, $fontSize, $fuente, $texto, $cuadroX, $cuadroAncho, $y, $colorSombra, $colorPrincipal) use ($dividirTexto) {
        $lineas = $dividirTexto($texto, 15);
        $fontExists = $fuente && file_exists($fuente);
        $altoTotal = 0;
        $yActual = $y;
        
        foreach ($lineas as $linea) {
            if (!$fontExists) {
                // Fallback: texto simple
                $x = $cuadroX + (int)(($cuadroAncho - (strlen($linea) * $fontSize * 0.6)) / 2);
                imagestring($imagen, 5, $x, $yActual, $linea, $colorPrincipal);
                $altoLinea = $fontSize;
            } else {
                $bbox = \imagettfbbox($fontSize, 0, $fuente, $linea);
                $tAncho = abs($bbox[4] - $bbox[0]);
                $tAlto = abs($bbox[5] - $bbox[1]);
                $x = $cuadroX + (int)(($cuadroAncho - $tAncho) / 2);
                
                if ($fontSize > 0) {
                    \imagettftext($imagen, $fontSize, 0, $x + 2, $yActual + $fontSize - 5, $colorSombra, $fuente, $linea);
                    \imagettftext($imagen, $fontSize, 0, $x, $yActual + $fontSize - 5, $colorPrincipal, $fuente, $linea);
                }
                
                $altoLinea = $tAlto;
            }
            
            // ESPACIADO ENTRE LÍNEAS
            $espaciado = 100;
            $altoTotal += $altoLinea + $espaciado;
            $yActual += $altoLinea + $espaciado;
        }
        
        return $altoTotal;
    };

    $generadas = 0;
    $errores   = [];

    foreach ($asistentes as $asistente) {
        try {
            $imagen = \imagecreatetruecolor($imgAncho, $imgAlto);
            \imagealphablending($imagen, false);
            \imagesavealpha($imagen, true);
            \imagecopy($imagen, $imagenOriginal, 0, 0, 0, 0, $imgAncho, $imgAlto);
            \imagealphablending($imagen, true);

            // COLORES
            $colorNombre = \imagecolorallocate($imagen, 223, 0, 0);     // DF0000 - Rojo
            $colorCip    = \imagecolorallocate($imagen, 212, 175, 55);  // D4AF37 - Dorado
            $colorSombra = \imagecolorallocate($imagen, 255, 200, 200); // Sombra rojo claro

            // TAMAÑOS DE FUENTE (ajustados para ExtraCondensed-MediumItalic)
            $fontSizeNombre = (int)($imgAncho * 0.032); // Más grande por ser condensada
            $fontSizeCip    = (int)($imgAncho * 0.028); // Más grande para CIP

            $nombreCompleto = strtoupper(trim(($asistente->nombres ?? '') . ' ' . ($asistente->apellidos ?? '')));
            $cipTexto = 'CIP: ' . ($asistente->cip ?? '');

            // NOMBRE
            $yNombre = $cuadroY + (int)($cuadroAlto * 0.05);
            $altoNombre = $dibujarTexto($imagen, $fontSizeNombre, $fuenteNombreRuta, $nombreCompleto, $cuadroX, $cuadroAncho, $yNombre, $colorSombra, $colorNombre);
            
            // CIP (con espaciado de 50 puntos desde el nombre)
            $yCip = $yNombre + $altoNombre + 50;
            $altoCip = $dibujarTexto($imagen, $fontSizeCip, $fuenteCipRuta, $cipTexto, $cuadroX, $cuadroAncho, $yCip, $colorSombra, $colorCip);
            
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

    \imagedestroy($imagenOriginal);

    $nombreFuente = $fuenteExiste ? basename($fuenteNombreRuta) : 'No encontrada';
    $mensajeFuente = $fuenteExiste ? "✅ Usando fuente: {$nombreFuente}" : "⚠️ No se encontró NotoSerif_ExtraCondensed-MediumItalic";
    
    return back()->with([
        'success' => "✅ Se generaron {$generadas} tarjetas. {$mensajeFuente}",
        'errores' => $errores,
    ]);
}
}