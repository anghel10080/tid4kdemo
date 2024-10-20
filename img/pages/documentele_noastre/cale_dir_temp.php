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
$alias = 'alias';



// Interogare pentru a obține căile temporare ale utilizatorilor cu status de profesor sau parinte sau elev și a utilizatorului curent
$stmt = $conn->prepare("SELECT id_utilizator, nume_prenume, status, temp_path
    FROM utilizatori
    WHERE ((status = 'parinte' AND ? != 'parinte')
    OR (status != 'parinte' AND ? = 'parinte')
    OR (status != 'parinte' AND ? != 'parinte')
    OR (status = 'elev' AND ? != 'elev')
    OR (status != 'elev' AND ? != 'elev')
    OR (status != 'elev' AND ? != 'elev')
    OR id_cookie = ?)");

// Leagă parametrii
$stmt->bind_param("sssssss", $status, $status, $status,$status, $status, $status, $id_cookie);

// Execută interogarea
$stmt->execute();


// Obține rezultatul
$result = $stmt->get_result();
$temp_paths = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $temp_paths[] = $row;
    }
}

$relative_path_prefix = '/sesiuni/';

foreach ($temp_paths as &$temp_path) {
    $temp_path['temp_path'] = str_replace('/home/tid4kdem/public_html/sesiuni/', $relative_path_prefix, $temp_path['temp_path']);
}

header('Content-Type: application/json');
echo json_encode($temp_paths);

$conn->close();
?>
