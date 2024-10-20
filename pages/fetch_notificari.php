<?php

// Include toate fișierele fetch necesare
include_once '/documentele_noastre/fetch_images.php'; //se iau potrivirile din grupa clasa copil
include_once '/documentele_noastre/fetch_documente.php';// la fel, se iau potrivirile din grupa_clasa_copil

include_once 'fetch_mesaje.php';// cred ca si acesta ar trebui mutat si luat din /documentele_noastre/grupa_clasa_copil

// Obțineți toate informațiile noi de la fiecare fișier fetch
$images_data = fetch_images();
$documente_data = fetch_documente();
$mesaje_data = fetch_mesaje();

// Atribuirea valorilor către variabilele corespunzătoare
$numar_imagini_nou = $images_data['numar_imagini_nou'];
$numar_iframe_nou = $documente_data['numar_iframe_nou'];
$numar_mesaje_nou = $mesaje_data['numar_mesaje_nou'];

// Construiți un array cu toate informațiile noi
$data = [
    'images' => $numar_imagini_nou,
    'documente' => $numar_iframe_nou,
    'mesaje' => $numar_mesaje_nou
];

// Afisează datele în loc să le returnezi în format JSON
header('Content-Type: text/plain');
echo "Data:\n";
print_r($data); exit;
?>
