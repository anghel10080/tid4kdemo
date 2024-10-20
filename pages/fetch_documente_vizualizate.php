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
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil_'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];

$sql = "SELECT igm.id_info, igm.nume_fisier, igm.extensie, igm.data_upload, igm.id_utilizator, u.nume_prenume, u.status, dgmv.id_utilizator AS vizualizat
        FROM informatii_" . $_SESSION['grupa_clasa_copil_'] . " igm
        JOIN utilizatori u ON igm.id_utilizator = u.id_utilizator
        LEFT JOIN documente_" . $_SESSION['grupa_clasa_copil_'] . "_vizualizate dgmv ON igm.id_info = dgmv.id_document AND dgmv.id_utilizator = ?
        WHERE igm.extensie = 'pdf'
        AND (
            (? = 'parinte' AND u.status = 'profesor' AND dgmv.id_utilizator IS NULL) OR
            (? = 'profesor' AND u.status = 'parinte' AND dgmv.id_utilizator IS NULL)
        )
        ORDER BY igm.data_upload DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iss", $id_utilizator, $status, $status);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$numar_imagini_nou = 0;

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['vizualizat'] === NULL) {
        $numar_imagini_nou++;

        // Inserarea înregistrării imaginii nevizualizate în tabela imagini_grupa_mica_vizualizate
        $insert_sql = "INSERT INTO documente_" . $_SESSION['grupa_clasa_copil_'] . "_vizualizate (id_document, id_utilizator) VALUES (?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "ii", $row['id_info'], $id_utilizator);
        mysqli_stmt_execute($insert_stmt);
        mysqli_stmt_close($insert_stmt);
    }
}

$data = [
      'numar_documente_nou' => $numar_imagini_nou,
];
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
