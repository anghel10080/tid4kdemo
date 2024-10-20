<?php
require_once '../config.php';

// Asocierea pentru id_copil 47 la grupa mare
$sql1 = "INSERT INTO asociere_multipla (id_copil, id_utilizator, grupa_clasa_copil) VALUES
(47, 34, 'grupa mare'),
(47, 35, 'grupa mare'),
(47, 36, 'grupa mare')";

// Asocierea pentru id_copil 48 la grupa mijlocie
$sql2 = "INSERT INTO asociere_multipla (id_copil, id_utilizator, grupa_clasa_copil) VALUES
(48, 34, 'grupa mijlocie'),
(48, 35, 'grupa mijlocie'),
(48, 36, 'grupa mijlocie')";

// Executarea primului SQL
if (mysqli_query($conn, $sql1)) {
    echo "Înregistrările pentru grupa mare au fost adăugate cu succes.<br>";
} else {
    echo "Eroare: " . $sql1 . "<br>" . mysqli_error($conn);
}

// Executarea celui de-al doilea SQL
if (mysqli_query($conn, $sql2)) {
    echo "Înregistrările pentru grupa mijlocie au fost adăugate cu succes.<br>";
} else {
    echo "Eroare: " . $sql2 . "<br>" . mysqli_error($conn);
}
?>
