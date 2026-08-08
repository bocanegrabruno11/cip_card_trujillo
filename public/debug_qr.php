<?php

require __DIR__ . '/../vendor/autoload.php';

echo "<h3>7. Test generación QR con endroid</h3>";
try {
    $qrCode = \Endroid\QrCode\QrCode::create('12345678')
        ->setSize(300)
        ->setMargin(5);

    $writer = new \Endroid\QrCode\Writer\PngWriter();
    $result = $writer->write($qrCode);
    $png    = $result->getString();
    $img    = imagecreatefromstring($png);

    if ($img) {
        echo "✅ QR generado correctamente - Tamaño: " . imagesx($img) . "x" . imagesy($img) . "px<br>";
        imagedestroy($img);
    } else {
        echo "❌ QR generado pero imagecreatefromstring falló<br>";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}