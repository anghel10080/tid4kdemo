<?php
require_once 'config.php';

// Înlocuiți TRUNCATE cu DELETE și resetare AUTO_INCREMENT
$sql = "DELETE FROM copii";
$conn->query($sql);
$sql = "ALTER TABLE copii AUTO_INCREMENT = 1";
$conn->query($sql);

$sql = "DELETE FROM sesiuni_utilizatori";
$conn->query($sql);
$sql = "ALTER TABLE sesiuni_utilizatori AUTO_INCREMENT = 1";
$conn->query($sql);

$sql = "DELETE FROM utilizatori";
$conn->query($sql);
$sql = "ALTER TABLE utilizatori AUTO_INCREMENT = 1";
$conn->query($sql);

echo "Tabelele utilizatori, sesiuni_utilizatori și copii au fost resetate.";
?>
