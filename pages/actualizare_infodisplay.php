<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_utilizator = $_POST['id_utilizator'];
    $nume_prenume = $_POST['nume_prenume'];
    $status = $_POST['status'];
    $numeUnitateScolara = $_POST['numeUnitateScolara'];
    $cadran_simulatTitlu1Rand2 = $_POST['cadran_simulatTitlu1Rand2'];
    $cadran_simulatTitlu1Rand3 = $_POST['cadran_simulatTitlu1Rand3'];
    $cadran_simulatTitlu2Rand3 = $_POST['cadran_simulatTitlu2Rand3'];
    $cadran_simulatTitlu3Rand3 = $_POST['cadran_simulatTitlu3Rand3'];

    // Obține ultimul timp de modificare pentru infodisplay privind informatia afisata, selectata utilizator
$sql = "SELECT timp_ultima_modificare FROM infodisplay ORDER BY timp_ultima_modificare DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

//setare fus orar corect
date_default_timezone_set('Europe/Bucharest');

if ($row = $result->fetch_assoc()) {
 // Setăm fusul orar pentru timpCurent și timpUltimaModificare
$fusOrar = new DateTimeZone("Europe/Bucharest"); // Fusul orar corect
$timpCurent = new DateTime("now", $fusOrar);
$timpUltimaModificare = new DateTime($row['timp_ultima_modificare'], $fusOrar);
$difSecunde = $timpCurent->getTimestamp() - $timpUltimaModificare->getTimestamp();

    // Verificăm că diferența este pozitivă
    if ($difSecunde < 0) {
        $timpScurs = 0;
    } else {
        // Aplicăm regula de trei simplă pentru a converti secundele în ore
        $timpScurs = $difSecunde / 3600;

        // Rotunjim la 2 zecimale pentru precizie
        $timpScurs = round($timpScurs, 2);
    }
} else {
    $timpScurs = 0;
}

    // Interogarea pentru inserarea noii înregistrări
    $sql = "INSERT INTO infodisplay (id_utilizator, nume_prenume, status, numeUnitateScolara, cadran_simulatTitlu1Rand2, cadran_simulatTitlu1Rand3, cadran_simulatTitlu2Rand3, cadran_simulatTitlu3Rand3, timp_scurs)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssd", $id_utilizator, $nume_prenume, $status, $numeUnitateScolara, $cadran_simulatTitlu1Rand2, $cadran_simulatTitlu1Rand3, $cadran_simulatTitlu2Rand3, $cadran_simulatTitlu3Rand3, $timpScurs);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Inregistrare realizată cu succes";
    } else {
        echo "Nu s-a efectuat nicio inregistrare.";
    }

    $stmt->close();
    $conn->close();
}
?>
