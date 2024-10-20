<?php
require_once 'config.php';  // Ajustează calea dacă fișierul config.php este în alt director

// Obținerea listei de tabele din baza de date
$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

// Parcurgerea fiecărei tabele și a fiecărei coloane pentru a înlocui stringul '/home/tid4kdem/public_html'
foreach ($tables as $table) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` WHERE Type LIKE '%char%' OR Type LIKE '%text%'");
    while ($column = $result->fetch_assoc()) {
        $colName = $column['Field'];
        $updateQuery = "UPDATE `$table` SET `$colName` = REPLACE(`$colName`, '/home/tid4kdem/public_html', '/home/tid4kdem/public_html')";
        if (!$conn->query($updateQuery)) {
            echo "Eroare la actualizarea tabelului $table, coloana $colName: " . $conn->error . "\n";
        } else {
            echo "Actualizare reușită pentru tabelul $table, coloana $colName\n";
        }
    }
}
// Închiderea conexiunii
$conn->close();
?>
