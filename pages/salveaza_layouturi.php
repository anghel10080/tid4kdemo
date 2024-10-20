<?php
// Inclusivitate a fișierului config.php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $layouts = $data['layouts'] ?? '';
    $currentLayoutIndex = $data['currentLayoutIndex'] ?? 0;
    $id_utilizator = $data['id_utilizator'] ?? 0;

    // Validare date
    if ($id_utilizator == 0) {
        echo json_encode(['success' => false, 'error' => 'ID utilizator invalid.']);
        exit;
    }

    // Verificăm dacă există deja o înregistrare pentru acest utilizator
    $query = "SELECT * FROM infodisplay_layout WHERE id_utilizator = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_utilizator);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizăm înregistrarea existentă
        $query = "UPDATE infodisplay_layout SET layouts = ?, currentLayoutIndex = ?, timp_ultima_modificare = NOW() WHERE id_utilizator = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sii', $layouts, $currentLayoutIndex, $id_utilizator);
    } else {
        // Inserăm o nouă înregistrare
        $query = "INSERT INTO infodisplay_layout (id_utilizator, layouts, currentLayoutIndex, timp_ultima_modificare) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isi', $id_utilizator, $layouts, $currentLayoutIndex);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Eroare la executarea interogării.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Metodă invalidă.']);
}
?>
