<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

use chillerlan\QRCode\{QRCode, QROptions};

// Definește URL-ul pentru codul QR
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Opțiuni pentru generarea codului QR
$options = new QROptions([
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel' => QRCode::ECC_L,
    'scale' => 5, // Mărimea blocurilor QR
    'imageBase64' => false,
]);

// Generarea codului QR
$qrcode = new QRCode($options);
$qrImage = $qrcode->render($url);
file_put_contents('url_qr.png', $qrImage);

// Crează obiectele Imagick pentru fiecare imagine
$logo = new Imagick('tid4k_cu_umbra.png');
$qrCode = new Imagick('url_qr.png');

// Redimensionează QR code-ul pentru a se potrivi cu spațiul destinat lui în logo
$qrCode->thumbnailImage(158, 158);

// Calculează poziția QR code-ului pe logo
$logoWidth = $logo->getImageWidth();
$logoHeight = $logo->getImageHeight();
$qrX = round(($logoWidth - 115) / 2);
$qrY = round(($logoHeight - 115) / 2);

// Suprapune QR code-ul peste logo
$logo->compositeImage($qrCode, imagick::COMPOSITE_OVER, $qrX, $qrY);

// Salvează imaginea combinată
$logo->writeImage('logo_qr_code.png');

// Curăță resursele
$logo->clear();
$qrCode->clear();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Pre-autorizare</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background-color: #0A3A5A;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .header-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .talk-to-text {
            font-size: 26px;
            color: white;
            position: relative;
            left: -150px;
            transform: translateY(174px);
            text-align: left;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .logo-and-qr {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo {
            height: 170px;
        }
        .qr-code {
            position: absolute;
            width: 100px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            left: 50%;
            margin-left: -35px;
            transform: translateY(7%);
        }
        .qr-code img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <header class="header-content">
        <div class="talk-to-text">talk-to</div>
        <div class="logo-and-qr">
            <img class="logo" src="tid4k_cu_umbra.png" alt="Logo TID4K">
            <div class="qr-code">
                <a href="http://localhost/config_sesiuni.php" target="_blank">
                    <img src="url_qr.png" />
                </a>
            </div>
        </div>
    </header>
</body>
</html>
