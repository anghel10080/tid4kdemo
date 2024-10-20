<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conectarea la baza de date si datele de sesiune
require_once(dirname(__DIR__, 2) . '/config.php');
require_once(dirname(__DIR__, 2) . '/sesiuni.php');


require_once(ROOT_PATH . 'pages/functii_si_constante.php');
determina_variabile_utilizator($conn);

$id_cookie = $_SESSION['id_cookie'];
$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];
$alias = 'alias';

$sql = "SELECT $alias.id_info, $alias.nume_fisier, $alias.extensie, $alias.data_upload, $alias.id_utilizator, u.nume_prenume, u.status, $alias.afisat
    FROM informatii_" . $grupa_clasa_copil_curent . " $alias
    JOIN utilizatori u ON $alias.id_utilizator = u.id_utilizator
    WHERE (u.status != 'parinte')
    AND $alias.extensie = 'pdf'
    AND ($alias.nume_fisier LIKE '%meniu%'
         OR $alias.nume_fisier LIKE '%meniul%'
         OR $alias.nume_fisier LIKE '%mancare%')
    ORDER BY $alias.data_upload DESC";


$result = $conn->query($sql);

$files_administrator = [];
$numar_meniuri_nou = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $files_administrator[] = $row;

        if ($row['afisat'] == false) {
            $numar_meniuri_nou++;
            // Actualizați coloana afisat pentru rândul curent
            $update_query = "UPDATE informatii_" . $grupa_clasa_copil_curent . " SET afisat=true WHERE id_info={$row['id_info']}";
            $conn->query($update_query);
        }
    }
}

$output = [
    'files_administrator' => $files_administrator,
    'numar_meniuri_nou' => $numar_meniuri_nou
    ];

// Verificăm dacă scriptul este apelat din infodisplay.php
if (isset($_GET['source']) && $_GET['source'] == 'infodisplay') {
    // Interogare pentru a extrage cel mai recent fișier PDF
    $sql_ultim_meniu = "SELECT $alias.id_info, $alias.nume_fisier, $alias.extensie, $alias.data_upload, u.temp_path
        FROM informatii_" . $grupa_clasa_copil_curent . " $alias
        JOIN utilizatori u ON $alias.id_utilizator = u.id_utilizator
        WHERE (u.status != 'parinte')
        AND $alias.extensie = 'pdf'
        AND ($alias.nume_fisier LIKE '%meniu%'
             OR $alias.nume_fisier LIKE '%meniul%'
             OR $alias.nume_fisier LIKE '%mancare%')
        ORDER BY $alias.data_upload DESC LIMIT 1";

    $result_ultim_meniu = $conn->query($sql_ultim_meniu);

    $ultim_meniu_info = [];

if ($result_ultim_meniu->num_rows > 0) {
    $row_ultim_meniu = $result_ultim_meniu->fetch_assoc();
    $cale_infodisplay = '/' . str_replace('/home/tid4kdem/public_html/', '', $row_ultim_meniu['temp_path']) . $row_ultim_meniu['nume_fisier'];
    $ultim_meniu_info = [
        'id_info' => $row_ultim_meniu['id_info'],
        'nume_fisier' => $row_ultim_meniu['nume_fisier'],
        'extensie' => $row_ultim_meniu['extensie'],
        'data_upload' => $row_ultim_meniu['data_upload'],
        'cale_infodisplay_afisat' => $cale_infodisplay
    ];
}
    // Afișare JSON pentru infodisplay.php
    header('Content-Type: application/json');
    echo json_encode($ultim_meniu_info);
} else {
    // Afișare JSON-ul original pentru alte surse
    header('Content-Type: application/json');
    echo json_encode($output);
}

$conn->close();
?>
