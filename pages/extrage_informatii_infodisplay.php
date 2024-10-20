<?php
require_once '../config.php';
require_once 'functii_si_constante.php';

header('Content-Type: application/json');

$tabela = $_GET['grupa'] ?? '';

if (empty($tabela)) {
    echo json_encode(['error' => 'Numele grupei nu este specificat sau invalid']);
    exit;
}

// Funcție pentru a verifica dacă un anunț este expirat
function isExpired($data_expirare) {
    if (empty($data_expirare)) {
        return false;
    }
    $now = new DateTime();
    $data_exp = new DateTime($data_expirare);
    return $now > $data_exp;
}

$files = [];

if ($tabela === 'informatii_meniul') {
    // Logica pentru extragerea și afișarea HTML-ului meniului (neschimbată)
    $query = "SELECT continut, data_expirare FROM informatii_meniul ORDER BY data_upload DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['error' => 'Eroare la interogarea bazei de date: ' . mysqli_error($conn)]);
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        if (!isExpired($row['data_expirare'])) {
            $files[] = ['html' => $row['continut'], 'data_expirare' => $row['data_expirare']];
        }
    }
} elseif ($tabela === 'informatii_anunturi') {
    // Logica pentru extragerea și afișarea HTML-ului anunțurilor
    $query = "SELECT continut, data_expirare FROM informatii_anunturi ORDER BY data_upload DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['error' => 'Eroare la interogarea bazei de date: ' . mysqli_error($conn)]);
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        if (!isExpired($row['data_expirare'])) {
            $files[] = ['html' => $row['continut'], 'data_expirare' => $row['data_expirare']];
        }
    }
} else {
    // Logica pentru extragerea informațiilor pentru celelalte grupuri (neschimbată)
    $limit = 10;
    $query = "SELECT inf.nume_fisier, CONCAT('/sesiuni', SUBSTRING_INDEX(usr.temp_path, '/sesiuni', -1)) AS temp_path FROM $tabela as inf JOIN utilizatori as usr ON inf.id_utilizator = usr.id_utilizator ORDER BY inf.id_info DESC LIMIT $limit";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['error' => 'Eroare la interogarea bazei de date: ' . mysqli_error($conn)]);
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $files[] = ['nume_fisier' => $row['nume_fisier'], 'temp_path' => $row['temp_path']];
    }
}

mysqli_close($conn);
echo json_encode($files);
?>
