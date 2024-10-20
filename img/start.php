<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'sesiuni.php';

    // Inițializăm $status dacă nu este deja setat în sesiune
if (!isset($_SESSION['status'])) {
    $sql = "SELECT status FROM utilizatori WHERE id_utilizator = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id_utilizator']);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();
    $_SESSION['status'] = $status;
} else {
    $status = $_SESSION['status'];
}

// Verificăm dacă utilizatorul a trimis date prin formularul de pre-autorizare
if(isset($_POST['submit']) && $_POST['submit'] == 1) {

    if(isset($_POST['nume_prenume'], $_POST['telefon'], $_POST['email'], $_POST['nume_copil'], $_POST['varsta_copil'])) {

        $nume_prenume = $_POST['nume_prenume'] ?? null;
        $telefon = $_POST['telefon'] ?? null;
        $email = $_POST['email'] ?? null;
        $nume_copil = $_POST['nume_copil'] ?? null;
        $varsta_copil = $_POST['varsta_copil'] ?? null;
        $grupa_clasa_copil = $_POST['grupa_clasa_copil'] ?? null;
        $id_utilizator = $_POST['id_utilizator'] ?? null;
        $_SESSION['id_utilizator'] = $id_utilizator;

    // Verificăm dacă oricare dintre variabile este null și redirecționăm dacă este necesar
    if (is_null($nume_prenume) || is_null($telefon) || is_null($email) || is_null($nume_copil) || is_null($varsta_copil) || is_null($grupa_clasa_copil) || is_null($id_utilizator)) {
        header("Location: /sesiuni.php");
        exit;
    }

        //avem nevoie de temp_path in cazul in care utilizatorul s-a logat cu numarul de telefon
$temp_path = '';
if (isset($_COOKIE['id_cookie'])) {
    $id_cookie = $_COOKIE['id_cookie'];
    $sql = "SELECT id_utilizator, temp_path FROM utilizatori WHERE id_cookie = ? LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $id_cookie);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['temp_path'] = $row['temp_path']; // salvăm valoarea în sesiune
            }
        }
        $stmt->close();
    }
}

//setam rolul utilizatorului curent astfel incat acesta sa poata accesa exclusiv paginile asociate grupe_clasei corespunzatoare si nu altceva
if ($status === 'parinte') {
    $sql = "SELECT grupa_clasa_copil FROM copii WHERE id_utilizator = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $id_utilizator);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $grupe_clase_copil = array();
            while ($row = $result->fetch_assoc()) {
                $grupe_clase_copil[] = $row['grupa_clasa_copil'];
            }
            $grupe_clase_copil_unicate = array_values(array_unique($grupe_clase_copil));
            $_SESSION['grupe_clase'] = $grupe_clase_copil_unicate;  // Aici folosesc același nume de sesiune ca în cazul non-parinte
        } else {
            echo "Eroare la executarea interogării pentru părinte.";
        }
        $stmt->close();
    } else {
        echo "Eroare la pregătirea interogării pentru părinte.";
    }
} else {
    $grupe_clase = array();
    $sql = "SELECT grupa_clasa_copil FROM asociere_multipla WHERE id_utilizator = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $_SESSION['id_utilizator']);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $grupe_clase[] = $row['grupa_clasa_copil'];
            }
            $grupe_clase_unicate = array_values(array_unique($grupe_clase));
            $_SESSION['grupe_clase'] = $grupe_clase_unicate;
        } else {
            echo "Eroare la executarea interogării pentru non-părinte.";
        }
        $stmt->close();
    } else {
        echo "Eroare la pregătirea interogării pentru non-părinte.";
    }
}

$numar_grupe_clase_utilizator = count($_SESSION['grupe_clase']);

if ($numar_grupe_clase_utilizator > 1) {
    if (isset($_SESSION['index_grupa_clasa_curenta'])) {
        if ($_SESSION['index_grupa_clasa_curenta'] >= $numar_grupe_clase_utilizator) {
            $_SESSION['index_grupa_clasa_curenta'] = 0; // Resetează la primul element dacă depășește
        }
    } else {
        $_SESSION['index_grupa_clasa_curenta'] = 0; // pentru test, poti modifica manual
    }

    // Dacă indexul nu este valid, resetează la 0
    if (!isset($_SESSION['grupe_clase'][$_SESSION['index_grupa_clasa_curenta']])) {
        $_SESSION['index_grupa_clasa_curenta'] = 0;
    }

    $_SESSION['rol'] = $_SESSION['grupe_clase'][$_SESSION['index_grupa_clasa_curenta']];
    $grupa_clasa_copil = $_SESSION['rol'];

    //inseram valoarea $index_grupa_clasa_curenta si $numar_grupe_clase_utilizator_clase_utilizator in tabela asociere_multipla
$numar_grupe_clase_utilizator_clase_utilizator = count($_SESSION['grupe_clase']);

$sql = "UPDATE asociere_multipla SET index_grupa_clasa_curenta = ?, numar_grupe_clase_utilizator = ? WHERE id_utilizator = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("iii", $_SESSION['index_grupa_clasa_curenta'], $numar_grupe_clase_utilizator_clase_utilizator, $_SESSION['id_utilizator']);
    if (!$stmt->execute()) {
        echo "Eroare la actualizarea indexului și a numărului de grupe.";
    }
    $stmt->close();
} else {
    echo "Eroare la pregătirea interogării pentru actualizarea indexului și a numărului de grupe.";
}
}

//verificam informatia din campurile formularului intors din pre_autorizare
if($status === 'parinte') {
          if(!empty($nume_prenume) && !empty($telefon) && !empty($email) && !empty($nume_copil) && !empty($varsta_copil) && !empty($grupa_clasa_copil)) {

// Verificam daca exista diferente intre datele introduse de utilizator si cele din baza de date
$sql = "SELECT nume_prenume, telefon, email, nume_copil, varsta_copil, grupa_clasa_copil FROM utilizatori JOIN copii ON utilizatori.id_utilizator = copii.id_utilizator WHERE utilizatori.id_utilizator = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_utilizator']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if($row['nume_prenume'] !== $nume_prenume || $row['telefon'] !== $telefon || $row['email'] !== $email || $row['nume_copil'] !== $nume_copil || $row['varsta_copil'] !== $varsta_copil || $row['grupa_clasa_copil'] !== $grupa_clasa_copil) {
    // Exista diferente, facem update la datele utilizatorului si ale copilului
    $sql = "UPDATE utilizatori SET nume_prenume = ?, telefon = ?, email = ? WHERE id_utilizator = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nume_prenume, $telefon, $email, $_SESSION['id_utilizator']);
    $stmt->execute();

    $sql = "UPDATE copii SET nume_copil = ?, varsta_copil = ?, grupa_clasa_copil = ? WHERE id_utilizator = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nume_copil, $varsta_copil, $grupa_clasa_copil, $_SESSION['id_utilizator']);
    $stmt->execute();
}
}
} else {
    if (!empty($nume_prenume) && !empty($telefon) && !empty($email) && !empty($grupa_clasa_copil)) {
        // cod pentru cazul cand status nu este parinte
        // (nume_copil si varsta_copil nu sunt luate in considerare)

        $sql = "SELECT nume_prenume, telefon, email, grupa_clasa_copil FROM utilizatori JOIN asociere_multipla ON utilizatori.id_utilizator = asociere_multipla.id_utilizator WHERE utilizatori.id_utilizator = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['id_utilizator']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['nume_prenume'] !== $nume_prenume || $row['telefon'] !== $telefon || $row['email'] !== $email || $row['grupa_clasa_copil'] !== $grupa_clasa_copil) {
            // facem update la datele utilizatorului

            $grupa_clasa_copil = $_SESSION['rol'];
            $sql = "UPDATE utilizatori SET nume_prenume = ?, telefon = ?, email = ? WHERE id_utilizator = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nume_prenume, $telefon, $email, $_SESSION['id_utilizator']);
            $stmt->execute();

            // și pentru asociere_multipla
            // $sql = "UPDATE asociere_multipla SET grupa_clasa_copil = ? WHERE id_utilizator = ?";
            // $stmt = $conn->prepare($sql);
            // $stmt->bind_param("si", $grupa_clasa_copil, $_SESSION['id_utilizator']);
            // $stmt->execute();
        }
    }
}

    // Setăm variabila $_SESSION["loggedin"] ca true
    $_SESSION["loggedin"] = true;
  

    // Redirecționăm utilizatorul către pagina "welcome.php"
    header("location: welcome.php");
    exit;


    }
}

/// Verificăm dacă utilizatorul este autentificat
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    // Utilizatorul este autentificat, îl redirecționăm către pagina "welcome.php"
    header("location: welcome.php");
    exit;
} else {
    // Dacă utilizatorul nu este autentificat, îl redirecționăm către pagina "pre_autorizare.php"
    header("location: /pages/pre_autorizare.php");
    exit;
}
?>
