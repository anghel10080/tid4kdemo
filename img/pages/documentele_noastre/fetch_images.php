<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



// Conectarea la baza de date si datele de sesiune
require_once(dirname(__DIR__, 2) . '/config.php');
require_once(dirname(__DIR__, 2) . '/sesiuni.php');

require_once(ROOT_PATH . 'pages/functii_si_constante.php');
determina_variabile_utilizator($conn);

$id_cookie = $_SESSION['id_cookie'];
$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];

// Construirea interogării SQL în funcție de statusul utilizatorului curent
$finalResults = [];
$files_utilizator = [];
$files_ceilalti = [];

// Configurația pentru statusuri opuse
$oppositeStatusConfig = [
    'parinte' => ['profesor', 'director', 'administrator', 'secretara', 'elev'],
    'elev' => ['profesor', 'director', 'administrator', 'secretara', 'parinte'],
    'profesor' => ['parinte', 'director', 'administrator', 'secretara', 'elev'],
    'director' => ['parinte', 'profesor', 'administrator', 'secretara', 'elev'],
    'administrator' => ['parinte', 'profesor', 'director', 'secretara', 'elev'],
    'secretara' => ['parinte', 'profesor', 'director', 'administrator', 'elev'],
];

// Determină statusurile opuse pentru statusul curent
$opposite_status = $oppositeStatusConfig[$status] ?? [];

// Convertirea array-ului în string pentru interogare
$opposite_status_str = implode("','", $opposite_status);

// Interogarea pentru statusul curent și pentru statusurile opuse
$sql = "SELECT DISTINCT alias.id_info, alias.nume_fisier, alias.extensie, alias.data_upload, alias.id_utilizator, u.nume_prenume, u.status
        FROM informatii_" . $grupa_clasa_copil_curent . " alias
        JOIN utilizatori u ON alias.id_utilizator = u.id_utilizator
        LEFT JOIN copii c ON alias.id_utilizator = c.id_utilizator
        LEFT JOIN asociere_multipla am ON alias.id_utilizator = am.id_utilizator
        WHERE (
                (u.id_utilizator = ? AND u.status = ?)
                OR (u.status IN ('$opposite_status_str'))
              )
        AND (c.grupa_clasa_copil = ? OR am.grupa_clasa_copil = ?)
        AND alias.extensie IN ('jpg', 'jpeg', 'png', 'gif')
        ORDER BY alias.data_upload DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isss", $id_utilizator, $status, $grupa_clasa_copil, $grupa_clasa_copil);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($row['id_utilizator'] == $_SESSION['id_utilizator']) {
            $files_utilizator[] = $row;
        } else {
            $files_ceilalti[] = $row;
        }
    }
}


$output = [
    'files_utilizator' => $files_utilizator,
    'files_ceilalti' => $files_ceilalti,
    'status' => $status,
];

header('Content-Type: application/json');
echo json_encode($output);

$conn->close();
?>
