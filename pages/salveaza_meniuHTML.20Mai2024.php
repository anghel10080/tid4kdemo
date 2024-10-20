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

// Verifică dacă datele au fost trimise prin POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $html_content = $_POST['html'] ?? '';

    // Validează dacă conținutul HTML a fost primit
    if (empty($html_content)) {
        echo json_encode(['success' => false, 'error' => 'Conținutul HTML nu a fost primit.']);
        exit;
    }

    // Interogare SQL pentru a insera HTML-ul în baza de date
    $query = "INSERT INTO informatii_meniul (id_utilizator, nume_fisier, extensie, tip_fisier, continut, data_upload, afisat) VALUES (?, 'meniu.html', 'html', 'text/html', ?, NOW(), 1)";
$stmt = mysqli_prepare($conn, $query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Eroare la pregătirea interogării: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "is", $id_utilizator, $html_content);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'HTML-ul meniului a fost salvat cu succes.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Eroare la inserarea în baza de date: ' . mysqli_stmt_error($stmt)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'error' => 'Metoda HTTP necorespunzătoare.']);
}
?>
