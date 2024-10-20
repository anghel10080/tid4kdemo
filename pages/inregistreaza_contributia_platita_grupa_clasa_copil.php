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
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];
$alias = 'alias';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_copil = $_POST['id_copil'];
    $luna = $_POST['luna'];
    $contributia_platita = $_POST['contributia_platita'];
    $numar_chitanta = $_POST['numar_chitanta'];

    // Extrageți $contributia din luna curentă
$sql_extract_contrib = "SELECT contributia FROM contributia_" . $_SESSION['grupa_clasa_copil_'] . " WHERE id_copil = ? AND luna = ?";
$stmt_extract_contrib = $conn->prepare($sql_extract_contrib);
$stmt_extract_contrib->bind_param("is", $id_copil, $luna);
$stmt_extract_contrib->execute();
$result_contrib = $stmt_extract_contrib->get_result();
$row_contrib = $result_contrib->fetch_assoc();
$contributia = $row_contrib['contributia'];


    // Denumiri lunilor în limba română
    $luni = ["Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"];

    // Obținem indexul pentru luna curentă
    $index_luna_curenta = array_search($luna, $luni);

    // Calculăm indexul pentru luna anterioară
    $index_luna_anterioara = ($index_luna_curenta - 1 + 12) % 12;

    // Obținem numele lunii anterioare
    $luna_anterioara = $luni[$index_luna_anterioara];

  // Extrageți $diferenta_contributie din luna anterioară
$sql_extract_diff = "SELECT diferenta_contributie FROM contributia_" . $_SESSION['grupa_clasa_copil_'] . " WHERE id_copil = ? AND luna = ?";
$stmt_extract_diff = $conn->prepare($sql_extract_diff);
$stmt_extract_diff->bind_param("is", $id_copil, $luna_anterioara);
$stmt_extract_diff->execute();
$result_diff = $stmt_extract_diff->get_result();
$row_diff = $result_diff->fetch_assoc();
$diferenta_contributie = $row_diff['diferenta_contributie'];

    $diferenta_contributie_actuala = $contributia_platita - $contributia + $diferenta_contributie;

  $sql = "INSERT INTO contributia_" . $_SESSION['grupa_clasa_copil_'] . " (id_copil, luna, contributia_platita, numar_chitanta, diferenta_contributie)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        contributia_platita = VALUES(contributia_platita),
        numar_chitanta = VALUES(numar_chitanta),
        diferenta_contributie = VALUES(diferenta_contributie),
        data_platii = CURRENT_TIMESTAMP";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isisi", $id_copil, $luna, $contributia_platita, $numar_chitanta, $diferenta_contributie_actuala);


    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Eroare: " . $stmt->error;
    }
}
?>
