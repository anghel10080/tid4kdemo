<?php
// Parametrii de conectare la baza de date
$host = "localhost";
$username = "id4k";
$password = "Infodisplay4K";
$database = "tid4k";

// Conectare la baza de date
$conn = mysqli_connect($host, $username, $password, $database);

// Verificare conexiune
if (!$conn) {
    die("Conexiune esuata: " . mysqli_connect_error());
}
