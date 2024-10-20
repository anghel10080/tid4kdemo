<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../sesiuni.php';

require_once 'functii_si_constante.php';
 determina_variabile_utilizator($conn);

$id_cookie = $_SESSION['id_cookie'];
$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil_'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];
$alias = 'alias';


if ($status === 'parinte' || $status === 'elev') {
    $sql = "SELECT DISTINCT u.id_utilizator, u.nume_prenume, u.status, (TIMESTAMPDIFF(MINUTE, ultima_activitate, NOW()) <= 5) AS este_conectat
    FROM utilizatori u
    LEFT JOIN asociere_multipla a ON u.id_utilizator = a.id_utilizator
    WHERE (
        (u.status = 'profesor' AND u.id_utilizator != ? AND a.grupa_clasa_copil = '$grupa_clasa_copil') OR
        (u.status IN ('director', 'administrator', 'secretara', 'contabil'))
    )
    ORDER BY CASE
        WHEN u.status = 'profesor' THEN 1
        ELSE 2
    END, u.nume_prenume";
}elseif ($status != 'parinte' && $status != 'elev') {
    $sql = "SELECT u.id_utilizator, u.nume_prenume, u.status, (TIMESTAMPDIFF(MINUTE, ultima_activitate, NOW()) <= 5) AS este_conectat
    FROM utilizatori u
    LEFT JOIN copii c ON (u.id_utilizator = c.id_utilizator AND u.status = 'parinte')
    LEFT JOIN (
        SELECT DISTINCT id_utilizator, grupa_clasa_copil
        FROM asociere_multipla
    ) AS a ON (u.id_utilizator = a.id_utilizator AND u.status = 'profesor') OR (u.id_utilizator = a.id_utilizator AND u.status = 'elev')
    WHERE (
        (u.status = 'parinte' AND c.grupa_clasa_copil = '$grupa_clasa_copil') OR
        (u.status = 'elev' AND a.grupa_clasa_copil = '$grupa_clasa_copil') OR
        (u.status = 'profesor' AND u.id_utilizator != ?) OR
        (u.status IN ('director', 'administrator', 'secretara', 'contabil'))
    )
    ORDER BY CASE
        WHEN u.status = 'parinte' THEN 1
        WHEN u.status = 'profesor' THEN 2
        ELSE 3
    END, u.nume_prenume";
}

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $id_cookie); // acum folosim  id_cookie în loc de id_utilizator
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$utilizatori_eligibili = [];
while ($row = mysqli_fetch_assoc($result)) {
    $utilizatori_eligibili[] = $row;
}

$data = ['utilizatori_eligibili' => $utilizatori_eligibili];

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();

?>
