<?php

function replaceInFiles($dir, $search, $replace) {
    // Scanează recursiv directorul pentru toate fișierele PHP
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($files as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'html'])) { // Adaugă aici și alte tipuri de fișiere dacă este necesar
            // Citește conținutul fișierului
            $content = file_get_contents($file->getPathname());
            
            // Înlocuiește textul dorit
            $newContent = str_replace($search, $replace, $content);

            // Verifică dacă s-a făcut vreo modificare înainte de a rescrie fișierul
            if ($content !== $newContent) {
                file_put_contents($file->getPathname(), $newContent);
                echo "Modified: " . $file->getPathname() . "<br />";
            }
        }
    }
}

// Setează calea directorului root al aplicației tale
$rootPath = '/home/tid4kdem/public_html';

// Calea veche care trebuie înlocuită
$oldPath = '/home/tid4kdem/public_html';

// Noua cale care va înlocui cea veche
$newPath = '/home/tid4kdem/public_html';

// Apelarea funcției
replaceInFiles($rootPath, $oldPath, $newPath);

echo "Done.";

?>
