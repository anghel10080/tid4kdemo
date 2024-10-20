<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $src = $_POST['src'];

    // Ajustarea căii pentru a naviga corect în structura de directoare
    $adjusted_src = '../../' . $src;

    // Debugging: înregistrarea căii ajustate
    error_log('Calea fișierului ajustată: ' . $adjusted_src);

    if (file_exists($adjusted_src)) {
        // Debugging: înregistrarea existenței fișierului
        error_log('Fișierul există și va fi descărcat: ' . $adjusted_src);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($adjusted_src) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($adjusted_src));
        flush(); // Flush system output buffer
        readfile($adjusted_src);
        exit;
    } else {
        // Debugging: înregistrarea lipsei fișierului
        error_log('Fișierul nu există: ' . $adjusted_src);
    }
}
?>
