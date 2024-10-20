<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once(dirname(__DIR__, 2) . '/config.php');
require_once(ROOT_PATH . 'pages/functii_si_constante.php');

$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil_'];
$id_utilizator = $_SESSION['id_utilizator'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Citirea datelor trimise în format JSON
    $data = json_decode(file_get_contents('php://input'), true);
    $src = $data['src'];

    // Obținerea numelui fișierului
    if (isset($_SESSION['id_utilizator']) && isset($_SESSION['grupa_clasa_copil_'])) {
        $id_utilizator = $_SESSION['id_utilizator'];
        $grupa_clasa_copil = $_SESSION['grupa_clasa_copil_'];
        $nume_fisier = basename($src);

        // Verificarea dacă fișierul aparține utilizatorului curent
        $stmt = $conn->prepare("SELECT COUNT(*) FROM informatii_$grupa_clasa_copil WHERE id_utilizator = ? AND nume_fisier = ?");
        $stmt->bind_param("is", $id_utilizator, $nume_fisier);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Ștergerea fișierului
            $stmt = $conn->prepare("DELETE FROM informatii_$grupa_clasa_copil WHERE id_utilizator = ? AND nume_fisier = ?");
            $stmt->bind_param("is", $id_utilizator, $nume_fisier);

            if ($stmt->execute()) {
                // Înregistrarea ștergerii din baza de date
                echo json_encode(['success' => true, 'message' => 'Fișierul a fost șters cu succes.']);
            } else {
                // Eroare la ștergerea din baza de date
                echo json_encode(['success' => false, 'message' => 'Eroare la ștergerea fișierului din baza de date.']);
            }

            $stmt->close();
        } else {
            // Fișierul nu aparține utilizatorului curent
            echo json_encode(['success' => false, 'message' => 'Nu puteți șterge ce nu vă aparține!']);
        }
    }
}
?>
