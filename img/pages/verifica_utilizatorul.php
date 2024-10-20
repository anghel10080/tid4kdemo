<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$_SESSION['source'] = 'introdu_utilizatori';
require_once '../config.php';
require_once 'functii_si_constante.php';
// Aceasta parte gestioneaza cererile GET pentru verificarea numarului de telefon si trimiterea rezultatelor inapoi in functia de verificareTelefon pentru popularea cu date a formularului de introducere
//aceasta parte gestioneaza cererile GET pentru listarea utilizatorilor
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list_users'])) {
    $response = [];

    // Interogare pentru profesori, directori și administratori
    $sql_profesori = "SELECT DISTINCT u.id_utilizator, u.nume_prenume, u.email, u.telefon, u.temp_path, a.grupa_clasa_copil
                      FROM utilizatori u
                      LEFT JOIN asociere_multipla a ON u.id_utilizator = a.id_utilizator
                      WHERE u.status IN ('profesor', 'director', 'administrator')
                      ORDER BY u.id_utilizator, a.grupa_clasa_copil";
    if ($result_profesori = $conn->query($sql_profesori)) {
        while ($row = $result_profesori->fetch_assoc()) {
            // Verifică dacă există avatarul folosind calea originală
            $original_path = $row['temp_path'];
            $avatar_path = $original_path .'avatar_utilizator/'. 'avatar_utilizator.png';
            $row['avatar'] = file_exists($avatar_path) ? 'da' : 'nu';
            $row['temp_path'] = str_replace('/home/tid4kdem/public_html/sesiuni', '', $row['temp_path']);

            if (!isset($response['profesori'][$row['id_utilizator']])) {
                $response['profesori'][$row['id_utilizator']] = $row;
                $response['profesori'][$row['id_utilizator']]['grupe'] = [];
            }
            if (!in_array($row['grupa_clasa_copil'], $response['profesori'][$row['id_utilizator']]['grupe'])) {
                $response['profesori'][$row['id_utilizator']]['grupe'][] = $row['grupa_clasa_copil'];
            }
        }
        $response['profesori'] = array_values($response['profesori']);
    }

    // Interogare pentru părinți
    $sql_parinti = "SELECT u.id_utilizator, u.nume_prenume, u.email, u.telefon, u.temp_path, c.nume_copil, c.varsta_copil, c.grupa_clasa_copil
                    FROM utilizatori u
                    JOIN copii c ON u.id_utilizator = c.id_utilizator
                    WHERE u.status = 'parinte'";
    if ($result_parinti = $conn->query($sql_parinti)) {
        while ($row = $result_parinti->fetch_assoc()) {
            // Verifică dacă există avatarul copilului folosind calea originală
            $original_path = $row['temp_path'];
            $avatar_path = $original_path .'avatar_utilizator/'.'avatar_copil.png';
            $row['avatar'] = file_exists($avatar_path) ? 'da' : 'nu';
            $row['temp_path'] = str_replace('/home/tid4kdem/public_html/sesiuni', '', $row['temp_path']);

            $response['parinti'][] = $row;
        }
    }

    echo json_encode($response);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_phone'])) {
    $telefon = $_GET['check_phone'];
    $sql_check = "SELECT * FROM utilizatori WHERE telefon = ?";

    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("s", $telefon);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($row['status'] !== 'parinte') {
                $sql_check_association = "SELECT grupa_clasa_copil FROM asociere_multipla WHERE id_utilizator = ? LIMIT 1";

                if ($stmt_check_association = $conn->prepare($sql_check_association)) {
                    $stmt_check_association->bind_param("i", $row['id_utilizator']);
                    $stmt_check_association->execute();
                    $result_association = $stmt_check_association->get_result();

                    if ($result_association->num_rows > 0) {
                        $row_association = $result_association->fetch_assoc();
                        $row['grupa_clasa_copil'] = $row_association['grupa_clasa_copil'];
                    }

                    $stmt_check_association->close();
                }

                echo json_encode($row);

            } else {
                $sql_child = "SELECT * FROM copii WHERE id_utilizator = ?";

                if ($stmt_child = $conn->prepare($sql_child)) {
                    $stmt_child->bind_param("i", $row['id_utilizator']);
                    $stmt_child->execute();
                    $result_child = $stmt_child->get_result();

                    if ($result_child->num_rows > 0) {
                        $row_child = $result_child->fetch_assoc();
                        $row = array_merge($row, $row_child);
                    }

                    $stmt_child->close();
                }

                echo json_encode($row);
            }
        } else {
            echo json_encode(["Numarul de telefon nu este inregistrat."]);
        }

        $stmt_check->close();
    }
    exit; // Important să încheiem execuția aici pentru a nu procesa și partea de POST
}
?>
