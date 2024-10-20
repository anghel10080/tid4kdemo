<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php'; // Include configurațiile
require_once 'functii_si_constante.php'; // Include funcțiile și constantele

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['id_utilizator'])) {
    echo json_encode(['success' => false, 'error' => 'Utilizatorul nu este autentificat.']);
    exit;
}

$id_utilizator = $_SESSION['id_utilizator'];

// Obțineți calea directorului utilizatorului
$queryPath = "SELECT CONCAT('/sesiuni', SUBSTRING_INDEX(temp_path, '/sesiuni', -1)) AS temp_path FROM utilizatori WHERE id_utilizator = ?";
$stmtPath = mysqli_prepare($conn, $queryPath);

if ($stmtPath === false) {
    echo json_encode(['success' => false, 'error' => 'Eroare la pregătirea interogării pentru cale: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmtPath, "i", $id_utilizator);
mysqli_stmt_execute($stmtPath);
$resultPath = mysqli_stmt_get_result($stmtPath);

if ($row = mysqli_fetch_assoc($resultPath)) {
    $userPath = $row['temp_path'];
} else {
    echo json_encode(['success' => false, 'error' => 'Calea directorului nu a putut fi găsită.']);
    exit;
}

mysqli_stmt_close($stmtPath);

// Verifică dacă fișierul a fost încărcat
if (isset($_FILES['image']['tmp_name'])) {
    $imagine = $_FILES['image']['tmp_name'];

    // Obține conținutul fișierului
    $continut = file_get_contents($imagine);
    $extensie = 'png';
    $tip_fisier = 'image/png';

    // Creează un nume unic pentru fișierul de imagine
    $nume_fisier = uniqid('meniu_') . '.png';

    // Interogare SQL pentru a insera imaginea în baza de date
    $query = "INSERT INTO informatii_meniul (id_utilizator, nume_fisier, extensie, tip_fisier, continut, data_upload, afisat) VALUES (?, ?, ?, ?, ?, NOW(), 1)";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Eroare la pregătirea interogării: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "issss", $id_utilizator, $nume_fisier, $extensie, $tip_fisier, $continut);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // Salvați fișierul în directorul utilizatorului
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $userPath . '/' . $nume_fisier;
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }
        file_put_contents($fullPath, $continut);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Eroare la inserarea în baza de date: ' . mysqli_stmt_error($stmt)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'error' => 'Nu a fost încărcat niciun fișier.']);
}
?>
