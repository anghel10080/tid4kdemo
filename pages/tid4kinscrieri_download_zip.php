<?php
// URL-ul API-ului pentru descărcare
$apiUrl = "http://82.77.117.4:5000/get_zips/";

// Cheia secretă pentru autentificare
$apiKey = "tid4k-form-secret-key-2024";

// Numele fișierului de descărcat (trebuie să fie transmis prin URL)
$filename = isset($_GET['file']) ? $_GET['file'] : '';

if (!$filename) {
    echo json_encode(['status' => 'error', 'message' => 'No file specified']);
    exit;
}

// URL-ul complet pentru descărcare
$fileUrl = $apiUrl . $filename;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $fileUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $apiKey"
]);
$data = curl_exec($ch);
if (curl_errno($ch)) {
    $error_msg = 'Error: ' . curl_error($ch);
    error_log($error_msg);
    echo json_encode(['status' => 'error', 'message' => $error_msg]);
    curl_close($ch);
    return;
}
curl_close($ch);

// Numele directorului unde se va salva fișierul
setlocale(LC_TIME, 'ro_RO.UTF-8');
$luna_curenta = strftime('%B');
$luna_curenta = ucfirst($luna_curenta); // Prima literă mare
$an_curent = date('Y');

$downloadDir = "/home/Înscrieri_{$luna_curenta}_{$an_curent}/";
if (!is_dir($downloadDir)) {
    mkdir($downloadDir, 0777, true);
}

$filePath = $downloadDir . $filename;
file_put_contents($filePath, $data);

echo json_encode(['status' => 'success', 'file' => $filePath]);
?>