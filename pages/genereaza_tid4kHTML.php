<?php
// Activăm afișarea erorilor pentru a facilita depanarea
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Citim datele JSON trimise în corpul solicitării
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Extragem selecțiile și conținutul HTML din datele primite
    $selections = $data['selections'] ?? [];  // Asigură că avem selecții valide
    $htmlContent = $data['html'] ?? '';       // Conținutul HTML primit pentru generare
    $layoutName = $data['layoutName'] ?? 'default';

    // Sanitizează numele layout-ului pentru a preveni caractere invalide în numele fișierului
    $layoutNameSanitized = preg_replace('/[^a-zA-Z0-9_-]/', '_', $layoutName);

    // Conținutul care va fi inserat în secțiunea <head> a fișierului HTML
    $headContent = '
    <link rel="stylesheet" href="../pages/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js";
    </script>
    <style>
    body, html {
    overflow-y: hidden;
    overflow-x: hidden;
    height: auto;
}
::-webkit-scrollbar {
display: none;
}
</style>
';

// Inserăm conținutul suplimentar în secțiunea <head> a HTML-ului primit
$htmlContent = str_replace('<head>', '<head>' . $headContent, $htmlContent);

// Convertim selecțiile în format JSON fără a scăpa caracterele speciale
$selectionsJson = json_encode($selections, JSON_UNESCAPED_UNICODE);

// Asigurăm că datele JSON sunt incluse corect în scriptul JavaScript folosind nowdoc
$bodyScript = <<<'EOD'
<script>
document.addEventListener('DOMContentLoaded', function() {
const selections = SELECTIONS_JSON_PLACEHOLDER;
console.log('Selecții:', selections);
displayContent(document.getElementById('displayArea'), selections);
});

// Funcții auxiliare pentru evidențierea zilei curente în meniu
function adaugaZile(data, zile) {
var rezultat = new Date(data);
rezultat.setDate(rezultat.getDate() + zile);
return rezultat;
}

function obtinePrimaZiASaptamanii(data, esteWeekend) {
var zi = data.getDay();
var diferenta = zi === 0 ? -6 : 1;
var primaZi = new Date(data);
primaZi.setDate(data.getDate() - zi + diferenta);

if (esteWeekend) {
    primaZi.setDate(primaZi.getDate() + 7);
}

return primaZi;
}

function evidentiereZiCurenta() {
var dataCurenta = new Date();
var esteWeekend = dataCurenta.getDay() === 0 || dataCurenta.getDay() === 6;
var primaZi = obtinePrimaZiASaptamanii(dataCurenta, esteWeekend);

for (var i = 1; i <= 5; i++) {
    var dataZilei = adaugaZile(primaZi, i - 1);
    var dataFormatata = dataZilei.toLocaleDateString('ro-RO', { day: '2-digit', month: 'long' });
    var ziuaSaptamanii = ['Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri'][i - 1];

    var th = document.querySelector(`table tr th:nth-child(${i + 1})`);
    if (th) {
        th.textContent = `${ziuaSaptamanii} (${dataFormatata})`;
}

if (!esteWeekend && i === dataCurenta.getDay()) {
    var celule = document.querySelectorAll(`table tr td:nth-child(${i + 1}), table tr th:nth-child(${i + 1})`);
    celule.forEach(function(celula) {
    celula.style.backgroundColor = '#D3D3D3';
});
}
}
}

// Observator pentru a detecta când tabelul este adăugat în DOM și a evidenția ziua curentă
const observer = new MutationObserver(function(mutations) {
if (document.querySelector('table')) {
    observer.disconnect();
    evidentiereZiCurenta();
}
});

observer.observe(document.body, { childList: true, subtree: true });

// Funcția principală pentru afișarea conținutului pe baza selecțiilor
function displayContent(displayArea, selections) {
const rowMap = {};
Object.keys(selections).forEach(id => {
const rowNumber = id.split('r')[1][0];
if (!rowMap[rowNumber]) {
    rowMap[rowNumber] = [];
}
rowMap[rowNumber].push(selections[id]);
});

const maxColumns = Math.max(...Object.values(rowMap).map(items => items.length));
displayArea.style.gridTemplateColumns = 'repeat(' + maxColumns + ', 1fr)';

Object.keys(rowMap).forEach(row => {
rowMap[row].forEach((selection, index) => {
const contentBox = document.createElement('div');
contentBox.className = selection.title === 'Meniul Săptămânii' ? 'meniuBox' : 'contentBox';
contentBox.style.gridColumnStart = index + 1;
contentBox.style.gridRowStart = parseInt(row);
displayArea.appendChild(contentBox);

const title = document.createElement('div');
title.className = 'infobox-title';
title.textContent = selection.title;
contentBox.appendChild(title);

fetchContent(selection, contentBox);
});
});
}

// Funcția pentru a prelua și afișa conținutul pentru fiecare selecție
function fetchContent(selection, container) {
const grupa = selection.group;
const titleText = selection.title;

fetch('../pages/extrage_informatii_infodisplay.php?grupa=' + grupa)
.then(response => response.json())
.then(data => {
let index = 0;
function displayFile() {
container.innerHTML = '';
const file = data[index];
const title = document.createElement('div');
title.textContent = titleText;
title.className = 'infobox-title';
container.appendChild(title);

// Procesare PDF
if (file.nume_fisier && file.nume_fisier.endsWith('.pdf')) {
    const pdfPath = file.temp_path + '/' + file.nume_fisier;
    pdfjsLib.getDocument(pdfPath).promise.then(pdfDoc => {
    pdfDoc.getPage(1).then(page => {
    const viewport = page.getViewport({ scale: 1 });
    const canvas = document.createElement('canvas');
    canvas.width = viewport.width;
    canvas.height = viewport.height;
    container.appendChild(canvas);

    const renderContext = {
    canvasContext: canvas.getContext('2d'),
    viewport: viewport
};
page.render(renderContext);
});
}).catch(err => {
console.error('Nu există astfel de documente:', err);
container.textContent = 'Nu există astfel de documente';
});
} else if (file.html) { // Procesare HTML pentru anunț
    const htmlContainer = document.createElement('div');
    htmlContainer.innerHTML = file.html;
    container.appendChild(htmlContainer);

    // Aplicăm stilurile din anunț (dacă există)
    const styleTags = htmlContainer.getElementsByTagName('style');
    for (let i = 0; i < styleTags.length; i++) {
        document.head.appendChild(styleTags[i].cloneNode(true));
    }

    // Stilizare suplimentară dacă este necesar
    htmlContainer.style.width = '100%';
    htmlContainer.style.overflow = 'auto';

    // Aplicăm CSS-ul necesar pentru HTML-ul de meniu
    const style = document.createElement('style');
    style.textContent = `
    table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
border: 1px solid black;
padding: 5px;
text-align: left;
}
th {
background-color: #f2f2f2;
}
.coloana-ore {
width: 60px;
}
.inputText {
font-size: 16px;
width: 100%;
min-height: 20px;
border: none;
background-color: transparent;
resize: none;
overflow: hidden;
}`;
document.head.appendChild(style);
}

// Stilizarea containerului pentru vizualizarea uniformă
container.style.border = '4px solid #ccc';
container.style.boxShadow = '0 0 10px #666';
container.style.margin = '10px';
container.style.display = 'flex';
container.style.flexDirection = 'column';
container.style.justifyContent = 'center';
container.style.alignItems = 'center';

index = (index + 1) % data.length;
setTimeout(displayFile, file.nume_fisier && file.nume_fisier.endsWith('.pdf') ? 8000 : 5000);

evidentiereZiCurenta();
}

displayFile();
})
.catch(error => {
console.error('Nu au fost găsite documente pentru ', error);
container.textContent = '' + titleText;
});
}
</script>
EOD;

// Înlocuim placeholder-ul cu datele JSON reale
$bodyScript = str_replace('SELECTIONS_JSON_PLACEHOLDER', $selectionsJson, $bodyScript);

// Inserăm scriptul în secțiunea <body> a HTML-ului
$htmlContent = str_replace('</body>', $bodyScript . '</body>', $htmlContent);

// Calea către fișierul HTML care va fi generat
$filePath = "../avizier/{$layoutNameSanitized}.html";

// Ștergem fișierul existent dacă există
if (file_exists($filePath)) {
    unlink($filePath);
}

// Scriem conținutul HTML în fișier
file_put_contents($filePath, $htmlContent);

// Returnăm un răspuns JSON care indică succesul și numele layout-ului
echo json_encode(['success' => true, 'layoutNameSanitized' => $layoutNameSanitized]);
} else {
    // În cazul în care solicitarea nu este de tip POST, returnăm un mesaj de eroare
    echo json_encode(['success' => false, 'message' => 'Date insuficiente.']);
}
?>
