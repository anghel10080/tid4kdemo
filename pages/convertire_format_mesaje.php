<?php
require_once '../config.php';
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

$query = "SELECT id_mesaj, data_trimitere FROM mesaje_" . $grupa_clasa_copil_curent . "";
$result = mysqli_query($conn, $query);

$romanian_months = ['ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie', 'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie'];
$english_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

while($row = mysqli_fetch_assoc($result)) {
    $id_mesaj = $row['id_mesaj'];

    // Trim and lowercase the string
    $data_trimitere = trim(mb_strtolower($row['data_trimitere']));
    $data_trimitere = str_replace($romanian_months, $english_months, $data_trimitere);

    $data_trimitere = DateTime::createFromFormat('j F Y H:i', $data_trimitere);

    if ($data_trimitere === false) {
        echo "Eroare la parsarea datei: " . $row['data_trimitere'];
        continue;
    }
    $data_trimitere_mysql = $data_trimitere->format('Y-m-d H:i:s');

    $query_update = "UPDATE mesaje_" . $grupa_clasa_copil_curent . " SET data_trimitere='$data_trimitere_mysql' WHERE id_mesaj=$id_mesaj";
    mysqli_query($conn, $query_update);
}




?>
