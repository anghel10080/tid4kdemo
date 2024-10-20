<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefon = $_POST['telefon'];

    // Interogăm utilizatorul pe baza numărului de telefon
    $sql_status = "SELECT id_utilizator, status, numar_accesari, ultima_activitate, nume_prenume, email FROM utilizatori WHERE telefon = ?";
    $stmt_status = $conn->prepare($sql_status);
    $stmt_status->bind_param("s", $telefon);
    $stmt_status->execute();
    $result_status = $stmt_status->get_result();
    $row_status = $result_status->fetch_assoc();

    if ($row_status) {
        $status_utilizator = $row_status['status'];
        $numar_accesari = $row_status['numar_accesari'];
        $ultima_activitate = $row_status['ultima_activitate']; // Data ultimei accesări
        $id_utilizator = $row_status['id_utilizator'];
        $nume_prenume = $row_status['nume_prenume'];
        $email = $row_status['email'];

        // Dacă utilizatorul este "vizitator", aplicăm restricțiile
        if ($status_utilizator == 'vizitator') {
            $limita_accesari = 3;
            $perioada_expirare = 7; // zile

            $data_curenta = new DateTime();
            $ultima_activitate_dt = new DateTime($ultima_activitate);

            // Calculăm diferența în zile
            $interval = $ultima_activitate_dt->diff($data_curenta);
            $diferenta_zile = $interval->days;

            // Dacă au trecut mai mult de 7 zile, resetăm numărul de accesări
            if ($diferenta_zile > $perioada_expirare) {
                $numar_accesari = 0;
            }

            // Verificăm dacă utilizatorul a depășit limita de accesări
            if ($numar_accesari >= $limita_accesari) {
                header('Content-Type: application/json');
                echo json_encode(array('error' => 'Ai depășit limita de 3 accesări în perioada de 7 zile.'));
                exit;
            } else {
                // Incrementăm numărul de accesări și actualizăm ultima activitate
                $numar_accesari++;
                $sql_update = "UPDATE utilizatori SET numar_accesari = ?, ultima_activitate = NOW() WHERE id_utilizator = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ii", $numar_accesari, $id_utilizator);
                $stmt_update->execute();

                // Informăm utilizatorul despre accesările rămase
                $accesari_ramase = $limita_accesari - $numar_accesari;

                // Returnăm datele utilizatorului, inclusiv numele și email-ul
                header('Content-Type: application/json');
                echo json_encode(array(
                    'success' => "Autentificare reușită! Mai aveți $accesari_ramase accesări rămase.",
                    'id_utilizator' => $id_utilizator,
                    'nume_prenume' => $nume_prenume,
                    'email' => $email,
                    // Alte date necesare...
                ));
                exit;
            }
        } else {
            // Utilizatorul nu este "vizitator", continuăm cu autentificarea normală

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

                        // Gestionăm sesiunea și cookie-urile
                        session_destroy();
                        session_id($row['id_cookie']);

                        // Setăm opțiunile pentru sesiune
                        $session_lifetime = 1800; // 30 minute
                        session_set_cookie_params($session_lifetime, '/', null, true, true);
                        ini_set('session.gc_maxlifetime', $session_lifetime);

                        // Setăm cookie-ul $id_cookie
                        $id_cookie = $row['id_cookie'];
                        setcookie('id_cookie', $id_cookie, time() + 60 * 60 * 24 * 365, "/"); // Cookie valabil 1 an
                        session_start();
                        $_SESSION['id_utilizator'] = $row['id_utilizator'];
                        $_SESSION['status'] = $row['status'];

                        // Actualizăm 'ultima_activitate' pentru utilizator
                        $sql_update_activity = "UPDATE utilizatori SET ultima_activitate = NOW() WHERE id_utilizator = ?";
                        $stmt_update_activity = $conn->prepare($sql_update_activity);
                        $stmt_update_activity->bind_param("i", $row['id_utilizator']);
                        $stmt_update_activity->execute();

                        header('Content-Type: application/json');
                        echo json_encode($row);
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(array('error' => 'Nu sunteți înscris în proiectul TID4K!'));
                    }
                }
            }
        }
    } else {
        // Dacă utilizatorul nu este găsit în baza de date
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Numărul de telefon nu este înregistrat.'));
    }
}

?>
