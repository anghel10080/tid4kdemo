<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

$id_utilizator = '';
$id_utilizator_existent = false;
$id_cookie_existent = false;
if (isset($_COOKIE['id_cookie'])) {
    $id_cookie = $_COOKIE['id_cookie'];
    $sql = "SELECT id_utilizator, temp_path FROM utilizatori WHERE id_cookie = ? LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $id_cookie);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_utilizator = $row['id_utilizator'];
                $temp_path = $row['temp_path']; // aici avem valoarea temp_path
                $id_cookie_existent = true;
                $_SESSION['id_cookie'] = $id_cookie;
                $_SESSION['id_utilizator_existent'] = true;
                $_SESSION['id_utilizator'] = $id_utilizator;
                $_SESSION['temp_path'] = $temp_path; // salvăm valoarea în sesiune
            }
        }
        $stmt->close();
    }
}


// Verifică dacă ID-ul utilizatorului este setat și nu este gol
$id_utilizator = !empty($_SESSION['id_utilizator']) ? $_SESSION['id_utilizator'] : null;

if (!$id_utilizator) {
    // Dacă ID-ul utilizatorului nu este setat sau este gol, redirecționăm către start.php
    header("location: /pages/pre_autorizare.php");
    exit;
}

//setam rolul utilizatorului curent astfel incat acesta sa poata accesa exclusiv paginile asociate grupe_clasei corespunzatoare si nu altceva
     $sql = "SELECT grupa_clasa_copil FROM copii WHERE id_utilizator = ? LIMIT 1";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param('s', $id_utilizator);
                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $_SESSION['rol'] = $row['grupa_clasa_copil']; // salvăm valoarea în sesiune
                        }
                    }
                    $stmt->close();
                }

// Actualizează ultima_activitate pentru utilizatorul curent
$query = "UPDATE utilizatori SET ultima_activitate = NOW() WHERE id_utilizator = ?";
$stmt2 = $conn->prepare($query);
$stmt2->bind_param("i", $id_utilizator);
$stmt2->execute();
$stmt2->close();

if (basename($_SERVER['SCRIPT_FILENAME']) !== 'sesiuni.php') {
    return;
}

// Redirecționăm către welcome.php dacă utilizatorul curent este utilizator existent
if ($_SESSION['id_utilizator_existent'] === true) {
     header("location: welcome.php");
     exit;
}

// Dacă ajungem aici, înseamnă că utilizatorul nu este înregistrat
header("location: start.php");
?>
