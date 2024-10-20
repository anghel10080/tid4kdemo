<?php
header('Content-Type: application/json');

require_once '../config.php';
require_once 'functii_si_constante.php';

session_start(); // Asigură-te că sesiunea este pornită pentru a accesa `$_SESSION['id_utilizator']`

// Preia datele din POST
$continut = $_POST['continut'];
$id_utilizator = $_SESSION['id_utilizator'];

// Funcție pentru a extrage data din textul anunțului
function extrageData($text) {
    // Codul funcției rămâne neschimbat
    $patterns = [
        '/(\d{1,2})\s+(ianuarie|februarie|martie|aprilie|mai|iunie|iulie|august|septembrie|octombrie|noiembrie|decembrie)\s+(\d{4})/i',
        '/(\d{1,2})\.(\d{1,2})\.(\d{4})/', // Format "21.04.2024"
        '/(\d{1,2})\s*(ianuarie|februarie|martie|aprilie|mai|iunie|iulie|august|septembrie|octombrie|noiembrie|decembrie)/i', // Format "21 mai"
        '/(\d{1,2})\.(\d{1,2})\.(\d{2})/' // Format "21.04.'24"
    ];

    $months = [
        'ianuarie' => '01', 'februarie' => '02', 'martie' => '03', 'aprilie' => '04', 'mai' => '05',
        'iunie' => '06', 'iulie' => '07', 'august' => '08', 'septembrie' => '09', 'octombrie' => '10',
        'noiembrie' => '11', 'decembrie' => '12'
    ];

    $now = new DateTime();
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            if (count($matches) == 4) {
                $day = $matches[1];
                $month = isset($months[strtolower($matches[2])]) ? $months[strtolower($matches[2])] : $matches[2];
                $year = $matches[3];
            } elseif (count($matches) == 3) {
                $day = $matches[1];
                $month = isset($months[strtolower($matches[2])]) ? $months[strtolower($matches[2])] : $matches[2];
                $year = $now->format('Y');
            } elseif (count($matches) == 4 && strlen($matches[3]) == 2) {
                $day = $matches[1];
                $month = $matches[2];
                $year = '20' . $matches[3];
            }
            return DateTime::createFromFormat('d-m-Y H:i:s', "$day-$month-$year 23:59:59");
        }
    }
    return false;
}

$nume_fisier = 'anunt';

// Setează extensia, tipul fișierului și thumbnail-ul
$extensie = 'html';
$tip_fisier = 'text';
$thumbnail = file_get_contents('document_generic.png');
$thumbnail = $conn->real_escape_string($thumbnail);

// **Nu mai modificăm `$continut`, ci îl folosim direct**
$continut_html = $conn->real_escape_string($continut);

// Extrage data din textul anunțului sau setează valabilitatea la 7 zile
$data_expirare = extrageData($continut);
if ($data_expirare === false) {
    $data_expirare = new DateTime();
    $data_expirare->modify('+7 days')->setTime(23, 59, 59);
}
$data_expirare_str = $data_expirare->format('Y-m-d H:i:s');

// Inserare în baza de date
$sql = "INSERT INTO informatii_anunturi (id_utilizator, nume_fisier, extensie, tip_fisier, continut, data_upload, thumbnail, data_expirare)
        VALUES ('$id_utilizator', '$nume_fisier', '$extensie', '$tip_fisier', '$continut_html', NOW(), '$thumbnail', '$data_expirare_str')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $sql . '<br>' . $conn->error]);
}

$conn->close();
?>
