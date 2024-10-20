<?php
$numeUnitateScolara = "Gradinita 122";

// URL-ul API-ului
$apiUrl = "http://82.77.117.4:5000/get_zips/Gradinita-122";

// Cheia secretă pentru autentificare
$apiKey = "tid4k-form-secret-key-2024";

// Funcția pentru preluarea datelor de la API
function fetchApiData($url, $apiKey) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Urmărește redirecționările
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Pentru debugging
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = 'Error: ' . curl_error($ch);
        error_log($error_msg);
        echo $error_msg;
        curl_close($ch);
        return null;
    }
    curl_close($ch);
    return $data;
}

// Funcția pentru extragerea informațiilor din numele fișierului
function extractFileInfo($filename) {
    $parts = explode('_', $filename);

    // Inițializează valorile cu '-'
    $nume = '-';
    $prenume = '-';
    $telefon = '-';
    $data = '-';
    $ora = '-';

    // Verifică și extrage fiecare parte dacă există
    if (isset($parts[0])) $nume = $parts[0];
    if (isset($parts[1])) $prenume = $parts[1];
    if (isset($parts[2])) $telefon = $parts[2];
    if (isset($parts[3])) $data = $parts[3];
    if (isset($parts[4])) $ora = $parts[4];

    $nume_prenume = $nume . ' ' . $prenume;

    return [
        'nume_prenume' => $nume_prenume,
        'telefon' => $telefon,
        'data' => $data,
        'ora' => $ora,
        'fisier_url' => $GLOBALS['apiUrl'] . '/' . $filename // URL-ul complet pentru descărcare
    ];
}

// Funcția pentru filtrarea fișierelor în funcție de unitatea școlară
function filterFiles($files, $unitateScolara) {
    $result = [];
    foreach ($files as $file) {
        if (strpos($file, $unitateScolara) !== false) {
            $result[] = $file;
        }
    }
    return $result;
}

// Funcția pentru sortarea fișierelor
function sortFiles($files) {
    usort($files, function($a, $b) {
        $aName = explode('_', $a)[0] . ' ' . explode('_', $a)[1];
        $bName = explode('_', $b)[0] . ' ' . explode('_', $b)[1];
        return strcmp($aName, $bName);
    });
    return $files;
}

// Fetch data from API
$zipContent = fetchApiData($apiUrl, $apiKey);

if (!$zipContent) {
    echo 'Error: No data returned from API';
    exit;
}

// Salvarea fișierului zip temporar
$tempZipFile = tempnam(sys_get_temp_dir(), 'zip');
file_put_contents($tempZipFile, $zipContent);

$zip = new ZipArchive;
$files = [];
if ($zip->open($tempZipFile) === TRUE) {
    // Verificăm dacă există un singur fișier ZIP și nu este combinat
    if ($zip->numFiles == 1 && strpos($zip->getNameIndex(0), 'combined') === false) {
        $file = $zip->getNameIndex(0);
        $files[] = $file;
    } else {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $file = $zip->getNameIndex($i);
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                // Citim fișierele zip conținute
                $subZipContent = $zip->getFromIndex($i);
                $subZipFile = tempnam(sys_get_temp_dir(), 'subzip');
                file_put_contents($subZipFile, $subZipContent);

                $subZip = new ZipArchive;
                if ($subZip->open($subZipFile) === TRUE) {
                    for ($j = 0; $j < $subZip->numFiles; $j++) {
                        $subFile = $subZip->getNameIndex($j);
                        $files[] = $subFile;
                    }
                    $subZip->close();
                }
                unlink($subZipFile);
            }
        }
    }
    $zip->close();
    unlink($tempZipFile);
} else {
    echo 'Failed to open zip file';
    exit;
}

// Filtrarea și sortarea fișierelor
$filteredFiles = filterFiles($files, $numeUnitateScolara);
$sortedFiles = sortFiles($filteredFiles);

// Extragerea informațiilor din fișiere și crearea răspunsului JSON
$response = [];
foreach ($sortedFiles as $file) {
    $fileInfo = extractFileInfo($file);
    if ($fileInfo !== null) {
        $response[] = $fileInfo;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>