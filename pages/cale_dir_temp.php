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

// Interogare pentru a obține căile temporare ale utilizatorilor cu status de profesor, administrativ  și a utilizatorului curent , care poate fi si parinte
// Condiția pentru când utilizatorul curent nu este parinte
$statusCondition1 = ($status != 'parinte') ? " OR status = 'parinte'" : "";

// Condiția pentru când utilizatorul curent este parinte
$statusCondition2 = ($status == 'parinte') ? " OR (status != 'parinte' AND id_cookie != '$id_cookie')" : "";

// Interogarea efectivă
$sql = ($status != 'parinte')
    ? "SELECT id_utilizator, nume_prenume, status, temp_path FROM utilizatori WHERE status != 'parinte'" . $statusCondition1
    : "SELECT id_utilizator, nume_prenume, status, temp_path FROM utilizatori WHERE id_cookie = '$id_cookie'" . $statusCondition2;


$result = $conn->query($sql);
$temp_paths = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $temp_paths[] = $row;
    }
}

$relative_path_prefix = '/sesiuni/';

foreach ($temp_paths as &$temp_path) {
    $temp_path['temp_path'] = str_replace('/home/tid4kdem/public_html/sesiuni/', $relative_path_prefix, $temp_path['temp_path']);
}

header('Content-Type: application/json');
echo json_encode($temp_paths);

$conn->close();
?>
