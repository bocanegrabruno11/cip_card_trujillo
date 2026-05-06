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
    $carpetaFuentes = storage_path('app/fonts');

    if (!file_exists($carpetaDestino)) {
        mkdir($carpetaDestino, 0755, true);
    }
    if (!file_exists($carpetaFuentes)) {
        mkdir($carpetaFuentes, 0755, true);
    }

    $fuenteNombreRuta = $carpetaFuentes . '/OpenSans-Bold.ttf';
    $fuenteCipRuta    = $carpetaFuentes . '/OpenSans-ExtraBold.ttf';

    if (!file_exists($fuenteNombreRuta)) {
        file_put_contents($fuenteNombreRuta, file_get_contents(
            'https://github.com/googlefonts/opensans/raw/main/fonts/ttf/OpenSans-Bold.ttf'
        ));
    }
    if (!file_exists($fuenteCipRuta)) {
        file_put_contents($fuenteCipRuta, file_get_contents(
            'https://github.com/googlefonts/opensans/raw/main/fonts/ttf/OpenSans-ExtraBold.ttf'
        ));
    }

    $cuadroXPorcentaje     = 0.63;
    $cuadroYPorcentaje     = 0.22;
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

    // ── QR: centrado en la mitad derecha de la imagen ────
    // La mitad derecha va desde imgAncho/2 hasta imgAncho
    // El QR se centra horizontalmente dentro de esa mitad derecha
    $mitadDerechaX      = (int)($imgAncho / 2);
    $mitadDerechaAncho  = (int)($imgAncho / 2);
    $qrSize             = (int)($cuadroAncho * 0.40 * 1.5); // tamaño actual + 50%
    $qrXFijo            = $mitadDerechaX + (int)(($mitadDerechaAncho - $qrSize) / 2);

    $centrarTexto = function($imagen, $fontSize, $fuente, $texto, $cuadroX, $cuadroAncho, $y, $colorSombra, $colorPrincipal) {
        $bbox   = \imagettfbbox($fontSize, 0, $fuente, $texto);
        $tAncho = abs($bbox[4] - $bbox[0]);
        $tAlto  = abs($bbox[5] - $bbox[1]);
        $x      = $cuadroX + (int)(($cuadroAncho - $tAncho) / 2);
        \imagettftext($imagen, $fontSize, 0, $x + 2, $y + 2, $colorSombra,    $fuente, $texto);
        \imagettftext($imagen, $fontSize, 0, $x,     $y,     $colorPrincipal, $fuente, $texto);
        return $tAlto;
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

            $colorNombre = \imagecolorallocate($imagen, 139, 0,   0);
            $colorCip    = \imagecolorallocate($imagen, 101, 51,  0);
            $colorSombra = \imagecolorallocate($imagen, 255, 200, 180);

            $fontSizeNombre = (int)($imgAncho * 0.022);
            $fontSizeCip    = (int)($imgAncho * 0.014);

            $nombre   = strtoupper(trim(($asistente->nombres ?? '') . ' ' . ($asistente->apellidos ?? '')));
            $cipTexto = 'CIP: ' . ($asistente->cip ?? '');

            $textoY = $cuadroY + (int)($cuadroAlto * 0.08);

            $bboxTest    = \imagettfbbox($fontSizeNombre, 0, $fuenteNombreRuta, $nombre);
            $nombreAncho = abs($bboxTest[4] - $bboxTest[0]);

            if ($nombreAncho > $cuadroAncho - 20) {
                $palabras = explode(' ', $nombre);
                $mitad    = (int)(ceil(count($palabras) / 2));
                $linea1   = implode(' ', array_slice($palabras, 0, $mitad));
                $linea2   = implode(' ', array_slice($palabras, $mitad));

                $altoLinea = $centrarTexto($imagen, $fontSizeNombre, $fuenteNombreRuta, $linea1, $cuadroX, $cuadroAncho, $textoY, $colorSombra, $colorNombre);
                $textoY   += $altoLinea + 100;
                $centrarTexto($imagen, $fontSizeNombre, $fuenteNombreRuta, $linea2, $cuadroX, $cuadroAncho, $textoY, $colorSombra, $colorNombre);
                $textoY   += $altoLinea + 50;
            } else {
                $altoLinea = $centrarTexto($imagen, $fontSizeNombre, $fuenteNombreRuta, $nombre, $cuadroX, $cuadroAncho, $textoY, $colorSombra, $colorNombre);
                $textoY   += $altoLinea + 200;
            }

            $altoCip = $centrarTexto($imagen, $fontSizeCip, $fuenteCipRuta, $cipTexto, $cuadroX, $cuadroAncho, $textoY, $colorSombra, $colorCip);

            // ── QR más abajo: parte desde debajo del CIP + margen extra ──
            $qrY = $textoY + $altoCip + 80;

            // ── Generar y dibujar QR ──────────────────────────────────────
            $qrCode = \Endroid\QrCode\QrCode::create($asistente->dni ?? 'SIN-DNI')
                ->setSize(180)
                ->setMargin(2);

            $qrImg   = \imagecreatefromstring($writerQR->write($qrCode)->getString());
            $qrAncho = \imagesx($qrImg);
            $qrAlto  = \imagesy($qrImg);

            \imagecopyresampled(
                $imagen, $qrImg,
                $qrXFijo, $qrY,   // X fijo centrado en mitad derecha, Y debajo del CIP
                0, 0,
                $qrSize, $qrSize, // tamaño + 50%
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

    return back()->with([
        'success' => "✅ Se generaron {$generadas} tarjetas correctamente.",
        'errores' => $errores,
    ]);
}

}