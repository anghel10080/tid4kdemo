<?php
// Partea inițială rămâne neschimbată
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';
require_once '../sesiuni.php';

// Interogare pentru a obține valorile unice ale coloanei grupa_clasa_copil
$sql = "SELECT DISTINCT grupa_clasa_copil FROM copii";
$result = mysqli_query($conn, $sql);

$grupe_clase_disponibile = [];

if (mysqli_num_rows($result) > 0) {
    // Adăugarea rezultatelor în array-ul $grupe_clase_disponibile
    while($row = mysqli_fetch_assoc($result)) {
        $grupa_clasa_formatata = str_replace(" ", "_", $row["grupa_clasa_copil"]);
        $grupe_clase_disponibile[] = $grupa_clasa_formatata;
    }
} else {
    echo "0 rezultate";
}

$prezenti_general_copii = 0;
$absenti_general_copii = 0;
$prezenti_general_profesori = 0;
$absenti_general_profesori = 0;

// Calcul pentru copii
foreach($grupe_clase_disponibile as $grupa_clasa) {
    $tabela = 'prezenta_' . $grupa_clasa;
    $sql = "SELECT SUM(prezenta_stare = 'prezent') as prezenti, SUM(prezenta_stare = 'absent') as absenti FROM $tabela WHERE DATE(prezenta_data) = CURDATE()";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $prezenti_general_copii += $row['prezenti'];
        $absenti_general_copii += $row['absenti'];
    }
}

// Calcul pentru profesori, directori, administratori, secretare
$sql_profesori = "SELECT SUM(DATE(ultima_activitate) = CURDATE() AND (status = 'profesor' OR status = 'director' OR status = 'administrator' OR status = 'secretara')) as prezenti_profesori, SUM(DATE(ultima_activitate) != CURDATE() AND (status = 'profesor' OR status = 'director' OR status = 'administrator' OR status = 'secretara')) as absenti_profesori FROM utilizatori";
$result_profesori = $conn->query($sql_profesori);
if ($result_profesori->num_rows > 0) {
    $row_profesori = $result_profesori->fetch_assoc();
    $prezenti_general_profesori = $row_profesori['prezenti_profesori'];
    $absenti_general_profesori = $row_profesori['absenti_profesori'];
}

$response = [
    'prezenti_general_copii' => $prezenti_general_copii,
    'absenti_general_copii' => $absenti_general_copii,
    'prezenti_general_profesori' => $prezenti_general_profesori,
    'absenti_general_profesori' => $absenti_general_profesori
];

echo json_encode($response);

// Închiderea conexiunii
mysqli_close($conn);
?>
