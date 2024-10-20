<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Crează obiectele Imagick pentru fiecare imagine
$logo = new Imagick('tid4k_cu_umbra.png');
$qrCode = new Imagick('url_qr.png');

// Redimensionează QR code-ul pentru a se potrivi cu spațiul destinat lui în logo
$qrCode->thumbnailImage(158, 158);

// Calculează poziția QR code-ului pe logo
$logoWidth = $logo->getImageWidth();
$logoHeight = $logo->getImageHeight();
$qrX = ($logoWidth - 115) / 2;
$qrY = ($logoHeight - 115) / 2;

// Rotunjirea valorilor pentru a preveni eroarea de tip "deprecated"
$qrX = round($qrX);
$qrY = round($qrY);

// Suprapune QR code-ul peste logo
$logo->compositeImage($qrCode, imagick::COMPOSITE_OVER, $qrX, $qrY);

// Salvează imaginea combinată
$logo->writeImage('logo_qr_code.png');

// Curăță resursele
$logo->clear();
$qrCode->clear();

echo "Imaginea combinată a fost creată cu succes.";
?>
