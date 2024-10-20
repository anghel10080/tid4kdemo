<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../config.php';
require_once '../../../sesiuni.php';

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

$sql = "SELECT id_utilizator, temp_path
        FROM utilizatori
        WHERE id_utilizator = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cookie);
$stmt->execute();
$stmt->bind_result($id_utilizator, $temp_path);
$stmt->fetch();
$stmt->close();

$temp_path = str_replace('/home/tid4kdem/public_html/sesiuni/', '/home/tid4kdem/public_html/sesiuni/', $temp_path);
$dir_path = $temp_path . 'Thumbnailuri';

if (!file_exists($dir_path)) {
    mkdir($dir_path, 0777, true);
}

$sql = "SELECT id_info, nume_fisier, thumbnail
        FROM informatii_" . $grupa_clasa_copil_curent . "
        WHERE id_utilizator = ? AND tip_fisier = 'application/pdf'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utilizator);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id_info, $nume_fisier, $thumbnail);

function create_thumbnail($pdf_file) {
    $imagick = new \Imagick();
    $imagick->readImage($pdf_file . '[0]'); // citește prima pagină a PDF-ului
    $imagick->setImageFormat('png'); // setează formatul de ieșire ca PNG
    $imagick->setImageCompressionQuality(90); // setează calitatea imaginii
    $imagick->resizeImage(200, 200, Imagick::FILTER_LANCZOS, 1); // redimensionează imaginea la 200x200 pixeli
    return $imagick->getImageBlob(); // obține imaginea ca string binar
}

while ($stmt->fetch()) {
    if(is_null($thumbnail)){
        $pdf_file = $temp_path . $nume_fisier;

        // Verifică dacă fișierul PDF există în directorul temporar
        if (!file_exists($pdf_file)) {
            // Dacă nu există, extrage fișierul PDF din baza de date și salvează-l în directorul temporar
            $sql_extract = "SELECT continut FROM informatii_" . $grupa_clasa_copil_curent . " WHERE id_info = ?";
            $stmt_extract = $conn->prepare($sql_extract);
            $stmt_extract->bind_param("i", $id_info);
            $stmt_extract->execute();
            $stmt_extract->bind_result($continut);
            $stmt_extract->fetch();

            // Convertește conținutul fișierului din baza de date în binar
            // $continut = base64_decode($continut);

            // Scrie fișierul în directorul temporar
            file_put_contents($pdf_file, $continut);
            $stmt_extract->close();
        }

        $thumbnail = create_thumbnail($pdf_file);
        $file_path = $dir_path . '/' . pathinfo($nume_fisier, PATHINFO_FILENAME) . '.png';
        file_put_contents($file_path, $thumbnail);

        // Actualizează coloana thumbnail în baza de date pentru acest document
        $stmt_update = $conn->prepare("UPDATE informatii_" . $grupa_clasa_copil_curent . " SET thumbnail = ? WHERE nume_fisier = ?");
        $stmt_update->bind_param('ss', $thumbnail, $nume_fisier);
        $stmt_update->execute();

        // echo 'Fișierul a fost salvat la: ' . $file_path . '<br>';
    }
}

$stmt->free_result();
$stmt->close();
$conn->close();
?>
