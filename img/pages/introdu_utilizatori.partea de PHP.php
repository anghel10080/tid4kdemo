<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$_SESSION['source'] = 'introdu_utilizatori';
require_once '../config.php';
require_once 'functii_si_constante.php';




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_copil_curent = NULL;
    $nume_prenume = $_POST['nume_prenume'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $nume_copil = $_POST['nume_copil'];
    $varsta_copil = $_POST['varsta_copil'];
    $grupa_clasa_copil = $_POST['grupa_clasa_copil'];
    $avatar = $_FILES['avatar'];
    //si datelele trimise prin semiformularul pentru Elev
    $telefon_elev = $_POST['telefon_elev'];
    $nume_prenume_elev = $_POST['nume_prenume_elev'];
    $email_elev = $_POST['email_elev'];
    $status_elev = $_POST['status_elev'];

     ?> <!--aici se trimite $status catre codul javascript care va popula formularul cu datele din baza de date-->
    <script type="text/javascript">
    var status = "<?php echo isset($status) ? $status : 'default'; ?>";
    </script>
    <?php

      $id_cookie = '';
    // Verifică dacă numărul de telefon există deja în baza de date
    $sql_check = "SELECT * FROM utilizatori WHERE telefon = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("s", $telefon);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_cookie = $row['id_cookie'];
            $id_utilizator = $row['id_utilizator'];
            $id_utilizator_curent = $id_utilizator;
            $status = $row['status'];

             // Actualizăm datele utilizatorului
    $sql_update = "UPDATE utilizatori SET nume_prenume = ?, telefon = ?, email = ?, status = ? WHERE id_utilizator = ?";
    if ($stmt_update = $conn->prepare($sql_update)) {
        $stmt_update->bind_param("ssssi", $nume_prenume, $telefon, $email, $status, $id_utilizator);
        $stmt_update->execute();
        $stmt_update->close();
    }

    // Verificăm dacă copilul este deja înregistrat pentru acest utilizator
if ($status === 'parinte') {
    $id_copil = NULL;  // Inițializează id_copil cu NULL
    $sql_check_child = "SELECT * FROM copii WHERE id_utilizator = ?";
    if ($stmt_check_child = $conn->prepare($sql_check_child)) {
        $stmt_check_child->bind_param("i", $id_utilizator);
        $stmt_check_child->execute();
        $result_child = $stmt_check_child->get_result();

        if ($result_child->num_rows > 0) {
            $row = $result_child->fetch_assoc();
            $id_copil_curent = $row['id_copil'];
            // Actualizăm datele copilului
            $sql_update_child = "UPDATE copii SET nume_copil = ?, varsta_copil = ?, grupa_clasa_copil = ? WHERE id_utilizator = ?";
            if ($stmt_update_child = $conn->prepare($sql_update_child)) {
                $stmt_update_child->bind_param("sisi", $nume_copil, $varsta_copil, $grupa_clasa_copil, $id_utilizator);
                $stmt_update_child->execute();
                $stmt_update_child->close();
            }
        }
        $stmt_check_child->close();
    }
}

//aici este nevoie de asociere_multipla si se apeleaza functia gestioneaza_asocierea_multipla()
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);

   // Actualizare avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $avatar_tmp_name = $_FILES['avatar']['tmp_name'];

    // Calea către directorul unde este deja salvat avatarul
    $cale_avatar = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/avatar_utilizator/";


    // Determinăm numele avatarului în funcție de $status
    $nume_avatar = $status === "parinte" ? 'avatar_copil.png' : 'avatar_utilizator.png';


    // Numele fișierului avatar va rămâne constant
    $avatar_path = $cale_avatar . $nume_avatar;

    // Mutăm fișierul încărcat în locația existentă, suprascriind fișierul vechi
    if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
        echo "Avatar actualizat cu succes.";
    } else {
        echo "Eroare la încărcarea avatarului.";
    }
}

echo "Utilizatorul ".$nume_prenume." a fost updatat !";

//daca au fost introduse datele elevului aici sunt inregistrate in baza de date
if (isset($status_elev) && $status_elev == 'elev') {
    // Verifică dacă telefonul_elev există în baza de date (adica elevul exista deja inregistrat)
    $sql_check_elev = "SELECT id_utilizator FROM utilizatori WHERE telefon = ?";
    $stmt_check_elev = $conn->prepare($sql_check_elev);
    $stmt_check_elev->bind_param("s", $telefon_elev);
    $stmt_check_elev->execute();
    $stmt_check_elev->bind_result($id_utilizator_elev);
    $stmt_check_elev->fetch();
    $stmt_check_elev->close();

    if (isset($id_utilizator_elev)) {
        // Actualizează datele pentru elev
        $sql_update_elev = "UPDATE utilizatori SET nume_prenume = ?, telefon = ?, email = ?, status = ? WHERE id_utilizator = ?";
        $stmt_update_elev = $conn->prepare($sql_update_elev);
        $stmt_update_elev->bind_param("ssssi", $nume_prenume_elev, $telefon_elev, $email_elev, $status_elev, $id_utilizator_elev);
        $stmt_update_elev->execute();
        $stmt_update_elev->close();
    } else {
        // id_cookie nu există, generăm unul nou
            $id_cookie_elev = uniqid();
            setcookie('id_cookie_elev', $id_cookie_elev, time() + 60 * 60 * 24 * 365, "/"); // Setează cookie-ul pentru 1 an

         // Creare temp_path
        $temp_path = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/"; //pastrez aceiasi valoare $temp_path ca si a parintelui

        // Creare director dacă nu există
        if (!is_dir($temp_path)) {
            mkdir($temp_path, 0777, true);
        }
        // Inserarea noilor date pentru un elev nou
        $sql_insert_elev = "INSERT INTO utilizatori (nume_prenume, telefon, email, id_cookie, status, temp_path) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert_elev = $conn->prepare($sql_insert_elev);
        $stmt_insert_elev->bind_param("ssssss", $nume_prenume_elev, $telefon_elev, $email_elev, $id_cookie_elev, $status_elev, $temp_path);
        $stmt_insert_elev->execute();
        $id_utilizator_elev_nou = $stmt_insert_elev->insert_id;  // Obține ID-ul generat pentru elevul nou inserat
        $stmt_insert_elev->close();

    // Verifică dacă $_POST['grupa_clasa_copil'] există și nu este gol
    if (isset($_POST['grupa_clasa_copil']) && !empty($_POST['grupa_clasa_copil'])) {
    $grupa_clasa_copil = $_POST['grupa_clasa_copil'];

    // Interogarea pentru inserarea unei noi înregistrări pentru elev, în tabela asociere_multipla
    $sql_insert_asociere_elev = "INSERT INTO asociere_multipla (id_utilizator, id_copil, grupa_clasa_copil) VALUES (?, ?, ?)";
    $stmt_insert_asociere_elev = $conn->prepare($sql_insert_asociere_elev);
    $stmt_insert_asociere_elev->bind_param("iis", $id_utilizator_elev_nou, $id_copil_curent, $grupa_clasa_copil);
    $stmt_insert_asociere_elev->execute();
    $stmt_insert_asociere_elev->close();
}
    }
}
            }

     else { //de aici incepe codul de introducere utilizatori noi
    // Dacă id_cookie nu există, generăm unul nou
    if (empty($id_cookie)) {
        $id_cookie = uniqid();
        setcookie('id_cookie', $id_cookie, time() + 60 * 60 * 24 * 365, "/"); // Setează cookie-ul pentru 1 an
    }


    // Creați o interogare SQL pentru a introduce datele în tabela utilizatori
    $sql = "INSERT INTO utilizatori (nume_prenume, telefon, email, status, id_cookie) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nume_prenume, $telefon, $email, $status, $id_cookie);

    if ($stmt->execute()) {
        // Obțineți id-ul utilizatorului introdus
        $id_utilizator = $conn->insert_id;
        $id_utilizator_curent = $id_utilizator;

        // Creare temp_path
       $temp_path = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/";

// Creare director dacă nu există
if (!is_dir($temp_path)) {
    mkdir($temp_path, 0777, true);
}

    // Adăugare temp_path în utilizatori
    $sql_temp_path = "UPDATE utilizatori SET temp_path = ? WHERE id_utilizator = ?";
    $stmt_temp_path = $conn->prepare($sql_temp_path);
    $stmt_temp_path->bind_param("si", $temp_path, $id_utilizator);


        if ($stmt_temp_path->execute()) {
            // Creați calea către avatarul utilizatorului
            $cale_avatar = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/avatar_utilizator/";
 // Determinăm numele avatarului în funcție de $status
    $nume_avatar = $status === "parinte" ? 'avatar_copil.png' : 'avatar_utilizator.png';

            // Creați un director , pentru avatar, dacă nu există deja
            if (!is_dir($cale_avatar)) {
                $oldmask = umask(0); // Salvăm masca curentă și setăm umask la 0
                if (mkdir($cale_avatar, 0777, true)) { // Creăm directorul cu permisiunile dorite
                    umask($oldmask); // Revenim la vechea mască
                } else {
                    echo "Eroare la crearea directorului: " . $cale_avatar;
                    umask($oldmask); // Revenim la vechea mască chiar și în caz de eroare
                    exit;
                }
            }

 if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $avatar_tmp_name = $_FILES['avatar']['tmp_name'];

    // reluam calea către avatarul utilizatorului
    $cale_avatar = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/avatar_utilizator/";

    // Numele fișierului avatar va rămâne constant
    $avatar_path = $cale_avatar . $nume_avatar;

    // Mutăm fișierul încărcat în locația destinată
    if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
        echo "Avatar încărcat cu succes.";
    } else {
        echo "Eroare la încărcarea avatarului.";
        // exit;
    }



    // Mutăm fișierul încărcat în locația existentă, suprascriind fișierul vechi
    if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
        echo "Avatar actualizat cu succes.";
    } else {
        echo "Eroare la încărcarea avatarului.";
    }
}

echo "Utilizatorul ".$nume_prenume." a fost updatat !";


            // Introduceți datele în tabela copii
            if ($status == 'parinte') {
    $sql = "INSERT INTO copii (id_utilizator, nume_copil, varsta_copil, grupa_clasa_copil, avatar_utilizator) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiss", $id_utilizator, $nume_copil, $varsta_copil, $grupa_clasa_copil, $cale_avatar);

    if ($stmt->execute()) {
        $id_copil_curent = $conn->insert_id;
        // Redirecționați utilizatorul către pagina de succes dacă datele au fost introduse cu succes
        echo "Datele au fost introduse complet: parinte si copil";
        sleep(3);
        //algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);
        exit;
    }
}
 else {
     //algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);
            }
        } else {
            echo "Eroare la adăugarea temp_path: " . $conn->error;
        }
        $stmt_temp_path->close();
        $stmt->close();
    } else {
        echo "Eroare la introducerea datelor utilizatorului: " . $conn->error;
    }
    }$stmt_check->close();

}

//algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);

}
} else {
    // Dacă nu s-a încărcat niciun avatar, folosim unul predefinit
    $content = file_get_contents("/home/tid4kdem/public_html/pages/avatar_copil.png");
    $result = file_put_contents($avatar_path . 'avatar_copil.png', $content);
    if ($result === false) {
        echo "Eroare la copierea fișierului avatar_copil.png.";
        exit;
    }
}
            if ($result === false) {
                echo "Eroare la copierea fișierului avatar_copil.png.";
                exit;
            }

            // Introduceți datele în tabela copii
            if ($status == 'parinte') {
    $sql = "INSERT INTO copii (id_utilizator, nume_copil, varsta_copil, grupa_clasa_copil, avatar_utilizator) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiss", $id_utilizator, $nume_copil, $varsta_copil, $grupa_clasa_copil, $cale_avatar);

    if ($stmt->execute()) {
        $id_copil_curent = $conn->insert_id;
        // Redirecționați utilizatorul către pagina de succes dacă datele au fost introduse cu succes
        echo "Datele au fost introduse complet: parinte si copil";
        sleep(3);
        //algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);
        exit;
    }
}
 else {
     //algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);
            }
        } else {
            echo "Eroare la adăugarea temp_path: " . $conn->error;
        }
        $stmt_temp_path->close();
        $stmt->close();
    } else {
        echo "Eroare la introducerea datelor utilizatorului: " . $conn->error;
    }
    }$stmt_check->close();

}

//algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);

}


//aceasta parte gestioneaza cererile GET pentru listarea utilizatorilor
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list_users'])) {
    $response = [];

    // Selectăm doar câmpurile necesare pentru profesori
    $sql_profesori = "SELECT id_utilizator, nume_prenume, email, telefon FROM utilizatori WHERE status = 'profesor'";
    if ($result_profesori = $conn->query($sql_profesori)) {
        $profesori = $result_profesori->fetch_all(MYSQLI_ASSOC);
        $response['profesori'] = $profesori;
    }

    // Selectăm doar câmpurile necesare pentru părinți și facem JOIN cu tabelul copii pentru a obține informații despre copii
    $sql_parinti = "SELECT u.id_utilizator, u.nume_prenume, u.email, u.telefon, c.nume_copil, c.varsta_copil, c.grupa_clasa_copil
                    FROM utilizatori u
                    JOIN copii c ON u.id_utilizator = c.id_utilizator
                    WHERE u.status = 'parinte'";
    if ($result_parinti = $conn->query($sql_parinti)) {
        $parinti = $result_parinti->fetch_all(MYSQLI_ASSOC);
        $response['parinti'] = $parinti;
    }

    echo json_encode($response);
    exit;
}
?>
