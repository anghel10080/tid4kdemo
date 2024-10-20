<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


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


$additionalCondition = "";
if ($status == 'elev') {
    $additionalCondition = " OR u.status != 'elev'";
} elseif ($status == 'profesor') {
    $additionalCondition = " OR u.status != 'profesor'";
} elseif ($status == 'director') {
    $additionalCondition = " OR u.status != 'director'";
} elseif ($status == 'administrator') {
    $additionalCondition = " OR u.status != 'administrator'";
} elseif ($status == 'secretara') {
    $additionalCondition = " OR u.status != 'secretara'";
}

$sql = "SELECT DISTINCT $alias.id_info, $alias.nume_fisier, $alias.extensie, $alias.data_upload, $alias.id_utilizator, u.nume_prenume, u.status
        FROM informatii_$grupa_clasa_copil_curent $alias
        JOIN utilizatori u ON $alias.id_utilizator = u.id_utilizator
        LEFT JOIN copii c ON $alias.id_utilizator = c.id_utilizator
        LEFT JOIN asociere_multipla am ON $alias.id_utilizator = am.id_utilizator
        WHERE (
            (u.id_cookie = '$id_cookie' AND u.status = '$status')
            OR (u.status IN ('$opposite_status_str') AND (c.grupa_clasa_copil = '$grupa_clasa_copil_curent' OR am.grupa_clasa_copil = '$grupa_clasa_copil_curent'))
            $additionalCondition
        )
        AND $alias.extensie = 'pdf'
        ORDER BY $alias.data_upload DESC";

$result = $conn->query($sql);


$fisiere_utilizator = [];
$fisiere_profesor = [];


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($row['id_utilizator'] == $_SESSION['id_utilizator']) {
            $fisiere_utilizator[] = $row;
        } else {
            $fisiere_profesor[] = $row;
        }
    }
}



$output = [
    'fisiere_utilizator' => $fisiere_utilizator,
    'fisiere_profesor' => $fisiere_profesor,
];

// Verificăm dacă scriptul este apelat din infodisplay.php
if (isset($_GET['source']) && $_GET['source'] == 'infodisplay') {
    // Interogare pentru a extrage toate documentele pdf
    $sql_iframes = "SELECT alias.id_info, alias.nume_fisier, alias.extensie, alias.data_upload, u.temp_path
        FROM informatii_" . $grupa_clasa_copil_curent . " alias
        JOIN utilizatori u ON alias.id_utilizator = u.id_utilizator
        WHERE (u.status != 'parinte')
        AND $alias.extensie = 'pdf'
        ORDER BY alias.data_upload DESC";

    $result_iframes = $conn->query($sql_iframes);

    $iframes_info = [];

    if ($result_iframes->num_rows > 0) {
        while($row_iframe = $result_iframes->fetch_assoc()) {
            $cale_infodisplay = '/' . str_replace('/home/tid4kdem/public_html/', '', $row_iframe['temp_path']) . $row_iframe['nume_fisier'];
            $iframes_info[] = [
                'id_info' => $row_iframe['id_info'],
                'nume_fisier' => $row_iframe['nume_fisier'],
                'extensie' => $row_iframe['extensie'],
                'data_upload' => $row_iframe['data_upload'],
                'cale_infodisplay_afisat' => $cale_infodisplay
            ];
        }
    }

    // Afișare JSON pentru infodisplay.php
    header('Content-Type: application/json');
    echo json_encode($iframes_info); // Modificat pentru a returna direct array-ul de documente
} else {
    // Afișare JSON-ul original pentru alte surse
    header('Content-Type: application/json');
    echo json_encode($output);
}

$conn->close();
?>
