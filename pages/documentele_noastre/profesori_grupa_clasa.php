<?php
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

$sql = "SELECT DISTINCT u.id_utilizator AS id, u.nume_prenume, u.email
        FROM utilizatori u
        JOIN asociere_multipla am ON u.id_utilizator = am.id_utilizator
        WHERE u.status = 'profesor' AND am.grupa_clasa_copil = '$grupa_clasa_copil'
        ORDER BY u.nume_prenume";

$result = $conn->query($sql);
$profesori = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $profesori[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($profesori);

$conn->close();
?>
