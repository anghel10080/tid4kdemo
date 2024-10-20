<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


require_once '../config.php';
require_once '../sesiuni.php';
require_once 'functii_si_constante.php';
 determina_variabile_utilizator($conn);


$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];


$sql = "SELECT igm.id_info, igm.nume_fisier, igm.extensie, igm.data_upload, igm.id_utilizator, u.nume_prenume, u.status, igmv.id_utilizator AS vizualizat
        FROM informatii_" . $_SESSION['grupa_clasa_copil_'] . " igm
        JOIN utilizatori u ON igm.id_utilizator = u.id_utilizator
        LEFT JOIN imagini_" . $_SESSION['grupa_clasa_copil_'] . "_vizualizate igmv ON igm.id_info = igmv.id_imagine AND igmv.id_utilizator = ?
        WHERE igm.tip_fisier LIKE 'image/%'
        AND (
            (? = 'parinte' AND u.status = 'profesor' AND igmv.id_utilizator IS NULL) OR
            (? = 'profesor' AND u.status = 'parinte' AND igmv.id_utilizator IS NULL)
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
        $insert_sql = "INSERT INTO imagini_" . $_SESSION['grupa_clasa_copil_'] . "_vizualizate (id_imagine, id_utilizator) VALUES (?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "ii", $row['id_info'], $id_utilizator);
        mysqli_stmt_execute($insert_stmt);
        mysqli_stmt_close($insert_stmt);
    }
}

$data = [
      'numar_imagini_nou' => $numar_imagini_nou,
];
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
