<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';
require_once '../sesiuni.php';
require_once 'functii_si_constante.php';
  // Apelarea functiei pentru a umple variabilele de sesiune
  determina_variabile_utilizator($conn);

$status = $_SESSION['status'];

/*------------ de aici, acest segment de cod determina id_utilizator, status, grupa_clasa_copil, alisa grupa_clasa_copil ----------*/
if ($status == 'parinte' || $status == 'elev') {
    $sql = "SELECT u.id_utilizator, u.id_cookie, u.status, u.nume_prenume, u.ultima_activitate, c.grupa_clasa_copil
    FROM utilizatori u
    LEFT JOIN copii c ON u.id_utilizator = c.id_utilizator
    WHERE u.id_utilizator = ?";
    // Logica existentă pentru părinți
} else {
  $sql = "SELECT u.id_utilizator, u.id_cookie, u.status, u.nume_prenume, u.ultima_activitate, a.grupa_clasa_copil
FROM utilizatori u
LEFT JOIN asociere_multipla a ON u.id_utilizator = a.id_utilizator
WHERE status != 'parinte' AND u.id_utilizator = ?";
    // Logica pentru profesori
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utilizator);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

    // variabila $grupa_clasa_copil este preluata din functii_si_constante
    $grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
    $grupa_clasa_copil_curent = str_replace(' ', '_', $grupa_clasa_copil);
    $id_utilizator = $row['id_utilizator'];
    $id_cookie = $row['id_cookie'];
    $nume_prenume_curent = $row['nume_prenume'];
    $ultima_activitate_curent = $row['ultima_activitate'];

    // Calculul aliasului pentru tabela de prezență (daca este necesar)
    $tabelaPrezentaCurenta = 'prezenta_' . $grupa_clasa_copil_curent;
    $aliasTabela = 'aliasTabela';
    $alias = 'alias';

/*------------ pana aici, acest segment de cod determina id_utilizator, status, grupa_clasa_copil, alias grupa_clasa_copil ----------*/



$data = [];

if ($status != 'parinte' && $status != 'elev') {
    // Obține utilizatorii unici cu status de 'parinte' și numele copiilor lor
 $sql = "SELECT DISTINCT u.id_utilizator, c.nume_copil
            FROM copii c
            JOIN utilizatori u ON c.id_utilizator = u.id_utilizator
            WHERE c.grupa_clasa_copil = '$grupa_clasa_copil' AND u.status = 'parinte'";

    $result = $conn->query($sql);
    $unique_parents = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $unique_parents[] = $row;
        }
    }


    // Pentru fiecare utilizator unic (parinte), obține informațiile relevante și construiește calea avatarului
  foreach ($unique_parents as $parent) {

$sql = "
SELECT
  u.nume_prenume,
  u.ultima_activitate,
  u.telefon,
  u.email,
  u.temp_path,
  c.id_copil,
  $aliasTabela.confirmata_parinte,
  $aliasTabela.prezenta_stare,
  (CASE
    WHEN u.ultima_activitate IS NULL AND $aliasTabela.prezenta_stare = 'prezent' THEN true
    WHEN u.ultima_activitate IS NULL AND $aliasTabela.prezenta_stare = 'absent' THEN false
    WHEN $aliasTabela.prezenta_data > u.ultima_activitate AND $aliasTabela.prezenta_stare = 'prezent' THEN true
    WHEN $aliasTabela.prezenta_data > u.ultima_activitate AND $aliasTabela.prezenta_stare = 'absent' THEN false
    WHEN $aliasTabela.prezenta_data <= u.ultima_activitate THEN true
    ELSE false
  END) AS este_conectat,
  (CASE
    WHEN u.ultima_activitate IS NULL THEN false
    WHEN $aliasTabela.prezenta_data <= u.ultima_activitate THEN true
    ELSE false
  END) AS prezenta_determinata_de_parinte,
  $aliasTabela.confirmata_de,
  $aliasTabela.confirmata_la
FROM utilizatori u
JOIN copii c ON u.id_utilizator = c.id_utilizator
LEFT JOIN $tabelaPrezentaCurenta $aliasTabela ON $aliasTabela.id_copil = c.id_copil AND DATE($aliasTabela.prezenta_data) = CURDATE()
WHERE u.id_utilizator = ?
";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $parent['id_utilizator']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $nume_prenume = $row['nume_prenume'];
    $ultima_activitate = $row['ultima_activitate'];
    $este_conectat = $row['este_conectat'];
    $prezenta_determinata_de_parinte = $row['prezenta_determinata_de_parinte'];
    $id_copil = $row['id_copil'];
    $confirmata_parinte = $row['confirmata_parinte'];
    $prezenta_stare = $row['prezenta_stare'];

// echo "confirmata_de este ".$nume_prenume_curent." si confirmata_la este ".$ultima_activitate_curent; exit;

    //se verifica daca confirmata_de si confirmata_la nu au capatat deja valori corespunzatoare, caz in care nu mai primesc alte valori
   if (($row['confirmata_de'] === NULL || $row['confirmata_la'] === NULL) || ($prezenta_stare == 'absent') || ($este_conectat && $prezenta_determinata_de_parinte && $confirmata_parinte == 0)) {
         // Îndeplinirea condițiilor pentru a determina valorile `confirmata_de` și `confirmata_la`
    if ($este_conectat) {
        if ($prezenta_determinata_de_parinte) {
            $confirmata_de = $nume_prenume;
            $confirmata_la = $ultima_activitate;
            $confirmata_parinte = TRUE;
           } else {
            $confirmata_de = $nume_prenume_curent;
            $confirmata_la = $ultima_activitate_curent;
        }
    } else {
        $confirmata_de = $nume_prenume_curent;
        $confirmata_la = $ultima_activitate_curent;
    }

    // Prepararea interogării SQL pentru a actualiza valorile `confirmata_de` și `confirmata_la` în baza de date
// Actualizează întotdeauna `confirmata_parinte`
$sql_update_confirmata_parinte = "
    UPDATE $tabelaPrezentaCurenta
    SET
      confirmata_parinte = ?
    WHERE id_copil = ? AND DATE(prezenta_data) = CURDATE()
";

$stmt_update = mysqli_prepare($conn, $sql_update_confirmata_parinte);
mysqli_stmt_bind_param($stmt_update, "ii", $confirmata_parinte, $id_copil);
mysqli_stmt_execute($stmt_update);

// Verifică dacă `confirmata_de` și `confirmata_la` nu sunt nule
if ($confirmata_de !== NULL && $confirmata_la !== NULL) {
    // Actualizează `confirmata_de` și `confirmata_la`
  $sql_update_confirmata_de_la = "
    UPDATE $tabelaPrezentaCurenta
    SET
      confirmata_de = ?,
      confirmata_la = ?
    WHERE id_copil = ? AND DATE(prezenta_data) = CURDATE()
";


    $stmt_update = mysqli_prepare($conn, $sql_update_confirmata_de_la);
    mysqli_stmt_bind_param($stmt_update, "ssi", $confirmata_de, $confirmata_la, $id_copil);
    mysqli_stmt_execute($stmt_update);
}

}

    $relative_path_prefix = '/sesiuni/';
    $temp_path = str_replace('/home/tid4kdem/public_html/sesiuni/', $relative_path_prefix, $row['temp_path']);

    $data[] = [
        'copil' => [
            'id_copil' => $row['id_copil'],
            'nume_copil' => $parent['nume_copil'],
            'cale_avatar' => !empty($temp_path) ? $temp_path . 'avatar_utilizator/avatar_copil.png' : 'pages/avatar.png',
            'este_conectat' => $row['este_conectat'],
            'prezenta_determinata_de_parinte' => $row['prezenta_determinata_de_parinte'],
        ],
        'parinte' => [
            'nume_prenume' => $row['nume_prenume'],
            'telefon' => $row['telefon'],
            'email' => $row['email'],
            'cale_avatar' => !empty($temp_path) ? $temp_path . 'avatar_utilizator/avatar_utilizator.png' : 'pages/avatar.png',
        ]
    ];
}

//conditie pentru emiterea primului json
if (isset($_GET['format']) && $_GET['format'] == 'format_copii') {
     header('Content-Type: application/json');
    echo json_encode($data);
}

} elseif ($status == 'parinte' || $status == 'elev') {
$sql = "
    SELECT DISTINCT u.nume_prenume, u.email, u.temp_path, DATE(u.ultima_activitate) = CURDATE() AS este_conectat
    FROM utilizatori u
    JOIN asociere_multipla am ON u.id_utilizator = am.id_utilizator
    WHERE u.status = 'profesor' AND am.grupa_clasa_copil = '$grupa_clasa_copil'
";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    $relative_path_prefix = '/sesiuni/';
    while($row = mysqli_fetch_assoc($result)) {
        $temp_path = str_replace('/home/tid4kdem/public_html/sesiuni/', $relative_path_prefix, $row['temp_path']);

        $data[] = [
        'profesor' => [
            'nume_prenume' => $row['nume_prenume'],
            'email' => $row['email'],
            'cale_avatar' => !empty($temp_path) ? $temp_path . 'avatar_utilizator/avatar_utilizator.png' : 'pages/avatar.png',
            'este_conectat' => $row['este_conectat'],
        ]
        ];
    }

    //conditie pentru emiterea celui de al doilea json
if (isset($_GET['format']) && $_GET['format'] == 'format_profesori') {
     header('Content-Type: application/json');
    echo json_encode($data);
}


}

if ($status != 'parinte' && $status != 'elev') {
$sql = "
    SELECT u.nume_prenume, u.email, u.temp_path, DATE(u.ultima_activitate) = CURDATE() AS este_conectat
    FROM utilizatori u
    WHERE u.status = 'profesor'
";


    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    $relative_path_prefix = '/sesiuni/';
    while($row = mysqli_fetch_assoc($result)) {
        $temp_path = str_replace('/home/tid4kdem/public_html/sesiuni/', $relative_path_prefix, $row['temp_path']);

        $data[] = [
        'profesor' => [
            'nume_prenume' => $row['nume_prenume'],
            'email' => $row['email'],
            'cale_avatar' => !empty($temp_path) ? $temp_path . 'avatar_utilizator/avatar_utilizator.png' : 'pages/avatar.png',
            'este_conectat' => $row['este_conectat'],
        ]
        ];
    }

    //conditie pentru emiterea celui de al treilea json
if (isset($_GET['format']) && $_GET['format'] == 'format_cancelarie') {
     header('Content-Type: application/json');
    echo json_encode($data);
}


}



$conn->close();


?>
