<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'sesiuni.php';

// Interogare pentru a afla statusul utilizatorului curent
$sql_status = "SELECT status FROM utilizatori WHERE id_utilizator = " . $_SESSION['id_utilizator'];
$result_status = mysqli_query($conn, $sql_status);
$row_status = mysqli_fetch_assoc($result_status);
$status_utilizator = $row_status['status'];

if ($status_utilizator == 'parinte') {
    $sql = "SELECT utilizatori.nume_prenume, copii.nume_copil, copii.grupa_clasa_copil
            FROM utilizatori
            JOIN copii ON utilizatori.id_utilizator = copii.id_utilizator
            WHERE utilizatori.id_utilizator = " . $_SESSION['id_utilizator'];
} else {
    $sql = "SELECT u.nume_prenume,
                   a.grupa_clasa_copil
            FROM asociere_multipla a
            JOIN utilizatori u ON a.id_utilizator = u.id_utilizator
            WHERE a.id_utilizator = " . $_SESSION['id_utilizator'] .
            " ORDER BY a.id_asociativ LIMIT 1";
}

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Verifică dacă $_SESSION['rol'] este setat
    if(isset($_SESSION['rol'])) {
        $grupa_clasa_copil = $_SESSION['rol'];
    } else {
        $grupa_clasa_copil = $row["grupa_clasa_copil"];
    }
    $nume_prenume = $row["nume_prenume"];
    $nume_copil = $row["nume_copil"] ?? "";

    echo "<h1 style='font-size: 60px; font-weight: bold;'>Bun venit, " . $nume_prenume . "!</h1>";
    if ($status_utilizator == 'parinte') {
        echo "<p style='font-size: 24px;'>Vei fi redirectionat in cateva secunde catre grupa/clasa copilului tau, " . $nume_copil . ".</p>";
    } else {
        echo "<p style='font-size: 24px;'>Vei fi redirectionat in cateva secunde catre grupa/clasa " . $grupa_clasa_copil . ".</p>";
    }
    echo "<p style='font-size: 24px;'>Daca nu esti redirectionat automat, te rugam sa accesezi <a href='/pages/grupa_clasa_copil.php'>pagina corespunzătoare</a></p>";
    echo "<script>setTimeout(function(){window.location.href='/pages/grupa_clasa_copil.php';}, 2000);</script>";

} else {
    echo "Nu există sesiuni active.";
}
?>
