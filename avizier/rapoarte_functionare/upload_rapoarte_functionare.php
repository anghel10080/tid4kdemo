<?php
if ($_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) {
    $upload_dir = '/home/tid4kdem/public_html/avizier/rapoarte_functionare/';
    $upload_file = $upload_dir . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_file)) {
        echo "File successfully uploaded.";
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "No file uploaded or upload error.";
}
?>
