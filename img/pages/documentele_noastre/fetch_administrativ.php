<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conectarea la baza de date si datele de sesiune
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
$alias = 'alias';

// Interogarea pentru selectarea personalului administrativ
$sql = "
SELECT
  u.nume_prenume,
  u.ultima_activitate,
  u.telefon,
  u.email,
  u.temp_path,
  (CASE
    WHEN u.ultima_activitate IS NULL THEN false
    WHEN u.ultima_activitate >= CURDATE() THEN true
    ELSE false
  END) AS este_conectat
FROM utilizatori u
WHERE u.status IN ('director', 'secretara', 'administrator', 'contabil')
";

$result = $conn->query($sql);
$personal_administrativ = [];

$relative_path_prefix = '/sesiuni/';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $temp_path = str_replace('/home/tid4kdem/public_html/sesiuni/', $relative_path_prefix, $row['temp_path']);
        $row['cale_avatar'] = !empty($temp_path) ? $temp_path . 'avatar_utilizator/avatar_utilizator.png' : 'pages/avatar.png';
        $personal_administrativ[] = $row;
    }
}

// Verificăm dacă scriptul este apelat din infodisplay.php
if (isset($_GET['source']) && $_GET['source'] == 'infodisplay') {
    // Interogare pentru a extrage cel mai recent fișier PDF
    $sql_administrativ = "SELECT $alias.id_info, $alias.nume_fisier, $alias.extensie, $alias.data_upload, u.temp_path
        FROM informatii_" . $grupa_clasa_copil_curent . " $alias
        JOIN utilizatori u ON $alias.id_utilizator = u.id_utilizator
        WHERE u.status IN ('director', 'administrator', 'secretara')
        ORDER BY $alias.data_upload DESC LIMIT 10";

    $result_administrativ = $conn->query($sql_administrativ);

    $administrativ_info = [];

    if ($result_administrativ->num_rows > 0) {
        while ($row_administrativ = $result_administrativ->fetch_assoc()) {
            $cale_infodisplay = '/' . str_replace('/home/tid4kdem/public_html/', '', $row_administrativ['temp_path']) . $row_administrativ['nume_fisier'];
            $administrativ_info[] = [
                'id_info' => $row_administrativ['id_info'],
                'nume_fisier' => $row_administrativ['nume_fisier'],
                'extensie' => $row_administrativ['extensie'],
                'data_upload' => $row_administrativ['data_upload'],
                'cale_infodisplay_afisat' => $cale_infodisplay
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($administrativ_info);
    exit;
}


// Verificăm dacă există un parametru GET numit "format"
if (isset($_GET['format']) && $_GET['format'] == 'format_administrativ') {
    $data = [];
    foreach ($personal_administrativ as $row) {
        $data[] = [
            'profesor' => [
                'nume_prenume' => $row['nume_prenume'],
                'email' => $row['email'],
                'cale_avatar' => $row['cale_avatar'],
                'este_conectat' => $row['este_conectat'] ? true : false
            ]
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode($personal_administrativ);
    exit;
}

$conn->close();
?>
