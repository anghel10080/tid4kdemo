<?php
define('ROOT_PATH', '/home/tid4kdem/public_html/');//calea relativa pentru fisiere atunci cand este folosit require_once sau include

// Eliminarea erorilor legate de tipul de întoarcere depreciat
error_reporting(E_ALL ^ E_DEPRECATED);


// Datele de configurare a conexiunii la baza de date
$host = "localhost";
$username = "id4k";
$password = "Infodisplay4K";
$database = "tid4k";

// Conexiunea la baza de date
$conn = mysqli_connect($host, $username, $password, $database);


// Verificăm dacă conexiunea a fost realizată cu succes
if (!$conn) {
    // Afisam mesajul de eroare
    die("A apărut o eroare în procesul de pre-autorizare. Vă rugăm să reluați scanarea codului QR și încercați din nou.");
}

?>
