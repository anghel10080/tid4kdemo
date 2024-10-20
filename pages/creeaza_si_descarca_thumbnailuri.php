<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';
require_once '../sesiuni.php';
require_once 'functii_si_constante.php';
determina_variabile_utilizator($conn);

$id_cookie = $_SESSION['id_cookie'];
$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];
$alias = 'alias';

$sql = "SELECT id_utilizator, temp_path FROM utilizatori WHERE id_cookie = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_cookie);
$stmt->execute();
$stmt->bind_result($id_utilizator, $temp_path);
$stmt->fetch();
$stmt->close();

$dir_path = $temp_path . '/Thumbnailuri';

// Salvăm valoarea curentă a umask
$old_umask = umask(0);

if (!file_exists($dir_path)) {
    if (!mkdir($dir_path, 0777, true)) {
        // Dacă mkdir eșuează, folosește un thumbnail generic
        $generic_thumbnail = '/path/to/documente_generic.png';
        $dir_path = null;
    }
}

// Resetăm umask la valoarea anterioară
umask($old_umask);

$sql = "SELECT id_info, nume_fisier, thumbnail
        FROM informatii_$grupa_clasa_copil_curent
        WHERE id_utilizator = ? AND tip_fisier = 'application/pdf'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utilizator);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id_info, $nume_fisier, $thumbnail);

function create_thumbnail($pdf_file) {
    try {
        $imagick = new \Imagick();
        $imagick->readImage($pdf_file . '[0]'); // citește prima pagină a PDF-ului
        $imagick->setImageFormat('png'); // setează formatul de ieșire ca PNG
        $imagick->setImageCompressionQuality(90); // setează calitatea imaginii
        $imagick->resizeImage(200, 200, Imagick::FILTER_LANCZOS, 1); // redimensionează imaginea la 200x200 pixeli
        return $imagick->getImageBlob(); // obține imaginea ca string binar
    } catch (Exception $e) {
        // Dacă apare o eroare la generarea thumbnail-ului, folosește un thumbnail generic
        return file_get_contents('/path/to/documente_generic.png');
    }
}

while ($stmt->fetch()) {
    // Generăm calea unde ar trebui să se găsească thumbnail-ul
    $thumbnail_path = $dir_path ? $dir_path . '/' . pathinfo($nume_fisier, PATHINFO_FILENAME) . '.png' : null;

    // Verificăm dacă thumbnail-ul există deja în baza de date și pe disc
    if (is_null($thumbnail) || ($thumbnail_path && !file_exists($thumbnail_path))) {
        $pdf_file = $temp_path . $nume_fisier;

        // Extragem fișierul PDF din baza de date și îl salvăm în directorul temporar
        $sql_extract = "SELECT continut FROM informatii_$grupa_clasa_copil_curent WHERE id_info = ?";
        $stmt_extract = $conn->prepare($sql_extract);
        $stmt_extract->bind_param("i", $id_info);
        $stmt_extract->execute();
        $stmt_extract->bind_result($continut);
        $stmt_extract->fetch();
        $stmt_extract->close();

        // Scriem fișierul în directorul temporar
        file_put_contents($pdf_file, $continut);

        // Generăm thumbnail-ul
        $thumbnail = create_thumbnail($pdf_file);
        if ($thumbnail_path) {
            file_put_contents($thumbnail_path, $thumbnail);
        }

        // Actualizăm coloana thumbnail în baza de date pentru acest document
        $stmt_update = $conn->prepare("UPDATE informatii_$grupa_clasa_copil_curent SET thumbnail = ? WHERE nume_fisier = ?");
        $stmt_update->bind_param('ss', $thumbnail, $nume_fisier);
        $stmt_update->execute();
    }
}

$stmt->free_result();
$stmt->close();
?>
