<!DOCTYPE html>
<html>
<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Accesarea directorului părinte și adăugarea căii la config.php
require_once(dirname(__DIR__, 2) . '/config.php'); // Acesta va defini ROOT_PATH

require_once(ROOT_PATH . 'pages/functii_si_constante.php');
require_once('redenumire_fisiere.php');

  // Apelarea functiei pentru a umple variabilele de sesiune, inclusa in functii_si_constante.php
  determina_variabile_utilizator($conn);

$nume_copil = get_nume_copil($_SESSION['grupa_clasa_copil']);
$file_name = create_file_name($nume_copil, $_SESSION['grupa_clasa_copil']);
?>
<script>
    const generatedFileName = "<?php echo $file_name; ?>";
</script>

<head>
  <title>TID4K - <?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="/favicon.ico">
  <script src="heic2any.min.js"></script>

</head>
<body>

<header class="header-container">
    <div class="grupa-clasa-copil-wrapper">
        <a href="/pages/grupa_clasa_copil.php">
            <h1 class="nume-grupa"><?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?></h1>
        </a>

        <a href="/pages/grupa_clasa_copil.php" ><div class="logo"></div></a>
    </div>
    <div class="profesori-search-container">
        <div class="profesori-container"></div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Căutare...">
        </div>
        <div class="separator"></div>
    </div>
</header>

<!--aici este codul HTML pentru coloana din stanga, care contine fereastra de afisare a continutului fisierului selectat si butoanele de upload si download-->
    <div class="column-left">
       <h2 class="col-stanga-titlu" id="col-stanga-titlu">Ce am facut de curand</h2>
       <!--afiseaza titlul continutului peste fereastra de afisare iframe sau img-->
           <div id="file-title-container" style="position: absolute; display: flex; color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.95); z-index: 3; left: 50%; transform: translateX(-50%); <!--border: 2px solid; border-color: yellow;-->">
            <p id="file-title"></p>
            </div>


         <iframe id="last_uploaded_file" class="fixed-iframe" style="display: none; width: 100%; height: 460px; border: 1px solid orange; box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);"></iframe>

            <img id="last_uploaded_image" class="fixed-img" src="" style="display: none; width: 100%; height: auto; border: 1px solid orange; box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);">

      <!--acesta este un apel al functiei care afiseaza butoanele de "Upload" si "Download" din functii_si_constante.php-->
      <?php
      afiseaza_butoane_upload_download()
      ?> <!--aici se termina apelul functiei afiseaza_butoane_upload_download()-->
    </div>

       <!-- Elemente pentru previzualizarea fișierelor care urmeaza a fi incarcate -->
<div id="filePreviewContainer" style="display: none;">
  <img id="imagePreview" style="display: none;" />
  <video id="videoPreview" style="display: none;" controls></video>
  <iframe id="pdfPreview" style="display: none;"></iframe>
</div>


<!--   aici este codul HTML pentru coloana din dreapta care afiseaza intreaga lista a fisierelor de la utilizator si profesori-->
    <div class="column-right">
      <h2 class="col-dreapta-titlu" id="col-dreapta-titlu">Documente</h2>
        <div id="files-column" class="files-column"> <!--acest div se ocupa in style.css de adaptarea automata a fontului la browserulmobil-->
            <div id="file_list_utilizator"></div>
            <div id="file_list_ceilalti"></div>
        </div>
    </div>

<!--    acesta este un apel al functiei care gestioneaza clic-urile butoanelor "upload" si "Download" din functii_si_constante.php-->
    <?php
    echo getGestioneazaUploadScript();
    ?> <!--aici se termina acest apel al functiei getGestioneazaUploadScript()-->

    <?php echo getShowFilePreview(); ?> <!--apelul functiei care permite previzualizarea-->

    <?php echo convertAppleFileScript(); ?> <!--apelul functiei care converteste formatul apple-->

    <?php echo configurePreviewStyleScript(); ?> <!--apelul functiei care seteaza dimensiunile si stilul ferestrei de preview-->

    <?php echo showRenameModalScript(); ?> <!--apelul functiei care permite editarea numelului
    fisierului ce urmeaza a fi uploadat-->

    <?php echo rezultateCautareScript(); ?> <!--apelul functiei care permite cautarea -->


  <footer class="footer-container">
    <p>TID4K &copy; 2023 - Gradinita 65</p>
  </footer>

<script>
let files_ceilalti = []; // variabila care se ocupa de fisierele asociate profesorilor
let files_utilizator = []; // variabila care se ocupa de fisierele asociate utilizatorului curent
let firstFileDisplayed = false; // fisierul primul din lista care se afiseza la incarcare
let displayedFileSrc = ''; // fisierul afisat in fereastra iFrame care si poate fi downloadat
var id_utilizator = <?php echo json_encode($id_utilizator);?>


function fetchFiles() {
    fetch('fetch_files.php')
        .then(response => response.json())
        .then(output => {
            files_utilizator = output.files_utilizator;
            files_ceilalti = output.files_ceilalti;
            status= output.status;


            let fileListHtmlUtilizator = '<h3>...de la dumneavoastra:</h3><ul>';
            let fileListHtmlCeilalti;

           if (status === 'parinte' || status === 'elev') {
           fileListHtmlCeilalti = '<h3>...de la profesori:</h3><ul>';
           } else if (status === 'director' || status === 'administrator' || status === 'secretara' || status === 'contabil') {
           fileListHtmlCeilalti = '<h3>...de la părinți, profesori și elevi:</h3><ul>';
           } else {
           fileListHtmlCeilalti = '<h3>...de la părinți:</h3><ul>';
           }


            if (files_utilizator.length > 0 || files_ceilalti.length > 0) {
            files_utilizator.forEach(file => {
                      let thumbnailPath;
            if (file.extensie.toLowerCase() === 'pdf') {
            thumbnailPath = temp_paths[file.id_utilizator] + '/Thumbnailuri/' + file.nume_fisier.replace('.pdf', '.png');
            } else if (['jpeg', 'jpg', 'png'].includes(file.extensie.toLowerCase())) {
            thumbnailPath = temp_paths[file.id_utilizator] + '/' + file.nume_fisier;
            } else {
            thumbnailPath = ''; // Calea implicită în cazul în care extensia nu este recunoscută
            }
                let fileListHtml = '<li class="file-item" data-id="' + file.id_info + '"><a href="javascript:void(0);" id="file_' + file.id_info + '">' +
                    '<img src="' + thumbnailPath + '" class="file-thumbnail" alt="thumbnail">' + // Adăugați miniatura înaintea numelui fișierului
                    '<span class="file-name">' + file.nume_fisier + '</span>' +
                    '</a> - ' +
                    '<span class="file-user-name">' + file.nume_prenume + '</span>' +
                    ' - ' +
                    '<span class="file-date">' + new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' }) + '</span>' +
                    '</li>';

    fileListHtmlUtilizator += fileListHtml;
});

         files_ceilalti.forEach(file => {
    let thumbnailPath;
    if (temp_paths[file.id_utilizator] !== undefined) {
        if (file.extensie.toLowerCase() === 'pdf') {
            thumbnailPath = temp_paths[file.id_utilizator] + '/Thumbnailuri/' + file.nume_fisier.replace('.pdf', '.png');
        } else if (['jpeg', 'jpg', 'png'].includes(file.extensie.toLowerCase())) {
            thumbnailPath = temp_paths[file.id_utilizator] + '/' + file.nume_fisier;
        } else {
            thumbnailPath = ''; // Calea implicită în cazul în care extensia nu este recunoscută
        }
    } else {
        console.error("Calea directorului temporar pentru utilizatorul", file.id_utilizator, "nu a fost găsită.");
        thumbnailPath = ''; // Setează calea implicită în cazul în care nu există o cale pentru directorul temporar
    }

console.log(thumbnailPath); // Afișează calea în consolă
                let fileListHtml = '<li class="file-item" data-id="' + file.id_info + '"><a href="javascript:void(0);" id="file_' + file.id_info + '">' +
                    '<img src="' + thumbnailPath + '" class="file-thumbnail" alt="thumbnail">' + // Adăugați miniatura înaintea numelui fișierului
                    '<span class="file-name">' + file.nume_fisier + '</span>' +
                    '</a> - ' +
                    '<span class="file-user-name">' + file.nume_prenume + '</span>' +
                    ' - ' +
                    '<span class="file-date">' + new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' }) + '</span>' +
                    '</li>';

    fileListHtmlCeilalti += fileListHtml;
});


                fileListHtmlUtilizator += '</ul>';
                fileListHtmlCeilalti += '</ul>';
                document.getElementById('file_list_utilizator').innerHTML = fileListHtmlUtilizator;
                document.getElementById('file_list_ceilalti').innerHTML = fileListHtmlCeilalti;

//codul de ascultare eveniment de click pe fisierul care se afiseaza in iframe sau img
files_utilizator.concat(files_ceilalti).forEach((file, index) => {
    console.log(file); // Acest rând va afișa obiectul file în consolă
    document.getElementById('file_' + file.id_info).addEventListener('click', (event) => {
        event.preventDefault();
        console.log('Clicked file:', file.nume_fisier);
        displayFile(event, file.id_info, file.extensie, file.nume_fisier, file.id_utilizator);

     // Revino la poziția inițială a paginii cu derulare fluidă
        scrollToTop();

    });

// Adăugați acest fragment de cod pentru a afișa primul fișier la încărcarea paginii
    if (!firstFileDisplayed && index === 0) {
        firstFileDisplayed = true;
        fetchTempPaths().then(() => {
            displayFile(new MouseEvent('click'), file.id_info, file.extensie, file.nume_fisier, file.id_utilizator);
        });
    }
});

          } else {
                document.getElementById('last_uploaded_file').src = '';
                document.getElementById('file_list_utilizator').innerHTML = 'Niciun fișier în baza de date.';
                document.getElementById('file_list_ceilalti').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('A apărut o eroare la preluarea fișierelor:', error);
        });
}


//functie de curatare a numelui fisierului de caractere nepotrivite, care pot genera erori
function cleanFileName(name) {
    // Listează caracterele interzise aici. Această listă poate varia în funcție de sistemul de operare și sistemul de fișiere.
    var forbiddenChars = ['#','<', '>', ':', '"', '/', '\\', '|', '?', '*'];
    var cleanName = name;
    for (var i = 0; i < forbiddenChars.length; i++) {
        // Înlocuiește fiecare caracter interzis cu un underline
        cleanName = cleanName.split(forbiddenChars[i]).join('_');
    }
    return cleanName;
}


//acesta este codul care se ocupa de incarcarea fisierelor introduse de utilizator
fileToUpload.addEventListener('change', (event) => {
    event.preventDefault(); // Previne comportamentul implicit al evenimentului 'change'
    if (fileToUpload.files.length > 0) {
        if (fileToUpload.files[0] && /\.(heic|heif|heis)$/i.test(fileToUpload.files[0].name)) {
            const appleFile = fileToUpload.files[0];
            convertAppleFile(appleFile)
                .then(convertedFile => {
                    const renamedFile = new File([convertedFile], generatedFileName + convertedFile.name.substr(convertedFile.name.lastIndexOf('.')), { type: convertedFile.type });
                    showFilePreview(convertedFile);
                    showRenameModal(convertedFile, renamedFile);
                })
                .catch(error => {
                    console.error('A apărut o eroare în timpul conversiei:', error);
                });
        } else {
            if (fileToUpload.files.length > 0) {
                const originalFile = fileToUpload.files[0];
                const renamedFile = new File([originalFile], generatedFileName + originalFile.name.substr(originalFile.name.lastIndexOf('.')), { type: originalFile.type });
                showFilePreview(renamedFile);
                showRenameModal(originalFile, renamedFile);
            }
        }
    }
});

//acesta este codul care stabileste caile catre relative catre fisierele care trebuiesc listate
let temp_paths = {};


function fetchTempPaths() {
    return fetch("cale_dir_temp.php")
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            console.log("Datele primite de la cale_dir_temp.php:", data);

            data.forEach((user) => {
                temp_paths[user.id_utilizator] = user.temp_path;
            });
            // Adăugați această linie pentru a verifica valorile temp_paths după ce sunt setate
            console.log('Calea dir temporar dupa setari:', temp_paths);
            return fetchFiles(); // este apelat aici fetchFiles pentru a permite ca valoarea variabilei temp_paths[user.id_utilizator] = user.temp_path sa fie disponibila si in fetchFiles << e nevoie de ea pentru afisarea thumbnailurilor in listarea fisierelor (de la profesor si de la dumneavoastra)
        })
        .catch((error) => {
            console.error("There was a problem with the fetch operation:", error);
        });
}

fetchTempPaths();


//functia displayFile care se ocupa de afisarea continutului last_uploaded_file sau last_uploaded_image in fereastra de afisare iframe sau img
function displayFile(event, id_info, extensie, nume_fisier, id_utilizator) {
    event.preventDefault();
    event.stopPropagation();

    const fileNameElements = document.querySelectorAll('.file-name');
    fileNameElements.forEach(element => {
        if (element.parentElement.id === `file_${id_info}`) {
            element.classList.add('file-name-selected');
        } else {
            element.classList.remove('file-name-selected');
        }
    });

    let temp_file_url = temp_paths[id_utilizator];
    let iframeSrc = temp_file_url + nume_fisier;

    if (extensie === 'pdf' || extensie === 'mp4') {
        document.getElementById('last_uploaded_file').src = iframeSrc;
        document.getElementById('last_uploaded_image').style.display = 'none';
        document.getElementById('last_uploaded_file').style.display = 'block';
        displayedFileSrc = iframeSrc;
    } else if (extensie === 'png' || extensie === 'jpg' || extensie === 'jpeg' || extensie === 'bmp') {
        document.getElementById('last_uploaded_image').src = iframeSrc;
        document.getElementById('last_uploaded_file').style.display = 'none';
        document.getElementById('last_uploaded_image').style.display = 'block';
        displayedFileSrc = iframeSrc;
    }

    console.log('temp_file_url:', temp_file_url);

    // Actualizează și afișează titlul fișierului selectat
    const fileTitleContainer = document.getElementById('file-title-container');
    const fileTitle = document.getElementById('file-title');
    fileTitle.textContent = nume_fisier;
    fileTitleContainer.style.display = 'block';
}


document.getElementById('downloadButton').addEventListener('click', function () {
    if (displayedFileSrc) {
        let downloadLink = document.createElement('a');
        downloadLink.href = displayedFileSrc;
        downloadLink.download = ''; // Aceasta va solicita browserul să folosească numele fișierului din URL pentru descărcare
        downloadLink.click();
    } else {
        alert('Niciun fișier selectat pentru descărcare.');
    }
});


function fetchProfesori() {
    fetch('profesori_grupa_clasa.php')
        .then(response => response.json())
        .then(profesori => {
            if (profesori.length > 0) {
                let profesoriHtml = '';

                profesori.forEach(profesor => {
                    if (profesor.nume_prenume) {
                      profesoriHtml += '<div class="profesor-info">';
profesoriHtml += '<span class="profesor-label">profesor:</span>';
profesoriHtml += '<span class="profesor-nume">' + profesor.nume_prenume + '</span>';
profesoriHtml += '<span class="profesor-label">, email:</span>';
profesoriHtml += '<span class="profesor-email">' + profesor.email + '</span>';
profesoriHtml += '</div>';

                    }
                });

                const profesoriContainer = document.createElement('div');
                profesoriContainer.innerHTML = profesoriHtml;
               document.querySelector('.profesori-container').appendChild(profesoriContainer);

            }
        })
        .catch(error => {
            console.error('A apărut o eroare la preluarea profesorilor:', error);
        });
}


fetchProfesori();

//functia care face ca revenirea la fereastra de afisare. iframe sau img, sa fie mult mai fluida
function scrollToTop(elapsedTime) {
    const duration = 1000; // Durata în milisecunde pentru derulare
    const easeInOutCubic = (t) => {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    };

    const startTime = elapsedTime || performance.now();
    const startPosition = window.scrollY;

    if (startPosition === 0) {
        return;
    }

    const animateScroll = (currentTime) => {
        const timeElapsed = currentTime - startTime;
        const progress = Math.min(timeElapsed / duration, 1);
        const easing = easeInOutCubic(progress);

        window.scrollTo(0, startPosition - startPosition * easing);

        if (progress < 1) {
            window.requestAnimationFrame(animateScroll);
        }
    };

    window.requestAnimationFrame(animateScroll);
}


</script>



</body>
</html>
