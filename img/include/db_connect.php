<?php
// Detalii conexiune la baza de date
$host = "localhost";
$user = "id4k";
$password = "Infodisplay4K";
$db_name = "tid4k";

// Conectare la baza de date MySQL
$con = mysqli_connect($host, $user, $password, $db_name);

// Verificare conexiune
if (!$con) {
    die("Conexiunea la baza de date a esuat: " . mysqli_connect_error());
}
?>
