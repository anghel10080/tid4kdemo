<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';
require_once '../sesiuni.php';
require_once 'functii_si_constante.php';

  // Apelarea functiei pentru a umple variabilele de sesiune: id_utilizator, status, grupa_clasa_copil
  determina_variabile_utilizator($conn);

$sql = "SELECT mesaje_" . $_SESSION['grupa_clasa_copil_'] . ".*, utilizatori.nume_prenume AS nume_destinatar, utilizatori.status AS status_destinatar,
        expeditor.nume_prenume AS nume_expeditor, expeditor.status AS status_expeditor
        FROM mesaje_" . $_SESSION['grupa_clasa_copil_'] . "
        LEFT JOIN utilizatori ON mesaje_" . $_SESSION['grupa_clasa_copil_'] . ".id_destinatar = utilizatori.id_utilizator
        LEFT JOIN utilizatori AS expeditor ON mesaje_" . $_SESSION['grupa_clasa_copil_'] . ".id_expeditor = expeditor.id_utilizator
        WHERE id_destinatar = ? OR id_expeditor = ?
        ORDER BY data_trimitere DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id_utilizator, $id_utilizator);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$mesaje = [];
while ($row = mysqli_fetch_assoc($result)) {
    $mesaje[] = $row;
}

header('Content-Type: application/json');
echo json_encode($mesaje);

$conn->close();
?>
