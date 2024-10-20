<!DOCTYPE html>
<html>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once(dirname(__DIR__, 2) . '/config.php');
require_once(ROOT_PATH . 'pages/functii_si_constante.php');
require_once('redenumire_fisiere.php');

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
        <a href="/pages/grupa_clasa_copil.php"><div class="logo"></div></a>
    </div>
    <div class="profesori-search-container">
        <div class="profesori-container"></div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Căutare...">
        </div>
        <div class="separator"></div>
    </div>
</header>


<div class="column-left">
    <h2 class="col-stanga-titlu" id="col-stanga-titlu">Ce am făcut de curând!</h2>
    <div id="file-title-container" style="position: absolute; display: flex; color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.95); z-index: 3; left: 50%; transform: translateX(-50%);">
        <p id="file-title"></p>
    </div>
    <iframe id="last_uploaded_file" class="fixed-iframe" style="display: none; width: 100%; height: 460px; border: 1px solid orange; box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);"></iframe>
    <?php
    echo genereazaOverlayIframe(); // Apelul funcției pentru generarea overlay-ului pentru iframe
    echo overlayIframe(); //apelul functiei pentru ascultarea de clic pe overlay, download sau delete
    ?>
    <img id="last_uploaded_image" class="fixed-img" src="" style="display: none; width: 100%; height: auto; border: 1px solid orange; box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);">
    <?php
    echo genereazaOverlayImg(); // Apelul funcției pentru generarea overlay-ului pentru img
    echo overlayImg(); //apelul functiei pentru ascultarea de clic pe overlay, download sau delete
    ?>
</div>

<div id="filePreviewContainer" style="display: none;">
  <img id="imagePreview" style="display: none;" />
  <video id="videoPreview" style="display: none;" controls></video>
  <iframe id="pdfPreview" style="display: none;"></iframe>
</div>

<div class="column-right">
  <h2 class="col-dreapta-titlu" id="col-dreapta-titlu">Documente</h2>
  <div id="files-column" class="files-column">
    <div id="file_list_utilizator"></div>
    <div id="file_list_ceilalti"></div>
  </div>
</div>

<footer class="footer-container">
  <p>TID4K &copy; 2024</p>
</footer>

<script>
let files_ceilalti = [];
let files_utilizator = [];
let firstFileDisplayed = false;
let displayedFileSrc = '';
var id_utilizator = <?php echo json_encode($id_utilizator);?>;

function fetchFiles() {
    fetch('fetch_files.php')
        .then(response => response.json())
        .then(output => {
            files_utilizator = output.files_utilizator;
            files_ceilalti = output.files_ceilalti;
            status = output.status;

            let fileListHtmlUtilizator = '<h3>...de la dumneavoastră:</h3><ul>';
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
                        thumbnailPath = temp_paths[file.id_utilizator] + 'Thumbnailuri/' + file.nume_fisier.replace('.pdf', '.png');
                    } else if (['jpeg', 'jpg', 'png'].includes(file.extensie.toLowerCase())) {
                        thumbnailPath = temp_paths[file.id_utilizator] + file.nume_fisier;
                    } else {
                        thumbnailPath = '';
                    }
                    let fileListHtml = '<li class="file-item" data-id="' + file.id_info + '" data-path="' + temp_paths[file.id_utilizator] + file.nume_fisier + '"><a href="javascript:void(0);" id="file_' + file.id_info + '">'+
                        '<img src="' + thumbnailPath + '" class="file-thumbnail" alt="thumbnail">' +
                        '<span class="file-name">' + file.nume_fisier + '</span></a> - ' +
                        '<span class="file-user-name">' + file.nume_prenume + '</span> - ' +
                        '<span class="file-date">' + new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' }) + '</span>' +
                        '</li>';
                    fileListHtmlUtilizator += fileListHtml;
                });

                files_ceilalti.forEach(file => {
                    let thumbnailPath;
                    if (temp_paths[file.id_utilizator] !== undefined) {
                        if (file.extensie.toLowerCase() === 'pdf') {
                            thumbnailPath = temp_paths[file.id_utilizator] + 'Thumbnailuri/' + file.nume_fisier.replace('.pdf', '.png');
                        } else if (['jpeg', 'jpg', 'png'].includes(file.extensie.toLowerCase())) {
                            thumbnailPath = temp_paths[file.id_utilizator]  + file.nume_fisier;
                        } else {
                            thumbnailPath = '';
                        }
                    } else {
                        console.error("Calea directorului temporar pentru utilizatorul", file.id_utilizator, "nu a fost găsită.");
                        thumbnailPath = '';
                    }
                    console.log(thumbnailPath);
                    let fileListHtml = '<li class="file-item" data-id="' + file.id_info + '" data-path="' + temp_paths[file.id_utilizator] + file.nume_fisier + '"><a href="javascript:void(0);" id="file_' + file.id_info + '">'+
                        '<img src="' + thumbnailPath + '" class="file-thumbnail" alt="thumbnail">' +
                        '<span class="file-name">' + file.nume_fisier + '</span></a> - ' +
                        '<span class="file-user-name">' + file.nume_prenume + '</span> - ' +
                        '<span class="file-date">' + new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' }) + '</span>' +
                        '</li>';
                    fileListHtmlCeilalti += fileListHtml;
                });

                fileListHtmlUtilizator += '</ul>';
                fileListHtmlCeilalti += '</ul>';
                document.getElementById('file_list_utilizator').innerHTML = fileListHtmlUtilizator;
                document.getElementById('file_list_ceilalti').innerHTML = fileListHtmlCeilalti;

                files_utilizator.concat(files_ceilalti).forEach((file, index) => {
                    console.log(file);
                    document.getElementById('file_' + file.id_info).addEventListener('click', (event) => {
                        event.preventDefault();
                        console.log('Clicked file:', file.nume_fisier);
                        displayFile(event, file.id_info, file.extensie, file.nume_fisier, file.id_utilizator);

                        scrollToTop();
                    });

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
            console.log('Calea dir temporar dupa setari:', temp_paths);
            return fetchFiles();
        })
        .catch((error) => {
            console.error("There was a problem with the fetch operation:", error);
        });
}

fetchTempPaths();

function displayFile(event, id_info, extensie, nume_fisier, id_utilizator) {
    event.preventDefault();
    event.stopPropagation();

// Ascunde overlay-urile
    const overlayImg = document.getElementById("overlayImg");
    const overlayIframe = document.getElementById("overlayIframe");

    if (overlayImg) {
        overlayImg.style.display = "none";
        overlayImg.style.backgroundColor = "rgba(0, 0, 0, 0)";
        document.getElementById("downloadIconImg").style.visibility = "hidden";
        document.getElementById("deleteIconImg").style.visibility = "hidden";
    }

    if (overlayIframe) {
        overlayIframe.style.display = "none";
        overlayIframe.style.backgroundColor = "rgba(0, 0, 0, 0)";
        document.getElementById("downloadIconIframe").style.visibility = "hidden";
        document.getElementById("deleteIconIframe").style.visibility = "hidden";
    }

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
    } else if (['png', 'jpg', 'jpeg', 'bmp'].includes(extensie.toLowerCase())) {
        document.getElementById('last_uploaded_image').src = iframeSrc;
        document.getElementById('last_uploaded_file').style.display = 'none';
        document.getElementById('last_uploaded_image').style.display = 'block';
        displayedFileSrc = iframeSrc;
    }

    console.log('temp_file_url:', temp_file_url);

    const fileTitleContainer = document.getElementById('file-title-container');
    const fileTitle = document.getElementById('file-title');
    fileTitle.textContent = nume_fisier;
    fileTitleContainer.style.display = 'block';
}

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

function scrollToTop(elapsedTime) {
    const duration = 1000;
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

        // Funcția de simulare a scroll-ului
        const simulateScroll = () => {
            window.scrollBy(0, 100); // Scroll down
            setTimeout(() => {
                window.scrollBy(0, -100); // Scroll back up
            }, 500); // Așteptăm 500ms înainte de a face scroll înapoi
        };

        // Simulăm scroll-ul la sfârșitul încărcării paginii pentru a recalibra elementele fixe
        setTimeout(simulateScroll, 1000); // Așteptăm 1s pentru a ne asigura că pagina este complet încărcată
</script>

</body>
</html>
