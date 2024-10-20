<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'vendor/autoload.php';

use chillerlan\QRCode\{QRCode, QROptions};

$text = "http://www.tid4k.ro/start.php";

$options = new QROptions([
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel' => QRCode::ECC_L,
    'imageBase64' => false,
]);

$qrcode = new QRCode($options);
$image = $qrcode->render($text);
header('Content-Type: image/png');
echo $image;
?>
