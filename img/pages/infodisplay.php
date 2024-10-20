<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';
require_once 'functii_si_constante.php';
// Apelarea functiei pentru a umple variabilele de sesiune
determina_variabile_utilizator($conn);

// script pentru salvarea tid4k.html in subdirectorul /../avizier/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'saveTid4k') {
    $htmlContent = $_POST['htmlContent'];
    $filePath = __DIR__ . '/../avizier/tid4k.html';// Ajustează calea dacă este necesar
    file_put_contents($filePath, $htmlContent);
    // Apelează scriptul pentru a adăuga JavaScript
    include('tid4k_plus_javascript.php');
    echo 'Pagina tid4k.html a fost salvată cu succes în /avizier/.';
    exit;
}

// extragerea ultimei inregistrari din tabela infodisplay selectiile pentru cadran-simulat
$sql = "SELECT * FROM infodisplay ORDER BY timp_ultima_modificare DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$ultimaInregistrare = $result->fetch_assoc();

// Verifică dacă timpul ultimei modificări este null și atribuie data și ora curentă dacă este așa
$timpUltimaModificare = $ultimaInregistrare['timp_ultima_modificare'] ?? date('Y-m-d H:i:s');
$dataOra = new DateTime($timpUltimaModificare);
$formatareDataOra = $dataOra->format('d.m.Y, H:i');

// Setarea variabilelor cu valorile din ultima înregistrare, dacă există
$cadran_simulatTitlu1Rand2 = isset($ultimaInregistrare['cadran_simulatTitlu1Rand2']) ? trim($ultimaInregistrare['cadran_simulatTitlu1Rand2']) : '';
$cadran_simulatTitlu1Rand3 = isset($ultimaInregistrare['cadran_simulatTitlu1Rand3']) ? trim($ultimaInregistrare['cadran_simulatTitlu1Rand3']) : '';
$cadran_simulatTitlu2Rand3 = isset($ultimaInregistrare['cadran_simulatTitlu2Rand3']) ? trim($ultimaInregistrare['cadran_simulatTitlu2Rand3']) : '';
$cadran_simulatTitlu3Rand3 = isset($ultimaInregistrare['cadran_simulatTitlu3Rand3']) ? trim($ultimaInregistrare['cadran_simulatTitlu3Rand3']) : '';
$numeUnitateScolara = isset($ultimaInregistrare['numeUnitateScolara']) ? trim($ultimaInregistrare['numeUnitateScolara']) : '';

// Transmiterea datelor către JavaScript
echo "<script>var ultimaInregistrare = " . json_encode($ultimaInregistrare) . ";</script>";
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Încarcă pdf.js din CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    </script>
</head>
<body>
 <!--    inserez aici cadranul-simulat ID4K-->
    <div id="id4kCadran" style="display:none; width:450px; height:300px; border:1px solid black;">
<!-- Rândul 1 -->
<div id="rand1" class="cadran_simulatfontMic" style="width:100%; height:5%; text-align: center;">
    <div id="numeUnitate" style="float:left; color: violet; text-shadow: 1px 1px 2px #000000;">
        <?php echo 'Unitatea Școlară DEMO'; ?>
    </div>
        <div class="logo-qrcode-mic">
            <img src="../logo_qr_code.png" style="width:100%; height:auto;">
        </div>
      <!-- Optional, afișarea celui care a configurat ultima dată ID4K -->
    <!--<div id="ultima-modificare" style="float: right; text-align: right; color: gray; font-style: italic; font-size: small; margin-left: 10px;">-->
    <!--    <span style="font-weight: bold;"><?php echo $ultimaInregistrare['nume_prenume']; ?></span>,-->
    <!--    <?php echo $ultimaInregistrare['status']; ?>,-->
    <!--    modificat la: <?php echo $formatareDataOra; ?>-->
    <!--</div>-->
    <div style="clear: both;"></div>
    <!-- Sfârșitul afișării celui care a configurat ultima dată ID4K -->
</div> <!-- Sfârșitul codului pentru rândul 1 -->

<!-- Rândul 2 -->
<div id="rand2" class="cadran_simulatfontMare" style="width:100%; height15%; text-align: center;">
    <select id="<?php echo $cadran_simulatTitlu1Rand2; ?>" data-chenar="chenar1Rand2" class="cadran_simulatfontMare cadran_simulat_select" style="color: green; text-shadow: 1px 1px 2px #000000;">
      <option value="" disabled selected><?php echo $cadran_simulatTitlu1Rand2; ?></option>
      <!-- Restul opțiunilor vor fi adăugate dinamic folosind JavaScript -->
    </select>
    <div id="chenar1Rand2" class="chenar_mare" style="width:80%; height: 120px; border-color: red; margin-bottom: 3px;"></div>  <!-- culoare roșie -->
  </div>
<!-- Rândul 3 -->
<div id="rand3" class="fontMic" style="width:100%; height:15%; text-align: left; font-size:small; margin-left: 15px;">

  <div style="float:left; width: 33%;">
    <select id="<?php echo $cadran_simulatTitlu1Rand3; ?>" data-chenar="chenar1Rand3" class="cadran_simulatfontMic cadran_simulat_select" style="width: 90%; color: blue; text-shadow: 1px 1px 2px #000000;">
      <option value="" disabled selected><?php echo $cadran_simulatTitlu1Rand3; ?></option>
    </select>
    <div id="chenar1Rand3" class="chenar_mic" style="width:80%; height: 40px; border: 1px solid blue;"></div>  <!-- culoare albastră -->
  </div>

  <div style="float:left; width: 33%;">
    <select id="<?php echo $cadran_simulatTitlu2Rand3; ?>" data-chenar="chenar2Rand3" class="cadran_simulatfontMic cadran_simulat_select" style="width: 90%; color: darkgray; text-shadow: 1px 1px 2px #000000;">
      <option value="" disabled selected><?php echo $cadran_simulatTitlu2Rand3; ?></option>
    </select>
    <div id="chenar2Rand3" class="chenar_mic" style="width:80%; height: 40px; border: 1px solid darkgray;"></div>  <!-- culoare gri închis -->
  </div>

  <div style="float:left; width: 33%;">
    <select id="<?php echo $cadran_simulatTitlu3Rand3; ?>" data-chenar="chenar3Rand3" class="cadran_simulatfontMic cadran_simulat_select" style="width: 90%; color: gray; text-shadow: 1px 1px 2px #000000;">
      <option value="" disabled selected><?php echo $cadran_simulatTitlu3Rand3; ?></option>
    </select>
    <div id="chenar3Rand3" class="chenar_mic" style="width:80%; height: 40px; border: 1px solid gray;"></div>  <!-- culoare gri -->
  </div>

</div>
</div>
</body>
<!--aici se termina codul pentru cadranul-simulat ID4K-->
</html>

<!--codul javascript pentru cadranul-simulat ID4K-->
<script>
// Funcția existentă pentru afișarea/ascunderea div-ului
function toggleDisplay() {
    var x = document.getElementById("id4kCadran");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
        var capturedHTML = captureAndProcessCadran(); // Stochează HTML-ul capturat în variabila capturedHTML
        saveTid4kHTML(capturedHTML); // Apelează funcția de salvare cu HTML-ul capturat ca argument
    }
}


function saveTid4kHTML(htmlContent) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../pages/infodisplay.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log('tid4k.html a fost salvat cu succes.');
        } else {
            console.error('Eroare la salvarea tid4k.html.');
        }
    };
    xhr.send('action=saveTid4k&htmlContent=' + encodeURIComponent(htmlContent));
}

  // id_utilizator cu valoarea din functii_si_constante
    var idUtilizator = <?php echo $_SESSION['id_utilizator']; ?>;
    var numeUtilizator = '<?php echo $_SESSION['nume_prenume_curent']; ?>';
    var statusUtilizator = '<?php echo $_SESSION['status']; ?>';

      // construire obiect pentru stocarea valorilor selectate de catre utilizator in cadran-simulat
var selectiiUtilizator = {
    id_utilizator: idUtilizator,
    nume_prenume: numeUtilizator,
    status: statusUtilizator,
    numeUnitateScolara: '',
    cadran_simulatTitlu1Rand2: '',
    cadran_simulatTitlu1Rand3: '',
    cadran_simulatTitlu2Rand3: '',
    cadran_simulatTitlu3Rand3: ''
};

// Inițializează valorile selectate cu valorile implicite
selectiiUtilizator.numeUnitateScolara = document.getElementById('numeUnitate').innerText;
selectiiUtilizator.cadran_simulatTitlu1Rand2 = '<?php echo $cadran_simulatTitlu1Rand2; ?>';
selectiiUtilizator.cadran_simulatTitlu1Rand3 = '<?php echo $cadran_simulatTitlu1Rand3; ?>';
selectiiUtilizator.cadran_simulatTitlu2Rand3 = '<?php echo $cadran_simulatTitlu2Rand3; ?>';
selectiiUtilizator.cadran_simulatTitlu3Rand3 = '<?php echo $cadran_simulatTitlu3Rand3; ?>';


document.addEventListener("DOMContentLoaded", function() {
  // Funcția existentă pentru mărirea sau micșorarea cadrului
  document.getElementById('id4kCadran').addEventListener("click", function() {
    if (this.style.width === "600px") {
      this.style.width = "450px";
      this.style.height = "300px";
    } else {
      this.style.width = "600px";
      this.style.height = "450px";
    }
  });

  // Inserăm codul pentru a popula selectoarele cu date din PHP
  var rezultate = <?php echo json_encode(cadran_simulatValoriVariabile()); ?>;

  // Popularea selectoarelor afisate in cadran-simulat
  var select1 = document.getElementById('<?php echo $cadran_simulatTitlu1Rand2; ?>');
  var select2 = document.getElementById('<?php echo $cadran_simulatTitlu1Rand3; ?>');
  var select3 = document.getElementById('<?php echo $cadran_simulatTitlu2Rand3; ?>');
  var select4 = document.getElementById('<?php echo $cadran_simulatTitlu3Rand3; ?>');


  // Setează valorile selectoarelor cu valorile din ultima înregistrare
  function seteazaValoriSiIncarcaContinut() {
   if (ultimaInregistrare) {
    console.log("Ultima înregistrare:", ultimaInregistrare);

    if (select1 && ultimaInregistrare.cadran_simulatTitlu1Rand2) {
        console.log("Setare select1:", ultimaInregistrare.cadran_simulatTitlu1Rand2);
        select1.value = ultimaInregistrare.cadran_simulatTitlu1Rand2;
        incarcaContinutInChenar(select1.id);
    }
    if (select2 && ultimaInregistrare.cadran_simulatTitlu1Rand3) {
        console.log("Setare select2:", ultimaInregistrare.cadran_simulatTitlu1Rand3);
        select2.value = ultimaInregistrare.cadran_simulatTitlu1Rand3;
        incarcaContinutInChenar(select2.id);
    }
    if (select3 && ultimaInregistrare.cadran_simulatTitlu2Rand3) {
        console.log("Setare select3:", ultimaInregistrare.cadran_simulatTitlu2Rand3);
        select3.value = ultimaInregistrare.cadran_simulatTitlu2Rand3;
        incarcaContinutInChenar(select3.id);
    }
    if (select4 && ultimaInregistrare.cadran_simulatTitlu3Rand3) {
        console.log("Setare select4:", ultimaInregistrare.cadran_simulatTitlu3Rand3);
        select4.value = ultimaInregistrare.cadran_simulatTitlu3Rand3;
        incarcaContinutInChenar(select4.id);
    }
}
  }

  rezultate['lista_valori_variabile_cadran_simulat'].forEach(function(item) {
    var optiune = document.createElement('option');
    optiune.text = item;
    optiune.value = item;
    select1.add(optiune);
  });

  rezultate['lista_grupe_disponibile'].forEach(function(item) {
    var optiune = document.createElement('option');
    optiune.text = item;
    optiune.value = item;
    select2.add(optiune);
  });

  rezultate['lista_clase_disponibile'].forEach(function(item) {
    var optiune = document.createElement('option');
    optiune.text = item;
    optiune.value = item;
    select3.add(optiune);
  });

  rezultate['lista_obligatorie_de_afisat_cadran_simulat'].forEach(function(item) {
    var optiune = document.createElement('option');
    optiune.text = item;
    optiune.value = item;
    select4.add(optiune);
  });

  // Logica pentru lista_obligatorie_de_afisat
  function eliminaOptiuneDinCelelalteSelectoare(idSelect, valoare) {
    const selectoare = document.querySelectorAll('.cadran_simulat_select');
    selectoare.forEach(function(select) {
      if (select.id !== idSelect) {
        let optiuni = Array.from(select.options);
        optiuni.forEach(function(optiune) {
          if (optiune.value === valoare) {
            select.removeChild(optiune);
          }
        });
      }
    });
  }

let optiuneSelectata = null;
var selectoare = document.querySelectorAll('.cadran_simulat_select');
selectoare.forEach(function(select) {
    select.addEventListener('change', function() {
        optiuneSelectata = this.value;
        eliminaOptiuneDinCelelalteSelectoare(this.id, this.value);
        incarcaContinutInChenar(this.id);
    });
});

  //de aici incepe codul care afiseaza efectiv informatia asociata fiecarui chenar pe baza optiunii selectate
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

    if (urlSursa) {
        // Loghează valoarea optiuneSelectata în consola browserului
        console.log("Valoarea optiuneSelectata: " + optiuneSelectata);
        console.log("URL cerere: " + urlSursa);

        let idChenar = select.getAttribute('data-chenar');
        let chenar = document.getElementById(idChenar);

        // Resetează conținutul chenarului
        chenar.innerHTML = '';

        var xhr = new XMLHttpRequest();
        console.log("Se deschide cererea HTTP");
        xhr.open("GET", urlSursa, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
                var data = JSON.parse(xhr.responseText);

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
            }
        };
        xhr.send();
    }
}

//partea de cod care supravegheaza selectiile utilizatorului si le trimite catre actualizare_infodisplay.php , de unde datele se stocheaza in tabela infodisplay
 var selectoare = document.querySelectorAll('.cadran_simulat_select');
    selectoare.forEach(function(select) {
        select.addEventListener('change', function() {
            var valoareSelectata = this.value.trim();
            var idSelect = this.id;

            // Actualizează obiectul selectiiUtilizator
            switch (idSelect) {
                case '<?php echo $cadran_simulatTitlu1Rand2; ?>':
                    selectiiUtilizator.cadran_simulatTitlu1Rand2 = valoareSelectata;
                    break;
                case '<?php echo $cadran_simulatTitlu1Rand3; ?>':
                    selectiiUtilizator.cadran_simulatTitlu1Rand3 = valoareSelectata;
                    break;
                case '<?php echo $cadran_simulatTitlu2Rand3; ?>':
                    selectiiUtilizator.cadran_simulatTitlu2Rand3 = valoareSelectata;
                    break;
                case '<?php echo $cadran_simulatTitlu3Rand3; ?>':
                    selectiiUtilizator.cadran_simulatTitlu3Rand3 = valoareSelectata;
                    break;
            }

            // Trimite datele actualizate la server
            actualizeazaInformatiiInDB();
        });
    });

    function actualizeazaInformatiiInDB() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/pages/actualizare_infodisplay.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

       var data = "id_utilizator=" + encodeURIComponent(selectiiUtilizator.id_utilizator) +
               "&nume_prenume=" + encodeURIComponent(selectiiUtilizator.nume_prenume) +
               "&status=" + encodeURIComponent(selectiiUtilizator.status) +
               "&numeUnitateScolara=" + encodeURIComponent(selectiiUtilizator.numeUnitateScolara) +
               "&cadran_simulatTitlu1Rand2=" + encodeURIComponent(selectiiUtilizator.cadran_simulatTitlu1Rand2) +
               "&cadran_simulatTitlu1Rand3=" + encodeURIComponent(selectiiUtilizator.cadran_simulatTitlu1Rand3) +
               "&cadran_simulatTitlu2Rand3=" + encodeURIComponent(selectiiUtilizator.cadran_simulatTitlu2Rand3) +
               "&cadran_simulatTitlu3Rand3=" + encodeURIComponent(selectiiUtilizator.cadran_simulatTitlu3Rand3);

        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                console.log(this.responseText);
            }
        };
        console.log("Trimitere date:", data);

        xhr.send(data);

         // Folosim setTimeout pentru a amâna trimiterea datelor cu 10 secunde
    // setTimeout(actualizeazaInformatiiInDB, 5000);

    }//aici se termina codul de actualizare_infodisplay

            // aceasta instructiune este pusa temporar aici
     seteazaValoriSiIncarcaContinut();// apeleaza functia corelata cu ultimaInregistrare

});

/*script pentru generarea paginii tid4k.html*/
function captureAndProcessCadran() {
    // Selectează elementul pe care dorești să-l elimini folosind ID-ul
const elementDeEliminat = document.getElementById('ultima-modificare');

// Verifică dacă elementul există și apoi elimină-l din DOM
if (elementDeEliminat) {
    elementDeEliminat.remove();
}
    var cadran = document.getElementById('id4kCadran').cloneNode(true);

    // logica pentru a elimina elementele <img> și <canvas> din zonele de afișare
    cadran.querySelectorAll('.chenar_mare, .chenar_mic').forEach(function(chenar) {
        // Elimină toate elementele <img> din zona de afișare curentă
        chenar.querySelectorAll('img').forEach(function(img) {
            img.remove();
        });

        // Elimină toate elementele <canvas> din zona de afișare curentă
        chenar.querySelectorAll('canvas').forEach(function(canvas) {
            canvas.remove();
        });
    });

    // Ajustează stilurile elementului clonat
    cadran.style.paddingTop = "20px"; // Ajustează această valoare conform înălțimii header-ului tău
    cadran.style.display = "block"; // Asigură că elementul este vizibil
    cadran.style.width = "97%"; // Setează lățimea dorită
    cadran.style.height = "auto"; // Setează înălțimea dorită
    cadran.style.border = "0px solid orange"; // Setează bordura dorită

    cadran.querySelectorAll('.chenar_mare').forEach(function(chenar) {
        chenar.style.width = "80%";
        chenar.style.height = "50%";
    });

    cadran.querySelectorAll('.chenar_mic').forEach(function(chenar) {
        chenar.style.width = "80%";
        chenar.style.height = "70%";
    });

    cadran.querySelectorAll('select').forEach(function(select) {
        var selectedText = select.options[select.selectedIndex].text;
        var div = document.createElement('div');
        div.textContent = selectedText;
        div.className = select.className;
        select.parentNode.replaceChild(div, select);
    });

    // Construirea structurii complete de HTML pentru tid4k.html
    var fullHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
            <meta http-equiv="Pragma" content="no-cache">
            <meta http-equiv="Expires" content="0">

            <title>TID4K Display</title>
            <link rel="stylesheet" type="text/css" href="../pages/style.css">
            <style>
            body, html {
            overflow-y: hidden; /* Ascunde bara de derulare verticală */
            overflow-x: hidden; /* Ascunde bara de derulare orizontală */
            height: 100%; /* Asigură-te că body-ul ocupă întreg ecranul */
            }
        </style>
        </head>
        <body>${cadran.outerHTML}
          <!-- Div pentru QR Cod Stânga Jos -->
    <div id="qrCodStanga" style="position: fixed; bottom: 0; left: 0; margin: 2px;">
        <!-- Conținutul QR Cod Stânga (se va adăuga dinamic prin JavaScript) -->
    </div>

    <!-- Div pentru QR Cod Dreapta Jos -->
    <div id="qrCodDreapta" style="position: fixed; bottom: 0; right: 0; margin: 2px;">
        <!-- Conținutul QR Cod Dreapta (se va adăuga dinamic prin JavaScript) -->
    </div>
        </body>
        </html>`;



    return fullHTML; // Returnează HTML-ul complet capturat
}

 /*aici se termina scriptul pentru tid4k.html*/
</script>
