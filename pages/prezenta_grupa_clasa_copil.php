<?php
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    require_once '../config.php';
    require_once '../sesiuni.php';
    require_once 'functii_si_constante.php';

  // Apelarea functiei pentru a umple variabilele de sesiune: id_utilizator, status, grupa_clasa_copil
  determina_variabile_utilizator($conn);


// Daca utilizatorul nu este autentificat, redirecționează către pagina de start
// if (!isset($_SESSION['id_utilizator']) || $_SESSION['rol'] !== $_SESSION['grupa_clasa_copil']) {
//     header('Location: /index.php');
//     exit();
// }

// Preiau array-ul de copii trimis prin POST
$copii = $_POST['copii'];

$all_updated_successfully = true;

// Inițializează contoarele pentru copiii prezenți și absenți
$prezenti = 0;
$absenti = 0;

// Parcurgem fiecare copil
foreach ($copii as $copil) {
    $id_copil = $copil['id_copil'];
    $nume_copil = $copil['nume_copil'];
    $prezenta_stare = $copil['prezenta_stare'];

    // Încercăm să găsim id_copil în baza de date
    $sql = "SELECT id_copil FROM copii WHERE nume_copil = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nume_copil);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Dacă nu putem găsi copilul în baza de date, trecem la următorul
    if (!$row) {
        echo "Nu s-a putut găsi copilul cu numele '" . $nume_copil . "' în baza de date.";
        continue;
    }

    $id_copil = $row['id_copil'];

    // Incrementăm numărătoarea corespunzătoare
    if ($prezenta_stare === 'prezent') {
        $prezenti++;
    } else {
        $absenti++;
    }

    // Încercăm să găsim id_copil în baza de date pentru ziua curentă
    $check_sql = "SELECT id_prezenta FROM prezenta_" . $_SESSION['grupa_clasa_copil_'] . " WHERE id_copil = ? AND DATE(prezenta_data) = CURDATE()";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $id_copil);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $check_row = mysqli_fetch_assoc($check_result);

    // Dacă există deja o înregistrare, o actualizăm
    if ($check_row) {
        $sql = "UPDATE prezenta_" . $_SESSION['grupa_clasa_copil_'] . " SET nume_copil = ?, prezenta_stare = ?, prezenta_data = NOW() WHERE id_copil = ? AND DATE(prezenta_data) = CURDATE()";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $nume_copil, $prezenta_stare, $id_copil);
    } else {
        // Dacă nu există nicio înregistrare, inserăm una nouă
        $sql = "INSERT INTO prezenta_" . $_SESSION['grupa_clasa_copil_'] . " (id_copil, nume_copil, prezenta_stare, prezenta_data) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $id_copil, $nume_copil, $prezenta_stare);
    }

    $updated = mysqli_stmt_execute($stmt);

    // Verifică dacă actualizarea a reușit
    if (!$updated || mysqli_stmt_affected_rows($stmt) < 0) {
        $all_updated_successfully = false;
        echo " ID-ul copilului este : ".$id_copil." / "; echo "starea prezentei este : ".$prezenta_stare. " / ";
        echo "A apărut o eroare la actualizarea stării de prezență a copilului cu ID-ul " . $id_copil . ".";
    }
}

if ($all_updated_successfully) {
    // Returnează numărul de copii prezenți și absenți ca JSON
    echo json_encode(array(
        'prezenti' => $prezenti,
        'absenti' => $absenti,
    ));
}
?>
