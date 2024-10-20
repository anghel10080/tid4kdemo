<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Accesarea directorului părinte și adăugarea căii la config.php
require_once(dirname(__DIR__, 2) . '/config.php'); // Acesta va defini ROOT_PATH

require_once(ROOT_PATH . 'pages/functii_si_constante.php');

  // Apelarea functiei pentru a umple variabilele de sesiune, inclusa in functii_si_constante.php
  determina_variabile_utilizator($conn);

if(isset($row['id_utilizator'])) {
    // Procesarea înregistrării aici
    $id_utilizator = $row['id_utilizator'];
    $status = $row['status'];
} else {
    // Sari peste această înregistrare
    return; // Sari peste restul fișierului dacă id_utilizator este NULL
}

//obtinere nume copil corelat cu utilizatorul curent
function get_nume_copil($id_utilizator) {
    global $conn;
    $query = "SELECT nume_copil FROM copii WHERE id_utilizator = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_utilizator);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (isset($row['nume_copil'])) {
        return $row['nume_copil'];
    } else {
        // Returnează un șir gol dacă nu există un nume de copil corespunzător
        return "";
    }
}


//generare id unic de 7caractere pentru concatenare cu noul nume al fisierului
    function generate_unique_id() {
        return substr(md5(uniqid(rand(), true)), 0, 7);
    }

//crearea noului nume al fisierului in functie de perioada zilei si anului
    function create_file_name($nume_copil, $id_utilizator) {
        $nume_copil = get_nume_copil($id_utilizator);

        $current_date = new DateTime();
        $current_hour = intval($current_date->format('H'));
        $current_month = intval($current_date->format('m'));

        $perioada_zilei = ($current_hour >= 0 && $current_hour < 12) ? 'dimineata' : 'dupa-amiaza';

        $sezon = '';
        if ($current_month >= 3 && $current_month <= 5) {
            $sezon = 'Primavara';
        } elseif ($current_month >= 6 && $current_month <= 8) {
            $sezon = 'Vara';
        } elseif ($current_month >= 9 && $current_month <= 11) {
            $sezon = 'Toamna';
        } else {
            $sezon = 'Iarna';
        }

        $unique_id = generate_unique_id();

        //se elmina din sir valorile de NULL care intorc erori in apelare
        require_once 'evita_NULL.php';

        $returnString = $sezon . ' ' . $perioada_zilei . ' cu ' . $nume_copil . '_' . $unique_id;

        $returnString = sanitizeNullValuesInString($returnString);

        return $returnString;

    }
?>
