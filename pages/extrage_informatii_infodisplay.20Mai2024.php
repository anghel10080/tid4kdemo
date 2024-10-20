<?php
require_once '../config.php';
require_once 'functii_si_constante.php';

header('Content-Type: application/json');

$tabela = $_GET['grupa'] ?? '';

if (empty($tabela)) {
    echo json_encode(['error' => 'Numele grupei nu este specificat sau invalid']);
    exit;
}

// Verificăm dacă interogăm pentru meniu, anunțuri sau alte informații
if ($tabela === 'informatii_meniul') {
    // Logica pentru extragerea și afișarea HTML-ului meniului
    $query = "SELECT continut FROM informatii_meniul ORDER BY data_upload DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['error' => 'Eroare la interogarea bazei de date: ' . mysqli_error($conn)]);
        exit;
    }

    $files = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $files[] = ['html' => $row['continut']];
    }
    mysqli_close($conn);
    echo json_encode($files);
} elseif ($tabela === 'informatii_anunturi') {
    // Logica pentru extragerea și afișarea HTML-ului anunțurilor
    $query = "SELECT continut, data_expirare FROM informatii_anunturi ORDER BY data_upload DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['error' => 'Eroare la interogarea bazei de date: ' . mysqli_error($conn)]);
        exit;
    }

    $files = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $files[] = ['html' => $row['continut'], 'data_expirare' => $row['data_expirare']];
    }
    mysqli_close($conn);
    echo json_encode($files);
} else {
    // Logica pentru extragerea informațiilor pentru celelalte grupuri
    $limit = 10;
    $query = "SELECT inf.nume_fisier, CONCAT('/sesiuni', SUBSTRING_INDEX(usr.temp_path, '/sesiuni', -1)) AS temp_path FROM $tabela as inf JOIN utilizatori as usr ON inf.id_utilizator = usr.id_utilizator ORDER BY inf.id_info DESC LIMIT $limit";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['error' => 'Eroare la interogarea bazei de date: ' . mysqli_error($conn)]);
        exit;
    }

    $files = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $files[] = ['nume_fisier' => $row['nume_fisier'], 'temp_path' => $row['temp_path']];
    }
    mysqli_close($conn);
    echo json_encode($files);
}
?>
