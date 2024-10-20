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

// Funcție pentru a crea recursiv directoare și a seta permisiunile corecte
function create_directory_recursive($path) {
    if (!file_exists($path)) {
        if (!mkdir($path, 0777, true)) {
            error_log("Failed to create directory: $path");
            return false;
        } else {
            error_log("Directory created successfully: $path");
            if (!chmod($path, 0777)) {
                error_log("Failed to set permissions for directory: $path");
                return false;
            }
        }
    } else {
        error_log("Directory already exists: $path");
        if (!is_writable($path)) {
            if (!chmod($path, 0777)) {
                error_log("Failed to set permissions for directory: $path");
                return false;
            }
        }
    }
    return true;
}

if (!create_directory_recursive($dir_path)) {
    die("Failed to create directory: $dir_path");  // Oprește execuția dacă nu poate crea directorul
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
    $imagick = new \Imagick();
    $imagick->readImage($pdf_file . '[0]'); // citește prima pagină a PDF-ului
    $imagick->setImageFormat('png'); // setează formatul de ieșire ca PNG
    $imagick->setImageCompressionQuality(90); // setează calitatea imaginii
    $imagick->resizeImage(200, 200, Imagick::FILTER_LANCZOS, 1); // redimensionează imaginea la 200x200 pixeli
    return $imagick->getImageBlob(); // obține imaginea ca string binar
}

$generic_image_path = __DIR__ . '/document_generic.png';

while ($stmt->fetch()) {
    // Generăm calea unde ar trebui să se găsească thumbnail-ul
    $thumbnail_path = $dir_path . '/' . pathinfo($nume_fisier, PATHINFO_FILENAME) . '.png';

    // Verificăm dacă thumbnail-ul există deja în baza de date și pe disc
    if (is_null($thumbnail) || !file_exists($thumbnail_path)) {
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
        if (file_put_contents($pdf_file, $continut) === false) {
            error_log("Failed to write PDF file: $pdf_file");
            $pdf_file = $generic_image_path;  // Setează calea imaginii generice
        }

        // Generăm thumbnail-ul
        if ($pdf_file != $generic_image_path) {
            try {
                $thumbnail = create_thumbnail($pdf_file);
            } catch (ImagickException $e) {
                error_log("Failed to create thumbnail for: $pdf_file. Using generic image.");
                $thumbnail = file_get_contents($generic_image_path);
            }
        } else {
            $thumbnail = file_get_contents($generic_image_path);
        }

        file_put_contents($thumbnail_path, $thumbnail);

        // Actualizăm coloana thumbnail în baza de date pentru acest document
        $stmt_update = $conn->prepare("UPDATE informatii_$grupa_clasa_copil_curent SET thumbnail = ? WHERE nume_fisier = ?");
        $stmt_update->bind_param('ss', $thumbnail, $nume_fisier);
        $stmt_update->execute();
    }
}

$stmt->free_result();
$stmt->close();
?>