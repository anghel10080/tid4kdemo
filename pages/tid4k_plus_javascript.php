<?php
@unlink('debug.log'); // Aceasta va șterge fișierul debug.log dacă există

if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    $filePath = __DIR__ . '/../avizier/tid4k.html';
    $htmlContent = file_get_contents($filePath);

    // Elimină toate scripturile JavaScript din conținutul HTML
    $htmlContent = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $htmlContent);

    // Elimină liniile goale sau liniile care conțin doar spații albe
    $htmlContent = preg_replace('/^\s*[\r\n]/m', '', $htmlContent);

    // Salvează conținutul modificat înapoi în tid4k.html
    file_put_contents($filePath, $htmlContent);
}


require_once '../config.php';//credentialele bazei de date

$filePath = __DIR__ . '/../avizier/tid4k.html';
$htmlContent = [];
$htmlContent = file_get_contents($filePath);

// Definește codul JavaScript pentru PDF.js
$javascriptContentForPDF = "\n<script src='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js'></script>\n" .
     "<script>\n" .
     "    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';\n" .
     "</script>\n";

// Adaugă codul JavaScript pentru PDF.js în <head>
$htmlContent = str_replace('</head>', $javascriptContentForPDF . '</head>', $htmlContent);
// // Resalvează fișierul modificat
// file_put_contents($filePath, $htmlContent);
/*
$htmlContent = [];
$htmlContent = file_get_contents($filePath);*/

$cuvinteCheie = ['grupa mica', 'grupa mijlocie', 'grupa mare', 'clasa pregatitoare', 'clasa i', 'clasa ii', 'clasa iii', 'clasa iv', 'clasa v', 'clasa vi', 'clasa vii', 'clasa viii', 'clasa ix', 'clasa x', 'clasa xi', 'clasa xii', 'activitati', 'administrative', 'doar imagini', 'doar documente', 'ministerul educatiei', 'inspectorat', 'meniul saptamanii'];
$rezultateCautare = array_fill_keys($cuvinteCheie, ['gasit' => false, 'idChenar' => '']);

// Convertim cuvintele cheie la forma cu underscore pentru pattern
$cuvinteCheiePattern = array_map(function($cuvant) {
    return str_replace(' ', '\s+', preg_quote($cuvant, '/')); // Escape special characters and replace spaces with '\s+'
}, $cuvinteCheie);

$pattern = '/<div class="cadran_simulatfont(?:Mare|Mic)\s+cadran_simulat_select">\s*([^<]*?(' . implode('|', $cuvinteCheiePattern) . ')[^<]*?)\s*<\/div>\s*<div id="([^"]+)"/i';
preg_match_all($pattern, $htmlContent, $matches, PREG_SET_ORDER);

foreach ($matches as $match) {
    // Convertim cuvântul găsit la forma inițială (fără underscore) pentru a verifica în array-ul rezultateCautare
    $cuvantGasit = str_replace('_', ' ', strtolower($match[2]));
    if (array_key_exists($cuvantGasit, $rezultateCautare)) {
        $rezultateCautare[$cuvantGasit]['gasit'] = true;
        $rezultateCautare[$cuvantGasit]['idChenar'] = $match[3];
    }
}

// După ce ai executat codul anterior și ai obținut $rezultateCautareJS
$rezultateCautare = json_decode($rezultateCautareJS, true);

$grupaGasita = false; // Inițializează variabila pentru a verifica dacă grupa a fost găsită
$clasaGasita = false;
$continutGrupa = [];
$continutClasa = [];
$continutActivitati = [];
$continutAdministrative = [];
$continutDocumente = [];
$continutImagini = [];
$continutMinister = [];
$continutInspectorat = [];
$continutMeniul = [];

$loculGrupaGasit = [];
$loculClasaGasit = [];
$loculActivitatiGasit = [];
$loculAdministrativeGasit = [];
$loculDocumenteGasit = [];
$loculImaginiGasit = [];
$loculMinisterGasit = [];
$loculInspectoratGasit = [];
$loculMeniulGasit = [];

$javascriptGrupe = '';
$javascriptClase = '';
$javascriptActivitati = '';
$javascriptAdministrative = '';
$javascriptImagini = '';
$javascriptDocumente = '';
$javascriptMinister = '';
$javascriptInspectorat = '';
$javascriptMeniul = '';


$categorii = [
    'grupa_mica' => [
        'cuvantCheie' => 'grupa mica',
    ],
    'grupa_mijlocie' => [
        'cuvantCheie' => 'grupa mijlocie',
    ],
    'grupa_mare' => [
        'cuvantCheie' => 'grupa mare',
    ],
    'clasa_XII' => [
        'cuvantCheie' => 'clasa xii',
    ],
      'clasa_I' => [
        'cuvantCheie' => 'clasa i',
    ],
    'clasa_II' => [
        'cuvantCheie' => 'clasa ii',
    ],
    'clasa_III' => [
        'cuvantCheie' => 'clasa iii',
    ],
    'clasa_IV' => [
        'cuvantCheie' => 'clasa iv',
    ],
    'clasa_V' => [
        'cuvantCheie' => 'clasa v',
    ],
    'clasa_VI' => [
        'cuvantCheie' => 'clasa vi',
    ],
    'clasa_VII' => [
        'cuvantCheie' => 'clasa vii',
    ],
    'clasa_VIII' => [
        'cuvantCheie' => 'clasa viii',
    ],
    'clasa_IX' => [
        'cuvantCheie' => 'clasa ix',
    ],
    'clasa_X' => [
        'cuvantCheie' => 'clasa X',
    ],
    'clasa_XI' => [
        'cuvantCheie' => 'clasa xi',
    ],
    'clasa_XII' => [
        'cuvantCheie' => 'clasa xii',
    ],
    'clasa_pregatitoare' => [
        'cuvantCheie' => 'clasa pregatitoare',
    ],
    'activitati' => [
        'cuvantCheie' => 'activitati',
    ],
    'administrative' => [
        'cuvantCheie' => 'administrative',
        'continut' => [
    '/sesiuni/tid4K_650cb1b14b653/MMA Toamna dimineata cu grupa mare_10733bc.pdf',
    '/sesiuni/tid4K_650cb1b14b653/Toamna dupa-amiaza cu grupa mare_db28853.pdf',
    '/sesiuni/tid4K_64c2a59d2192b/Vara dupa-amiaza cu Maya_f2ee778.pdf'
        ],
    ],
    'imagini' => [
        'cuvantCheie' => 'doar imagini',
    ],
    'documente' => [
        'cuvantCheie' => 'doar documente',
    ],
     'minister' => [
        'cuvantCheie' => 'ministerul educatiei',
        'continut' => [
    '/sesiuni/tid4K_6507455e20713/Minister Primavara dupa-amiaza cu _c3b237f.pdf',
    '/sesiuni/tid4K_6507455e20713/Minister Primavara dupa-amiaza cu _3368a3b.pdf',
    '/sesiuni/tid4K_6507455e20713/Minister Primavara dupa-amiaza cu _4e8fa14.pdf'
        ],
    ],
    'inspectorat' => [
        'cuvantCheie' => 'inspectorat',
        'continut' => [
     '/sesiuni/tid4K_650cb1b14b653/MMA Toamna dimineata cu grupa mare_10733bc.pdf',
    '/sesiuni/tid4K_650cb1b14b653/Toamna dupa-amiaza cu grupa mare_db28853.pdf',
    '/sesiuni/tid4K_64c2a59d2192b/Vara dupa-amiaza cu Maya_f2ee778.pdf'
        ],
    ],
    'meniul' => [
        'cuvantCheie' => 'meniul saptamanii',
    ],
];




//functia de extragere a informatiilor pentru $continut, din baza de date
function extrageInformatii($cuvantCheie) {
    global $conn;

     $informatii = [];

             // selectia meniului , in oricare grupa/clasa ar putea sa se gaseasca
        if ($cuvantCheie == 'meniul') {
                // Selectează doar înregistrările care contin in denumire :"meniul, meniu, mancare"
                $query = "SELECT nume_fisier, extensie, data_upload, id_utilizator FROM (
   SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_grupa_mica WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_grupa_mijlocie WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_grupa_mare WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_I WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_II WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_III WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_IV WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_V WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_VI WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_VII WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_VIII WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_IX WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_X WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_XI WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
    UNION
    SELECT nume_fisier, extensie, data_upload, id_utilizator FROM informatii_clasa_XII WHERE nume_fisier LIKE '%meniu%' OR nume_fisier LIKE '%meniul%' OR nume_fisier LIKE '%mancare%'
) AS rezultate_combinate
ORDER BY data_upload DESC
LIMIT 1";

 $result = mysqli_query($conn, $query);


    while ($row = mysqli_fetch_assoc($result)) {
        // Extrage și procesează temp_path pentru fiecare înregistrare
        $id_utilizator = $row['id_utilizator'];
        // Extrage temp_path pentru id_utilizator specific
        $queryUtilizator = "SELECT temp_path FROM utilizatori WHERE id_utilizator = $id_utilizator";
        $resultUtilizator = mysqli_query($conn, $queryUtilizator);
        $rowUtilizator = mysqli_fetch_assoc($resultUtilizator);
        $temp_path = str_replace('/home/tid4kdem/public_html', '', $rowUtilizator['temp_path']);

        // Construiește calea completă a fișierului
        $caleFisier = $temp_path . $row['nume_fisier'];

        // Adaugă calea fișierului la array-ul de informații
        $informatii[] = $caleFisier;
    }
    return $informatii;
            }

    // extragere informatii din baza de date pentru alte cuvinte cheie decat meniul
    if ($cuvantCheie == 'activitati' || $cuvantCheie == 'documente' || $cuvantCheie == 'imagini' || $cuvantCheie == 'administrative') {
        // Definirea grupelor și claselor
        $cuvinteCheieGrupeClase = [
            'grupa_mica', 'grupa_mijlocie', 'grupa_mare',
            'clasa_pregatitoare', 'clasa_I', 'clasa_II', 'clasa_III', 'clasa_IV',
            'clasa_V', 'clasa_VI', 'clasa_VII', 'clasa_VIII', 'clasa_IX',
            'clasa_X', 'clasa_XI', 'clasa_XII'
        ];


        // Iterează prin toate grupele și clasele pentru a extrage informații
        foreach ($cuvinteCheieGrupeClase as $grupaClasa) {
            $numeTabel = 'informatii_' . $grupaClasa;

            // Alege interogarea în funcție de caz
            if ($cuvantCheie == 'documente') {
                // Selectează doar înregistrările cu extensia 'pdf'
                $query = "SELECT nume_fisier, extensie, data_upload, id_utilizator FROM {$numeTabel} WHERE extensie = 'pdf' AND nume_fisier NOT LIKE '%meniu%'  ORDER BY data_upload DESC LIMIT 10";
            } elseif ($cuvantCheie == 'activitati') {

            $query = "SELECT nume_fisier, data_upload, id_utilizator FROM {$numeTabel} WHERE nume_fisier NOT LIKE '%meniu%' AND nume_fisier NOT LIKE '%minister%' AND nume_fisier NOT LIKE '%inspectorat%'  ORDER BY data_upload DESC LIMIT 10";
            } elseif ($cuvantCheie == 'imagini') {
                // Selectează doar înregistrările cu extensia 'png', 'jpg', imagine
                $query = "SELECT nume_fisier, extensie, data_upload, id_utilizator FROM {$numeTabel} WHERE extensie LIKE '%jpg' OR extensie LIKE '%jpeg' OR extensie LIKE '%png' AND nume_fisier NOT LIKE '%meniu%' ORDER BY data_upload DESC LIMIT 10";
            } elseif ($cuvantCheie == 'administrative') {
                // Selectează doar înregistrările administrative asociate cu utilizator cu status = director, administrator, secretara
                $query = "SELECT f.nume_fisier, f.extensie, f.data_upload, f.id_utilizator
                FROM {$numeTabel} AS f
                JOIN utilizatori AS u ON f.id_utilizator = u.id_utilizator
                WHERE u.status IN ('director', 'administrator', 'secretara')
                AND nume_fisier NOT LIKE '%meniu%'
                ORDER BY f.data_upload DESC
                LIMIT 10;
                ";
            }
            $result = mysqli_query($conn, $query);


    while ($row = mysqli_fetch_assoc($result)) {
        // Extrage și procesează temp_path pentru fiecare înregistrare
        $id_utilizator = $row['id_utilizator'];
        // Extrage temp_path pentru id_utilizator specific
        $queryUtilizator = "SELECT temp_path FROM utilizatori WHERE id_utilizator = $id_utilizator";
        $resultUtilizator = mysqli_query($conn, $queryUtilizator);
        $rowUtilizator = mysqli_fetch_assoc($resultUtilizator);
        $temp_path = str_replace('/home/tid4kdem/public_html', '', $rowUtilizator['temp_path']);

        // Construiește calea completă a fișierului
        $caleFisier = $temp_path . $row['nume_fisier'];

        // Adaugă calea fișierului la array-ul de informații
        $informatii[] = $caleFisier;
    }
        }
    } else {
        // Procesare normală pentru cuvintele cheie care nu sunt "activitati"
        $numeTabel = 'informatii_' . str_replace(' ', '_', $cuvantCheie);
        $query = "SELECT nume_fisier, data_upload, id_utilizator FROM {$numeTabel} WHERE nume_fisier NOT LIKE '%meniu%' AND nume_fisier NOT LIKE '%minister%' AND nume_fisier NOT LIKE '%inspectorat%' ORDER BY data_upload DESC LIMIT 10";

        $result = mysqli_query($conn, $query);

        $informatii = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Extrage și procesează temp_path pentru fiecare înregistrare
        $id_utilizator = $row['id_utilizator'];
        // Extrage temp_path pentru id_utilizator specific
        $queryUtilizator = "SELECT temp_path FROM utilizatori WHERE id_utilizator = $id_utilizator";
        $resultUtilizator = mysqli_query($conn, $queryUtilizator);
        $rowUtilizator = mysqli_fetch_assoc($resultUtilizator);
        $temp_path = str_replace('/home/tid4kdem/public_html', '', $rowUtilizator['temp_path']);

        // Construiește calea completă a fișierului
        $caleFisier = $temp_path . $row['nume_fisier'];

        // Adaugă calea fișierului la array-ul de informații
        $informatii[] = $caleFisier;
    }
}

    return $informatii;
}

//bucla de verificare a gasirii cuvintelor cheie in continutul HTML
foreach ($matches as $match) {
    foreach ($categorii as $categorie => $date) {
        if (strtolower($match[1]) === $date['cuvantCheie']) {
            file_put_contents('debug.log', "Categorie gasita: " . $categorie . " cu ID: " . $match[3] . "\n", FILE_APPEND);

            if (strpos($categorie, 'grupa') !== false) {
                // Apelează funcția pentru a extrage informațiile
                $informatiiExtrase = extrageInformatii($categorie);
                $continutGrupa = json_encode($informatiiExtrase);
                $loculGrupaGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Grupa gasita: " . $categorie . ", Continut: " . $continutGrupa . ", Locul gasit: " . $loculGrupaGasit . "\n", FILE_APPEND);
    // algoritm afisarea imagini cu creare element src in div-ul corespunzator, exemplu : grupa mica
                $javascriptGrupe = "\n\n<script>\n" .
"document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutGrupa . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculGrupaGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = '59%';\n" .
    "                imgElement.style.height = '59%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 7000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // Încarcă PDF-ul aici\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 9000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            } elseif (strpos($categorie, 'clasa') !== false) {
                // Apelează funcția pentru a extrage informațiile
                $informatiiExtrase = extrageInformatii($categorie);
                $continutClasa = json_encode($informatiiExtrase);
                $loculClasaGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Clasa gasita: " . $categorie . ", Continut: " . $continutClasa . ", Locul gasit: " . $loculClasaGasit . "\n", FILE_APPEND);
    // algoritm afisarea imagini cu creare element src in div-ul corespunzator, exemplu : clasa XII
                $javascriptClase = "\n\n<script>\n" .
"document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutClasa . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculClasaGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = '59%';\n" .
    "                imgElement.style.height = '59%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 7000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // Încarcă PDF-ul aici\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 9000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            } elseif (strpos($categorie, 'activitati') !== false) {
                $informatiiExtrase = extrageInformatii($categorie);
                $continutActivitati = json_encode($informatiiExtrase);
                $loculActivitatiGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Activitati gasite: " . $categorie . ", Continut: " . $continutActivitati . ", Locul gasit: " . $loculActivitatiGasit . "\n", FILE_APPEND);
    // algoritm afisare documente pdf cu creare element canvas in div-ul corespunzator, exemplu : Activitati
                $javascriptActivitati = "\n\n<script>\n" .
     "document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutActivitati . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculActivitatiGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = '59%';\n" .
    "                imgElement.style.height = '59%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 7000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // Încarcă PDF-ul aici\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 9000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            } elseif (strpos($categorie, 'administrative') !== false) {
                $informatiiExtrase = extrageInformatii($categorie);
                $continutAdministrative = json_encode($informatiiExtrase);
                $loculAdministrativeGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Administrative gasite: " . $categorie . ", Continut: " . $continutAdministrative . ", Locul gasit: " . $loculAdministrativeGasit . "\n", FILE_APPEND);
    // algoritm afisare documente pdf cu creare element canvas in div-ul corespunzator, exemplu : Administrative
                $javascriptAdministrative = "\n\n<script>\n" .
     "document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutAdministrative . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculAdministrativeGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = '59%';\n" .
    "                imgElement.style.height = '59%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 7000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // Încarcă PDF-ul aici\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 9000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            } elseif (strpos($categorie, 'documente') !== false) {
                $informatiiExtrase = extrageInformatii($categorie);
                $continutDocumente = json_encode($informatiiExtrase);
                $loculDocumenteGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Documente gasite: " . $categorie . ", Continut: " . $continutDocumente . ", Locul gasit: " . $loculDocumenteGasit . "\n", FILE_APPEND);
    // algoritm pentru "doar documente"
                $javascriptDocumente = "\n\n<script>\n" .
    "document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutDocumente . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculDocumenteGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = '59%';\n" .
    "                imgElement.style.height = '59%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 7000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // Încarcă PDF-ul aici\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 9000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            } elseif (strpos($categorie, 'imagini') !== false) {
                $informatiiExtrase = extrageInformatii($categorie);
                $continutImagini = json_encode($informatiiExtrase);
                $loculImaginiGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Imagini gasite: " . $categorie . ", Continut: " . $continutImagini . ", Locul gasit: " . $loculImaginiGasit . "\n", FILE_APPEND);
    //algoritm pentru "doar imagini"
                $javascriptImagini = "\n\n<script>\n" .
"document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutImagini . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculImaginiGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = '30%';\n" .
    "                imgElement.style.height = '30%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 7000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 9000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            } elseif (strpos($categorie, 'minister') !== false) {
                // $informatiiExtrase = extrageInformatii($categorie);
                // $continutMinister = json_encode($informatiiExtrase);
                $continutMinister = json_encode($date['continut']);
                $loculMinisterGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Ministerul Educatiei gasite: " . $categorie . ", Continut: " . $continutMinister . ", Locul gasit: " . $loculMinisterGasit . "\n", FILE_APPEND);
    // algoritm pentru Ministerul Educatiei
                $javascriptMinister = "\n\n<script>\n" .
"document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutMinister . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculMinisterGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = '59%';\n" .
    "                imgElement.style.height = '59%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 7000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // Încarcă PDF-ul aici\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 9000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            } elseif (strpos($categorie, 'inspectorat') !== false) {
                // $informatiiExtrase = extrageInformatii($categorie);
                // $continutInspectorat = json_encode($informatiiExtrase);
                $continutInspectorat = json_encode($date['continut']);
                $loculInspectoratGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Inspectorat gasite: " . $categorie . ", Continut: " . $continutInspectorat . ", Locul gasit: " . $loculInspectoratGasit . "\n", FILE_APPEND);
    // algoritm pentru Inspectorat
                $javascriptInspectorat = "\n\n<script>\n" .
    "document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutInspectorat . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculInspectoratGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = '59%';\n" .
    "                imgElement.style.height = '59%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 7000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // Încarcă PDF-ul aici\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 9000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            } elseif (strpos($categorie, 'meniul') !== false) {
                $informatiiExtrase = extrageInformatii($categorie);
                $continutMeniul = json_encode($informatiiExtrase);

                $loculMeniulGasit = json_encode([$match[3]]);
                file_put_contents('debug.log', "Meniul Saptamanii gasite: " . $categorie . ", Continut: " . $continutMeniul . ", Locul gasit: " . $loculMeniulGasit . "\n", FILE_APPEND);
    // algoritm pentru Meniul Saptamanii
                $javascriptMeniul = "\n\n<script>\n" .
    "document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var continut = " . $continutMeniul . ";\n" . // Lista de conținut
    "    var loculGasit = " . $loculMeniulGasit . ";\n" . // Lista de ID-uri ale chenarelor
    "    loculGasit.forEach(function(idChenar) {\n" .
    "        var chenar = document.getElementById(loculGasit);\n" .
    "        var currentIndex = 0;\n" .
    "        function afiseazaContinut() {\n" .
    "            if(currentIndex >= continut.length) currentIndex = 0; // Resetează indexul dacă a ajuns la sfârșitul listei\n" .
    "            chenar.innerHTML = '';\n" .
    "            var elementContinut = continut[currentIndex];\n" .
    "            var extensie = elementContinut.split('.').pop().toLowerCase();\n" .
    "            if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg') {\n" .
    "                var imgElement = document.createElement('img');\n" .
    "                imgElement.src = elementContinut;\n" .
    "                imgElement.style.width = 'auto';\n" .
    "                imgElement.style.height = '59%';\n" .
    "                imgElement.style.objectFit = 'contain';\n" .
    "                chenar.appendChild(imgElement);\n" .
    "                currentIndex++;\n" .
    "                setTimeout(afiseazaContinut, 70000); // Schimbă după 7 secunde\n" .
    "            } else if (extensie === 'pdf') {\n" .
    "                var canvasElement = document.createElement('canvas');\n" .
    "                chenar.appendChild(canvasElement);\n" .
    "                // Încarcă PDF-ul aici\n" .
    "                // După ce PDF-ul a fost complet încărcat și afișat, incrementează indexul și apelează afiseazaContinut\n" .
    "                // De exemplu:\n" .
    "                if (pdfjsLib && elementContinut) {\n" .
    "                    pdfjsLib.getDocument(elementContinut).promise.then(function(pdfDoc) {\n" .
    "                        pdfDoc.getPage(1).then(function(page) {\n" .
    "                            var viewport = page.getViewport({scale: 0.5});\n" .
    "                            canvasElement.height = viewport.height;\n" .
    "                            canvasElement.width = viewport.width;\n" .
    "                            var renderContext = {\n" .
    "                                canvasContext: canvasElement.getContext('2d'),\n" .
    "                                viewport: viewport\n" .
    "                            };\n" .
    "                            page.render(renderContext).promise.then(function() {\n" .
    "                                currentIndex++;\n" .
    "                                setTimeout(afiseazaContinut, 90000); // Schimbă după 9 secunde\n" .
    "                            });\n" .
    "                        });\n" .
    "                    });\n" .
    "                }\n" .
    "            }\n" .
    "        }\n" .
    "        afiseazaContinut(); // Începe bucla de afișare\n" .
    "    });\n" .
    "});\n" .
    "</script>\n";
            }
            ${$categorie . 'Gasita'} = true;
        }
    }
}

    // Adaugă codul JavaScript la sfârșitul fișierului sau unde este necesar
    $javascriptScrollControl = "\n\n<script>\n" .
    // algoritm pentru scalarea automata in pagina si eliminarea scroll
     "window.addEventListener('load', function() {\n" .
     "    if (document.body.scrollHeight > window.innerHeight) {\n" .
     "        var cadran = document.getElementById('id4kCadran');\n" .
     "        if (cadran) {\n" .
     "            cadran.style.height = '40vh';\n" .
     "            document.querySelectorAll('.chenar_mare, .chenar_mic').forEach(function(chenar) {\n" .
     "                chenar.style.height = '59%';\n" .
     "            });\n" .
     "        }\n" .
     "    }\n" .
     "});\n" .
     "</script>\n";

     //codul care face cererea catre tid4_plus_javascript.php la fiecare 2 minute pentru refresh de informatie
 $javascriptRefresh = "<script>\n" .
"setInterval(function() {\n" .
"    var xhr = new XMLHttpRequest();\n" .
"    xhr.open('GET', '../pages/tid4k_plus_javascript.php?ajax=1', true);\n" .
"    xhr.send(); // Trimite cererea fără a aștepta un răspuns\n" .
"}, 300000); // 120000 ms = 2 minute\n" .
"setInterval(function() {\n" .
"    window.location.reload(); // Reîncarcă pagina\n" .
"}, 300000); // 139000 ms = 2.25 minute\n" .
"</script>\n";



//codul pentru incarcarea meniului spatamanal in format HTML
$javascriptIncarcaMeniulSaptamanal = "\n\n<script>\n" .
     "function incarcaMeniulSaptamanal() {\n" .
    "    var xhr = new XMLHttpRequest();\n" .
    "    xhr.open('GET', '../pages/tabel_meniu_afisat.php', true);\n" .
    "    xhr.onload = function() {\n" .
    "        if (this.status == 200) {\n" .
    "            var chenar = document.getElementById('chenar1Rand2');\n" .
    "            if (chenar) { // Asigurăm că elementul este prezent înainte de a-l actualiza\n" .
    "                chenar.innerHTML = this.responseText;\n" .
    "                actualizeazaOre();\n" .
    "                initializeazaSalvareaLocala();\n" .
    "                actualizeazaAnteturileTabelului();\n" .
    "                adaugaEmoji();\n" .
    "            }\n" .
    "        }\n" .
    "    };\n" .
    "    xhr.onerror = function() { console.error('Eroare la încărcarea meniului saptamanal.'); };\n" . // Gestionăm erorile posibile ale lui XMLHttpRequest
    "    xhr.send();\n" .
    "}\n\n" .
    "document.addEventListener('DOMContentLoaded', function() {\n" .
    "    var chenar = document.getElementById('chenar1Rand2');\n" .
    "    if (chenar) { incarcaMeniulSaptamanal(); }\n" . // Ne asigurăm că elementul este prezent înainte de a apela funcția
    "    else { console.error('Elementul #chenar1Rand2 nu este prezent în DOM la încărcarea paginii.'); }\n" .
    "});\n" .

    "// Definește seturile de ore\n" .
    "var setOre1 = ['08:15', '10:00', '12:00', '15:15', '19:00'];\n" .
    "var setOre2 = ['09:00', '10:30', '12:30', '16:00'];\n" .

"function actualizeazaOre(oraSelectata) {\n" .
"    var setSelectat;\n" .
"    if (setOre1.includes(oraSelectata)) {\n" .
"        setSelectat = setOre1;\n" .
"    } else if (setOre2.includes(oraSelectata)) {\n" .
"        setSelectat = setOre2;\n" .
"    } else {\n" .
"        return;\n" .
"    }\n\n" .
"    var campuriOra = document.querySelectorAll('textarea.oraInput');\n" .
"    if (!campuriOra.length) return; // Verificăm dacă elementele există\n" .
"    var meniuInputuri = document.querySelectorAll('table tr td:nth-child(n+2) textarea.inputText');\n" .
"    if (!meniuInputuri.length) return; // Verificăm dacă elementele există\n\n" .
"    setSelectat.forEach(function(ora, index) {\n" .
"        if (campuriOra[index]) {\n" .
"            campuriOra[index].value = ora;\n" .
"        }\n" .
"    });\n\n" .
"    var tabel = document.querySelector('table');\n" .
"    if (!tabel) return; // Verificăm dacă tabelul există\n" .
"    while (tabel.rows.length - 1 > setSelectat.length) {\n" .
"        tabel.deleteRow(-1);\n" .
"    }\n" .
"    var indexMeniu = 0;\n" .
"    while (tabel.rows.length - 1 < setSelectat.length) {\n" .
"        var randNou = tabel.insertRow(-1);\n" .
"        for (var i = 0; i < 6; i++) {\n" .
"            var celulaNoua = randNou.insertCell(i);\n" .
"            if (i === 0) {\n" .
"                celulaNoua.innerHTML = '<textarea class=\"inputText oraInput\" oninput=\"actualizeazaOre(this.value)\">' + setSelectat[tabel.rows.length - 2] + '</textarea>';\n" .
"            } else {\n" .
"                var valoareMeniu = meniuInputuri.length > indexMeniu ? meniuInputuri[indexMeniu].value : '';\n" .
"                celulaNoua.innerHTML = '<textarea class=\"inputText\">' + valoareMeniu + '</textarea>';\n" .
"                indexMeniu++;\n" .
"            }\n" .
"        }\n" .
"    }\n" .
"}\n\n" .


// Cod pentru identificarea și afișarea automată a datei asociate zilei în antetul coloanelor din tabel
"function actualizeazaAnteturileTabelului() {\n" .
"    var dataCurenta = new Date();\n" .
"    var primaZi = obtinePrimaZiASaptamanii(dataCurenta);\n" .
"    var tabel = document.querySelector('table');\n" .
"    if (!tabel) return; // Verificăm dacă tabelul există\n" .
"    for (var i = 1; i <= 5; i++) {\n" .
"        var th = tabel.querySelector('tr th:nth-child(' + (i + 1) + ')');\n" .
"        if (!th) continue; // Continuăm dacă elementul th nu există\n" .
"        var dataZilei = adaugaZile(primaZi, i - 1);\n" .
"        var dataFormatata = dataZilei.toLocaleDateString('ro-RO', { day: '2-digit', month: 'long' });\n" .
"        var ziuaSaptamanii = ['Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri'][i - 1];\n" .
"        th.textContent = ziuaSaptamanii + ' (' + dataFormatata + ')';\n" .
"        if (i === dataCurenta.getDay()) {\n" .
"            var celule = tabel.querySelectorAll('tr td:nth-child(' + (i + 1) + '), tr th:nth-child(' + (i + 1) + ')');\n" .
"            celule.forEach(function(celula) {\n" .
"                if (celula) celula.style.backgroundColor = '#D3D3D3';\n" .
"            });\n" .
"        }\n" .
"    }\n" .



    "function adaugaZile(data, zile) {\n" .
    "    var rezultat = new Date(data);\n" .
    "    rezultat.setDate(rezultat.getDate() + zile);\n" .
    "    return rezultat;\n" .
    "}\n\n" .

 "function obtinePrimaZiASaptamanii(data) {\n" .
"    var zi = data.getDay();\n" .
"    var esteWeekend = zi === 0 || zi === 6;\n" . // Determină dacă este weekend
"    var diferenta = zi === 0 ? -6 : 1;\n" . // Diferența standard pentru a ajunge la Luni
"    var primaZi = new Date(data);\n" .
"    primaZi.setDate(data.getDate() - zi + diferenta);\n" .
"    if (esteWeekend) {\n" .
"        primaZi.setDate(primaZi.getDate() + 7);\n" . // Ajustează pentru weekend
"    }\n" .
"    return primaZi;\n" .
"}\n\n" .
"}\n\n" .

"document.addEventListener('DOMContentLoaded', actualizeazaAnteturileTabelului);\n\n" .

"function adaugaEmoji() {\n" .
"    const cuvinteCheie = {\n" .
"        'almette': '🧈 Almette\u200B (35gr)',\n" .
"        'ananas': '🍍 Ananas\u200B (50gr)',\n" .
"        'ardei': '🫑 ardei\u200B gras (35gr)',\n" .
"        'banana': '🍌 Banană\u200B (1buc)',\n" .
"        'biscuiti': '🍪 biscuiți\u200B (60gr) ',\n" .
"        'Biscuiti': '🍪 Biscuiți\u200B (60gr) ',\n" .
"        'branza': '🧀 brânza\u200B (35gr)',\n" .
"        'broccoli': '🥬 broccoli\u200B (35gr)',\n" .
"        'brios': '🧁 Briose\u200B (60gr)',\n" .
"        'brownie': '🍪 Brownie\u200B (60gr)',\n" .
"        'burger': 'Burger\u200B 🍔(100gr)',\n" .
"        'capsuni': '🍓 Căpșuni\u200B (50gr)',\n" .
"        'cartofi': '🥔 cartofi\u200B (250gr)',\n" .
"        'Cartofi': '🥔 Cartofi\u200B (250gr)',\n" .
"        'cascaval': '🧀 cașcaval\u200B (35gr)',\n" .
"        'ceai': '☕ Ceai\u200B (300ml)',\n" .
"        'ceapa': '🧅 ceapă\u200B ',\n" .
"        'cereale': '🥣 cereale\u200B (250gr)',\n" .
"        'Cereale': '🥣 Cereale\u200B (250gr)',\n" .
"        'chec': '🍰 Chec\u200B (60gr)',\n" .
"        'ciocolata': '🍫 Ciocolată\u200B ',\n" .
"        'ciorba': '🍲 Ciorbă\u200B (250ml)',\n" .
"        'clatit': '🥞 Clatite\u200B (60gr)',\n" .
"        'croissant': '🥐 Croissant\u200B (60gr)',\n" .
"        'cozonac': '🍞 Cozonac\u200B (60gr)',\n" .
"        'curcan': '🦃 curcan\u200B (85gr)',\n" .
"        'dulceata': '🍯 dulceață (30gr)',\n" .
"        'fasole': '🥘 fasole\u200B (250gr)',\n" .
"        'Fasole': '🥘 Fasole\u200B (250gr)',\n" .
"        'gogosi': '🍩 Gogoși\u200B (60gr)',\n" .
"        'iaurt': '🥛Iaurt\u200B (60ml)',\n" .
"        'inghetata': '🍨 Înghețată\u200B ',\n" .
"        'kiwi': '🥝 Kiwi\u200B (1buc) ',\n" .
"        'lamaie': '🍋 lămâie\u200B ',\n" .
"        'lapte': '🐄 lapte\u200B (60ml)',\n" .
"        'Lapte': '🐄 Lapte\u200B (60ml)',\n" .
"        'legume': '🥗 legume\u200B (60gr)',\n" .
"        'mamaliga': '🫓 mămăligă\u200B (250gr)',\n" .
"        'Mamaliga': '🫓 Mămăligă\u200B (250gr)',\n" .
"        'mar': '🍎 Măr\u200B (1buc)',\n" .
"        'mere': '🍎 Măr\u200B (1buc)',\n" .
"        'Mar': '🍎 Măr\u200B (1buc)',\n" .
"        'mandarin': '🍊 Mandarină\u200B (1buc)',\n" .
"        'mazare': '🌱 mazăre\u200B (250gr)',\n" .
"        'Mazare': '🌱 Mazăre\u200B (250gr)',\n" .
"        'miere': '🍯 miere\u200B (30gr)',\n" .
"        'morcov':'🥕 morcovi\u200B (50gr)',\n" .
"        'omleta': '🍳 Omletă\u200B (100gr)',\n" .
"        'Omleta': '🍳 Omletă\u200B (100gr)',\n" .
"        'ou': '🥚 ou\u200B (30gr) ',\n" .
"        'orez': '🍚 orez\u200B (250gr)',\n" .
"        'paine': '(🍞 felie de pâine\u200B 30gr)',\n" .
"        'pasta': '🧈 pastă\u200B (35gr)',\n" .
"        'paste': '🍝 Paste\u200B (250gr)',\n" .
"        'pate': '🧈 paté\u200B (35gr)',\n" .
"        'patrunjel': '🌿 pătrunjel\u200B ',\n" .
"        'para': '🍐 Pară\u200B (1buc)',\n" .
"        'Para': '🍐 Pară\u200B (1buc)',\n" .
"        'peste': '🐟 pește\u200B (85gr)',\n" .
"        'pizza': '🍕 Pizza\u200B (60gr)',\n" .
"        'pilaf': '🍚 Pilaf\u200B (250gr)',\n" .
"        'placinta': '🥧 Plăcintă\u200B (60gr)',\n" .
"        'portocal': '🍊 Portocală\u200B (1buc)',\n" .
"        'porc': '🐷 porc\u200B (85gr)',\n" .
"        'prajitura': '🍰 Prăjitură\u200B (60gr)',\n" .
"        'pui': '🍗 pui\u200B (85gr)',\n" .
"        'ridichi': '🍅 ridichi\u200B ',\n" .
"        'rosie': '🍅 roșie\u200B ',\n" .
"        'struguri': '🍇 Struguri\u200B (50gr)',\n" .
"        'suc': '🧃 Suc\u200B (60ml)',\n" .
"        'supa': '🍜 Supă\u200B (250ml)',\n" .
"        'sunca': '🥩 șuncă\u200B (35gr)',\n" .
"        'tartina': '🥪 Tartină\u200B (30gr)',\n" .
"        'Tartina': '🥪 Tartină\u200B (30gr)',\n" .
"        'usturoi': '🧄 usturoi\u200B ',\n" .
"        'unt': '🧈 unt\u200B (15gr)',\n" .
"        'varza': '🥬 varză\u200B (250gr) ',\n" .
"        'salata': '🥗 salată\u200B (60gr)',\n" .
"        'Salata': 'Salată\u200B ',\n" .
"        'tocanita': '🥘 Tocăniță\u200B (250gr)',\n" .
        // Continuă să adaugi aici restul cuvintelor cheie și emoji-urile corespunzătoare
"    };\n" .

"var textAreas = document.querySelectorAll('textarea.inputText');\n" .
"    if (textAreas.length > 0) { // Verifică dacă există textarea-uri\n" .
"        textAreas.forEach(function(textarea, index) {\n" .
"            textarea.addEventListener('input', function() {\n" .
"                let text = textarea.value;\n" .
"           Object.keys(cuvinteCheie).forEach(function(cuvant) {\n" .
"    const replacement = cuvinteCheie[cuvant] + '\u200B';\n" .
"    const regex = new RegExp(cuvant + '(?!\\s*\\u200B)', 'gu');\n" .
"    text = text.replace(regex, replacement);\n" .
"});\n" .

// Verificăm și adăugăm pâinea dacă este cazul
"const cuvinteCheieMancare = ['Ciorbă', 'Supă', 'mancare de mazare', 'mancare de fasole', 'varza'];\n" .
"const contineCuvinteCheieMancare = cuvinteCheieMancare.some(cuvant => text.includes(cuvant));\n" .
"const dejaContinePaine = text.includes('(🍞 felie de pâine\u200B 30gr)');\n" .

"if (contineCuvinteCheieMancare && !dejaContinePaine) {\n" .
"    text += ' (🍞 felie de pâine\u200B 30gr)';\n" .
"}\n" .


"            textarea.value = text; // Actualizează textul cu emoji-urile adăugate\n" .

            // Asigurați-vă că orice schimbare este imediat salvată
"            localStorage.setItem('input_' + index, text);\n" .
"        });\n" .
"    });\n" .
"}\n" .
"}\n\n" .


    // Salvarea locală a datelor modificate în câmpurile de input
 "function initializeazaSalvareaLocala() {\n" .
"    //culori diferite pentru zilele saptamanii\n" .
"    const zile = ['Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri'];\n" .
"    const culori = ['#FF00FF', '#32CD32', '#FFA500', '#1E90FF', '#FF69B4']; // Magenta, Verde, Portocaliu, Albastru, Roz\n" .

"    const thElements = document.querySelectorAll('table tr th');\n" .
"    thElements.forEach(function(th, index) {\n" .
"        if (index > 0 && index <= zile.length) { // Ignoră prima celulă ('Ora') și se aplică doar pentru zilele săptămânii\n" .
"            th.style.color = culori[index - 1];\n" .
"        }\n" .
"    });\n" .

"    // Verifică dacă localStorage este disponibil\n" .
"    if (typeof(Storage) !== 'undefined') {\n" .
"        // Încărcarea datelor salvate\n" .
"        document.querySelectorAll('textarea.inputText').forEach(function(textarea, index) {\n" .
"            var salvat = localStorage.getItem('input_' + index);\n" .
"            if (salvat !== null) {\n" .
"                textarea.value = salvat;\n" .
"            }\n" .
"            textarea.addEventListener('input', function() {\n" .
"                localStorage.setItem('input_' + index, textarea.value);\n" .
"            });\n" .
"        });\n" .
"    } else {\n" .
"        console.warn('LocalStorage nu este suportat de acest browser.');\n" .
"    }\n\n" .

"    // Ajustarea înălțimii celulei de tabel în funcție de conținut\n" .
"    var textAreas = document.querySelectorAll('textarea.inputText');\n" .
"    function adjustHeight(textArea) {\n" .
"        textArea.style.height = 'auto';\n" .
"        textArea.style.height = textArea.scrollHeight + 'px';\n" .
"    }\n\n" .

"    textAreas.forEach(function(textArea) {\n" .
"        adjustHeight(textArea);\n" .
"        textArea.addEventListener('input', function() {\n" .
"            adjustHeight(textArea);\n" .
"        });\n" .
"    });\n" .
"}\n\n" .


    "document.addEventListener('DOMContentLoaded', function() {\n" .
    "    actualizeazaOre();\n" . // Inițializează evenimentele legate de ore la încărcarea paginii.
    "    initializeazaSalvareaLocala();\n" . // Inițializează salvarea locală la încărcarea paginii.
    "});\n" .
    "</script>\n";


     // $htmlContent = str_replace('</head>', $javascriptContent . '</head>', $htmlContent); /*javascriptul este adaugat in head*/
     $allJavascriptContent = $javascriptGrupe . $javascriptClase . $javascriptActivitati . $javascriptAdministrative . $javascriptImagini . $javascriptDocumente . $javascriptMinister . $javascriptInspectorat . $javascriptMeniul . $javascriptScrollControl . $javascriptRefresh . $javascriptQRCode . $javascriptIncarcaMeniulSaptamanal;

// Înlocuiește blocul HTML pentru versiunea mică cu cel pentru versiunea mare și adaugă link
$htmlContent = preg_replace(
    '/<div class="logo-qrcode-mic">\s*<img src="\.\.\/logo_qr_code\.png" style="width:100%; height:auto;">\s*<\/div>/s',
    '<a href="/pages/grupa_clasa_copil.php"><div class="logo-qrcode-mare"><img src="../logo_qr_code.png" ></div></a>',
    $htmlContent
);



     $htmlContent = str_replace('</body>', $allJavascriptContent . '</body>', $htmlContent); /*javascriptul este adaugat in body*/


// Resalvează fișierul modificat
if (!is_null($htmlContent) && $htmlContent != '') {
    if (file_exists($filePath)) {
        unlink($filePath); // Șterge fișierul existent dacă există
    }

    file_put_contents($filePath, $htmlContent); // Rescrie fișierul cu noul conținut
}
?>
