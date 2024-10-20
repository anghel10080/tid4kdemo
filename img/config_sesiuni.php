<?php
// Pornim sesiunea
session_start();

require_once 'config.php';

// Setăm directorul pentru sesiunile PHP
$session_save_path = '/home/tid4kdem/public_html/sesiuni/';
if (!is_dir($session_save_path)) {
    mkdir($session_save_path, 0777, true);

}

// // Setăm opțiunile pentru sesiune
$session_lifetime = 30 * 24 * 60 * 60; // Sesiunea expiră după 30 de zile
session_set_cookie_params($session_lifetime, '/', null, true, true);


// Verifică dacă id_cookie există
$temp_path = '';
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



// Dacă id_cookie există, adaugă o nouă intrare în sesiuni_utilizatori
if ($id_cookie_existent) {
    $inceput_sesiune = date("Y-m-d H:i:s");
    $dispozitiv = $_SERVER['HTTP_USER_AGENT'];
    $sql = "INSERT INTO sesiuni_utilizatori (id_utilizator, inceput_sesiune, dispozitiv) VALUES ( ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iss", $id_utilizator, $inceput_sesiune, $dispozitiv);
        if (!$stmt->execute()) {
            echo "Eroare la adăugarea sesiunii: " . $stmt->error;
        }
        $stmt->close();
    }
}




// Redirectionează către sesiuni.php
header("location: sesiuni.php");
?>
