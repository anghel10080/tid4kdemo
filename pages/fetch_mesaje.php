<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../sesiuni.php';

require_once 'functii_si_constante.php';

  // Apelarea functiei pentru a umple variabilele de sesiune: id_utilizator, status, grupa_clasa_copil
  determina_variabile_utilizator($conn);

$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];

// Variabila primita din JavaScriptul grupa_mica.php pentru mesajele citite
if (isset($_POST['mesaje_citite'])) {
    $idsMesaje = json_decode($_POST['mesaje_citite']);

    foreach ($idsMesaje as $idMesaj) {
        $update_query = "UPDATE mesaje_" . $_SESSION['grupa_clasa_copil_'] . " SET citit=NOW() WHERE id=?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $idMesaj);
        mysqli_stmt_execute($stmt);
    }
}

$mesaje_utilizator = [];
$numar_mesaje_nou = 0;

$sql = "SELECT mgm.*, mgmv.id_utilizator AS vizualizat
        FROM mesaje_" . $_SESSION['grupa_clasa_copil_'] . " mgm
        LEFT JOIN mesaje_" . $_SESSION['grupa_clasa_copil_'] . "_vizualizate mgmv ON mgm.id_mesaj = mgmv.id_mesaj AND mgmv.id_utilizator = ?
        WHERE mgm.id_destinatar = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_utilizator, $id_utilizator);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $mesaje_utilizator[] = $row;

    // Dacă mesajul nu a fost vizualizat, creșteți numărul de mesaje noi
    if ($row['vizualizat'] === NULL) {
              $numar_mesaje_nou++;


        // Adăugați un rând în tabela mesaje_vizualizate pentru a marca mesajul ca vizualizat de utilizatorul curent
        $insert_query = "INSERT INTO mesaje_" . $_SESSION['grupa_clasa_copil_'] . "_vizualizate (id_mesaj, id_utilizator) VALUES (?, ?)";
        $stmt_insert = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt_insert, "ii", $row['id_mesaj'], $id_utilizator);
        mysqli_stmt_execute($stmt_insert);
    }
}



$data = [
    'mesaje_utilizator' => $mesaje_utilizator,
    'numar_mesaje_nou' => $numar_mesaje_nou,
];


header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
