<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefon = $_POST['telefon'];

    // Prima interogare pentru a identifica statutul utilizatorului
    $sql_status = "SELECT status FROM utilizatori WHERE telefon = ?";
    $stmt_status = $conn->prepare($sql_status);
    $stmt_status->bind_param("s", $telefon);
    $stmt_status->execute();
    $result_status = $stmt_status->get_result();
    $row_status = $result_status->fetch_assoc();
    $status_utilizator = $row_status['status'] ?? '';

    // Alegem interogarea potrivită în funcție de statut
    if ($status_utilizator == 'parinte') {
        $sql = "SELECT u.id_utilizator, u.nume_prenume, u.email, u.id_cookie, u.status, c.nume_copil, c.varsta_copil, c.grupa_clasa_copil, u.temp_path
                FROM utilizatori AS u
                JOIN copii AS c ON u.id_utilizator = c.id_utilizator
                WHERE u.telefon = ?
                LIMIT 1";
    } else {
        $sql = "SELECT u.id_utilizator, u.nume_prenume, u.email, u.id_cookie, u.status, a.grupa_clasa_copil, u.temp_path
                FROM utilizatori AS u
                JOIN asociere_multipla AS a ON u.id_utilizator = a.id_utilizator
                WHERE u.telefon = ?
                ORDER BY a.id_asociativ
                LIMIT 1";
    }

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $telefon);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['status'] = $row['status'];

                // distrugem sesiunea curenta, pentru id_utilizator curent; memoram id_utilizator, deja inregistrat si pornim o noua sesiune pentru acesta
                session_destroy();
                session_id($row['id_cookie']);

                // Setăm opțiunile pentru sesiune
                $session_lifetime = 1800; // 30 minute
                session_set_cookie_params($session_lifetime, '/', null, true, true);
                ini_set('session.gc_maxlifetime', $session_lifetime);

                // Setăm cookie-ul $id_cookie
                $id_cookie = $row['id_cookie'];
                setcookie('id_cookie', $id_cookie, time() + 60 * 60 * 24 * 365, "/"); // Setează cookie-ul pentru 1 an
                session_start();
                $_SESSION['id_utilizator'] = $row['id_utilizator'];
                $_SESSION['status'] = $row['status'];

                header('Content-Type: application/json');
                echo json_encode($row);
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('error' => 'Nu sunteti inscris in proiectul TID4K!, va poate ajuta Grupa Unitatii Scolare.'));
            }
        }
    }
}

?>
