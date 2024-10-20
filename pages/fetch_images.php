<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Activarea raportării erorilor și afișarea lor
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../config.php'; // Include config.php, unde sunt stocate informațiile de conectare la baza de date
require_once '../sesiuni.php';

require_once 'functii_si_constante.php';
 determina_variabile_utilizator($conn);

$id_cookie = $_SESSION['id_cookie'];
$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];
$alias = 'alias';

// Partea PHP pentru determinarea lui $opposite_status_str rămâne neschimbată
$oppositeStatusConfig = [
    'parinte' => ['profesor', 'director', 'administrator', 'secretara'],
    'elev' => ['profesor', 'director', 'administrator', 'secretara'],
    'profesor' => ['parinte', 'director', 'administrator', 'secretara'],
    'director' => ['parinte', 'profesor', 'administrator', 'secretara'],
    'administrator' => ['parinte', 'profesor', 'director', 'secretara'],
    'secretara' => ['parinte', 'profesor', 'director', 'administrator'],
];

// Determină statusurile opuse pentru statusul curent
$opposite_status = $oppositeStatusConfig[$status] ?? [];

// Convertirea array-ului în string pentru interogare
$opposite_status_str = implode("','", $opposite_status);

// $additionalCondition = "";
// if ($status == 'elev') {
//     $additionalCondition = " OR u.status != 'elev'";
// } elseif ($status == 'profesor') {
//     $additionalCondition = " OR u.status != 'profesor'";
// } elseif ($status == 'director') {
//     $additionalCondition = " OR u.status != 'director'";
// } elseif ($status == 'administrator') {
//     $additionalCondition = " OR u.status != 'administrator'";
// } elseif ($status == 'secretara') {
//     $additionalCondition = " OR u.status != 'secretara'";
// }


// Interogarea SQL
$sql = "SELECT DISTINCT alias.id_info, alias.nume_fisier, alias.extensie, alias.data_upload, alias.id_utilizator, u.nume_prenume, u.status
        FROM informatii_" . $grupa_clasa_copil_curent . " alias
        JOIN utilizatori u ON alias.id_utilizator = u.id_utilizator
        LEFT JOIN copii c ON alias.id_utilizator = c.id_utilizator
        LEFT JOIN asociere_multipla am ON alias.id_utilizator = am.id_utilizator
        WHERE (
                (u.id_cookie = '$id_cookie' AND u.status = '$status')
                OR (u.status IN ('$opposite_status_str'))
              )
        AND (c.grupa_clasa_copil = '$grupa_clasa_copil' OR am.grupa_clasa_copil = '$grupa_clasa_copil')
        AND alias.tip_fisier LIKE 'image/%'
        ORDER BY alias.data_upload DESC";

$result = $conn->query($sql);


$files_utilizator = [];
$files_profesor = [];


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($row['id_utilizator'] == $_SESSION['id_utilizator']) {
            $files_utilizator[] = $row;
        } else {
            $files_profesor[] = $row;
        }
    }
}


$output = [
    'files_utilizator' => $files_utilizator,
    'files_profesor' => $files_profesor,
];

// Verificăm dacă scriptul este apelat din infodisplay.php
if (isset($_GET['source']) && $_GET['source'] == 'infodisplay') {
    // Interogare pentru a extrage toate imaginile
    $sql_images = "SELECT alias.id_info, alias.nume_fisier, alias.extensie, alias.data_upload, u.temp_path
        FROM informatii_" . $grupa_clasa_copil_curent . " alias
        JOIN utilizatori u ON alias.id_utilizator = u.id_utilizator
        WHERE (u.status != 'parinte')
        AND alias.tip_fisier LIKE 'image/%'
        ORDER BY alias.data_upload DESC";

    $result_images = $conn->query($sql_images);

    $images_info = [];

    if ($result_images->num_rows > 0) {
        while($row_image = $result_images->fetch_assoc()) {
            $cale_infodisplay = '/' . str_replace('/home/tid4kdem/public_html/', '', $row_image['temp_path']) . $row_image['nume_fisier'];
            $images_info[] = [
                'id_info' => $row_image['id_info'],
                'nume_fisier' => $row_image['nume_fisier'],
                'extensie' => $row_image['extensie'],
                'data_upload' => $row_image['data_upload'],
                'cale_infodisplay_afisat' => $cale_infodisplay
            ];
        }
    }

    // Afișare JSON pentru infodisplay.php
    header('Content-Type: application/json');
    echo json_encode($images_info); // Modificat pentru a returna direct array-ul de imagini
} else {
    // Afișare JSON-ul original pentru alte surse
    header('Content-Type: application/json');
    echo json_encode($output);
}

$conn->close();
?>
