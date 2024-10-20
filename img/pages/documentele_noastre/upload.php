<!DOCTYPE html>
<html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
?>

<head>
  <title>TID4K - <?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="style.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="/favicon.ico">

</head>

<?php
if (isset($_FILES["fileToUpload"])) { // Verificăm dacă formularul a fost trimis
    $nume_fisier = basename($_FILES["fileToUpload"]["name"]);
    $extensie = strtolower(pathinfo($nume_fisier, PATHINFO_EXTENSION));
    $tip_fisier = $_FILES["fileToUpload"]["type"];
    $continut = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);


    // Setare  directorul temporar asociat utilizatorului
    $temp_file_path = $_SESSION["temp_path"] . $nume_fisier;

    if (in_array($extensie, ['pdf', 'jpg', 'jpeg', 'png', 'mp4']) && in_array($tip_fisier, ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'video/mp4'])) { // Verificăm dacă extensia fișierului este acceptată
        $sql = "SELECT * FROM informatii_" . $grupa_clasa_copil_curent . " WHERE nume_fisier = ? AND tip_fisier = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nume_fisier, $tip_fisier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) { // Verificăm dacă fișierul există deja în baza de date
            echo "Fișierul există deja în baza de date.";
        } else {
            $sql = "INSERT INTO informatii_" . $grupa_clasa_copil_curent . " (id_utilizator, nume_fisier, extensie, tip_fisier, continut) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $id_utilizator = $_SESSION['id_utilizator'];
            $stmt->bind_param("issss", $id_utilizator, $nume_fisier, $extensie, $tip_fisier, $continut);



        if ($stmt->execute()) { // Verificăm dacă inserarea în baza de date a avut succes
    http_response_code(200);
    echo '
        <body>
        <div class="centered-container">
            <h2 id="success-message" style="text-align: center; font-size: 2rem;">Fișierul a fost încărcat cu succes!</h2>
        </div>
        </body>
        ';

    // Încercăm să salvăm conținutul fișierului în directorul temporar folosind file_put_contents
    file_put_contents($temp_file_path, $continut);

    // Verificăm dacă fișierul a fost scris cu succes
    if (!file_exists($temp_file_path)) {
        // Dacă file_put_contents a eșuat, încercăm move_uploaded_file
        if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $temp_file_path)) {
            // Dacă și move_uploaded_file a eșuat, raportăm eroarea
            echo "Eroare la mutarea fișierului " . $nume_fisier . ". ";
        }
    }
} else {
                http_response_code(500);
                echo "Eroare la încărcarea fișierului " . $nume_fisier . ". ";
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        echo "Tipul de fișier nu este suportat. Doar fișierele PDF, JPG, JPEG, PNG și MP4 sunt acceptate.";
    }
    $conn->close();

    //se intoarce in activitati.php dupa 1 secunda
  echo '<script>
setTimeout(function() {
    window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";
}, 2000);
</script>';
}
?>
</html>
