<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conectarea la baza de date și datele de sesiune
require_once(dirname(__DIR__, 2) . '/config.php');
require_once(dirname(__DIR__, 2) . '/sesiuni.php');

require_once(ROOT_PATH . 'pages/functii_si_constante.php');
determina_variabile_utilizator($conn);

$id_cookie = $_SESSION['id_cookie'];
$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];

// Construirea interogării SQL în funcție de statusul utilizatorului curent
$finalResults = [];
$files_utilizator = [];
$files_ceilalti = [];

// Configurația pentru statusuri opuse
$oppositeStatusConfig = [
    'parinte' => ['profesor', 'director', 'administrator', 'secretara', 'elev'],
     'elev' => ['profesor', 'director', 'administrator', 'secretara', 'parinte'],
    'profesor' => ['parinte', 'director', 'administrator', 'secretara', 'elev'],
    'director' => ['parinte', 'profesor', 'administrator', 'secretara', 'elev'],
    'administrator' => ['parinte', 'profesor', 'director', 'secretara', 'elev'],
    'secretara' => ['parinte', 'profesor', 'director', 'administrator', 'elev'],
];

// Determină statusurile opuse pentru statusul curent
$opposite_status = $oppositeStatusConfig[$status] ?? [];

// Convertirea array-ului în string pentru interogare
$opposite_status_str = implode("','", $opposite_status);

// Interogarea pentru statusul curent și pentru statusurile opuse
$sql = "SELECT DISTINCT alias.id_info, alias.nume_fisier, alias.extensie, alias.data_upload, alias.id_utilizator, u.nume_prenume, u.status
        FROM informatii_" . $grupa_clasa_copil_curent . " alias
        JOIN utilizatori u ON alias.id_utilizator = u.id_utilizator
        LEFT JOIN copii c ON alias.id_utilizator = c.id_utilizator
        LEFT JOIN asociere_multipla am ON alias.id_utilizator = am.id_utilizator
        WHERE (
                (u.id_utilizator = ? AND u.status = ?)
                OR (u.status IN ('$opposite_status_str'))
              )
        AND (c.grupa_clasa_copil = ? OR am.grupa_clasa_copil = ?)
        ORDER BY alias.data_upload DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isss", $id_utilizator, $status, $grupa_clasa_copil, $grupa_clasa_copil);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);



if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        if ($row['id_utilizator'] == $id_utilizator) {
            $files_utilizator[] = $row;
        } else {
            $files_ceilalti[] = $row;
        }
    }
}

$output = [
    'files_utilizator' => $files_utilizator,
    'files_ceilalti' => $files_ceilalti,
    'status' => $status,
];

if (isset($_GET['source']) && $_GET['source'] == 'infodisplay') {
    $ordinePredefinita = ['grupa_mica', 'grupa_mijlocie', 'grupa_mare', 'clasa_pregatitoare', 'clasa_I', 'clasa_II', 'clasa_III', 'clasa_IV', 'clasa_V', 'clasa_VI', 'clasa_VII', 'clasa_VIII', 'clasa_IX', 'clasa_X', 'clasa_XI', 'clasa_XII'];

    $sql_grupa_clasa = "SELECT DISTINCT grupa_clasa_copil FROM copii";
    $stmt_grupa_clasa = $conn->prepare($sql_grupa_clasa);
    $stmt_grupa_clasa->execute();
    $result_grupa_clasa = $stmt_grupa_clasa->get_result();

   $grupeClase = [];
if ($result_grupa_clasa->num_rows > 0) {
    while ($row = $result_grupa_clasa->fetch_assoc()) {
        // Transformați spațiile în underscore-uri, dar păstrați literele mari acolo unde este necesar
        $grupaFormatata = str_replace(' ', '_', $row['grupa_clasa_copil']);
        $grupeClase[] = $grupaFormatata;
    }
}

    $grupeClaseOrdonate = array_intersect($ordinePredefinita, $grupeClase);

    $optiuneSelectata = isset($_GET['optiuneSelectata']) ? $_GET['optiuneSelectata'] : null;

$fisiere_info = [];
    if (in_array($optiuneSelectata, $grupeClaseOrdonate)) {
        $tabela_informatii = "informatii_" . $optiuneSelectata;

        $sql_profesor_fisiere = "SELECT DISTINCT i.id_info, i.nume_fisier, i.extensie, i.data_upload, u.nume_prenume, u.status, u.temp_path FROM {$tabela_informatii} i JOIN utilizatori u ON i.id_utilizator = u.id_utilizator WHERE (u.status IN ('profesor', 'parinte', 'elev')) AND i.id_info IS NOT NULL AND i.nume_fisier IS NOT NULL AND i.extensie IS NOT NULL AND i.data_upload IS NOT NULL AND u.nume_prenume IS NOT NULL AND u.temp_path IS NOT NULL ORDER BY i.data_upload DESC LIMIT 10";

        $stmt_profesor_fisiere = $conn->prepare($sql_profesor_fisiere);
        $stmt_profesor_fisiere->execute();
        $result_profesor_fisiere = $stmt_profesor_fisiere->get_result();

        while ($row = $result_profesor_fisiere->fetch_assoc()) {
            $cale_infodisplay = '/' . str_replace('/home/tid4kdem/public_html/', '', $row['temp_path']) . $row['nume_fisier'];
            $fisiere_info[] = [
                'id_info' => $row['id_info'],
                'nume_fisier' => $row['nume_fisier'],
                'extensie' => $row['extensie'],
                'data_upload' => $row['data_upload'],
                'cale_infodisplay_afisat' => $cale_infodisplay
            ];
        }
    } else {
        foreach ($grupeClaseOrdonate as $grupa_clasa) {
            $grupa_clasa = str_replace(' ', '_', $grupa_clasa);
            $tabela_informatii = "informatii_" . $grupa_clasa;

            $sql_profesor_fisiere = "SELECT DISTINCT i.id_info, i.nume_fisier, i.extensie, i.data_upload, u.nume_prenume, u.status, u.temp_path FROM {$tabela_informatii} i JOIN utilizatori u ON i.id_utilizator = u.id_utilizator WHERE (u.status IN ('profesor', 'director', 'administrator', 'secretara', 'parinte', 'elev')) AND i.id_info IS NOT NULL AND i.nume_fisier IS NOT NULL AND i.extensie IS NOT NULL AND i.data_upload IS NOT NULL AND u.nume_prenume IS NOT NULL AND u.temp_path IS NOT NULL ORDER BY i.data_upload DESC LIMIT 10";
            $stmt_profesor_fisiere = $conn->prepare($sql_profesor_fisiere);
            $stmt_profesor_fisiere->execute();
            $result_profesor_fisiere = $stmt_profesor_fisiere->get_result();

            if ($result_profesor_fisiere->num_rows > 0) {
                while ($row = $result_profesor_fisiere->fetch_assoc()) {
                    $cale_infodisplay = '/' . str_replace('/home/tid4kdem/public_html/', '', $row['temp_path']) . $row['nume_fisier'];
                    $fisiere_info[] = [
                        'id_info' => $row['id_info'],
                        'nume_fisier' => $row['nume_fisier'],
                        'extensie' => $row['extensie'],
                        'data_upload' => $row['data_upload'],
                        'cale_infodisplay_afisat' => $cale_infodisplay
                    ];
                }
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($fisiere_info);
}

 else {
    // Afișare JSON-ul original
    header('Content-Type: application/json');
    echo json_encode($output);
}

$conn->close();
?>
