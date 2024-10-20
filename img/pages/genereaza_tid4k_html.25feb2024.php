<?php
error_log(print_r($_POST, true));
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';
require_once 'functii_si_constante.php';

// extragerea ultimei inregistrari din tabela infodisplay selectiile pentru cadran-simulat
$sql = "SELECT * FROM infodisplay ORDER BY timp_ultima_modificare DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$ultimaInregistrare = $result->fetch_assoc();
$timpUltimaModificare = $ultimaInregistrare['timp_ultima_modificare'];
$dataOra = new DateTime($timpUltimaModificare);

// Formatarea datei și orei (e.g., '07/01/2024, 17:29')
$formatareDataOra = $dataOra->format('d.m.Y, H:i');

    // Setarea variabilelor cu valorile din ultima înregistrare, dacă există
    $cadran_simulatTitlu1Rand2 = trim($ultimaInregistrare['cadran_simulatTitlu1Rand2']);
    $cadran_simulatTitlu1Rand3 = trim($ultimaInregistrare['cadran_simulatTitlu1Rand3']);
    $cadran_simulatTitlu2Rand3 = trim($ultimaInregistrare['cadran_simulatTitlu2Rand3']);
    $cadran_simulatTitlu3Rand3 = trim($ultimaInregistrare['cadran_simulatTitlu3Rand3']);
    $numeUnitateScolara = trim($ultimaInregistrare['numeUnitateScolara']);

// Transmiterea datelor către JavaScript
echo "<script>
var surse_php = " . json_encode($surse_php) . ";
var titluriCadran = {
  cadran_simulatTitlu1Rand2: '" . $cadran_simulatTitlu1Rand2 . "',
  cadran_simulatTitlu1Rand3: '" . $cadran_simulatTitlu1Rand3 . "',
  cadran_simulatTitlu2Rand3: '" . $cadran_simulatTitlu2Rand3 . "',
  cadran_simulatTitlu3Rand3: '" . $cadran_simulatTitlu3Rand3 . "'
var ultimaInregistrare = " . json_encode($ultimaInregistrare) . ";</script>";

// de aici incepe preluarea datelor provenite de la infodisplay.php
if (!empty($_POST['html'])) {
    $htmlContent = $_POST['html'];

    $htmlOutput = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TID4K Avizier</title>
    <link rel="stylesheet" type="text/css" href="/pages/style.css"> <!-- Asigură-te că calea este corectă -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';</script>
</head>
<body>

<script>
window.onload = function() {
    var cadran = document.getElementById('id4kCadran');
    if (cadran) {
        cadran.style.display = 'block';
        cadran.style.width = '100%';
        cadran.style.height = 'auto'; // Ajustezi înălțimea cadranului principal
    }

    // Ajustează înălțimea chenarelor "cadran_simulatfontMare"
    var chenareMare = document.querySelectorAll('.cadran_simulatfontMare');
    chenareMare.forEach(function(chenar) {
        chenar.style.height = 'auto'; // Se ajustează pentru a se potrivi conținutului
    });

    // Ajustează înălțimea chenarelor "fontMic"
    var chenareMic = document.querySelectorAll('.fontMic');
    chenareMic.forEach(function(chenar) {
        chenar.style.height = 'auto'; // Se ajustează pentru a se potrivi conținutului
    });

    // chenarul "Claselor" , cel din mijloc; ajustează înălțimea chenarelor "chenar_mare" și "chenar_mic"
    var chenareSpecifice = document.querySelectorAll('.chenar_mare, .chenar_mic');
    chenareSpecifice.forEach(function(chenar) {
        chenar.style.height = 'auto'; // Se ajustează pentru a se potrivi conținutului
    });

       // Modifică poziția unui chenar specific
    var chenarMare = document.querySelector('.chenar_mare');
    if (chenarMare) {
        chenarMare.style.position = 'relative'; // Sau 'absolute' dacă este necesar
        chenarMare.style.top = '5px'; // Deplasează chenarul 20px mai jos față de poziția sa inițială
        chenarMare.style.left = '150px'; // Deplasează chenarul 10px la dreapta față de poziția sa inițială
    }

    // Modifică poziția tuturor chenarelor "fontMic"
    var chenareMic = document.querySelectorAll('.fontMic');
    chenareMic.forEach(function(chenar) {
        chenar.style.position = 'relative'; // Poziționare relativă la poziția sa normală
        chenar.style.marginTop = '10px'; // Adaugă o margine la partea de sus pentru a-l deplasa mai jos
    });

    // Inițializează valorile selectate cu valorile implicite
    incarcaContinutInChenar('chenar1Rand2', selectiiUtilizator.cadran_simulatTitlu1Rand2);
    incarcaContinutInChenar('chenar1Rand3', selectiiUtilizator.cadran_simulatTitlu1Rand3);
    incarcaContinutInChenar('chenar2Rand3', selectiiUtilizator.cadran_simulatTitlu2Rand3);
    incarcaContinutInChenar('chenar3Rand3', selectiiUtilizator.cadran_simulatTitlu3Rand3);

};

// urmeaza codul care asigura dinamica informatiilor in fiecare chenar
function incarcaPDF(calePDF, idChenar, callback) {
    let chenar = document.getElementById(idChenar);
    let dimensiuniChenar = chenar.getBoundingClientRect();
    chenar.style.overflow = 'hidden';

    let canvas = document.createElement('canvas');
    canvas.className = 'canvas-pdf';
    chenar.innerHTML = ''; // Curățăm chenarul înainte de a adăuga un nou canvas
    chenar.appendChild(canvas);

    let ctx = canvas.getContext('2d');

    pdfjsLib.getDocument(calePDF).promise.then(function(pdfDoc) {
        pdfDoc.getPage(1).then(function(page) {
            var viewport = page.getViewport({scale: 1});
            var scale = dimensiuniChenar.width / viewport.width;
            viewport = page.getViewport({scale: scale});

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            var renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            page.render(renderContext).promise.then(function() {
                // După finalizarea randării paginii, apelăm callback-ul
                if (callback) callback();
            });
        });
    }).catch(function(error) {
        console.error('Eroare la încărcarea PDF-ului: ' + error.message);
        if (callback) callback(); // Apelăm callback-ul chiar și în caz de eroare
    });
}


function afiseazaPDFuri(pdfuri, idChenar, index = 0) {
    // Verificăm dacă indexul este în limitele array-ului
    if (index >= pdfuri.length) index = 0;

    // Încărcăm PDF-ul curent și apoi trecem la următorul după ce acesta este încărcat
    incarcaPDF(pdfuri[index].cale_infodisplay_afisat, idChenar, function() {
        // Funcția callback care este apelată după încărcarea PDF-ului
        setTimeout(function() {document.addEventListener("DOMContentLoaded", function() {
    var selectoare = document.querySelectorAll('.cadran_simulat_select');
    selectoare.forEach(function(select) {
        select.addEventListener('change', function() {
            var valoareSelectata = this.value;
            var idSelect = this.id;
            actualizeazaInformatiiInDB(idSelect, valoareSelectata);
        });
    });

  });
            afiseazaPDFuri(pdfuri, idChenar, index + 1); // Trecem la următorul PDF
        }, 5000); // Așteptăm 5 secunde între PDF-uri
    });
}


function incarcaImagine(caleImagine, idChenar) {
    let chenar = document.getElementById(idChenar);

    // Creează un element img și setează proprietățile necesare
    let img = document.createElement('img');
    img.src = caleImagine;
    img.alt = 'Imagine';
    img.style.maxWidth = '100%'; // Ajustează lățimea imaginii la lățimea chenarului
    img.style.height = 'auto'; // Păstrează raportul de aspect al imaginii

    // Gestionarea evenimentului de eroare
    img.onerror = function() {
        img.src = '/pages/imaginea_neincarcata.png';
    };

    // Curăță chenarul și adaugă imaginea
    chenar.innerHTML = '';
    chenar.appendChild(img);
}

function afiseazaImagini(imagini, idChenar) {
    let chenar = document.getElementById(idChenar);

    // Resetăm conținutul chenarului
    chenar.innerHTML = '';

    let indexImagineCurenta = 0; // Indexul pentru imaginea curentă

    function afiseazaImagineUrmatoare() {
        if (indexImagineCurenta < imagini.length) {
            incarcaImagine(imagini[indexImagineCurenta].cale_infodisplay_afisat, idChenar);
            indexImagineCurenta++;
            // Setăm un timeout pentru a afișa următoarea imagine după 5 secunde
            setTimeout(afiseazaImagineUrmatoare, 5000);
        }
    }

    // Începe afișarea imaginilor
    afiseazaImagineUrmatoare();
}

function incarcaContinutInChenar(selectId) {
    let select = document.getElementById(selectId);
    let urlSursa = rezultate['surse_php'][select.value];



        let idChenar = select.getAttribute('data-chenar');
        let chenar = document.getElementById(idChenar);

        // Resetează conținutul chenarului
        chenar.innerHTML = '';



                let pdfuri = [];
                let imagini = [];

                if (Array.isArray(data)) {
                    // Procesăm fiecare element din array
                    data.forEach(item => {
                        let extensie = item.cale_infodisplay_afisat.split('.').pop().toLowerCase();
                        if (extensie === 'pdf') {
                            pdfuri.push(item);
                        } else if (['jpg', 'jpeg', 'png'].includes(extensie)) {
                            imagini.push(item);
                        }
                    });
                } else if (data && data.cale_infodisplay_afisat) {
                    // Procesăm un singur element
                    let extensie = data.cale_infodisplay_afisat.split('.').pop().toLowerCase();
                    if (extensie === 'pdf') {
                        pdfuri.push(data);
                    } else if (['jpg', 'jpeg', 'png'].includes(extensie)) {
                        imagini.push(data);
                    }
                }

                // Afișăm PDF-urile și imaginile, dacă există
                if (pdfuri.length) {
                    afiseazaPDFuri(pdfuri, idChenar);
                }
                if (imagini.length) {
                    afiseazaImagini(imagini, idChenar);
                }

                if (pdfuri.length === 0 && imagini.length === 0) {
                    chenar.innerHTML = "Nu există informatie disponibila pentru afișare.";
                }

        };
</script>
    $htmlContent
</body>
</html>
HTML;

    file_put_contents('/home/tid4kdem/public_html/avizier/tid4k.html', $htmlOutput);
    echo "Pagina tid4k.html a fost generată cu succes.";
} else {
    echo "Niciun conținut HTML primit.";
}
?>
