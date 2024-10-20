<?php
 if (session_status() == PHP_SESSION_NONE) {
     session_start();
 }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $selections = $data['selections'] ?? [];  // Asigură că avem selecții valide
    $_SESSION['localStorageAccesibil'] = $selections;  // Sincronizare cu sesiunea

    $htmlContent = $data['html'];  // Conținutul HTML primit pentru generare

    $headContent = '
    <link rel="stylesheet" href="../pages/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js";
    </script>
     <style>
        body, html {
            overflow-y: hidden; /* Ascunde bara de derulare verticală */
            overflow-x: hidden; /* Ascunde bara de derulare orizontală */
            height: auto; /* Asigură-te că body-ul ocupă întreg ecranul */
        }
        /* Pentru a ascunde scrollbar-urile în interiorul elementelor cu overflow */
        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    ';

    $htmlContent = str_replace('<head>', '<head>' . $headContent, $htmlContent);

    // Script pentru reconstituirea UI-ului bazat pe selecțiile sincronizate
    $selectionsJson = json_encode($selections);
    $bodyScript = "
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selections = $selectionsJson;
    displayContent(document.getElementById('displayArea'), selections);
});

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
                title.style.fontSize = '20px';
                title.style.fontWeight = 'bold';
                title.style.marginBottom = '10px';
                container.appendChild(title);

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
                        console.error('Documentul PDF nu poate fi afișat:', err);
                        container.textContent = 'Documentul PDF nu poate fi afișat.';
                    });
                } else if (file.nume_fisier) {
                    const img = document.createElement('img');
                    img.src = file.temp_path + '/' + file.nume_fisier;
                    img.alt = 'Image for ' + grupa;
                    img.style.width = '100%';
                    container.appendChild(img);
                } else if (file.html) {
                    const htmlContainer = document.createElement('div');
                    htmlContainer.innerHTML = file.html;
                    container.appendChild(htmlContainer);

                    htmlContainer.style.width = container.clientWidth >= 2080 ? '2070px' : '100%';
                    htmlContainer.style.overflow = 'auto';

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
                            font-size: 14px;
                            width: 100%;
                            min-height: 10px;
                            max-height: 200px;
                            border: none;
                            background-color: transparent;
                            resize: none;
                            overflow: auto;
                        }`;
                    document.head.appendChild(style);
                }

                container.style.border = '4px solid #ccc';
                container.style.boxShadow = '0 0 10px #666';
                container.style.margin = '10px';
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.justifyContent = 'center';
                container.style.alignItems = 'center';

                index = (index + 1) % data.length;
                setTimeout(displayFile, file.nume_fisier && file.nume_fisier.endsWith('.pdf') ? 8000 : 5000);
            }

            displayFile();
        })
        .catch(error => {
            console.error('Nu au fost găsite documente pentru', error);
            container.textContent = '' + titleText;
        });
}

     function adjustHeight(textArea) {
            textArea.style.height = 'auto'; // Resetează înălțimea pentru recalculare
            textArea.style.height = textArea.scrollHeight + 'px'; // Ajustează înălțimea la înălțimea conținutului
        }

        document.addEventListener('DOMContentLoaded', function() {
            var textAreas = document.querySelectorAll('textarea.inputText');
            
            textAreas.forEach(function(textArea) {
                adjustHeight(textArea); // Ajustează înălțimea inițială pe baza conținutului preexistent
                textArea.addEventListener('input', function() {
                    adjustHeight(textArea); // Ajustează înălțimea la fiecare modificare a conținutului
                });
            });
        });
</script>
";

    $htmlContent = str_replace('</body>', $bodyScript . '</body>', $htmlContent);

    $filePath = '../avizier/tid4k.html';

    if (file_exists($filePath)) {
        unlink($filePath);
    }

    file_put_contents($filePath, $htmlContent);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
