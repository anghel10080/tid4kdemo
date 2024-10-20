<?php
require_once '../config.php';  // Include fișierul de configurare

$grupeOrder = ['grupa mica', 'grupa mijlocie', 'grupa mare'];
$claseOrder = ['clasa I', 'clasa II', 'clasa III', 'clasa IV', 'clasa V', 'clasa VI', 'clasa VII', 'clasa VIII', 'clasa IX', 'clasa X', 'clasa XI', 'clasa XII'];

$sql = "SHOW TABLES WHERE Tables_in_$database LIKE 'informatii_%'";
$result = mysqli_query($conn, $sql);

$grupe = [];
$clase = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_row($result)) {
        $tableName = $row[0];
        $name = substr($tableName, 10); // Remove 'informatii_'
        $formattedName = str_replace('_', ' ', $name);
        if (strpos($name, 'grupa') !== false) {
            $grupe[] = $formattedName;
        } elseif (strpos($name, 'clasa') !== false) {
            $clase[] = $formattedName;
        }
    }
}

function sortWithSuffix($a, $b, $order) {
    $indexA = $indexB = 1000;
    foreach ($order as $key => $value) {
        if (strpos($a, $value) === 0) {
            $indexA = $key;
            break;
        }
        if (strpos($b, $value) === 0) {
            $indexB = $key;
            break;
        }
    }
    return $indexA <=> $indexB;
}

usort($grupe, function($a, $b) use ($grupeOrder) {
    return sortWithSuffix($a, $b, $grupeOrder);
});

usort($clase, function($a, $b) use ($claseOrder) {
    return sortWithSuffix($a, $b, $claseOrder);
});

$options = array_merge($grupe, $clase);

foreach ($options as $option) {
    echo "<option value='" . trim(htmlspecialchars($option)) . "'>" . trim(htmlspecialchars($option)) . "</option>";
}

mysqli_close($conn);
?>
