<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Include funcțiile necesare
require_once 'functii_si_constante.php';  // Presupunem că funcțiile sunt salvate în acest fișier

// Apelarea funcției
$data = GrupeClaseExistente($conn);

// Afișarea rezultatelor
echo "<h1>Opțiuni Grupe și Clase</h1>";
echo "<select>";
foreach ($data['options'] as $option) {
    echo "<option value='" . htmlspecialchars($option) . "'>" . htmlspecialchars($option) . "</option>";
}
echo "</select>";

echo "<h2>Listă Grupe Disponibile</h2>";
echo "<ul>";
foreach ($data['lista_grupe_disponibile'] as $grupa) {
    echo "<li>" . htmlspecialchars($grupa) . "</li>";
}
echo "</ul>";

echo "<h2>Listă Clase Disponibile</h2>";
echo "<ul>";
foreach ($data['lista_clase_disponibile'] as $clasa) {
    echo "<li>" . htmlspecialchars($clasa) . "</li>";
}
echo "</ul>";

// Închiderea conexiunii la baza de date
mysqli_close($conn);
?>
